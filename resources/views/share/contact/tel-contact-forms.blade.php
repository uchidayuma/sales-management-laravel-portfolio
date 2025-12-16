<!-- お問い合わせ種別未選択 -->
<form id='personal-sample' contact-type-id='0' action="{{ route('contact.post') }}" method="POST" enctype="multipart/form-data" class="contact-form h-adr">
  @csrf
  <input type='hidden' name='c[contact_type_id]' value="0"/>
  <input type="hidden" class="p-country-name" value="Japan">
  <span class="p-country-name" style="display:none;">Japan</span>
  <table class="common-table-stripes-column">
    <tbody>
      <tr class='d-none'></tr>
      <tr>
        <th class="common-table-stripes-column__th">お名前<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname]" class="js-copy-surname form-control common-table-stripes-column__input mr20" value="{{old('c.surname')}}" placeholder="姓" required>
          <input type="text" name="c[name]" class="js-copy-name form-control common-table-stripes-column__input" value="{{old('c.name')}}" placeholder="名" required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">フリガナ<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname_ruby]" class="js-copy-surname_ruby form-control common-table-stripes-column__input mr20" value="{{old('c.surname_ruby')}}" placeholder="セイ" required>
          <input type="text" name="c[name_ruby]" class="js-copy-name_ruby form-control common-table-stripes-column__input" value="{{old('c.name_ruby')}}" placeholder="メイ" required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">郵便番号<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td"><input name="c[zipcode]" type="text" class="js-copy-zipcode form-control common-table-stripes-column__input p-postal-code" value="{{ old('c.zipcode') }}" required placeholder="1000001"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">顧客住所<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex justify-content-between">
          <input type="text" name="c[pref]"   class="js-copy-pref form-control p-region w15" value="{{ old('c.pref') }}" placeholder="サンプル都" required>
          <input type="text" name="c[city]"   class="js-copy-city form-control p-locality p-street-address p-extended-address p-street-address  w20" value="{{ old('c.city') }}" placeholder="サンプル中央区" required>
          {{-- <input type="text" name="c[street]" class="js-copy-street form-control p-street-address p-extended-address w50" value="{{ old('c.street') }}" placeholder="サンプルタウン1-1-1" required> --}}
          <input type="text" name="c[street]" class="js-copy-street form-control w50" value="{{ old('c.street') }}" placeholder="サンプルタウン1-1-1" required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[tel]" class="js-copy-tel form-control common-table-stripes-column__input mr20" value="{{old('c.tel')}}" placeholder="050-3561-2247" required>
          <input type="text" name="c[tel2]" class="js-copy-tel2 form-control common-table-stripes-column__input" value="{{old('c.tel2')}}" placeholder="070-7777-7777">
    @if($errors->has('c.tel'))
          <div class='alert alert-danger'>正しく電話番号を入力してください</div>
    @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="js-copy-fax form-control common-table-stripes-column__input" value="{{old('c.fax')}}" placeholder="050-3561-2247"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メールアドレス</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="js-copy-email form-control common-table-stripes-column__input w60" value="{{old('c.email')}}" placeholder="info@routeplus.co.jp"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">年代</th>
        <td class="common-table-stripes-column__td">
          <select name="c[age]" class="js-copy-age form-control common-table-stripes-column__input">
            <option value="">---</option><option value="1910">1910年代</option><option value="1920">1920年代</option><option value="1930">1930年代</option><option value="1940">1940年代</option><option value="1950">1950年代</option><option value="1960">1960年代</option><option value="1970">1970年代</option><option value="1980">1980年代</option><option value="1990">1990年代</option><option value="2000">2000年代</option><option value="2010">2010年代</option>
          </select>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">人工芝の使用用途</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="use1" type="checkbox" name="c[use_application][]" value="雑草対策">
            <label class="form-check-label pointer" for='use1'>雑草対策</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="use2" type="checkbox" name="c[use_application][]" value="ペット・ドッグラン用">
            <label class="form-check-label pointer" for='use2'>ペット・ドッグラン用</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="use3" type="checkbox" name="c[use_application][]" value="スポーツ">
            <label class="form-check-label pointer" for='use3'>スポーツ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="use4" type="checkbox" name="c[use_application][]" value="ゴルフ">
            <label class="form-check-label pointer" for='use4'>ゴルフ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="use5" type="checkbox" name="c[use_application][]" value="室内">
            <label class="form-check-label pointer" for='use5'>室内</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="use6" type="checkbox" name="c[use_application][]" value="景観目的">
            <label class="form-check-label pointer" for='use6'>景観目的</label>
          </div>
          <input type="text" name="c[use_application][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他使用用途"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="where1" type="checkbox" name="c[where_find][]" value="検索">
            <label class="form-check-label pointer" for='where1'>検索</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="where2" type="checkbox" name="c[where_find][]" value="ラジオ">
            <label class="form-check-label pointer" for='where2'>ラジオ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="where3" type="checkbox" name="c[where_find][]" value="チラシ">
            <label class="form-check-label pointer" for='where3'>チラシ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="where4" type="checkbox" name="c[where_find][]" value="紹介・口コミ">
            <label class="form-check-label pointer" for='where4'>紹介・口コミ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="where5" type="checkbox" name="c[where_find][]" value="Google広告">
            <label class="form-check-label pointer" for='where5'>Google広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="where6" type="checkbox" name="c[where_find][]" value="インスタ広告">
            <label class="form-check-label pointer" for='where6'>インスタ広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="where7" type="checkbox" name="c[where_find][]" value="facebook広告">
            <label class="form-check-label pointer" for='where7'>facebook広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="where8" type="checkbox" name="c[where_find][]" value="その他WEB広告">
            <label class="form-check-label pointer" for='where8'>その他WEB広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="where9" type="checkbox" name="c[where_find][]" value="通販サイト（楽天・アマゾンなど）">
            <label class="form-check-label pointer" for='where9'>通販サイト（楽天・アマゾンなど）</label>
          </div>

          <input type="text" name="c[where_find][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他認知経路">
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">現在使用しているSNS</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="sns1" type="checkbox" name="c[sns][]" value="Facebook">
            <label class="form-check-label pointer" for='sns1'>Facebook</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="sns2" type="checkbox" name="c[sns][]" value="Twitter">
            <label class="form-check-label pointer" for='sns2'>Twitter</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="sns3" type="checkbox" name="c[sns][]" value="LINE">
            <label class="form-check-label pointer" for='sns3'>LINE</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="sns4" type="checkbox" name="c[sns][]" value="Instagram">
            <label class="form-check-label pointer" for='sns4'>Instagram</label>
          </div>
          <input type="text" name="c[sns][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他SNS">
        </td>
      </tr>
  @if(isFc())
      <tr>
        <th class="common-table-stripes-column__th py-4">本部への対応依頼</th>
        <td class="common-table-stripes-column__td">
          <input class="form-check-label pointer" id="sampleAdmin" type="checkbox" name="c[sample_send_at]" value="checked" {{checked(!empty($user['admin_sample_send']))}}>
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="sampleAdmin">サンプルのみ本部からの送付を希望する</label>
        <br/>
          <input class="form-check-label pointer" id="leaveToAdmin" type="checkbox" name="leave_to_admin" value="checked">
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="leaveToAdmin">案件の対応を全て本部に依頼する</label>
        </td>
      </tr>
  @endif
  @if(isAdmin())
      <tr>
        <th class="common-table-stripes-column__th">
          メモ<br>
          <span class="ml10 common-table-stripes-column__span">本部にのみ表示されます</span>
        </th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="js-copy-memo form-control w100" rows="10" placeholder="メモ"></textarea>
        </td>
      </tr>
  @endif
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-end">
    <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact-submit'>問い合わせを登録</button></p>
  </div>
</form>

