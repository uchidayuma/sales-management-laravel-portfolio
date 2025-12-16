<?php

namespace App\Models;

use Carbon\Carbon;
use App\Mail\SendUserNotificateMail;
use Mail;

class UserNotification extends MyModel
{
    public function alertNotice($c, $alertDate, $isExist){
        $carbon = new Carbon();
        if (strtotime($carbon::today()) >= strtotime($alertDate) && !$isExist) {
            UserNotification::insert([
                'user_id' => $c['user_id'],
                'contact_id' => $c['contact_id'],
                'notification_type' => $c['notification_type'],
                'read' => 0,
            ]);
        }
        if (strtotime($carbon::today()) >= strtotime($alertDate) && isAllowEmailUser($c['user_id'])) {
            Mail::to($c['email'])->send(new SendUserNotificateMail($c));
            Mail::to(config('mail.fallback_notification', 'notifications@example.com'))->send(new SendUserNotificateMail($c));
            if (!empty($c['email2'])) {
                Mail::to($c['email2'])->send(new SendUserNotificateMail($c));
            }
            if (!empty($c['email3'])) {
                Mail::to($c['email3'])->send(new SendUserNotificateMail($c));
            }
        }
    }
}
