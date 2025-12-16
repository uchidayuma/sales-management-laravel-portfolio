<?php

namespace App\Http\View\Composers;

use App\Models\User;
use Illuminate\Support\Facades\View;

class CurrentUserComposer
{
    public function __construct(UserRepository $users)
    {
        $this->user = \Auth::user();
    }

    public function compose(View $view)
    {
        $user = $this->user;
        $view->with('user', $user);
    }
}