<!-- 個人サンプル請求 -->
<form id='personal-sample' contact-type-id='1' action="{{ route('contact.post') }}" method="POST" enctype="multipart/form-data" class="contact-form h-adr">
  @csrf
  <input type='hidden' name='c[contact_type_id]' value="1"/>
  <input type="hidden" class="p-country-name" value="Japan">
  <span class="p-country-name" style="display:none;">Japan</span>
  <table class="common-table-stripes-column">
    <tbody>
      <!-- しましまの整合性をとるためにdisplay:none -->
      <tr class='d-none'></tr>
      <tr>
        <th class="common-table-stripes-column__th">お名前<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname]" class="js-copy-surname form-control common-table-stripes-column__input mr20" placeholder="姓" dusk='p1-surname' required>
          <input type="text" name="c[name]" class="js-copy-name form-control common-table-stripes-column__input" placeholder="名" dusk='p1-name' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">フリガナ<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname_ruby]" class="js-copy-surname_ruby form-control common-table-stripes-column__input mr20" placeholder="セイ" dusk='p1-surname_ruby' required>
          <input type="text" name="c[name_ruby]" class="js-copy-name_ruby form-control common-table-stripes-column__input" placeholder="メイ" dusk='p1-name_ruby' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">郵便番号<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td"><input name="c[zipcode]" type="text" class="js-copy-zipcode form-control common-table-stripes-column__input p-postal-code" value="{{ old('c.zipcode') }}" required placeholder="1000001" dusk='p1-zipcode'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">顧客住所<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex justify-content-between">
          <input type="text" name="c[pref]"   class="js-copy-pref form-control p-region w15" value="{{ old('c.pref') }}" placeholder="サンプル都" dusk='p1-pref' required>
          <input type="text" name="c[city]"   class="js-copy-city form-control p-locality p-street-address p-extended-address w20" value="{{ old('c.city') }}" placeholder="サンプル中央区" dusk='p1-city' required>
          <input type="text" name="c[street]" class="js-copy-street form-control w50" value="{{ old('c.street') }}" placeholder="サンプルタウン1-1-1" dusk='p1-street' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[tel]" class="js-copy-tel form-control common-table-stripes-column__input mr20" placeholder="050-3561-2247" dusk='p1-tel' required>
          <input type="text" name="c[tel2]" class="js-copy-tel2 form-control common-table-stripes-column__input" placeholder="070-7777-7777" dusk='p1-tel2'>
    @if($errors->has('c.tel'))
          <div class='alert alert-danger'>正しく電話番号を入力してください</div>
    @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="js-copy-fax form-control common-table-stripes-column__input" placeholder="050-3561-2247" dusk='p1-fax'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メールアドレス</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="js-copy-email form-control common-table-stripes-column__input w60" placeholder="info@routeplus.co.jp" dusk='p1-email'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">年代</th>
        <td class="common-table-stripes-column__td">
          <select name="c[age]" class="js-copy-age form-control common-table-stripes-column__input" dusk='p1-age'>
            <option value="">---</option><option value="1910">1910年代</option><option value="1920">1920年代</option><option value="1930">1930年代</option><option value="1940">1940年代</option><option value="1950">1950年代</option><option value="1960">1960年代</option><option value="1970">1970年代</option><option value="1980">1980年代</option><option value="1990">1990年代</option><option value="2000">2000年代</option><option value="2010">2010年代</option>
          </select>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">人工芝の使用用途</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-use1" type="checkbox" name="c[use_application][]" value="雑草対策">
            <label class="form-check-label pointer" for='p1-use1'>雑草対策</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-use2" type="checkbox" name="c[use_application][]" value="ペット・ドッグラン用">
            <label class="form-check-label pointer" for='p1-use2'>ペット・ドッグラン用</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-use3" type="checkbox" name="c[use_application][]" value="スポーツ">
            <label class="form-check-label pointer" for='use3'>スポーツ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-use4" type="checkbox" name="c[use_application][]" value="ゴルフ">
            <label class="form-check-label pointer" for='p1-use4'>ゴルフ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-use5" type="checkbox" name="c[use_application][]" value="室内">
            <label class="form-check-label pointer" for='p1-use5'>室内</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-use6" type="checkbox" name="c[use_application][]" value="景観目的">
            <label class="form-check-label pointer" for='p1-use6'>景観目的</label>
          </div>
          <input type="text" name="c[use_application][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他使用用途" dusk='p1-use_application-etc'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-where1" type="checkbox" name="c[where_find][]" value="検索">
            <label class="form-check-label pointer" for='p1-where1'>検索</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-where2" type="checkbox" name="c[where_find][]" value="ラジオ">
            <label class="form-check-label pointer" for='p1-where2'>ラジオ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-where3" type="checkbox" name="c[where_find][]" value="チラシ">
            <label class="form-check-label pointer" for='p1-where3'>チラシ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-where4" type="checkbox" name="c[where_find][]" value="紹介・口コミ">
            <label class="form-check-label pointer" for='p1-where4'>紹介・口コミ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-where5" type="checkbox" name="c[where_find][]" value="Google広告">
            <label class="form-check-label pointer" for='p1-where5'>Google広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-where6" type="checkbox" name="c[where_find][]" value="インスタ広告">
            <label class="form-check-label pointer" for='p1-where6'>インスタ広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-where7" type="checkbox" name="c[where_find][]" value="facebook広告">
            <label class="form-check-label pointer" for='p1-where7'>facebook広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-where8" type="checkbox" name="c[where_find][]" value="その他WEB広告">
            <label class="form-check-label pointer" for='p1-where8'>その他WEB広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-where9" type="checkbox" name="c[where_find][]" value="通販サイト（楽天・アマゾンなど）">
            <label class="form-check-label pointer" for='p1-where9'>通販サイト（楽天・アマゾンなど）</label>
          </div>

          <input type="text" name="c[where_find][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他認知経路" dusk='p1-where_find-etc'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">現在使用しているSNS</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-sns1" type="checkbox" name="c[sns][]" value="Facebook">
            <label class="form-check-label pointer" for='p1-sns1'>Facebook</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-sns2" type="checkbox" name="c[sns][]" value="Twitter">
            <label class="form-check-label pointer" for='p1-sns2'>Twitter</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-sns3" type="checkbox" name="c[sns][]" value="LINE">
            <label class="form-check-label pointer" for='p1-sns3'>LINE</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" id="p1-sns4" type="checkbox" name="c[sns][]" value="Instagram">
            <label class="form-check-label pointer" for='p1-sns4'>Instagram</label>
          </div>
          <input type="text" name="c[sns][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他SNS" dusk='p1-sns-etc'>
        </td>
      </tr>
  @if(isFc())
      <tr>
        <th class="common-table-stripes-column__th py-4">本部への対応依頼</th>
        <td class="common-table-stripes-column__td">
          <input class="form-check-label pointer" id="sampleAdmin1" type="checkbox" name="c[sample_send_at]" value="checked" {{checked(!empty($user['admin_sample_send']))}}>
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="sampleAdmin1">サンプルのみ本部からの送付を希望する</label>
        <br/>
          <input class="form-check-label pointer" id="leaveToAdmin1" type="checkbox" name="leave_to_admin" value="checked">
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="leaveToAdmin1">案件の対応を全て本部に依頼する</label>
        </td>
      </tr>
  @endif
  @if(isAdmin())
      <tr>
        <th class="common-table-stripes-column__th">
          メモ<br>
          <span class="ml10 common-table-stripes-column__span">本部にのみ表示されます</span>
        </th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="js-copy-memo form-control w100" rows="10" placeholder="メモ"></textarea>
        </td>
      </tr>
  @endif
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-end">
    <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact1-submit'>問い合わせを登録</button></p>
  </div>
</form>


