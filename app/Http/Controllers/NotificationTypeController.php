<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationType;
use App\Models\Config;

class NotificationTypeController extends Controller
{
    public function __construct(NotificationType $notificationType)
    {
        $this->model = $notificationType;
    }

    public function create()
    {
        adminOnly();

        $settings = NotificationType::all();
        $leave_day = Config::where('key', 'leave_alone_days')->first();

        return view('admin.notification.create', compact('settings', 'leave_day'));
    }

    public function update(Request $request)
    {
        $inputs = $request->all();

        foreach ($inputs['nt'] as $key => $rc) {
            NotificationType::where('id', $key)->update(['period' => $rc]);
        }

        return redirect()->route('notifications.create')->with('success', 'アラート設定の更新が完了しました。');
    }
}
