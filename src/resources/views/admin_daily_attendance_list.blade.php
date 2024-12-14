@extends('layouts.base')
@section('title', '【管理者】勤怠一覧')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="admin-daily-attendance-list" class="cmn-page">
    <div class="l-container-60">
      <h1 class="c-title">{{ $date->isoFormat('YYYY年MM月DD日') }}の勤怠</h1>
      <div class="day">
        <a class="day-prev" href="{{ route('admin-attendance.show-daily-list', ['year' => $prevDate->format('Y'), 'month' => $prevDate->format('m'), 'day' =>  $prevDate->format('d')]) }}">前日</a>
        <p class="day-current">{{ $date->isoFormat('YYYY年MM月DD日') }}</p>
        @if ($date->format('Y-m-d') < now()->format('Y-m-d'))
          <a class="day-next" href="{{ route('admin-attendance.show-daily-list', ['year' => $nextDate->format('Y'), 'month' => $nextDate->format('m'), 'day' => $nextDate->format('d')]) }}">翌日</a>
        @else
          {{-- 表示させていないが同じ幅を確保するため「翌日」を残す --}}
          <p class="day-next day-opacity-0">翌日</p>
        @endif
      </div>
      @if ($isNoData)
        <p class="no-data-message">指定された日付けの勤怠データはありません</p>
      @else
        <table class="c-table c-table--attendance-list table">
          <tr class="header">
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
          </tr>
          @foreach ($users as $user)
          <tr class="data">
            <td>{{ $user->family_name }}&ensp;{{ $user->given_name }}</td>
            <td>{{ $user->attendances[0]->start_time === null ? '--:--' : $user->attendances[0]->start_time }}</td>
            <td>{{ $user->attendances[0]->end_time === null ? '--:--' : $user->attendances[0]->end_time }}</td>
            <td>{{ $user->attendances[0]->total_break_time === null ? '--:--' : $user->attendances[0]->total_break_time }}</td>
            <td>{{ $user->attendances[0]->total_work_time === null ? '--:--' : $user->attendances[0]->total_work_time }}</td>
            @if ($user->attendances[0]->id !== null)
              <td><a href="{{ route('admin-attendance.show', $user->attendances[0]->id) }}">詳細</a></td>
            @else
                <td><a href="{{ route('admin-attendance.create', ['year' => $date->format('Y'), 'month' => $date->format('m'), 'day' => $date->format('d'), 'id' => $user->id ]) }}">作成</a></td>
            @endif
          </tr>
          @endforeach
        </table>
      @endif
    </div>
  </div>
@endsection