<!-- 個人図面見積もり -->
<form id='personal-drow' contact-type-id='2' action="{{ route('contact.post') }}" method="POST" enctype="multipart/form-data" class="contact-form h-adr">
  @csrf
  <span class="p-country-name" style="display:none;">Japan</span>
  <input type='hidden' name='c[contact_type_id]' value="2"/>
  <table class="common-table-stripes-column">
    <tbody>
      <!-- しましまの整合性をとるためにdisplay:none -->
      <tr class='d-none'></tr>
      <tr>
        <th class="common-table-stripes-column__th">無料サンプル<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="freesample1" name="c[free_sample]" value="必要" checked>
            <label class="form-check-label pointer" for='freesample1'>必要</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="freesample2" name="c[free_sample]" value="請求済み">
            <label class="form-check-label pointer" for='freesample2'>請求済み</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="freesample3" name="c[free_sample]" value="不要">
            <label class="form-check-label pointer" for='freesample3'>不要</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お名前<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname]" class="js-copy-surname form-control common-table-stripes-column__input mr20" placeholder="姓"  dusk='p2-surname' required>
          <input type="text" name="c[name]" class="js-copy-name form-control common-table-stripes-column__input" placeholder="名" dusk='p2-name' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">フリガナ<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname_ruby]" class="js-copy-surname_ruby form-control common-table-stripes-column__input mr20" placeholder="セイ" dusk='p2-surname_ruby' required>
          <input type="text" name="c[name_ruby]" class="js-copy-name_ruby form-control common-table-stripes-column__input" placeholder="メイ" dusk='p2-name_ruby' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">郵便番号<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <input name="c[zipcode]" type="text" class="js-copy-zipcode form-control common-table-stripes-column__input p-postal-code" value="{{ old('c.zipcode') }}" required placeholder="1000001" dusk='p2-zipcode'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">顧客住所<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex justify-content-between">
          <input type="text" name="c[pref]" class="js-copy-pref form-control p-region w15" value="{{ old('c.pref') }}" placeholder="サンプル都" dusk='p2-pref' required>
          <input type="text" name="c[city]" class="js-copy-city form-control p-locality p-street-address p-extended-address w20" value="{{ old('c.city') }}" placeholder="サンプル中央区" dusk='p2-city' required>
          <input type="text" name="c[street]" class="js-copy-street form-control w50" value="{{ old('c.street') }}" placeholder="サンプルタウン1-1-1" dusk='p2-street' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[tel]" class="js-copy-tel form-control common-table-stripes-column__input mr20" placeholder="050-3561-2247" dusk='p2-tel' required>
          <input type="text" name="c[tel2]" class="js-copy-tel2 form-control common-table-stripes-column__input" placeholder="070-7777-7777" dusk='p2-tel2'>
    @if($errors->has('c.tel'))
          <div class='alert alert-danger'>正しく電話番号を入力してください</div>
    @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="js-copy-fax form-control common-table-stripes-column__input" placeholder="050-3561-2247" dusk='p2-fax'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メールアドレス</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="js-copy-email form-control common-table-stripes-column__input w60" placeholder="info@routeplus.co.jp" dusk='p2-email'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">年代</th>
        <td class="common-table-stripes-column__td">
          <select name="c[age]" class="js-copy-age form-control common-table-stripes-column__input" dusk='p2-age'>
            <option value="">---</option><option value="1910">1910</option><option value="1920">1920</option><option value="1930">1930</option><option value="1940">1940</option><option value="1950">1950</option><option value="1960">1960</option><option value="1970">1970</option><option value="1980">1980</option><option value="1990">1990</option><option value="2000">2000</option><option value="2010">2010</option>
          </select>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お見積もり内容</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p2-quote_details1" name="c[quote_details]" value="施工希望">
            <label class="form-check-label pointer" for='p2-quote_details1'>施工希望</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p2-quote_details2" name="c[quote_details]" value="材料のみ">
            <label class="form-check-label pointer" for='p2-quote_details2'>材料のみ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p2-quote_details3" name="c[quote_details]" value="施工希望、材料のみ">
            <label class="form-check-label pointer" for='p2-quote_details3'>施工希望、材料のみ</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">下地状況</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-ground1" name="c[ground_condition][]" value="土">
            <label class="form-check-label pointer" for='p2-ground1'>土</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-ground2" name="c[ground_condition][]" value="雑草または天然芝">
            <label class="form-check-label pointer" for='p2-ground2'>雑草または天然芝</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-ground3" name="c[ground_condition][]" value="コンクリート">
            <label class="form-check-label pointer" for='p2-ground3'>コンクリート</label>
          </div>
          </div>
          <input type="text" name="c[ground_condition][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他の下地状況はこちらに入力" dusk='p2-ground_condition-etc'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">施工場所サイズ</th>
        <td class="common-table-stripes-column__td d-flex align-items-center">
          <span class='f12'>縦</span><input type="text" name="c[vertical_size]" class="form-control common-table-stripes-column__input w10" placeholder="24" min="0"  dusk='p2-vertical_size'><span class='mr20 f12'>m</span>
          <span class='f12 mr20'>×</span>
          <span class='f12'>横</span><input type="text" name="c[horizontal_size]" class="form-control common-table-stripes-column__input w10" placeholder="12" min="0" dusk='p2-horizontal_size'><span class='f12'>m</span>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">希望商品</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="p2-product0" name="c[desired_product][]" value="サンプル芝プレミアム40mm">
            <label class="form-check-label pointer" for='p2-product0'>サンプル芝プレミアム40mm</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="p2-product1" name="c[desired_product][]" value="サンプル芝30mm">
            <label class="form-check-label pointer" for='p2-product1'>サンプル芝30mm</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="p2-product2" name="c[desired_product][]" value="サンプル芝COOL">
            <label class="form-check-label pointer" for='p2-product2'>サンプル芝COOL</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="p2-product3" name="c[desired_product][]" value="SB30CP1">
            <label class="form-check-label pointer" for='p2-product3'>SB30CP1</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="p2-product4" name="c[desired_product][]" value="グレー">
            <label class="form-check-label pointer" for='p2-product4'>グレー</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="p2-product5" name="c[desired_product][]" value="ラテブラウン">
            <label class="form-check-label pointer" for='p2-product5'>ラテブラウン</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="p2-product6" name="c[desired_product][]" value="ゴルフ用8mm">
            <label class="form-check-label pointer" for='p2-product6'>ゴルフ用8mm</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="p2-product7" name="c[desired_product][]" value="ゴルフ用12mm">
            <label class="form-check-label pointer" for='p2-product7'>ゴルフ用12mm</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="p2-product8" name="c[desired_product][]" value="サンプル芝O2">
            <label class="form-check-label pointer" for='p2-product8'>サンプル芝O2</label>
          </div>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">人工芝の使用用途</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-use1" name="c[use_application][]" value="雑草対策">
            <label class="form-check-label pointer" for='p2-use1'>雑草対策</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-use2" name="c[use_application][]" value="ペット・ドッグラン用">
            <label class="form-check-label pointer" for='p2-use2'>ペット・ドッグラン用</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-use3" name="c[use_application][]" value="スポーツ">
            <label class="form-check-label pointer" for='p2-use3'>スポーツ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-use4" name="c[use_application][]" value="ゴルフ">
            <label class="form-check-label pointer" for='p2-use4'>ゴルフ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-use5" name="c[use_application][]" value="室内">
            <label class="form-check-label pointer" for='p2-use5'>室内</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-use6" name="c[use_application][]" value="景観目的">
            <label class="form-check-label pointer" for='p2-use6'>景観目的</label>
          </div>
          <input type="text" name="c[use_application][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他使用用途" dusk='p2-use_application-etc'></td>
      </tr>
      <tr>
        <th>添付資料<span class='f09' style="color: red">既にファイルが登録されている箇所も変更できます。</span></th>
        <td class='d-flex flex-wrap justify-content-between'>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document1]" class='js-file js-image1'/>
            </div>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document2]" class='js-file js-image2' />
            </div>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document3]" class='js-file js-image3' />
            </div>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document4]" class='js-file js-image4' />
            </div>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document5]" class='js-file js-image5' />
            </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">備考・その他ご要望</th>
        <td class="common-table-stripes-column__td"><textarea name="c[comment]" class="js-copy-etc form-control common-table-stripes-column__input w100" placeholder="コメント" dusk='p2-comment'></textarea></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-where1" name="c[where_find][]" value="検索">
            <label class="form-check-label pointer" for='p2-where1'>検索</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-where2" name="c[where_find][]" value="ラジオ">
            <label class="form-check-label pointer" for='p2-where2'>ラジオ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-where3" name="c[where_find][]" value="チラシ">
            <label class="form-check-label pointer" for='p2-where3'>チラシ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-where4" name="c[where_find][]" value="紹介・口コミ">
            <label class="form-check-label pointer" for='p2-where4'>紹介・口コミ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-where5" name="c[where_find][]" value="Google広告">
            <label class="form-check-label pointer" for='p2-where5'>Google広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-where6" name="c[where_find][]" value="インスタ広告">
            <label class="form-check-label pointer" for='p2-where6'>インスタ広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-where7" name="c[where_find][]" value="facebook広告">
            <label class="form-check-label pointer" for='p2-where7'>facebook広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-where8" name="c[where_find][]" value="その他WEB広告">
            <label class="form-check-label pointer" for='p2-where8'>その他WEB広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-where9" name="c[where_find][]" value="通販サイト（楽天・アマゾンなど）">
            <label class="form-check-label pointer" for='p2-where9'>通販サイト（楽天・アマゾンなど）</label>
          </div>

          <input type="text" name="c[where_find][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他認知経路" dusk='p2-where_find-etc'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">現在使用しているSNS</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-sns1" name="c[sns][]" value="Facebook">
            <label class="form-check-label pointer" for='p2-sns1'>Facebook</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-sns2" name="c[sns][]" value="Twitter">
            <label class="form-check-label pointer" for='p2-sns2'>Twitter</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-sns3" name="c[sns][]" value="LINE">
            <label class="form-check-label pointer" for='p2-sns3'>LINE</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p2-sns4" name="c[sns][]" value="Instagram">
            <label class="form-check-label pointer" for='p2-sns4'>Instagram</label>
          </div>
          <input type="text" name="c[sns][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他SNS" dusk='p2-sns-etc'>
        </td>
      </tr>
  @if(isAdmin())
      <tr>
        <th class="common-table-stripes-column__th">
          メモ<br>
          <span class="ml10 common-table-stripes-column__span">本部にのみ表示されます</span>
        </th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="js-copy-memo form-control w100" rows="10" placeholder="メモ" dusk='p2-memo'></textarea>
        </td>
      </tr>
  @endif
  @if(isFc())
      <tr>
        <th class="common-table-stripes-column__th py-4">本部への対応依頼</th>
        <td class="common-table-stripes-column__td">
          <input class="form-check-label pointer" id="sampleAdmin2" type="checkbox" name="c[sample_send_at]" value="checked" {{checked(!empty($user['admin_sample_send']))}}>
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="sampleAdmin2">サンプルのみ本部からの送付を希望する</label>
        <br/>
          <input class="form-check-label pointer" id="leaveToAdmin2" type="checkbox" name="leave_to_admin" value="checked">
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="leaveToAdmin2">案件の対応を全て本部に依頼する</label>
        </td>
      </tr>
  @endif
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-end">
    <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact2-submit'>問い合わせを登録</button></p>
  </div>
</form>


