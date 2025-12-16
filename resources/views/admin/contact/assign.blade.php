@extends('layouts.layout') @section('css')
<link href="{{ asset('styles/contact/assign.min.css') }}" rel="stylesheet" />
@endsection @section('javascript')
<script type="text/javascript">
    window.distance = {{ $distance }};
    window.franchises = @json($franchises);
    window.targetFc = @json($recommendFc);
    window.case = @json($case);
</script>
@if(config('app.env') == 'production' || config('app.env') == 'testing'))
<script src="https://maps.google.com/maps/api/js?key={{ config('app.google_api_key') }}&language=ja" defer></script>
@else
<script src="http://maps.google.com/maps/api/js?key={{ config('app.google_api_key') }}&language=ja" defer></script>
@endif
  <script src="{{ asset('js/contact/assign.js') }}" defer></script>
  <script src="{{ asset('plugins/map/labelcss.js') }}" defer></script>
</script>
@endsection @section('content')
<section class="d-flex alins-item-center justify-content-between mb20">
    {!! $breadcrumbs->render() !!}
    <div class="d-flex alins-item-center">
        <a href="{{ route('contact.assign', ['id' => $case->id, 'distance' => 100]) }}" class="btn btn-info all-center mr10 {{ isActiveBtn($distance == '100') }}">100km</a>
        <a href="{{ route('contact.assign', ['id' => $case->id, 'distance' => 200]) }}" class="btn btn-info all-center mr10 {{ isActiveBtn($distance == '200') }}">200km</a>
        <a href="{{ route('contact.assign', ['id' => $case->id, 'distance' => 300]) }}" class="btn btn-info all-center mr10 {{ isActiveBtn($distance == '300') }}">300km</a>
        <a href="{{ route('contact.assign', ['id' => $case->id, 'distance' => 3500]) }}" class="btn btn-info all-center mr10 {{ isActiveBtn($distance == '3500') }}">全国</a>
    </div>
</section>
<div class="flex-center position-ref full-height">
    <div class="content">
        <form method="POST" action="{{ route('contact.assign.commit') }}" id="assign-form">
            @csrf
            <input type="hidden" name="id" value="{{$case->id}}" />
            <input type="hidden" name="fcid" value="{{ !empty($recommendFc['id']) ? $recommendFc['id'] : '' }}" />
            <div id="map_wrapper_div" class="mb15">
                <div id="map_tuts"></div>
                <table id="js-selected-fc" class="recommend-table assign-table mb30 ml10 br5">
                    <tr id="{{ !empty($recommendFc['id']) ? $recommendFc['id'] : '' }}">
                        <td class="js-target-fcid" hidden></td>
                        <td class="js-target-name assign-table__name">
                            1 . {{ !empty($recommendFc["company_name"]) ? $recommendFc["company_name"] : '' }}
                        </td>
                        <td class="js-target-area assign-table__area">
                             担当エリア：{{ !empty($recommendFc["area_name"]) ? $recommendFc["area_name"] : '' }}
                        </td>
                        <td class="js-target-address assign-table__address">
                            {{ !empty($recommendFc['id']) ? $recommendFc["pref"].$recommendFc["city"].$recommendFc["street"] : '' }}
                        </td>
                        <td class="js-target-distance assign-table__distance">
                            距離:{{  !empty($recommendFc["disatnce"]) ? round($recommendFc["distance"], 1) : 0 }}km
                        </td>
                        <td class="js-target-year assign-table__year">
                            年間施工件数:{{ !empty($recommendFc["year_count"]) ? $recommendFc["year_count"] : 0 }}件
                        </td>
                        <td class="js-target-progress assign-table__progress">
                            現在施工件数:{{ !empty($recommendFc["progress_count"]) ? $recommendFc["progress_count"] : 0 }}件
                        </td>
                    </tr>
                </table>
            </div>
            <div class="table-wrapper mb20 br5">
                <table class="franchises-table assign-table">
                  @foreach($franchises AS $key => $fc)
                    <tr id="{{ $fc['id'] }}" class="js-fc-row franchises-table__row" dusk="fc{{ $fc['id'] }}">
                        <td class="js-fcid" hidden>{{ $fc["id"] }}</td>
                        <td class="js-name assign-table__name pl15">
                            {{ $key + 1 }} . {{ $fc["company_name"] }}
                        </td>
                        <td class="js-area assign-table__area">
                            担当エリア：{{ !empty($fc["area_name"]) ? $fc["area_name"] : '' }}
                        </td>
                        <td class="js-address assign-table__address">
                            {{ $fc["pref"].$fc["city"].$fc["street"] }}
                        </td>
                        <td class="js-distance assign-table__distance">
                            距離:{{ round($fc["distance"], 1) }}km
                        </td>
                        <td class="js-year assign-table__year">
                            年間施工件数:{{ $fc["year_count"] }}件
                        </td>
                        <td class="js-progress assign-table__progress">
                            現在施工件数:{{ $fc["progress_count"] }}件
                        </td>
                    </tr>
                  @endforeach
                </table>
            </div>
            <button id="submit-btn" class="btn btn-primary btn-submit mb10" type="button" onclick="assign()">確定する</button>
        </form>
    </div>
</div>
@endsection
