<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
use App\Http\Controllers\UserController;

Route::get('/user', [UserController::class, 'index']);
*/
Route::get('/mail', 'Auth\RegisterController@send');

Auth::routes();

Route::get('/cron', 'UserNotificationController@cronNotificate');
Route::get('/tonewsystem', function () {
    return view(' tonewsystem');
});

Route::group(['middleware' => ['auth','check.user.role']], function () {

    // ルートプラス管理用
    Route::group(['prefix' => 'admin'], function () {
        Route::get('loginas', 'AdminController@loginAs')->name('admin.loginas');
        Route::post('loginas', 'AdminController@loginAsUserId')->name('admin.loginasuserid');
    });
    Route::get('/', 'DashboardController@index')->name('dashboard');
    //　お知らせ
    Route::group(['prefix' => 'articles'], function () {
        Route::get('', 'ArticleController@index')->name('articles.index');
        Route::get('create', 'ArticleController@create')->name('articles.create');
        Route::post('store', 'ArticleController@store')->name('articles.store');
        Route::get('edit/{article}', 'ArticleController@edit')->name('articles.edit');
        Route::post('update/{article}', 'ArticleController@update')->name('articles.update');
        Route::post('image/ajaxupload', 'ArticleController@ajaxImageUpload');
        Route::get('/unread/ajaxGet', 'ArticleController@ajaxUnreadGet');
        Route::get('/cronnotice', 'ArticleController@cronNotice');
        Route::get('{article}/destroy', 'ArticleController@destroy')->name('articles.destroy');
        Route::get('{article}', 'ArticleController@show')->name('articles.show');
    });

    // 通知関連
    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', 'UserNotificationController@index')->name('notifications.index');
        Route::get('create', 'NotificationTypeController@create')->name('notifications.create');
        Route::post('update', 'NotificationTypeController@update')->name('notifications.update');
        Route::get('/unread/ajaxGet', 'UserNotificationController@unreadAjaxGet');
        Route::post('/ajaxRead', 'UserNotificationController@ajaxRead');
    });

    // 設定
    Route::group(['prefix' => 'settings'], function () {
        Route::get('office_holidays', 'SettingController@officeHoliday')->name('settings.officeholiday');
        Route::post('office_holidays', 'SettingController@ajaxCreateOfficeHoliday');
        Route::delete('office_holidays', 'SettingController@ajaxDestoryOfficeHoliday');
        Route::get('csv_export_options', 'SettingController@csvExportOption')->name('settings.csvexportoption');
        Route::post('add_csv_export_options', 'SettingController@ajaxCsvExportOptionForm')->name('settings.ajaxcsvexportoptionadd');
        Route::post('delete_csv_export_options', 'SettingController@ajaxDestoryCsvExportOptionForm')->name('settings.ajaxdeletecsvexportoptionadd');
        Route::get('fc-apply-areas', 'FcApplyAreaController@index')->name('settings.fcapplyareas.index');
        Route::post('fc-apply-areas-store', 'FcApplyAreaController@store')->name('settings.fcapplyareas.store');
        Route::post('fc-apply-areas-update/{id}', 'FcApplyAreaController@update')->name('settings.fcapplyareas.update');
        Route::post('fc-apply-areas-destroy/{id}', 'FcApplyAreaController@destroy')->name('settings.fcapplyareas.destroy');
    });

    // 問い合わせ管理
    Route::group(['prefix' => 'contact'], function () {
        Route::get('new', 'ContactController@getContactForm')->name('contact.form');
        Route::post('new', 'ContactController@postContactForm')->name('contact.post');
        // Route::get('fc/new', 'ContactController@getOwnContactForm')->name('fc.contact.form');
        Route::post('fc/new', 'ContactController@postOwnContactForm')->name('fc.contact.post');
        Route::get('cancel', 'ContactController@cancelList')->name('contact.cancel'); //キャンセル案件一覧
        Route::get('unassigned/list', 'ContactController@unassignedList')->name('unassigned.list');
        Route::get('unassigned/search', 'ContactController@unassignedSearch')->name('unassigned.search');
        Route::post('skip/confirmation', 'ContactController@skipOnsiteConfirmation')->name('skip.onsite-confirmation'); //現場報告をスキップ
        Route::post('appointment', 'ContactController@setAppointment')->name('set.appointment'); //アポ日時登録
        Route::get('download/image/{id}/{path}/{file}/{name}', 'ContactController@downloadImage')->name('download.image'); // 施工画像ダウンロード
        Route::get('fileDownload/{id}', 'ContactController@downloadFile')->name('download.file'); //顧客からのファイルダウンロード
        Route::post('destroy/{id}', 'ContactController@Destroy')->name('contact.destroy'); //お問い合わせ削除
        Route::post('cancel/{id}', 'ContactController@contactCancel')->name('contact.cancel.submit'); //案件1つをキャンセルに変更
        Route::get('customers/list', 'ContactController@customersList')->name('contact.customers'); //顧客一覧
        //Route::get('customers/filter', 'ContactController@customersListFilter')->name('customers.filter');//顧客一覧フィルター
        Route::get('customers/search', 'ContactController@customersSearch')->name('customers.search'); //顧客検索
        Route::get('sample/list', 'ContactController@sampleList')->name('sample.list'); //サンプル送付一覧
        Route::post('sample/list/show', 'ContactController@sampleListSent')->name('sample.list.sent');

        // 案件詳細
        Route::get('{id}', 'ContactController@show')->name('contact.show');
        Route::get('assigned/list', 'ContactController@assignedList')->name('assigned.list');
        Route::get('assigned/search', 'ContactController@assignedSearch')->name('assigned.search');
        Route::get('edit/{id}', 'ContactController@assignedEdit')->name('assigned.edit');
        Route::put('update/{id}', 'ContactController@contactUpdate')->name('contact.update');
        Route::get('assign/{id}/{distance?}', 'ContactController@assign')->name('contact.assign');
        Route::post('assign/commit', 'ContactController@assignCommit')->name('contact.assign.commit');
        Route::put('update/type/{id}', 'ContactController@switchContactType')->name('contact.type.update');
        Route::put('restore/cancel/{id}', 'ContactController@restoreCancel')->name('contact.restore.cancel');

        //現場確認・完了報告
        Route::get('before/report', 'ContactController@addBeforeImages')->name('before.report');
        Route::post('before/report/post', 'ContactController@postBeforeImages')->name('post.before.report'); //現場報告写真登録画面
        Route::get('pending/list', 'ContactController@pendingList')->name('pending.list'); //商談結果登録画面
        Route::post('pending/list', 'ContactController@pendingListCommit')->name('pending.post'); //商談結果登録画面

        // 見積もり作成すべき案件一覧 step_id = 4
        Route::get('quotations/needs', 'QuotationController@needsIndex')->name('quotations.needs');
        // 発注待ち一覧
        Route::get('transaction/pending/list', 'ContactController@transactionPendingList')->name('transaction.pending.list'); // 本部発注フェーズ一覧
        // CSVエクスポート
        Route::post('csv/export', 'ContactController@csvExport')->name('contact.csv.export');
        Route::post('csv/sample/export', 'ContactController@csvSampleExport')->name('contact.csv.sample.export');
        Route::post('csv/custom/export', 'ContactController@csvCustomExport')->name('contact.csv.custom-export');
        // 同一顧客モーダル
        Route::get('ajax/{id}', 'ContactController@ajaxSameCustomer')->name('contact.ajaxSameCustomer.list'); // 同一顧客一覧表示
        Route::post('ajaxSameCustomerDestroy', 'ContactController@ajaxSameCustomerDestroy')->name('contact.ajaxSameCustomer.destroy'); // 同一顧客解除
        Route::post('ajaxSameCustomerAdd', 'ContactController@ajaxSameCustomerAdd')->name('contact.ajaxSameCustomer.add'); // 同一顧客追加
        Route::get('ajax/Button/{id}', 'ContactController@ajaxSameCustomerButton')->name('contact.ajaxSameCustomer.button'); // 同一顧客確認
    });

    //施工完了報告
    Route::group(['prefix' => 'report'], function () {
        Route::get('list', 'ReportController@index')->name('report.list');
        Route::get('pending', 'ReportController@pending')->name('report.pending');
        Route::get('create/{id}', 'ReportController@create')->name('report.create');
        Route::post('update/{id}', 'ReportController@update')->name('report.update');
        Route::post('admin/finish', 'ReportController@adminFinish')->name('report.admin.finish');
    });

    // ユーザー管理
    Route::group(['prefix' => 'users'], function () {
        // 契約更新管理
        Route::get('contracts', 'UserController@contractIndex')->name('users.contracts');
        Route::get('', 'UserController@index')->name('users.index');
        Route::get('search', 'UserController@search')->name('fc.search');
        Route::get('logout', 'UserController@getLogout')->name('users.logout');
        Route::get('edit/{id}', 'UserController@edit')->name('users.edit');
        Route::post('update/{id}', 'UserController@update')->name('users.update');
        Route::get('create', 'UserController@create')->name('users.create');
        Route::post('store', 'UserController@store')->name('users.store');
        Route::get('{id}', 'UserController@show')->name('users.show');
        Route::get('changepassword/{id}', 'UserController@showChangePasswordForm');
        Route::post('changepassword/{id}', 'UserController@changePassword')->name('users.changepassword');
        Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
        Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
        Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
        // CSVエクスポート
        Route::get('csv/post/export', 'UserController@csvPostExport')->name('users.csv.post.export');
        Route::get('csv/export', 'UserController@csvExport')->name('users.csv.export');

        Route::get('test/cron1year', 'UserController@cronSend1yearMail');
        Route::get('test/cronswitchinvoicepaymenttype', 'UserController@cronSwichInvoicePaymentType');
        // area_open_sendsテーブルに入れる処理
        Route::get('test/croncontract', 'UserController@cronAreaOpenIsFc');
        Route::get('test/cronopen', 'UserController@cronSendOpenMail');
        Route::get('test/cronpreopenemail', 'UserController@cronSendPreOpenMail');
    // CRONで毎日自己獲得案件達成状況をチェック
        Route::get('test/cronowncontactcheck', 'UserController@cronOwnContactCountCheck');
        // Route::get('test/owncontact/count/{user_id}/{start}/{end}', 'UserController@ownGetContactCount');
        // 契約更新
        Route::get('ajax/samefc/status/{userId}/{targetYear}', 'UserController@ajaxSameAreaFcStatus');
        Route::post('ajax/samefc/checkupdate/{id}', 'UserController@ajaxAreaOpenStatusToggle');

    });

    //見積もり
    Route::group(['prefix' => 'quotations'], function () {
        Route::get('', 'QuotationController@index')->name('quotations.index');
        Route::get('{id}', 'QuotationController@show')->name('quotations.show');
        Route::get('edit/{id}', 'QuotationController@edit')->name('quotations.edit');
        Route::get('{id}/download', 'QuotationController@download')->name('quotations.download');
        Route::get('new/{id}/{copyId?}', 'QuotationController@create')->name('quotations.create');
        Route::post('new', 'QuotationController@store')->name('quotations.store');
        Route::post('update', 'QuotationController@update')->name('quotations.update');
        Route::post('materialupdate', 'QuotationController@materialUpdate')->name('quotations.material-update');
        Route::post('material', 'QuotationController@materialStore')->name('quotations.material.store');
        Route::post('/update/contact/{contactId}', 'QuotationController@contactUpdate')->name('quotations.select.update');
        Route::post('file/ajax/upload', 'QuotationController@ajaxUploadFile');
        Route::delete('file/ajax/upload', 'QuotationController@ajaxDeleteFile');
        Route::post('ajax/post', 'QuotationController@ajaxStore');
        Route::post('ajax/pdf/parse', 'QuotationController@ajaxPdfParse')->name('quotations.ajax.parse');
        // 本部見積もり
        Route::get('/admin/needs', 'QuotationController@adminNeeds')->name('quotations.admin.needs');
        Route::post('/admin/mail', 'QuotationController@adminMailDispatch')->name('quotations.admin.mail');
        Route::get('/ajax/{id}', 'QuotationController@ajaxQuotations');
        // 本部見積もり結果登録待ち
        Route::get('pending/list', 'QuotationController@pendingList')->name('quotations.pending.list'); //商談結果登録画面
        Route::post('pending/list', 'ContactController@pendingListCommit')->name('pending.post'); //商談結果登録画面
    });

    //部材発注
    Route::group(['prefix' => 'transactions'], function () {
        Route::get('', 'TransactionController@index')->name('transactions');
        Route::get('/admin/dispatched', 'TransactionController@adminDispatchedIndex')->name('transactions.admin.dispatched'); // 発注聖書
        Route::get('/admin/settings', 'TransactionController@adminSettings')->name('transactions.admin.shipping-price'); // 発注聖書
        Route::post('/admin/settings', 'TransactionController@adminSettingsUpdate')->name('transactions.admin.shipping-price-update'); // 発注聖書
        Route::get('/create/order/{contactId?}', 'TransactionController@create')->name('create.order');
        Route::post('/comfirm', 'TransactionController@comfirm')->name('transaction.comfirm');
        Route::post('/create/order', 'TransactionController@store')->name('transaction.store');
        // 発注をスキップ
        Route::post('/create/order/skip', 'TransactionController@skip')->name('transaction.skip');
        // 送料入力
        Route::get('/shippingCost/pendings', 'TransactionController@shippingCostInputPendingList')->name('transaction.cost-input.pending.list');
        Route::post('/shippingCost/store/{id}', 'TransactionController@shippingCostStore')->name('transaction.shipping-cost.store');
        // FC支払い待ち
        Route::get('/payment/pendings', 'TransactionController@paymentPendingList')->name('transaction.payment.pending.list');
        // FC支払いページ
        Route::get('/payment/{id}', 'TransactionController@paymentShow')->name('transaction.payment.show');
        Route::post('/payment/invoice/{contactId}/{transactionId}', 'TransactionController@paymentInvoice')->name('transaction.payment.invoice.store');
        // 発送連絡待ち
        Route::get('/dispatch/pending', 'TransactionController@dispatchPending')->name('dispatch.pending');
        Route::post('/dispatch/store', 'TransactionController@dispatchStore')->name('dispatch.store');
        Route::post('/shipping/update', 'TransactionController@shippingUpdate')->name('shipping.update');
        Route::get('/dispatched', 'TransactionController@dispatched')->name('dispatched.list');
        Route::post('/input/shipping-cost', 'TransactionController@shippingCostUpdate')->name('input.shipping-cost');
        // 更新
        Route::post('/update/{id}', 'TransactionController@update')->name('transaction.update');
        // 詳細
        Route::get('download/{id}', 'TransactionController@download')->name('transactions.download');
        Route::get('/{id}', 'TransactionController@show')->name('transactions.show');
        Route::get('/edit/{transactionId}', 'TransactionController@edit')->name('transactions.edit');
        Route::get('/', 'TransactionController@index')->name('transactions');
        // 一覧から削除
        Route::post('/destroy', 'TransactionController@destroy')->name('transactions.destroy');
        // 発注書の削除(キャンセル)
        Route::post('/delete/{transactionId}', 'TransactionController@delete')->name('transactions.delete');
        Route::post('/canvas/ajax/upload/{state?}/{dir?}', 'TransactionController@screenUpload');
        Route::post('/ajax/update', 'TransactionController@ajaxUpdate');
        Route::get('/ajax/isskip/{id}', 'TransactionController@ajaxIsskip');
    });

    // 請求書
    Route::group(['prefix' => 'invoices'], function () {
        Route::get('/', 'InvoiceController@index')->name('invoices.index');
    });

    //在庫管理
    Route::group(['prefix' => 'products'], function () {
        Route::get('/', 'ProductController@index')->name('products.index');
        Route::get('{id}', 'ProductController@show')->name('products.show');
        Route::get('edit/{id}', 'ProductController@edit')->name('products.edit');
        Route::post('update/{id}', 'ProductController@update')->name('products.update');
        Route::get('ajax/get', 'ProductController@ajaxGet');
        Route::post('ajax/update', 'ProductController@ajaxUpdate');
    });
    // 支払い
    Route::group(['prefix' => 'payments'], function () {
        Route::get('/', 'PaymentController@subscribeCreate')->name('payments.subscribe.create');
        Route::post('/', 'PaymentController@subscribeStore')->name('payments.subscribe.store');
    });
    //ランキング
    Route::get('/rankings{query_string?}', 'RankingController@index')->name('rankings.index');

    //顧客検索
    Route::group(['prefix' => 'search'], function () {
        Route::get('/', 'ContactController@getSearchForm')->name('search.index');
        Route::get('/result', 'ContactController@searchResult')->name('search.result');
    });
    Route::get('/test/email', 'ContactController@testSend');
    //データ分析
    Route::group(['prefix' => 'analysis'], function () {
        Route::get('/', 'AnalysisController@index')->name('analysis.index');
        Route::get('/contacts', 'AnalysisController@contacts')->name('analysis.contacts');
        Route::get('/fc', 'AnalysisController@fcIndex')->name('analysis.fc.index');
        Route::get('/contactdetail', 'AnalysisController@contactDetail')->name('analysis.contactdetail');
        Route::get('/contactdetail/ajaxGet', 'AnalysisController@ajaxGetContactDetail');
    });

    //データ分析
    Route::group(['prefix' => 'config'], function () {
        Route::post('/leaveday', 'ConfigController@updateLeaveDay')->name('config.leaveday-update');
        Route::post('/transactions/freeitem', 'ConfigController@updateFreeItem')->name('config.update-free-item');
    });
});