<!-- 個人訪問見積もり -->
<form id='personal-visit' contact-type-id='3' action="{{ route('contact.post') }}" method="POST" enctype="multipart/form-data" class="contact-form">
  @csrf
  <input type='hidden' name='c[contact_type_id]' value="3"/>
  <table class="common-table-stripes-column">
    <tbody>
      <!-- しましまの整合性をとるためにdisplay:none -->
      <tr class='d-none'></tr>
      <tr>
        <th class="common-table-stripes-column__th">無料サンプル<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p3-freesample1" name="c[free_sample]" value="必要" checked>
            <label class="form-check-label pointer" for='p3-freesample1'>必要</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p3-freesample2" name="c[free_sample]" value="請求済み">
            <label class="form-check-label pointer" for='p3-freesample2'>請求済み</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p3-freesample3" name="c[free_sample]" value="不要">
            <label class="form-check-label pointer" for='p3-freesample3'>不要</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お名前<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname]" class="js-copy-surname form-control common-table-stripes-column__input mr20" placeholder="姓"  dusk='p3-surname' required>
          <input type="text" name="c[name]" class="js-copy-name form-control common-table-stripes-column__input" placeholder="名" dusk='p3-name' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">フリガナ<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname_ruby]" class="js-copy-surname_ruby form-control common-table-stripes-column__input mr20" placeholder="セイ" dusk='p3-surname_ruby' required>
          <input type="text" name="c[name_ruby]" class="js-copy-name_ruby form-control common-table-stripes-column__input" placeholder="メイ" dusk='p3-name_ruby' required>
        </td>
      </tr>
      <tr class="h-adr">
        <th class="common-table-stripes-column__th">郵便番号<span class="ml10 common-table-stripes-column__span">必須</span><br>顧客住所<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <input type="hidden" class="p-country-name" value="Japan">
          <input name="c[zipcode]" type="text" class="js-copy-zipcode form-control common-table-stripes-column__input p-postal-code mb5" value="{{ old('c.zipcode') }}" required placeholder="1000001" dusk='p3-zipcode'>
          <div class="d-flex justify-content-between">
            <input type="text" name="c[pref]" class="js-copy-pref form-control p-region w15" value="{{ old('c.pref') }}" placeholder="サンプル都" dusk='p3-pref' required>
            <input type="text" name="c[city]" class="js-copy-city form-control p-locality p-street-address p-extended-address w20" value="{{ old('c.city') }}" placeholder="サンプル中央区" dusk='p3-city' required>
            <input type="text" name="c[street]" class="js-copy-street form-control w50" value="{{ old('c.street') }}" placeholder="サンプルタウン1-1-1" dusk='p3-street' required>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[tel]" class="js-copy-tel form-control common-table-stripes-column__input mr20" placeholder="050-3561-2247" dusk='p3-tel' required>
          <input type="text" name="c[tel2]" class="js-copy-tel2 form-control common-table-stripes-column__input" placeholder="070-7777-7777" dusk='p3-tel2'>
    @if($errors->has('c.tel'))
          <div class='alert alert-danger'>正しく電話番号を入力してください</div>
    @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="js-copy-fax form-control common-table-stripes-column__input" placeholder="050-3561-2247" dusk='p3-fax'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メールアドレス</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="js-copy-email form-control common-table-stripes-column__input w60" placeholder="info@routeplus.co.jp" dusk='p3-email'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">年代</th>
        <td class="common-table-stripes-column__td">
          <select name="c[age]" class="js-copy-age form-control common-table-stripes-column__input" dusk='p3-age'>
            <option value="">---</option><option value="1910">1910</option><option value="1920">1920</option><option value="1930">1930</option><option value="1940">1940</option><option value="1950">1950</option><option value="1960">1960</option><option value="1970">1970</option><option value="1980">1980</option><option value="1990">1990</option><option value="2000">2000</option><option value="2010">2010</option>
          </select>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">下地状況</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-ground1" name="c[ground_condition][]" value="土">
            <label class="form-check-label pointer" for='p3-ground1'>土</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-ground2" name="c[ground_condition][]" value="雑草または天然芝">
            <label class="form-check-label pointer" for='p3-ground2'>雑草または天然芝</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-ground3" name="c[ground_condition][]" value="コンクリート">
            <label class="form-check-label pointer" for='p3-ground3'>コンクリート</label>
          </div>
          </div>
          <input type="text" name="c[ground_condition][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他の下地状況はこちらに入力" dusk='p3-ground_condition-etc'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">第1訪問希望日時</th>
        <td class="common-table-stripes-column__td d-flex align-items-center">
          <span class='f11 mr5'>訪問日</span>
          <input data-provide="datepicker" class="form-control datepicker js-date w30per mr20" type="date" name='c[desired_datetime1][date]' value='訪問日時を選択' dusk='p3-desired_datetime1-date' autocomplete="off"/>
          <select class='form-control w30per' name='c[desired_datetime1][time]' dusk='p3-desired_datetime1-time' >
            <option value=''>訪問時間を選択</option>
    @for($i=8; $i<20; $i++)
            <option value={{$i}}>{{$i}}時ごろ</option>
    @endfor
          </select>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">第2訪問希望日時</th>
        <td class="common-table-stripes-column__td d-flex align-items-center">
          <span class='f11 mr5'>訪問日</span>
          <input data-provide="datepicker" class="form-control datepicker js-date w30per mr20" type="date" name='c[desired_datetime2][date]' dusk='p3-desired_datetime2-date' autocomplete="off"/>
          <select class='form-control w30per' name='c[desired_datetime2][time]' dusk='p3-desired_datetime2-time'>
            <option value=''>訪問時間を選択</option>
    @for($i=8; $i<20; $i++)
            <option value={{$i}}>{{$i}}時ごろ</option>
    @endfor
          </select>
        </td>
      </tr>
      <tr class="h-adr">
        <th class="common-table-stripes-column__th">訪問先住所（顧客住所と異なる場合）</th>
        <td class="common-table-stripes-column__td h-adr">
          <input type="hidden" class="p-country-name" value="Japan">
          <input type="text" class="form-control common-table-stripes-column__input p-postal-code mb5" value="" placeholder="1000001" dusk='p3-zipcode'>
          <div class="common-table-stripes-column__td d-flex justify-content-between p0">
            <input type="text" name="c[visit_address]" class="form-control p-region p-locality p-street-address p-extended-address" placeholder="サンプル都練馬区桜台4丁目" dusk='p3-visit_address'>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">平米数</th>
        <td class="common-table-stripes-column__td d-flex align-items-center"><input type="number" name="c[square_meter]" class="form-control common-table-stripes-column__input w15" placeholder="44" dusk='p3-square_meter'><span class='f11'>平米</span></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お見積もり内容</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p3-quote_details1" name="c[quote_details]" value="施工希望">
            <label class="form-check-label pointer" for='p2-quote_details1'>施工希望</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p3-quote_details2" name="c[quote_details]" value="材料のみ">
            <label class="form-check-label pointer" for='p2-quote_details2'>材料のみ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p3-quote_details3" name="c[quote_details]" value="施工希望、材料のみ">
            <label class="form-check-label pointer" for='p2-quote_details3'>施工希望、材料のみ</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">人工芝の使用用途</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-use1" name="c[use_application][]" value="雑草対策">
            <label class="form-check-label pointer" for='p3-use1'>雑草対策</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-use2" name="c[use_application][]" value="ペット・ドッグラン用">
            <label class="form-check-label pointer" for='p3-use2'>ペット・ドッグラン用</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-use3" name="c[use_application][]" value="スポーツ">
            <label class="form-check-label pointer" for='p3-use3'>スポーツ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-use4" name="c[use_application][]" value="ゴルフ">
            <label class="form-check-label pointer" for='p3-use4'>ゴルフ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-use5" name="c[use_application][]" value="室内">
            <label class="form-check-label pointer" for='p3-use5'>室内</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-use6" name="c[use_application][]" value="景観目的">
            <label class="form-check-label pointer" for='p3-use6'>景観目的</label>
          </div>
          <input type="text" name="c[use_application][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他使用用途" dusk='p3-use_application-etc'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">備考・その他ご要望</th>
        <td class="common-table-stripes-column__td"><textarea name="c[comment]" class="js-copy-etc form-control common-table-stripes-column__input w100" placeholder="コメント" dusk='p3-comment'></textarea></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-where1" name="c[where_find][]" value="検索">
            <label class="form-check-label pointer" for='p3-where1'>検索</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-where2" name="c[where_find][]" value="ラジオ">
            <label class="form-check-label pointer" for='p3-where2'>ラジオ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-where3" name="c[where_find][]" value="チラシ">
            <label class="form-check-label pointer" for='p3-where3'>チラシ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-where4" name="c[where_find][]" value="紹介・口コミ">
            <label class="form-check-label pointer" for='p3-where4'>紹介・口コミ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-where5" name="c[where_find][]" value="Google広告">
            <label class="form-check-label pointer" for='p3-where5'>Google広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-where6" name="c[where_find][]" value="インスタ広告">
            <label class="form-check-label pointer" for='p3-where6'>インスタ広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-where7" name="c[where_find][]" value="facebook広告">
            <label class="form-check-label pointer" for='p3-where7'>facebook広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-where8" name="c[where_find][]" value="その他WEB広告">
            <label class="form-check-label pointer" for='p3-where8'>その他WEB広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-where9" name="c[where_find][]" value="通販サイト（楽天・アマゾンなど）">
            <label class="form-check-label pointer" for='p3-where9'>通販サイト（楽天・アマゾンなど）</label>
          </div>

          <input type="text" name="c[where_find][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他認知経路" dusk='p3-where_find-etc'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">現在使用しているSNS</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-sns1" name="c[sns][]" value="Facebook">
            <label class="form-check-label pointer" for='p3-sns1'>Facebook</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-sns2" name="c[sns][]" value="Twitter">
            <label class="form-check-label pointer" for='p3-sns2'>Twitter</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-sns3" name="c[sns][]" value="LINE">
            <label class="form-check-label pointer" for='p3-sns3'>LINE</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p3-sns4" name="c[sns][]" value="Instagram">
            <label class="form-check-label pointer" for='p3-sns4'>Instagram</label>
          </div>
          <input type="text" name="c[sns][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他SNS" dusk='p3-sns-etc'>
        </td>
      </tr>
  @if(isAdmin())
      <tr>
        <th class="common-table-stripes-column__th">
          メモ<br>
          <span class="ml10 common-table-stripes-column__span">本部にのみ表示されます</span>
        </th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="js-copy-memo form-control w100" rows="10" placeholder="メモ" dusk='p3-memo'></textarea>
        </td>
      </tr>
  @endif
  @if(isFc())
      <tr>
        <th class="common-table-stripes-column__th py-4">本部への対応依頼</th>
        <td class="common-table-stripes-column__td">
          <input class="form-check-label pointer" id="sampleAdmin3" type="checkbox" name="c[sample_send_at]" value="checked" {{checked(!empty($user['admin_sample_send']))}}>
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="sampleAdmin3">サンプルのみ本部からの送付を希望する</label>
        <br/>
          <input class="form-check-label pointer" id="leaveToAdmin3" type="checkbox" name="leave_to_admin" value="checked">
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="leaveToAdmin3">案件の対応を全て本部に依頼する</label>
        </td>
      </tr>
  @endif
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-end">
    <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact3-submit'>問い合わせを登録</button></p>
  </div>
</form>



