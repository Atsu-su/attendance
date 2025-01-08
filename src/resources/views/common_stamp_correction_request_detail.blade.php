@extends('layouts.base')
@section('title', '勤怠詳細')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="attendance-detail" class="cmn-page">
    <div class="l-container-60">
      @if ($request->is_approved)
        <h1 class="c-title">勤怠詳細（承認済）</h1>
      @else
        <h1 class="c-title">勤怠詳細（承認待ち）</h1>
      @endif
      <table class="c-table-detail request-content-table">
        <tr>
          <th>名前</th>
          <td class="name">{{ $request->user->family_name }}&ensp;{{ $request->user->given_name }}</td>
        </tr>
        <tr>
          <th>日付</th>
          <td class="date">{{ $request->attendance->date }}</td>
        </tr>
        <tr>
          <th>出勤・退勤</th>
          <td class="time">{{ $request->start_time }}<span class="wave">～</span>{{ $request->end_time }}</td>
        </tr>
        @foreach($requestBreakTimes as $requestBreakTime)
          <tr>
            <th>休憩{{ $loop->iteration == 1 ? '' : $loop->iteration }}</th>
            <td class="time">{{ $requestBreakTime->start_time }}<span class="wave">～</span>{{ $requestBreakTime->end_time }}</td>
          </tr>
        @endforeach
        <tr>
          <th>備考</th>
          <td><pre class="c-pre remarks-pre">{{ $request->remarks }}</pre></td>
        </tr>
      </table>
      @if (auth('admin')->check() && !$request->is_approved)
        <form action="{{ route('admin-attendance.update', ['stamp_correction_request' => $request->id]) }}" method="post">
          @csrf
          <button class="button c-btn c-btn--black c-btn--attendance-correction" type="submit">承認</button>
        </form>
      @endif
    </div>
  </div>
@endsection