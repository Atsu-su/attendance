@extends('layouts.base')
@section('title', '【管理者】勤怠一覧')
@section('header')
  @include('components.admin.header')
@endsection
@section('content')
  <div id="attendance-list" class="cmn-page">
    <div class="l-container-60">
      <h1 class="c-title">2024年12月2日の勤怠</h1>
      <div class="month">
        <p class="month-prev">前日</p>
        <p class="month-current">2023年12月2日</p>
        <p class="month-next">翌日</p>
      </div>
      <table class="c-table c-table--attendance-list table">
        <tr class="header">
          <th>名前</th>
          <th>出勤</th>
          <th>退勤</th>
          <th>休憩</th>
          <th>合計</th>
          <th>詳細</th>
        </tr>
        <tr class="data">
          <td>とまと</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>パラシュート部隊</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>線路</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>架線</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>サマンサ</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>とどろく土星</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>ろくろ首</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>長崎のかぜ</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>流行した病の亜種</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>加熱した金属の破片</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
      </table>
    </div>
  </div>
@endsection