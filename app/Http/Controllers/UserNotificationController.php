<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\UserNotification;
use App\Models\NotificationType;
use Illuminate\Http\Request;
use App\Mail\SendUserNotificateMail;
use Mail;

class UserNotificationController extends MyController
{
    public function __construct(UserNotification $userNotification)
    {
        parent::__construct();
        $this->model = $userNotification;
        $this->breadcrumbs->addCrumb('<i class="fas fa-bell"></i>通知', 'notifications');
    }

    public function index()
    {
        $this->breadcrumbs->addCrumb('通知一覧', '/notifications')->setLastItemWithHref(true);
        $breadcrumbs = $this->breadcrumbs;

        $notifications = $this->model->select('user_notifications.*', 'user_notifications.created_at AS notificate_date', 'c.*', 'nt.name AS type_name')
            ->where('user_notifications.user_id', \Auth::id())
            ->join('notification_types AS nt', 'nt.id', '=', 'user_notifications.notification_type')
            ->join('contacts AS c', 'c.id', '=', 'user_notifications.contact_id')
            ->orderBy('user_notifications.created_at', 'DESC')
            ->paginate(30);

        return view('fc.notification.index', compact('notifications', 'breadcrumbs'));
    }

    public function cronNotificate()
    {
        $carbon = new Carbon();
        $notificates = NotificationType::all();
        $contacts = Contact::select('nt.period', 'nt.id AS notification_type', 'nt.url', 'u.id AS user_id', 'u.email', 'u.email2', 'u.email3', 'contacts.created_at', 'contacts.updated_at', 'contacts.id AS contact_id', 'contacts.contact_type_id', 'contacts.step_id')
                ->where('u.status', 1)
                ->where('u.role', 2)
                ->where('u.allow_notification', 1)
                ->where('contacts.status', 1)
                ->whereDate('contacts.created_at', '>', config('app.notice_date'))
                ->leftJoin('users AS u', 'contacts.user_id', '=', 'u.id')
                ->join('notification_types AS nt', 'nt.step_id', '=', 'contacts.step_id')
                ->orderBy('u.id', 'ASC')
                ->get();

        foreach ($contacts as $c) {
            \Log::debug(print_r($c, true));
            $alertDate = alertDateTime($c['updated_at'], $c['period']);
            $isExist = UserNotification::where('contact_id', $c['contact_id'])
                                       ->where('user_id', $c['user_id'])
                                       ->where('notification_type', $c['notification_type'])
                                       ->where('read', 0)
                                       ->first();
            if( $c['step_id'] == self::STEP_APPOINT && ($c['contact_type_id'] == 3 || $c['contact_type_id'] == 7) ){
                $this->model->alertNotice($c, $alertDate, $isExist);
            }elseif($c['step_id'] == self::STEP_QUOTATION || $c['step_id'] == self::STEP_RESULT){
                $this->model->alertNotice($c, $alertDate, $isExist);
            }
            // SESは14/秒しか送信できないので、待つ
            sleep(5);
        }
    }

    public function unreadAjaxGet()
    {
        $user_id = \Auth::id();

        $unread = UserNotification::select('user_notifications.*', 'nt.*')
          ->join('notification_types AS nt', 'nt.id', '=', 'user_notifications.notification_type')
          // TODO 営業日計算
          ->where('read', 0)->where('user_id', $user_id)
          ->get();

        return response()->json($unread);
    }

    public function ajaxRead(Request $request)
    {
        $user_id = \Auth::id();
        $posts = $request->all();

        $result = UserNotification::where('user_id', $user_id)->update(['read' => 1]);
        if ($result != null) {
            return response()->json(['status' => true]);
        } else {
            return response()->json([
              'status' => false,
              'message' => '通信に失敗しました。',
            ]);
        }
    }
}
