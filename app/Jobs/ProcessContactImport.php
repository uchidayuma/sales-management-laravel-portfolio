<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessContactImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $fileKey;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @param string|null $fileKey
     */
    public function __construct($filePath, $fileKey = null)
    {
        $this->filePath = $filePath;
        $this->fileKey = $fileKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Update status to processing
            if ($this->fileKey) {
                $this->updateStatus('processing', $successCount, $errorCount, 'ファイルを処理中です');
            }

            // ファイルの存在確認
            if (!file_exists($this->filePath)) {
                Log::error('CSV import file not found', ['file' => $this->filePath]);
                if ($this->fileKey) {
                    $this->updateStatus('failed', $successCount, $errorCount, 'ファイルが見つかりません');
                }
                return;
            }

            // CSVファイルを開く
            $handle = fopen($this->filePath, 'r');
            if ($handle === false) {
                Log::error('Failed to open CSV file', ['file' => $this->filePath]);
                if ($this->fileKey) {
                    $this->updateStatus('failed', $successCount, $errorCount, 'ファイルを開くことができません');
                }
                return;
            }

            // ヘッダー行を読み込む
            $header = fgetcsv($handle);
            if ($header === false) {
                Log::error('Failed to read CSV header', ['file' => $this->filePath]);
                fclose($handle);
                if ($this->fileKey) {
                    $this->updateStatus('failed', $successCount, $errorCount, 'CSVヘッダーが読み込めません');
                }
                return;
            }

            // ヘッダーをトリム（余分な空白を除去）
            $header = array_map('trim', $header);

            // ヘッダーのインデックスマップを作成
            $headerMap = array_flip($header);
            $lineNumber = 2; // ヘッダーは1行目

            $batchData = [];
            $batchSize = 100; // 100件ごとにバッチ挿入

            // CSVデータを読み込む
            while (($row = fgetcsv($handle)) !== false) {
                try {
                    // 空行をスキップ
                    if (empty(implode('', $row))) {
                        $lineNumber++;
                        continue;
                    }

                    // 必須カラムを取得（キーが存在し、値がセットされているか確認）
                    $surname = (isset($headerMap['surname']) && isset($row[$headerMap['surname']])) ? trim($row[$headerMap['surname']]) : null;
                    $name = (isset($headerMap['name']) && isset($row[$headerMap['name']])) ? trim($row[$headerMap['name']]) : null;
                    $company_name = (isset($headerMap['company_name']) && isset($row[$headerMap['company_name']])) ? trim($row[$headerMap['company_name']]) : null;
                    $pref = (isset($headerMap['pref']) && isset($row[$headerMap['pref']])) ? trim($row[$headerMap['pref']]) : null;
                    $city = (isset($headerMap['city']) && isset($row[$headerMap['city']])) ? trim($row[$headerMap['city']]) : null;
                    $street = (isset($headerMap['street']) && isset($row[$headerMap['street']])) ? trim($row[$headerMap['street']]) : null;
                    $tel = (isset($headerMap['tel']) && isset($row[$headerMap['tel']])) ? trim($row[$headerMap['tel']]) : null;

                    // オプショナルカラムを取得
                    $surname_ruby = (isset($headerMap['surname_ruby']) && isset($row[$headerMap['surname_ruby']])) ? trim($row[$headerMap['surname_ruby']]) : null;
                    $name_ruby = (isset($headerMap['name_ruby']) && isset($row[$headerMap['name_ruby']])) ? trim($row[$headerMap['name_ruby']]) : null;
                    $email = (isset($headerMap['email']) && isset($row[$headerMap['email']])) ? trim($row[$headerMap['email']]) : null;
                    $zipcode = (isset($headerMap['zipcode']) && isset($row[$headerMap['zipcode']])) ? trim($row[$headerMap['zipcode']]) : null;

                    // バリデーション用の配列
                    $contactData = [
                        'surname' => $surname,
                        'name' => $name,
                        'company_name' => $company_name,
                        'pref' => $pref,
                        'city' => $city,
                        'street' => $street,
                        'tel' => $tel,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // 必須フィールドチェック
                    $requiredFields = ['surname', 'name', 'company_name', 'pref', 'city', 'street', 'tel'];
                    foreach ($requiredFields as $field) {
                        if (empty($contactData[$field])) {
                            throw new \Exception("Required field '{$field}' is empty at line {$lineNumber}");
                        }
                    }

                    // オプショナルフィールドを追加
                    if (!empty($surname_ruby)) {
                        $contactData['surname_ruby'] = $surname_ruby;
                    }
                    if (!empty($name_ruby)) {
                        $contactData['name_ruby'] = $name_ruby;
                    }
                    if (!empty($email)) {
                        // メール形式のバリデーション
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            throw new \Exception("Invalid email format: {$email}");
                        }
                        $contactData['email'] = $email;
                    }
                    if (!empty($zipcode)) {
                        $contactData['zipcode'] = $zipcode;
                    }

                    $batchData[] = $contactData;

                    // バッチサイズに達したら挿入
                    if (count($batchData) >= $batchSize) {
                        try {
                            Contact::insert($batchData);
                            $successCount += count($batchData);
                            
                            // Update status periodically
                            if ($this->fileKey) {
                                $this->updateStatus('processing', $successCount, $errorCount, 'ファイルを処理中です');
                            }
                        } catch (\Exception $insertException) {
                            Log::error('Batch insert error', [
                                'error' => $insertException->getMessage(),
                                'batch_size' => count($batchData),
                            ]);
                            $errorCount += count($batchData);
                        }
                        $batchData = [];
                    }

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'line' => $lineNumber,
                        'error' => $e->getMessage(),
                    ];
                    Log::warning('Error importing contact data', [
                        'line' => $lineNumber,
                        'error' => $e->getMessage(),
                    ]);
                }

                $lineNumber++;
            }

            // 残りのデータを挿入
            if (!empty($batchData)) {
                try {
                    Contact::insert($batchData);
                    $successCount += count($batchData);
                } catch (\Exception $insertException) {
                    Log::error('Final batch insert error', [
                        'error' => $insertException->getMessage(),
                        'batch_size' => count($batchData),
                    ]);
                    $errorCount += count($batchData);
                }
            }

            fclose($handle);

            // 一時ファイルを削除
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }

            // Update status to completed
            $completionMessage = $errorCount > 0 
                ? "処理完了: {$successCount}件成功、{$errorCount}件エラー"
                : "処理完了: {$successCount}件成功";
            
            if ($this->fileKey) {
                $this->updateStatus('completed', $successCount, $errorCount, $completionMessage);
            }

            // ログに処理結果を記録
            Log::info('CSV import completed', [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'file' => $this->filePath,
                'file_key' => $this->fileKey,
                'total_errors' => count($errors),
            ]);

        } catch (\Exception $e) {
            Log::error('CSV import failed with exception', [
                'file' => $this->filePath,
                'file_key' => $this->fileKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Update status to failed
            if ($this->fileKey) {
                $this->updateStatus('failed', 0, 0, 'エラーが発生しました: ' . $e->getMessage());
            }

            // 一時ファイルを削除
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }
        }
    }

    /**
     * Update import status in cache
     * 
     * @param string $status
     * @param int $successCount
     * @param int $errorCount
     * @param string $message
     */
    private function updateStatus($status, $successCount, $errorCount, $message)
    {
        if (!$this->fileKey) {
            return;
        }

        $cacheKey = "contact_import_{$this->fileKey}";
        $currentData = Cache::get($cacheKey, []);
        
        Cache::put($cacheKey, array_merge($currentData, [
            'status' => $status,
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'message' => $message,
            'updated_at' => now(),
        ]), now()->addHours(24));
    }
}
