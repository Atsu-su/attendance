@extends('layouts.base')
@section('title', '勤怠一覧')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="attendance-list" class="cmn-page">
    <div class="l-container-60">
      <h1 class="c-title">勤怠一覧</h1>
      <div class="month">
        <p class="month-prev">前月</p>
        <p class="month-current">2023年11月</p>
        <p class="month-next">翌月</p>
      </div>
      <table class="c-table c-table--attendance-list table">
        <tr class="header">
          <th>日付</th>
          <th>出勤</th>
          <th>退勤</th>
          <th>休憩</th>
          <th>合計</th>
          <th>詳細</th>
        </tr>
        <tr class="data">
          <td>06/01(金)</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>06/04(月)</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>06/05(火)</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>06/05(火)</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>06/05(火)</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>06/05(火)</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>06/05(火)</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>06/05(火)</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>06/05(火)</td>
          <td>08:00</td>
          <td>20:00</td>
          <td>01:00</td>
          <td>09:00</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>06/05(火)</td>
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