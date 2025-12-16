<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $action
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role == 2) {
            if (Auth::user()->status == 2) {
                // 404エラーページへリダイレクト
                abort(404);
            }
            // リクエストのHTTPメソッドを判定
            $method = $request->method();
            $currentDateTime = Carbon::now();
            $restrictionDateTime = Carbon::createFromFormat('Y-m-d H:i:s', config('app.restriction_datetime'));

            // POST, PUT, PATCH メソッドに対してのみリダイレクト
            if (in_array($method, ['POST', 'PUT', 'PATCH']) && $currentDateTime->greaterThanOrEqualTo($restrictionDateTime)) {
                return redirect('/tonewsystem');
            }
        }

        return $next($request);
    }
}
