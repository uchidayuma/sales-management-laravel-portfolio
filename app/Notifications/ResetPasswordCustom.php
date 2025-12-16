<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordCustom extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
                    //->line('The introduction to the notification.')
                    //->action('Notification Action', url('/'))
                    //->line('Thank you for using our application!');
                    //->title('パスワードリセットのお知らせ')
                    ->from('no-reply@example.com', config('app.name'))
                    ->subject('パスワード設定')
                    ->greeting('パスワードの再設定')
                    ->line('下のボタンをクリックしてパスワードを再設定してください。')
                    ->action('パスワード再設定', url(config('app.url').route('password.reset', $this->token, false)))
                    ->line('もし心当たりがない場合は、本メッセージは破棄してください。')
                    ->salutation('サンプル株式会社');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
        ];
    }
}
