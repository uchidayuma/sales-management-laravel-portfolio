<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class PaymentController extends MyController
{
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumbs->addCrumb('<i class="fas fa-yen-sign"></i>支払い', '');
    }

    public function subscribeCreate()
    {
        $this->breadcrumbs->addCrumb('定期支払い登録', '/create');
        $breadcrumbs = $this->breadcrumbs;

        $user = new User();
        $intent = $user->createSetupIntent();

        return view('fc.payment.subscribe', compact('intent', 'breadcrumbs'));
    }

    public function subscribeStore(Request $request)
    {
        // dd($request->all());
        try {
            Stripe::setApiKey(config('app.stripe_secret'));

            $user = User::find(\Auth::id());
            \Stripe\Subscription::create([
              'customer' => 'cus_GXO3K9xcWyDaTy',
              'items' => [['plan' => config('app.plan_subscription')]],
            ]);

            return redirect(route('dashboard'))->with('success', 'Charge successful, you get the course!');
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        // またしてもミスって単発支払いやってしまった
        // try {
        //     Stripe::setApiKey(config('app.stripe_secret'));

        //     $customer = Customer::create(array(
        //       'email' => $request->stripeEmail,
        //       'source' => $request->stripeToken,
        //   ));

        //     $charge = Charge::create(array(
        //       'customer' => $customer->id,
        //       'amount' => 1999,
        //       'currency' => 'usd',
        //   ));

        //     return redirect(route('dashboard'))->with('success', 'Charge successful, you get the course!');
        // } catch (\Exception $ex) {
        //     return $ex->getMessage();
        // }
    }
}
