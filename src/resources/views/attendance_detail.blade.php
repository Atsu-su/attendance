@extends('layouts.base')
@section('title', '勤怠詳細')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="attendance-detail" class="cmn-page">
    <div class="l-container-60">
      <h1 class="c-title">勤怠詳細<span>（未申請 or 承認待ち）</span></h1>
      @if ($isApplicable == true)
        <form action="/detail" method="POST">
          @csrf
          <table class="c-table-detail edit-table">
            <tr>
              <th>名前</th>
              <td class="name">{{ $attendance->user->name }}</td>
            </tr>
            <tr>
              <th>日付</th>
              {{-- <td><input class="input-date" type="date" name="date" value="{{ $attendance->date }}"></td> --}}
              <td class="date">{{ $attendance->date }}</td>
            </tr>
            <tr>
              <th>出勤・退勤</th>
              <td><input class="input-time" type="text" name="start_time" value="{{ $attendance->start_time }}"><span class="wave">～</span><input class="input-time" type="text" name="end_time" value="{{ $attendance->end_time }}"></td>
            </tr>
            <tr>
              <th>休憩</th>
              <td><input class="input-time" type="text" name="break_start_time" value="{{ $attendance->break_start_time }}"><span class="wave">～</span><input class="input-time" type="text" name="break_end_time" value="{{ $attendance->break_end_time }}"></td>
            </tr>
            <tr>
              <th>備考</th>
              <td><textarea class="remarks" name="remarks"></textarea></td>
            </tr>
          </table>
          <button class="button c-btn c-btn--black c-btn--attendance-correction" type="submit">修正</button>
          {{-- 確認用モーダル --}}
          {{-- @include('components.modal_confirm') --}}
        </form>
      @else
        <table class="c-table-detail request-content-table">
          <tr>
            <th>名前</th>
            <td class="name">{{ $request->user->name }}</td>
          </tr>
          <tr>
            <th>日付</th>
            <td class="date">{{ $request->date }}</td>
          </tr>
          <tr>
            <th>出勤・退勤</th>
            <td class="time">{{ $request->start_time }}<span class="wave">～</span>{{ $request->end_time }}</td>
          </tr>
          <tr>
            <th>休憩</th>
            <td class="time">{{ $request->break_start_time }}<span class="wave">～</span>{{ $request->break_end_time }}</td>
          </tr>
          <tr>
            <th>備考</th>
            <td>{{ $request->remarks }}</td>
          </tr>
        </table>
        <p class=request-content-message>*承認待ちのため修正はできません。</p>
      @endif
    </div>
  </div>
@endsection