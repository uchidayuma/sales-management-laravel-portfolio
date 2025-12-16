@if(empty($transactionId))
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="office" value='1' {{ checked(!empty($_GET['address_type']) ? $_GET['address_type'] == '1' : false) }}>
  <label class="form-check-label" for="office">貴社住所へ送付</label>
</div>
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="storage" value='2' {{ checked(!empty($_GET['address_type']) ? $_GET['address_type'] == '2' : false) }}>
  <label class="form-check-label" for="storage">貴社資材置き場へ送付</label>
</div>
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="factory" value='3' {{ checked(!empty($_GET['address_type']) ? $_GET['address_type'] == '3' : false) }}>
  <label class="form-check-label" for="factory">本部工場で受け取る</label>
</div>
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="customer" value='4' {{ checked(!empty($_GET['address_type']) ? $_GET['address_type'] == '4' : false) }}>
  <label class="form-check-label" for="customer">顧客へ直接送付</label>
</div>
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="optional" value='5' {{ checked(!empty($_GET['address_type']) ? $_GET['address_type'] == '5' : false) }}>
  <label class="form-check-label" for="optional">貴社任意受け取り場所へ送付</label>
</div>
@else
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="office" value='1' {{ checked($transactions[0]['address_type'] == '1') }}>
  <label class="form-check-label" for="office">貴社住所へ送付</label>
</div>
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="storage" value='2' {{ checked($transactions[0]['address_type'] == '2') }}>
  <label class="form-check-label" for="storage">貴社資材置き場へ送付</label>
</div>
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="factory" value='3' {{ checked($transactions[0]['address_type'] == '3' ) }}>
  <label class="form-check-label" for="factory">本部工場で受け取る</label>
</div>
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="customer" value='4' {{ checked($transactions[0]['address_type'] == '4') }}>
  <label class="form-check-label" for="customer">顧客へ直接送付</label>
</div>
<div class="form-check lh-18">
  <input class="form-check-input" type="radio" name="t[address_type]" id="optional" value='5' {{ checked($transactions[0]['address_type'] == '5') }}>
  <label class="form-check-label" for="optional">貴社任意受け取り場所へ送付</label>
</div>
@endif
