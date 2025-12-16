@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal-default-theme.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/contact/assigned-list.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/remodal/remodal.min.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/contact/assigned-list.js') }}" defer></script>
@endsection

@section('content')

<div class="breadcrumbs">
  {!! $breadcrumbs->render() !!}
</div>

<table class="common-table-stripes-row">
  <thead class="common-table-stripes-row-thead">
    <tr>
    @if(isAdmin())
      <th scope="col">担当FC</th>
    @endif
      <th scope="col">案件No</th>
      <th scope="col">依頼日</th>
      <th scope="col">依頼種別</th>
      <th scope="col">名前</th>
      <th scope="col">住所</th>
      <th scope="col">編集</th>
    @if(isFc())
      <th scope="col">アポ日時</th>
      <th scope="col">見積もり</th>
    @endif
    </tr>
  </thead>
  <tbody>
@foreach($contacts as $c)
  @if(alertInView($c->updated_at, !empty($c->alert_days) ? $c->alert_days : 9999))
    <tr class='alert-tr'>
      <input type='hidden' class='js-contact-id' value="{{ $c->id }}"/>
      <td class='text-nowrap'><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk="contact-detail">{{ displayContactId($c) }}</a><span class='alert-tr__label'>至急対応！</span></td>
  @else
    <tr>
      <input type='hidden' class='js-contact-id' value="{{ $c->id }}"/>
    @if(isAdmin() && !empty($c->fcid))
      <td class="common-table-stripes-row-tbody__td"><a href="{{  route('users.show', ['id' => $c->fcid]) }}" target='blank'>{{ $c->fc_name }}</a></td>
    @endif
      <td class=''><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk="contact-detail">{{ displayContactId($c) }}</a></td>
  @endif
      <td class="common-table-stripes-row-tbody__td f08">{{date('Y年m月d日', strtotime(  !empty($c->fc_assigned_at) ? $c->fc_assigned_at : $c->created_at))}}</td>
      <td class="common-table-stripes-row-tbody__td">{{ returnContactType($c->contact_type_id) }}</td>
      <td class="common-table-stripes-row-tbody__td js-name">{{ customerName($c) }}</td>
      <td class="common-table-stripes-row-tbody__td">{{$c->pref}}{{$c->city}}{{$c->street}}</td>
      <td class="common-table-stripes-row-tbody__td">
        <a type="button" href="{{ route('assigned.edit', ['id' => $c->id ]) }}" class="edit-buttn btn btn-warning" dusk="contact-edit">編集</a>
      </td>
  @if(isFc())
      <td class="common-table-stripes-row-tbody__td">
        <button type="button" class="btn btn-warning modal-open-btn" dusk="modal-open-{{$c->id}}" data-toggle="modal" data-target="#appointment-modal">アポ日時を入力</button>
      </td>
      <td class="common-table-stripes-row-tbody__td">
        <a href="{{ route('quotations.create', ['id' => $c->id]) }}" {{ isHidden(isMaterialOnly($c->id)) }}><button type="button" dusk="create-button" class="btn btn-primary px-3">作成</button></a>
      </td>
  @endif
    </tr>
@endforeach
  </tbody>
</table>

<div class="d-flex justify-content-center">
  {{ $contacts->appends(request()->query())->links() }}
</div>

<!-- Modal -->
<div class="modal fade" id='appointment-modal' tabindex="-1" role="dialog" aria-labelledby="appointmentModal" aria-hiddn="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h2 class='h2 mb30'><p id='name'></p>案件アポ日時入力</h2>
        @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
        @endif
        </div>

        <div class="modal-body">
            <form action="{{ route('set.appointment') }}" class='form-group' id="js-form" method="POST" data-enctype="multipart/form-data">
              @csrf
              <input type='hidden' id='contact-id' name='id' value='' name="contact_id">
              <div class='row mb10'>
                <div class="col-md-6">
                  <input data-provide="datepicker" class="form-control datepicker js-start-date" name='date' type="datetime" value="{{ date('Y-m-d')}}">
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <select class='form-control' name='hours'>
            @for($i=7;$i<23;$i++)
                      <option value="{{ $i }}">{{$i }}時</option>
            @endfor
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <select class='form-control' name='minutes'>
                      <option value="0">0分</option>
            @for($i=0;$i<12;$i++)
                      <option value="{{ $i * 5 }}">{{$i * 5 }}分</option>
            @endfor
                    </select>
                  </div>
                </div>
              </div> <!-- row -->
              <button type="submit" class="btn btn-warning" dusk="btn-appointment">アポ日時確定</button>
              <button type="submit" class='btn btn-success js-skip-btn' dusk="btn-skipOnsiteConfirmation" data-action="{{ route('skip.onsite-confirmation') }} ">現場報告をスキップ</button>

            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
          </div>
    </div>
  </div>
</div><!-- Modal -->
@endsection