<!-- 個人その他 -->
<form id='personal-visit' contact-type-id='4' action="{{ route('contact.post') }}" method="POST" enctype="multipart/form-data" class="contact-form h-adr">
  @csrf
  <input type='hidden' name='c[contact_type_id]' value="4"/>
  <table class="common-table-stripes-column">
    <tbody>
      <!-- しましまの整合性をとるためにdisplay:none -->
      <tr class='d-none'></tr>
      <tr>
        <th class="common-table-stripes-column__th">無料サンプル<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p4-freesample1" name="c[free_sample]" value="必要" checked>
            <label class="form-check-label pointer" for='p4-freesample1'>必要</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p4-freesample2" name="c[free_sample]" value="請求済み">
            <label class="form-check-label pointer" for='p4-freesample2'>請求済み</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="p4-freesample3" name="c[free_sample]" value="不要">
            <label class="form-check-label pointer" for='p4-freesample3'>不要</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お名前<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname]" class="js-copy-surname form-control common-table-stripes-column__input mr20" placeholder="姓"  dusk='p4-surname' required>
          <input type="text" name="c[name]" class="js-copy-name form-control common-table-stripes-column__input" placeholder="名" dusk='p4-name' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">フリガナ<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname_ruby]" class="js-copy-surname_ruby form-control common-table-stripes-column__input mr20" placeholder="セイ" dusk='p4-surname_ruby' required>
          <input type="text" name="c[name_ruby]" class="js-copy-name_ruby form-control common-table-stripes-column__input" placeholder="メイ" dusk='p4-name_ruby' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">郵便番号<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <input type="hidden" class="p-country-name" value="Japan">
          <input name="c[zipcode]" type="text" class="js-copy-zipcode form-control common-table-stripes-column__input p-postal-code mb5" value="{{ old('c.zipcode') }}" required placeholder="1000001" dusk='p4-zipcode'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">顧客住所<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex justify-content-between">
          <input type="text" name="c[pref]" class="js-copy-pref form-control p-region w15" value="{{ old('c.pref') }}" placeholder="サンプル都" dusk='p4-pref' required>
          <input type="text" name="c[city]" class="js-copy-city form-control p-locality p-street-address p-extended-address w20" value="{{ old('c.city') }}" placeholder="サンプル中央区" dusk='p4-city' required>
          <input type="text" name="c[street]" class="js-copy-street form-control w50" value="{{ old('c.street') }}" placeholder="サンプルタウン1-1-1" dusk='p4-street' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[tel]" class="js-copy-tel form-control common-table-stripes-column__input mr20" placeholder="050-3561-2247" dusk='p4-tel' required>
          <input type="text" name="c[tel2]" class="js-copy-tel2 form-control common-table-stripes-column__input" placeholder="070-7777-7777" dusk='p4-tel2'>
    @if($errors->has('c.tel'))
          <div class='alert alert-danger'>正しく電話番号を入力してください</div>
    @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="js-copy-fax form-control common-table-stripes-column__input" placeholder="050-3561-2247" dusk='p4-fax'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メールアドレス</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="js-copy-email form-control common-table-stripes-column__input w60" placeholder="info@routeplus.co.jp" dusk='p4-email'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">年代</th>
        <td class="common-table-stripes-column__td">
          <select name="c[age]" class="js-copy-age form-control common-table-stripes-column__input" dusk='p4-age'>
            <option value="">---</option><option value="1910">1910</option><option value="1920">1920</option><option value="1930">1930</option><option value="1940">1940</option><option value="1950">1950</option><option value="1960">1960</option><option value="1970">1970</option><option value="1980">1980</option><option value="1990">1990</option><option value="2000">2000</option><option value="2010">2010</option>
          </select>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">ご用件</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-quote1" name="c[quote_details][]" value="来場予約">
            <label class="form-check-label pointer" for='p4-quote1'>来場予約</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-quote2" name="c[quote_details][]" value="商品に関するご質問">
            <label class="form-check-label pointer" for='p4-quote2'>商品に関するご質問</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-quote3" name="c[quote_details][]" value="施工に関するご質問">
            <label class="form-check-label pointer" for='p4-quote3'>施工に関するご質問</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-quote4" name="c[quote_details][]" value="施工パートナー加盟に関するご質問">
            <label class="form-check-label pointer" for='p4-quote4'>施工パートナー加盟に関するご質問</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-quote5" name="c[quote_details][]" value="その他のご質問">
            <label class="form-check-label pointer" for='p4-quote5'>その他のご質問</label>
          </div>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">備考・その他ご要望</th>
        <td class="common-table-stripes-column__td"><textarea name="c[comment]" class="js-copy-etc form-control common-table-stripes-column__input w100" placeholder="コメント" dusk='p4-comment'></textarea></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-where1" name="c[where_find][]" value="検索">
            <label class="form-check-label pointer" for='p4-where1'>検索</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-where2" name="c[where_find][]" value="ラジオ">
            <label class="form-check-label pointer" for='p4-where2'>ラジオ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-where3" name="c[where_find][]" value="チラシ">
            <label class="form-check-label pointer" for='p4-where3'>チラシ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-where4" name="c[where_find][]" value="紹介・口コミ">
            <label class="form-check-label pointer" for='p4-where4'>紹介・口コミ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-where5" name="c[where_find][]" value="Google広告">
            <label class="form-check-label pointer" for='p4-where5'>Google広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-where6" name="c[where_find][]" value="インスタ広告">
            <label class="form-check-label pointer" for='p4-where6'>インスタ広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-where7" name="c[where_find][]" value="facebook広告">
            <label class="form-check-label pointer" for='p4-where7'>facebook広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-where8" name="c[where_find][]" value="その他WEB広告">
            <label class="form-check-label pointer" for='p4-where8'>その他WEB広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-where9" name="c[where_find][]" value="通販サイト（楽天・アマゾンなど）">
            <label class="form-check-label pointer" for='p4-where9'>通販サイト（楽天・アマゾンなど）</label>
          </div>

          <input type="text" name="c[where_find][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他認知経路" dusk='p4-where_find-etc'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">現在使用しているSNS</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-sns1" name="c[sns][]" value="Facebook">
            <label class="form-check-label pointer" for='p4-sns1'>Facebook</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-sns2" name="c[sns][]" value="Twitter">
            <label class="form-check-label pointer" for='p4-sns2'>Twitter</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-sns3" name="c[sns][]" value="LINE">
            <label class="form-check-label pointer" for='p4-sns3'>LINE</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="p4-sns4" name="c[sns][]" value="Instagram">
            <label class="form-check-label pointer" for='p4-sns4'>Instagram</label>
          </div>
          <input type="text" name="c[sns][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他SNS" dusk='p4-sns-etc'>
        </td>
      </tr>
  @if(isAdmin())
      <tr>
        <th class="common-table-stripes-column__th">
          メモ<br>
          <span class="ml10 common-table-stripes-column__span">本部にのみ表示されます</span>
        </th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="js-copy-memo form-control w100" rows="10" placeholder="メモ" dusk='p4-memo'></textarea>
        </td>
      </tr>
  @endif
  @if(isFc())
      <tr>
        <th class="common-table-stripes-column__th py-4">本部への対応依頼</th>
        <td class="common-table-stripes-column__td">
          <input class="form-check-label pointer" id="leaveToAdmin4" type="checkbox" name="leave_to_admin" value="checked">
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="leaveToAdmin4">案件の対応を全て本部に依頼する</label>
        </td>
      </tr>
  @endif
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-end">
    <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact4-submit'>問い合わせを登録</button></p>
  </div>
</form>


<!-- 法人サンプル請求 -->
<form id='company-sample' contact-type-id='5' action="{{ route('contact.post') }}" method="POST" enctype="multipart/form-data" class="contact-form h-adr">
  @csrf
  <input type='hidden' name='c[contact_type_id]' value="5"/>
  <input type="hidden" class="p-country-name" value="Japan">
  <table class="common-table-stripes-column">
    <tbody>
      <!-- しましまの整合性をとるためにdisplay:none -->
      <tr class='d-none'></tr>
      <tr>
        <th class="common-table-stripes-column__th">会社名・フリガナ<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[company_name]" class="form-control common-table-stripes-column__input mr20 js-copy-company_name" dusk='h1-company_name' placeholder="サンプル株式会社" required>
          <input type="text" name="c[company_ruby]" class="form-control common-table-stripes-column__input js-copy-company_ruby" dusk='h1-company_ruby' placeholder="ユウゲンガイシャシバンチュ" required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">担当者名<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname]" class="js-copy-surname form-control common-table-stripes-column__input mr20" dusk='h1-surname' placeholder="担当者名" required>
          <input type="text" name="c[surname_ruby]" class="js-copy-surname_ruby form-control common-table-stripes-column__input" dusk='h1-surname-ruby' placeholder="担当者名フリガナ（任意）">
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">業種</th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[industry]" class="js-copy-industry form-control common-table-stripes-column__input" placeholder="小売・卸売" dusk='h1-industry'>
        </td>
      <tr>
        <th class="common-table-stripes-column__th">郵便番号<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td"><input name="c[zipcode]" type="text" class="js-copy-zipcode form-control common-table-stripes-column__input p-postal-code" dusk='h1-zipcode' required placeholder="1000001"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">会社住所<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex justify-content-between">
          <input type="text" name="c[pref]"   class="js-copy-pref form-control p-region w15" placeholder="サンプル都" dusk='h1-pref' required>
          <input type="text" name="c[city]"   class="js-copy-city form-control p-locality p-street-address p-extended-address w20" placeholder="サンプル中央区" dusk='h1-city' required>
          <input type="text" name="c[street]" class="js-copy-street form-control w50" dusk='h1-street' placeholder="サンプルタウン1-1-1" required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[tel]" class="js-copy-tel form-control common-table-stripes-column__input mr20" placeholder="050-3561-2247" dusk='h1-tel' required>
          <input type="text" name="c[tel2]" class="js-copy-tel2 form-control common-table-stripes-column__input" placeholder="070-7777-7777" dusk='h1-tel2'>
    @if($errors->has('c.tel'))
          <div class='alert alert-danger'>正しく電話番号を入力してください</div>
    @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="js-copy-fax form-control common-table-stripes-column__input" placeholder="050-3561-2247"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メールアドレス</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="js-copy-email form-control common-table-stripes-column__input w60" placeholder="info@routeplus.co.jp"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">人工芝の使用用途</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-use1" name="c[use_application][]" value="雑草対策">
            <label class="form-check-label pointer" for='h1-use1'>雑草対策</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-use2" name="c[use_application][]" value="ペット・ドッグラン用">
            <label class="form-check-label pointer" for='h1-use2'>ペット・ドッグラン用</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-use3" name="c[use_application][]" value="スポーツ">
            <label class="form-check-label pointer" for='h1-use3'>スポーツ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-use4" name="c[use_application][]" value="ゴルフ">
            <label class="form-check-label pointer" for='h1-use4'>ゴルフ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-use5" name="c[use_application][]" value="室内">
            <label class="form-check-label pointer" for='h1-use5'>室内</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-use6" name="c[use_application][]" value="景観目的">
            <label class="form-check-label pointer" for='h1-use6'>景観目的</label>
          </div>
          <input type="text" name="c[use_application][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他使用用途" dusk='h1-use_application-etc'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-where1" name="c[where_find][]" value="検索">
            <label class="form-check-label pointer" for='h1-where1'>検索</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-where2" name="c[where_find][]" value="ラジオ">
            <label class="form-check-label pointer" for='h1-where2'>ラジオ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-where3" name="c[where_find][]" value="チラシ">
            <label class="form-check-label pointer" for='h1-where3'>チラシ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-where4" name="c[where_find][]" value="紹介・口コミ">
            <label class="form-check-label pointer" for='h1-where4'>紹介・口コミ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-where5" name="c[where_find][]" value="Google広告">
            <label class="form-check-label pointer" for='h1-where5'>Google広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-where6" name="c[where_find][]" value="インスタ広告">
            <label class="form-check-label pointer" for='h1-where6'>インスタ広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-where7" name="c[where_find][]" value="facebook広告">
            <label class="form-check-label pointer" for='h1-where7'>facebook広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-where8" name="c[where_find][]" value="その他WEB広告">
            <label class="form-check-label pointer" for='h1-where8'>その他WEB広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h1-where9" name="c[where_find][]" value="通販サイト（楽天・アマゾンなど）">
            <label class="form-check-label pointer" for='h1-where9'>通販サイト（楽天・アマゾンなど）</label>
          </div>

          <input type="text" name="c[where_find][etc]" class="form-control common-table-stripes-column__input mt10 w60" dusk='h1-where_find-etc' placeholder="その他認知経路">
        </td>
      </tr>
  @if(isAdmin())
      <tr>
        <th class="common-table-stripes-column__th">
          メモ<br>
          <span class="ml10 common-table-stripes-column__span">本部にのみ表示されます</span>
        </th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="js-copy-memo form-control w100" rows="10" placeholder="メモ" dusk='p4-memo'></textarea>
        </td>
      </tr>
  @endif
  @if(isFc())
      <tr>
        <th class="common-table-stripes-column__th py-4">本部への対応依頼</th>
        <td class="common-table-stripes-column__td">
          <input class="form-check-label pointer" id="sampleAdmin5" type="checkbox" name="c[sample_send_at]" value="checked" {{checked(!empty($user['admin_sample_send']))}}>
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="sampleAdmin5">サンプルのみ本部からの送付を希望する</label>
        <br/>
          <input class="form-check-label pointer" id="leaveToAdmin5" type="checkbox" name="leave_to_admin" value="checked">
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="leaveToAdmin5">案件の対応を全て本部に依頼する</label>
        </td>
      </tr>
  @endif
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-end">
    <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact5-submit'>問い合わせを登録</button></p>
  </div>
