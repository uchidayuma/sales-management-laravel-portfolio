<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        if (\App::environment('production')) {
            // $schedule->call('App\Http\Controllers\UserNotificationController@cronNotificate')->weekdays()->at('09:02');
            // $schedule->call('App\Http\Controllers\TransactionController@createInvoice')->monthlyOn(1, '03:00');
            $schedule->call('App\Http\Controllers\TransactionController@refreshToken')->dailyAt('2:00');
            // $schedule->call('App\Http\Controllers\SlackController@getNewContact')->dailyAt('9:00');
            // $schedule->call('App\Http\Controllers\SlackController@getPriceChangeLog')->hourly();
            // $schedule->call('App\Http\Controllers\ArticleController@cronNotice')->hourlyAt(20);
            // $schedule->call('App\Http\Controllers\UserController@cronAreaOpenIsFc')->monthlyOn(1, '00:01');
            // 2023年5月19日からスタート
            // $schedule->call('App\Http\Controllers\UserController@cronSend1yearMail')->dailyAt('10:00');
            // $schedule->call('App\Http\Controllers\UserController@cronSwichInvoicePaymentType')->monthlyOn(5, '07:00');
            // $schedule->call('App\Http\Controllers\UserController@cronOwnContactCountCheck')->dailyAt('09:00');
            // $schedule->call('App\Http\Controllers\UserController@cronSendOpenMail')->dailyAt(12);
            // $schedule->call('App\Http\Controllers\UserController@cronSendPreOpenMail')->dailyAt(12);
        } elseif (\App::environment('testing')) {
            // $schedule->call('App\Http\Controllers\UserController@cronSend1yearMail')->dailyAt(12);
            // $schedule->call('App\Http\Controllers\UserController@cronSwichInvoicePaymentType')->dailyAt(16);
            // $schedule->call('App\Http\Controllers\UserController@cronAreaOpenIsFc')->dailyAt(11);
            // $schedule->call('App\Http\Controllers\UserController@cronSendOpenMail')->dailyAt(12);
            // $schedule->call('App\Http\Controllers\UserController@cronSendPreOpenMail')->dailyAt(12);
            // $schedule->call('App\Http\Controllers\UserController@cronOwnContactCountCheck')->hourlyAt(10);
        } else {
            // $schedule->call('App\Http\Controllers\SlackController@getPriceChangeLog')->everyMinute();
            // $schedule->call('App\Http\Controllers\UserNotificationController@cronNotificate')->everyMinute();
            // $schedule->call('App\Http\Controllers\UserController@cronAreaOpenIsFc')->everyMinute();
            // $schedule->call('App\Http\Controllers\UserController@cronSendOpenMail')->everyMinute();
            // $schedule->call('App\Http\Controllers\UserController@cronSendPreOpenMail')->everyMinute();
            // $schedule->call('App\Http\Controllers\UserController@cronSend1yearMail')->everyMinute();
            // $schedule->call('App\Http\Controllers\UserController@cronOwnContactCountCheck')->everyMinute();
            // $schedule->call('App\Http\Controllers\ArticleController@cronNotice')->everyMinute();
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
