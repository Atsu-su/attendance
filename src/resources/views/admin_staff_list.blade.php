@extends('layouts.base')
@section('title', '【管理者】スタッフ一覧')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="staff-list" class="cmn-page">
    <div class="container">
      <h1 class="c-title">スタッフ一覧</h1>
      <table class="c-table c-table--staff-list table">
        <tr class="header">
          <th>名前</th>
          <th>メールアドレス</th>
          <th>月次勤怠</th>
        </tr>
        @foreach ($users as $user)
        <tr class="data">
          <td>{{ $user->family_name}}&ensp;{{ $user->given_name }}</td>
          <td>{{ $user->email }}</td>
          <td>詳細</td>
        </tr>
        @endforeach
      </table>
    </div>
  </div>
@endsection