</form>


<!-- 法人図面見積もり -->
<form id='company-drow' contact-type-id='6' action="{{ route('contact.post') }}" method="POST" enctype="multipart/form-data" class="contact-form h-adr">
  @csrf
  <input type='hidden' name='c[contact_type_id]' value="6"/>
  <input type="hidden" class="p-country-name" value="Japan">
  <table class="common-table-stripes-column">
    <tbody>
      <!-- しましまの整合性をとるためにdisplay:none -->
      <tr class='d-none'></tr>
      <tr>
        <th class="common-table-stripes-column__th">無料サンプル<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h2-freesample1" name="c[free_sample]" value="必要" checked>
            <label class="form-check-label pointer" for='h2-freesample1'>必要</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h2-freesample2" name="c[free_sample]" value="請求済み">
            <label class="form-check-label pointer" for='h2-freesample2'>請求済み</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h2-freesample3" name="c[free_sample]" value="不要">
            <label class="form-check-label pointer" for='h2-freesample3'>不要</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">会社名・フリガナ<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[company_name]" class="form-control common-table-stripes-column__input mr20 js-copy-company_name" dusk='h2-company_name' placeholder="サンプル株式会社" required>
          <input type="text" name="c[company_ruby]" class="form-control common-table-stripes-column__input js-copy-company_ruby" dusk='h2-company_ruby' placeholder="ユウゲンガイシャシバンチュ" required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">担当者名<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname]" class="js-copy-surname form-control common-table-stripes-column__input mr20" dusk='h2-surname' placeholder="担当者名" required>
          <input type="text" name="c[surname_ruby]" class="js-copy-surname_ruby form-control common-table-stripes-column__input" dusk='h2-surname-ruby' placeholder="担当者名フリガナ（任意）">
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">業種</th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[industry]" class="js-copy-industry form-control common-table-stripes-column__input" placeholder="小売・卸売" dusk='h2-industry'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[tel]" class="js-copy-tel form-control common-table-stripes-column__input mr20" placeholder="050-3561-2247" dusk='h2-tel' required>
          <input type="text" name="c[tel2]" class="js-copy-tel2 form-control common-table-stripes-column__input" placeholder="070-7777-7777" dusk='h2-tel2'>
    @if($errors->has('c.tel'))
          <div class='alert alert-danger'>正しく電話番号を入力してください</div>
    @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="js-copy-fax form-control common-table-stripes-column__input" placeholder="050-3561-2247"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メールアドレス</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="js-copy-email form-control common-table-stripes-column__input w60" placeholder="info@routeplus.co.jp" dusk='h2-email'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">郵便番号<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td"><input name="c[zipcode]" type="text" class="js-copy-zipcode form-control common-table-stripes-column__input p-postal-code" dusk='h2-zipcode' required placeholder="1000001"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">会社住所<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex justify-content-between">
          <input type="text" name="c[pref]"   class="js-copy-pref form-control p-region w15" placeholder="サンプル都" dusk='h2-pref' required>
          <input type="text" name="c[city]"   class="js-copy-city form-control p-locality p-street-address p-extended-address w20" placeholder="サンプル中央区" dusk='h2-city' required>
          <input type="text" name="c[street]" class="js-copy-street form-control w50" dusk='h2-street' placeholder="サンプルタウン1-1-1" required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お見積もり内容</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h2-quote_details1" name="c[quote_details]" value="施工希望">
            <label class="form-check-label pointer" for='h2-quote_details1'>施工希望</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h2-quote_details2" name="c[quote_details]" value="材料のみ">
            <label class="form-check-label pointer" for='h2-quote_details2'>材料のみ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h2-quote_details3" name="c[quote_details]" value="施工希望、材料のみ">
            <label class="form-check-label pointer" for='h2-quote_details3'>施工希望、材料のみ</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">下地状況</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-ground1" name="c[ground_condition][]" value="土">
            <label class="form-check-label pointer" for='h2-ground1'>土</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-ground2" name="c[ground_condition][]" value="雑草または天然芝">
            <label class="form-check-label pointer" for='h2-ground2'>雑草または天然芝</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-ground3" name="c[ground_condition][]" value="コンクリート">
            <label class="form-check-label pointer" for='h2-ground3'>コンクリート</label>
          </div>
          </div>
          <input type="text" name="c[ground_condition][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他の下地状況はこちらに入力" dusk='h2-ground_condition-etc'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">施工場所サイズ</th>
        <td class="common-table-stripes-column__td d-flex align-items-center">
          <span class='f12'>縦</span><input type="text" name="c[vertical_size]" class="form-control common-table-stripes-column__input w10" placeholder="24" min="0"  dusk='h2-vertical_size'><span class='mr20 f12'>m</span>
          <span class='f12 mr20'>×</span>
          <span class='f12'>横</span><input type="text" name="c[horizontal_size]" class="form-control common-table-stripes-column__input w10" placeholder="12" min="0" dusk='h2-horizontal_size'><span class='f12'>m</span>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">希望商品</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="h2-product0" name="c[desired_product][]" value="サンプル芝プレミアム40mm">
            <label class="form-check-label pointer" for='h2-product0'>サンプル芝プレミアム40mm</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="h2-product1" name="c[desired_product][]" value="サンプル芝30mm">
            <label class="form-check-label pointer" for='h2-product1'>サンプル芝30mm</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="h2-product2" name="c[desired_product][]" value="サンプル芝COOL">
            <label class="form-check-label pointer" for='h2-product2'>サンプル芝COOL</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="h2-product3" name="c[desired_product][]" value="SB30CP1">
            <label class="form-check-label pointer" for='h2-product3'>SB30CP1</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="h2-product4" name="c[desired_product][]" value="グレー">
            <label class="form-check-label pointer" for='h2-product4'>グレー</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="h2-product5" name="c[desired_product][]" value="ラテブラウン">
            <label class="form-check-label pointer" for='h2-product5'>ラテブラウン</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="h2-product6" name="c[desired_product][]" value="ゴルフ用8mm">
            <label class="form-check-label pointer pointer" for='h2-product6'>ゴルフ用8mm</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="h2-product7" name="c[desired_product][]" value="ゴルフ用12mm">
            <label class="form-check-label pointer pointer" for='h2-product7'>ゴルフ用12mm</label>
          </div>
          <div class="form-check form-check-inline pointer">
            <input class="form-check-input" type="checkbox" id="h2-product8" name="c[desired_product][]" value="サンプル芝O2">
            <label class="form-check-label pointer" for='h2-product8'>サンプル芝O2</label>
          </div>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">人工芝の使用用途</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-use1" name="c[use_application][]" value="雑草対策">
            <label class="form-check-label pointer" for='h2-use1'>雑草対策</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-use2" name="c[use_application][]" value="ペット・ドッグラン用">
            <label class="form-check-label pointer" for='h2-use2'>ペット・ドッグラン用</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-use3" name="c[use_application][]" value="スポーツ">
            <label class="form-check-label pointer" for='h2-use3'>スポーツ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-use4" name="c[use_application][]" value="ゴルフ">
            <label class="form-check-label pointer" for='h2-use4'>ゴルフ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-use5" name="c[use_application][]" value="室内">
            <label class="form-check-label pointer" for='h2-use5'>室内</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-use6" name="c[use_application][]" value="景観目的">
            <label class="form-check-label pointer" for='h2-use6'>景観目的</label>
          </div>
          <input type="text" name="c[use_application][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他使用用途" dusk='h2-use_application-etc'></td>
      </tr>
      <tr>
        <th>添付資料<span class='f09' style="color: red">既にファイルが登録されている箇所も変更できます。</span></th>
        <td class='d-flex flex-wrap justify-content-between'>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document1]" class='js-file js-image1'/>
            </div>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document2]" class='js-file js-image2'/>
            </div>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document3]" class='js-file js-image3'/>
            </div>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document4]" class='js-file js-image4'/>
            </div>
            <div class="uploader js-uploader">
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
                <input type="file" name="c[document5]" class='js-file js-image5'/>
            </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">備考・その他ご要望</th>
        <td class="common-table-stripes-column__td"><textarea name="c[comment]" class="js-copy-etc form-control common-table-stripes-column__input w100" placeholder="コメント" dusk='h2-comment'></textarea></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-where1" name="c[where_find][]" value="検索">
            <label class="form-check-label pointer" for='h2-where1'>検索</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-where2" name="c[where_find][]" value="ラジオ">
            <label class="form-check-label pointer" for='h2-where2'>ラジオ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-where3" name="c[where_find][]" value="チラシ">
            <label class="form-check-label pointer" for='h2-where3'>チラシ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-where4" name="c[where_find][]" value="紹介・口コミ">
            <label class="form-check-label pointer" for='h2-where4'>紹介・口コミ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-where5" name="c[where_find][]" value="Google広告">
            <label class="form-check-label pointer" for='h2-where5'>Google広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-where6" name="c[where_find][]" value="インスタ広告">
            <label class="form-check-label pointer" for='h2-where6'>インスタ広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-where7" name="c[where_find][]" value="facebook広告">
            <label class="form-check-label pointer" for='h2-where7'>facebook広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-where8" name="c[where_find][]" value="その他WEB広告">
            <label class="form-check-label pointer" for='h2-where8'>その他WEB広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h2-where9" name="c[where_find][]" value="通販サイト（楽天・アマゾンなど）">
            <label class="form-check-label pointer" for='h2-where9'>通販サイト（楽天・アマゾンなど）</label>
          </div>

          <input type="text" name="c[where_find][etc]" class="form-control common-table-stripes-column__input mt10 w60" dusk='h2-where_find-etc' placeholder="その他認知経路">
        </td>
      </tr>
  @if(isAdmin())
      <tr>
        <th class="common-table-stripes-column__th">
          メモ<br>
          <span class="ml10 common-table-stripes-column__span">本部にのみ表示されます</span>
        </th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="js-copy-memo form-control w100" rows="10" placeholder="メモ" dusk='h2-memo'></textarea>
        </td>
      </tr>
  @endif
  @if(isFc())
      <tr>
        <th class="common-table-stripes-column__th py-4">本部への対応依頼</th>
        <td class="common-table-stripes-column__td">
          <input class="form-check-label pointer" id="sampleAdmin6" type="checkbox" name="c[sample_send_at]" value="checked" {{checked(!empty($user['admin_sample_send']))}}>
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="sampleAdmin6">サンプルのみ本部からの送付を希望する</label>
        <br/>
          <input class="form-check-label pointer" id="leaveToAdmin6" type="checkbox" name="leave_to_admin" value="checked">
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="leaveToAdmin6">案件の対応を全て本部に依頼する</label>
        </td>
      </tr>
  @endif
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-end">
    <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact6-submit'>問い合わせを登録</button></p>
  </div>
