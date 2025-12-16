<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('contact_type_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('step_id')->unsigned()->default(1);
            $table->integer('quotation_id')->unsigned()->nullable()->comment('最終的に採用された見積もりのID（案件の金額に相当）');
            $table->string('free_sample')->nullable()->comment('無料サンプル');
            $table->string('email');
            $table->string('fax')->nullable();
            $table->string('tel');
            $table->integer('zipcode');
            $table->string('pref');
            $table->string('city');
            $table->string('street');
            $table->boolean('type')->default(1)->comment('1=個人問い合わせ、0=法人');
            $table->string('ground_condition')->nullable()->comment('下地状況');
            $table->dateTime('desired_datetime1')->nullable()->comment('訪問希望日時1');
            $table->dateTime('desired_datetime2')->nullable()->comment('訪問希望日時2');
            $table->dateTime('finished_datetime')->nullable()->comment('施工完了日');
            $table->string('visit_address')->nullable()->comment('訪問住所');
            $table->float('square_meter', 10, 0)->nullable()->comment('平米数');
            $table->string('use_application')->nullable()->comment('人工芝の使用用途');
            $table->string('surname')->nullable()->comment('姓');
            $table->string('name')->nullable()->comment('名');
            $table->string('surname_ruby')->nullable()->comment('せい');
            $table->string('name_ruby')->nullable()->comment('めい');
            $table->string('company_name')->nullable()->comment('会社名');
            $table->string('company_ruby')->nullable()->comment('かいしゃめい');
            $table->string('industry')->nullable()->comment('業種');
            $table->string('quote_details')->nullable()->comment('お見積もり内容（施工希望・材料のみ）');
            $table->integer('vertical_size')->nullable()->comment('縦');
            $table->integer('horizontal_size')->nullable()->comment('横');
            $table->string('desired_product')->nullable()->comment('希望の商品');
            $table->string('comment')->nullable();
            $table->string('age')->nullable()->comment('問い合わせ者の年代');
            $table->string('requirement')->nullable()->comment('必要事項');
            $table->string('where_find')->nullable()->comment('どこでサンプルFCを知ったか？');
            $table->string('sns')->nullable()->comment('SNSは知っているか？');
            $table->dateTime('visit_time')->nullable()->comment('アポ日時');
            $table->integer('shipping_id')->unsigned()->nullable()->comment('問い合わせ業者');
            $table->bigInteger('shipping_number')->unsigned()->nullable()->comment('問い合わせ番号');
            $table->string('before_image1')->nullable()->comment('訪問見積もり時の写真1');
            $table->string('before_image2')->nullable()->comment('訪問見積もり時の写真2');
            $table->string('before_image3')->nullable()->comment('訪問見積もり時の写真3');
            $table->string('after_image1')->nullable()->comment('施工完了時の写真1');
            $table->string('after_image2')->nullable()->comment('施工完了時の写真2');
            $table->string('after_image3')->nullable()->comment('施工完了時の写真3');
            $table->tinyInteger('own_contact')->default(0);
            $table->tinyinteger('status')->default(1);
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('contacts');
    }
}
