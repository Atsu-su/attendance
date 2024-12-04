@extends('layouts.base')
@section('title', '【管理者】スタッフ一覧')
@section('header')
  @include('components.admin.header')
@endsection
@section('content')
  <div id="attendance-list" class="cmn-page">
    <div class="l-container-60">
      <h1 class="c-title">スタッフ一覧</h1>
      <table class="c-table c-table--staff-list table">
        <tr class="header">
          <th>名前</th>
          <th>メールアドレス</th>
          <th>月次勤怠</th>
        </tr>
        <tr class="data">
          <td>とまと</td>
          <td>tomato@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>パラシュート部隊</td>
          <td>parachute@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>線路</td>
          <td>railroad@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>架線</td>
          <td>cable@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>サマンサ</td>
          <td>samantha@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>とどろく土星</td>
          <td>saturn@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>ろくろ首</td>
          <td>longneck@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>長崎のかぜ</td>
          <td>windblowinnagasaki@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>流行した病の亜種</td>
          <td>anothercorona@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
        <tr class="data">
          <td>加熱した金属の破片</td>
          <td>heatedmetal@wavelucky.co.jp.us</td>
          <td>詳細</td>
        </tr>
      </table>
    </div>
  </div>
@endsection