</form>


<!-- 法人訪問見積もり -->
<form id='personal-visit' contact-type-id='7' action="{{ route('contact.post') }}" method="POST" enctype="multipart/form-data" class="contact-form">
  @csrf
  <input type='hidden' name='c[contact_type_id]' value="7"/>
  <table class="common-table-stripes-column">
    <tbody>
      <!-- しましまの整合性をとるためにdisplay:none -->
      <tr class='d-none'></tr>
      <tr>
        <th class="common-table-stripes-column__th">無料サンプル<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h3-freesample1" name="c[free_sample]" value="必要" checked>
            <label class="form-check-label pointer" for='h3-freesample1'>必要</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h3-freesample2" name="c[free_sample]" value="請求済み">
            <label class="form-check-label pointer" for='h3-freesample2'>請求済み</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h3-freesample3" name="c[free_sample]" value="不要">
            <label class="form-check-label pointer" for='h3-freesample3'>不要</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">会社名・フリガナ<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[company_name]" class="form-control common-table-stripes-column__input mr20 js-copy-company_name" dusk='h3-company_name' placeholder="サンプル株式会社" required>
          <input type="text" name="c[company_ruby]" class="form-control common-table-stripes-column__input js-copy-company_ruby" dusk='h3-company_ruby' placeholder="ユウゲンガイシャシバンチュ" required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">担当者名<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname]" class="js-copy-surname form-control common-table-stripes-column__input mr20" dusk='h3-surname' placeholder="担当者名" required>
          <input type="text" name="c[surname_ruby]" class="js-copy-surname_ruby form-control common-table-stripes-column__input" dusk='h3-surname-ruby' placeholder="担当者名フリガナ（任意）">
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">業種</th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[industry]" class="js-copy-industry form-control common-table-stripes-column__input" placeholder="小売・卸売" dusk='h3-industry'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[tel]" class="js-copy-tel form-control common-table-stripes-column__input mr20" placeholder="050-3561-2247" dusk='h3-tel' required>
          <input type="text" name="c[tel2]" class="js-copy-tel2 form-control common-table-stripes-column__input" placeholder="070-7777-7777" dusk='h3-tel2'>
    @if($errors->has('c.tel'))
          <div class='alert alert-danger'>正しく電話番号を入力してください</div>
    @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="js-copy-fax form-control common-table-stripes-column__input" placeholder="050-3561-2247" dusk='h3-fax'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メールアドレス</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="js-copy-email form-control common-table-stripes-column__input w60" placeholder="info@routeplus.co.jp" dusk='h3-email'></td>
      </tr>
      <tr class="h-adr">
        <th class="common-table-stripes-column__th">郵便番号<span class="ml10 common-table-stripes-column__span">必須</span><br>会社住所<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <input type="hidden" class="p-country-name" value="Japan">
          <input name="c[zipcode]" type="text" class="js-copy-zipcode form-control common-table-stripes-column__input p-postal-code mb5" value="{{ old('c.zipcode') }}" required placeholder="1000001" dusk='h3-zipcode'>
          <div class="d-flex justify-content-between">
            <input type="text" name="c[pref]" class="js-copy-pref form-control p-region w15" value="{{ old('c.pref') }}" placeholder="サンプル都" dusk='h3-pref' required>
            <input type="text" name="c[city]" class="js-copy-city form-control p-locality p-street-address p-extended-address w20" value="{{ old('c.city') }}" placeholder="サンプル中央区" dusk='h3-city' required>
            <input type="text" name="c[street]" class="js-copy-street form-control w50" value="{{ old('c.street') }}" placeholder="サンプルタウン1-1-1" dusk='h3-street' required>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">下地状況</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-ground1" name="c[ground_condition][]" value="土">
            <label class="form-check-label pointer" for='h3-ground1'>土</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-ground2" name="c[ground_condition][]" value="雑草または天然芝">
            <label class="form-check-label pointer" for='h3-ground2'>雑草または天然芝</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-ground3" name="c[ground_condition][]" value="コンクリート">
            <label class="form-check-label pointer" for='h3-ground3'>コンクリート</label>
          </div>
          </div>
          <input type="text" name="c[ground_condition][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他の下地状況はこちらに入力" dusk='h3-ground_condition-etc'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">第1訪問希望日時</th>
        <td class="common-table-stripes-column__td d-flex align-items-center">
          <span class='f11 mr5'>訪問日</span>
          <input data-provide="datepicker" class="form-control datepicker js-date w30per mr20" type="date" name='c[desired_datetime1][date]' value='訪問日時を選択' dusk='h3-desired_datetime1-date' autocomplete="off"/>
          <select class='form-control w30per' name='c[desired_datetime1][time]' dusk='h3-desired_datetime1-time'>
            <option value=''>訪問時間を選択</option>
    @for($i=8; $i<20; $i++)
            <option value={{$i}}>{{$i}}時ごろ</option>
    @endfor
          </select>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">第2訪問希望日時</th>
        <td class="common-table-stripes-column__td d-flex align-items-center">
          <span class='f11 mr5'>訪問日</span>
          <input data-provide="datepicker" class="form-control datepicker js-date w30per mr20" type="date" name='c[desired_datetime2][date]' dusk='h3-desired_datetime2-date' autocomplete="off"/>
          <select class='form-control w30per' name='c[desired_datetime2][time]' dusk='h3-desired_datetime2-time'>
            <option value=''>訪問時間を選択</option>
    @for($i=8; $i<20; $i++)
            <option value={{$i}}>{{$i}}時ごろ</option>
    @endfor
          </select>
        </td>
      </tr>
      <tr class='h-adr'>
        <th class="common-table-stripes-column__th">訪問先住所（顧客住所と異なる場合）</th>
        <td class="common-table-stripes-column__td">
          <input type="hidden" class="p-country-name" value="Japan">
          <input type="text" class="form-control common-table-stripes-column__input p-postal-code mb5" value="" placeholder="1000001" dusk='h3-zipcode'>
          <div class="common-table-stripes-column__td d-flex justify-content-between p0">
            <input type="text" name="c[visit_address]" class="form-control p-region p-locality p-street-address p-extended-address" placeholder="サンプル都練馬区桜台4丁目" dusk='h3-visit_address'>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">平米数</th>
        <td class="common-table-stripes-column__td d-flex align-items-center"><input type="number" name="c[square_meter]" class="form-control common-table-stripes-column__input w15" placeholder="44" dusk='h3-square_meter'><span class='f11'>平米</span></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お見積もり内容</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h3-quote_details1" name="c[quote_details]" value="施工希望">
            <label class="form-check-label pointer" for='h3-quote_details1'>施工希望</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h3-quote_details2" name="c[quote_details]" value="材料のみ">
            <label class="form-check-label pointer" for='h3-quote_details2'>材料のみ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h3-quote_details3" name="c[quote_details]" value="施工希望、材料のみ">
            <label class="form-check-label pointer" for='h3-quote_details3'>施工希望、材料のみ</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">人工芝の使用用途</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-use1" name="c[use_application][]" value="雑草対策">
            <label class="form-check-label pointer" for='h3-use1'>雑草対策</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-use2" name="c[use_application][]" value="ペット・ドッグラン用">
            <label class="form-check-label pointer" for='h3-use2'>ペット・ドッグラン用</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-use3" name="c[use_application][]" value="スポーツ">
            <label class="form-check-label pointer" for='h3-use3'>スポーツ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-use4" name="c[use_application][]" value="ゴルフ">
            <label class="form-check-label pointer" for='h3-use4'>ゴルフ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-use5" name="c[use_application][]" value="室内">
            <label class="form-check-label pointer" for='h3-use5'>室内</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-use6" name="c[use_application][]" value="景観目的">
            <label class="form-check-label pointer" for='h3-use6'>景観目的</label>
          </div>
          <input type="text" name="c[use_application][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他使用用途" dusk='h3-use_application-etc'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">備考・その他ご要望</th>
        <td class="common-table-stripes-column__td"><textarea name="c[comment]" class="js-copy-etc form-control common-table-stripes-column__input w100" placeholder="コメント" dusk='h3-comment'></textarea></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-where1" name="c[where_find][]" value="検索">
            <label class="form-check-label pointer" for='h3-where1'>検索</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-where2" name="c[where_find][]" value="ラジオ">
            <label class="form-check-label pointer" for='h3-where2'>ラジオ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-where3" name="c[where_find][]" value="チラシ">
            <label class="form-check-label pointer" for='h3-where3'>チラシ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-where4" name="c[where_find][]" value="紹介・口コミ">
            <label class="form-check-label pointer" for='h3-where4'>紹介・口コミ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-where5" name="c[where_find][]" value="Google広告">
            <label class="form-check-label pointer" for='h3-where5'>Google広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-where6" name="c[where_find][]" value="インスタ広告">
            <label class="form-check-label pointer" for='h3-where6'>インスタ広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-where7" name="c[where_find][]" value="facebook広告">
            <label class="form-check-label pointer" for='h3-where7'>facebook広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-where8" name="c[where_find][]" value="その他WEB広告">
            <label class="form-check-label pointer" for='h3-where8'>その他WEB広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h3-where9" name="c[where_find][]" value="通販サイト（楽天・アマゾンなど）">
            <label class="form-check-label pointer" for='h3-where9'>通販サイト（楽天・アマゾンなど）</label>
          </div>

          <input type="text" name="c[where_find][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他認知経路" dusk='h3-where_find-etc'>
        </td>
      </tr>
  @if(isAdmin())
      <tr>
        <th class="common-table-stripes-column__th">
          メモ<br>
          <span class="ml10 common-table-stripes-column__span">本部にのみ表示されます</span>
        </th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="js-copy-memo form-control w100" rows="10" placeholder="メモ" dusk='h3-memo'></textarea>
        </td>
      </tr>
  @endif
  @if(isFc())
      <tr>
        <th class="common-table-stripes-column__th py-4">本部への対応依頼</th>
        <td class="common-table-stripes-column__td">
          <input class="form-check-label pointer" id="sampleAdmin7" type="checkbox" name="c[sample_send_at]" value="checked" {{checked(!empty($user['admin_sample_send']))}}>
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="sampleAdmin7">サンプルのみ本部からの送付を希望する</label>
        <br/>
          <input class="form-check-label pointer" id="leaveToAdmin7" type="checkbox" name="leave_to_admin" value="checked">
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="leaveToAdmin7">案件の対応を全て本部に依頼する</label>
        </td>
      </tr>
  @endif
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-end">
    <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact7-submit'>問い合わせを登録</button></p>
  </div>