//新規機能
Route::group(['prefix' => 'feature'], function () {
    Route::get('/simulator', 'SimulatorController@index');
});

//Route::get('/', 'HomeController@index')->name('home');
//freee連携
//Route::get('/invoices/sdk', 'TransactionController@createInvoice')->name('freeeInvoice');
//Route::get('/refreshtoken', 'TransactionController@refreshToken')->name('refreshToken');
//Route::get('/freeeauth', 'TransactionController@freeeAuth')->name('freeeAuth');

Route::get('test/cronslack', 'SlackController@getNewContact');
Route::get('test/getdeliveryday/{day}', 'TransactionController@getDeliveryDays');

Route::get('fc/password/reset/{token}', 'SetPasswordController@showPasswordResetForm')->name('fc.password.reset');
Route::post('fc/password/reset', 'SetPasswordController@resetPassword')->name('fc.password.update');

//Route::get('/invoice/create/freee/post/{fromMonth?}/{toMonth?}', 'TransactionController@createInvoice')->name('createInvoice');
//Route::get('/invoice/create/freee/fc/post/{fcId}/{fromMonth}/{toMonth}', 'TransactionController@createSingleInvoice');
//Route::get('/invoice/create/freee/refresh', 'TransactionController@refreshToken')->name('refreshToken');
