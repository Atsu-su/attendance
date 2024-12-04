@extends('layouts.base')
@section('title', '勤怠詳細')
@section('header')
  @include('components.user.header')
@endsection
@section('content')
  <div id="attendance-detail" class="cmn-page">
    <div class="l-container-60">
      <h1 class="c-title">勤怠詳細<span>（未申請 or 承認待ち）</span></h1>
      <form action="">
        <table class="c-table-detail table">
          <tr>
            <th>名前</th>
            <td class="name">にし&emsp;れいな</td>
          </tr>
          <tr>
            <th>日付</th>
            <td><input class="input-date" type="date"></td>
          </tr>
          <tr>
            <th>出勤・退勤</th>
            <td><input class="input-time" type="text" value="09:00"><span class="wave">～</span><input class="input-time" type="text" value="18:00"></td>
          </tr>
          <tr>
            <th>休憩</th>
            <td><input class="input-time" type="text" value="12:00"><span class="wave">～</span><input class="input-time" type="text" value="13:00"></td>
          </tr>
          <tr>
            <th>備考</th>
            <td><textarea class="remarks" rows="4"></textarea></td>
          </tr>
        </table>
        {{-- 申請中の場合ボタンは表示されない --}}
        <button class="button c-btn c-btn--black c-btn--attendance-correction">修正</button>
        {{-- <p>*承認待ちのため修正はできません。</p> --}}
      </form>
    </div>
  </div>
@endsection