</form>

<!-- 法人その他 -->
<form id='personal-visit' contact-type-id='8' action="{{ route('contact.post') }}" method="POST" enctype="multipart/form-data" class="contact-form h-adr">
  @csrf
  <input type='hidden' name='c[contact_type_id]' value="8"/>
  <table class="common-table-stripes-column">
    <tbody>
      <!-- しましまの整合性をとるためにdisplay:none -->
      <tr class='d-none'></tr>
      <tr>
        <th class="common-table-stripes-column__th">無料サンプル<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h4-freesample1" name="c[free_sample]" value="必要" checked>
            <label class="form-check-label pointer" for='h4-freesample1'>必要</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h4-freesample2" name="c[free_sample]" value="請求済み">
            <label class="form-check-label pointer" for='h4-freesample2'>請求済み</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="h4-freesample3" name="c[free_sample]" value="不要">
            <label class="form-check-label pointer" for='h4-freesample3'>不要</label>
          </div>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">会社名・フリガナ<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[company_name]" class="form-control common-table-stripes-column__input mr20 js-copy-company_name" dusk='h4-company_name' placeholder="サンプル株式会社" required>
          <input type="text" name="c[company_ruby]" class="form-control common-table-stripes-column__input js-copy-company_ruby" dusk='h4-company_ruby' placeholder="ユウゲンガイシャシバンチュ" required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">担当者名<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[surname]" class="js-copy-surname form-control common-table-stripes-column__input mr20" dusk='h4-surname' placeholder="担当者名" required>
          <input type="text" name="c[surname_ruby]" class="js-copy-surname_ruby form-control common-table-stripes-column__input" dusk='h4-surname-ruby' placeholder="担当者名フリガナ（任意）">
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">業種</th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[industry]" class="js-copy-industry form-control common-table-stripes-column__input" placeholder="小売・卸売" dusk='h4-industry'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex">
          <input type="text" name="c[tel]" class="js-copy-tel form-control common-table-stripes-column__input mr20" placeholder="050-3561-2247" dusk='h4-tel' required>
          <input type="text" name="c[tel2]" class="js-copy-tel2 form-control common-table-stripes-column__input" placeholder="070-7777-7777" dusk='h4-tel2'>
    @if($errors->has('c.tel'))
          <div class='alert alert-danger'>正しく電話番号を入力してください</div>
    @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="js-copy-fax form-control common-table-stripes-column__input" placeholder="050-3561-2247" dusk='h4-fax'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メールアドレス</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="js-copy-email form-control common-table-stripes-column__input w60" placeholder="info@routeplus.co.jp" dusk='h4-email'></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">郵便番号<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td">
          <input type="hidden" class="p-country-name" value="Japan">
          <input name="c[zipcode]" type="text" class="js-copy-zipcode form-control common-table-stripes-column__input p-postal-code mb5" value="{{ old('c.zipcode') }}" required placeholder="1000001" dusk='h4-zipcode'>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">会社住所<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td d-flex justify-content-between">
          <input type="text" name="c[pref]" class="js-copy-pref form-control p-region w15" value="{{ old('c.pref') }}" placeholder="サンプル都" dusk='h4-pref' required>
          <input type="text" name="c[city]" class="js-copy-city form-control p-locality p-street-address p-extended-address w20" value="{{ old('c.city') }}" placeholder="サンプル中央区" dusk='h4-city' required>
          <input type="text" name="c[street]" class="js-copy-street form-control w50" value="{{ old('c.street') }}" placeholder="サンプルタウン1-1-1" dusk='h4-street' required>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">ご用件</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-quote1" name="c[quote_details][]" value="来場予約">
            <label class="form-check-label pointer" for='h4-quote1'>来場予約</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-quote2" name="c[quote_details][]" value="商品に関するご質問">
            <label class="form-check-label pointer" for='h4-quote2'>商品に関するご質問</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-quote3" name="c[quote_details][]" value="施工に関するご質問">
            <label class="form-check-label pointer" for='h4-quote3'>施工に関するご質問</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-quote4" name="c[quote_details][]" value="施工パートナー加盟に関するご質問">
            <label class="form-check-label pointer" for='h4-quote4'>施工パートナー加盟に関するご質問</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-quote5" name="c[quote_details][]" value="その他のご質問">
            <label class="form-check-label pointer" for='h4-quote5'>その他のご質問</label>
          </div>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">備考・その他ご要望</th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[comment]" class="js-copy-etc form-control common-table-stripes-column__input w100" placeholder="コメント" dusk='h4-comment'></textarea>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-where1" name="c[where_find][]" value="検索">
            <label class="form-check-label pointer" for='h4-where1'>検索</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-where2" name="c[where_find][]" value="ラジオ">
            <label class="form-check-label pointer" for='h4-where2'>ラジオ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-where3" name="c[where_find][]" value="チラシ">
            <label class="form-check-label pointer" for='h4-where3'>チラシ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-where4" name="c[where_find][]" value="紹介・口コミ">
            <label class="form-check-label pointer" for='h4-where4'>紹介・口コミ</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-where5" name="c[where_find][]" value="Google広告">
            <label class="form-check-label pointer" for='h4-where5'>Google広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-where6" name="c[where_find][]" value="インスタ広告">
            <label class="form-check-label pointer" for='h4-where6'>インスタ広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-where7" name="c[where_find][]" value="facebook広告">
            <label class="form-check-label pointer" for='h4-where7'>facebook広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-where8" name="c[where_find][]" value="その他WEB広告">
            <label class="form-check-label pointer" for='h4-where8'>その他WEB広告</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="h4-where9" name="c[where_find][]" value="通販サイト（楽天・アマゾンなど）">
            <label class="form-check-label pointer" for='h4-where9'>通販サイト（楽天・アマゾンなど）</label>
          </div>

          <input type="text" name="c[where_find][etc]" class="form-control common-table-stripes-column__input mt10 w60" placeholder="その他認知経路" dusk='h4-where_find-etc'>
        </td>
      </tr>
  @if(isAdmin())
      <tr>
        <th class="common-table-stripes-column__th">
          メモ<br>
          <span class="ml10 common-table-stripes-column__span">本部にのみ表示されます</span>
        </th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="js-copy-memo form-control w100" rows="10" placeholder="メモ" dusk='h4-memo'></textarea>
        </td>
      </tr>
  @endif
  @if(isFc())
      <tr>
        <th class="common-table-stripes-column__th py-4">本部への対応依頼</th>
        <td class="common-table-stripes-column__td">
          <input class="form-check-label pointer" id="leaveToAdmin8" type="checkbox" name="leave_to_admin" value="checked">
          <label class="form-check-label pointer form-check-label pointer-sample pl-1" for="leaveToAdmin8">案件の対応を全て本部に依頼する</label>
        </td>
      </tr>
  @endif
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-end">
    <p><button type="submit" class="px-xl-5 btn btn-primary" dusk='contact8-submit'>問い合わせを登録</button></p>
  </div>
</form>
