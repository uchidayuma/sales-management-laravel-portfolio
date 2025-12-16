@extends('layouts.layout')

@section('css')
<style>
.StripeElement {
  box-sizing: border-box;

  height: 40px;

  padding: 10px 12px;

  border: 1px solid transparent;
  border-radius: 4px;
  background-color: white;

  box-shadow: 0 1px 3px 0 #e6ebf1;
  -webkit-transition: box-shadow 150ms ease;
  transition: box-shadow 150ms ease;
}

.StripeElement--focus {
  box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
  border-color: #fa755a;
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}
</style>
@endsection

@section('javascript')
<script src="https://js.stripe.com/v3" defer></script>
<script src="/js/payment/checkout.js" defer></script>
@endsection

@section('content')
  {!! $breadcrumbs->render() !!}
@if(!$user->stripe_id)
  <p class='p40'>システムの定期利用料をクレジットカードで支払う場合は</p>
@else
  <form action="{{ route('payments.subscribe.store') }}" method="post" id="payment-form">
    @csrf
    <div class="card">
        @csrf                    
        <div class="form-group">
            <div class="card-header">
                <label for="card-element">
                    Enter your credit card information
                </label>
            </div>
            <div class="card-body">
                <div id="card-element">
                <!-- A Stripe Element will be inserted here. -->
                </div>
                <!-- Used to display form errors. -->
                <div id="card-errors" role="alert"></div>
                <input type="hidden" name="plan" value="monthly" />
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-dark" type="submit">Pay</button>
        </div>
    </div>
  </form>
@endif
@endsection
