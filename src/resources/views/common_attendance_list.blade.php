@extends('layouts.base')
@section('title', '勤怠一覧')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="attendance-list" class="cmn-page">
    <div class="l-container-60">
      @if (auth('admin')->check())
        <h1 class="c-title">{{ $user->family_name }}{{ $user->given_name }}さんの勤怠</h1>
      @else
        <h1 class="c-title">勤怠一覧</h1>
      @endif
      <div class="month">
        @if (auth('admin')->check())
          <a class="month-prev" href="{{ route('admin-attendance.show-list', ['year' => $prevDate->format('Y'), 'month' => $prevDate->format('m'), 'id' =>  $user->id])}}">前月</a>
        @else
          <a class="month-prev" href="{{ route('attendance.show-list', ['year' => $prevDate->format('Y'), 'month' => $prevDate->format('m')])}}">前月</a>
        @endif
        <p class="month-current">{{ $date->isoFormat('YYYY年MM月') }}</p>
        @if ($date->format('Y-m') < now()->format('Y-m'))
          @if (auth('admin')->check())
            <a class="month-next" href="{{ route('admin-attendance.show-list', ['year' => $nextDate->format('Y'), 'month' => $nextDate->format('m'), 'id' => $user->id])}}">翌月</a>
          @else
            <a class="month-next" href="{{ route('attendance.show-list', ['year' => $nextDate->format('Y'), 'month' => $nextDate->format('m')])}}">翌月</a>
          @endif
        @else
          <p class="month-next month-opacity-0">翌月</p>
        @endif
      </div>
      @if ($attendances->count() === 0)
        <p class="no-data-message">指定された月の勤怠データはありません</p>
      @else
        <table class="c-table c-table--attendance-list table">
          <tr class="header">
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
          </tr>
          @for ($i = 0; $i < $days; $i++)
          <tr class="data">
            {{-- $dateの値はfor文内で1日ずつ加算される --}}
            <td>{{ $i === 0 ? $date->isoFormat('MM月DD日(ddd)') : $date->addDays(1)->isoFormat('MM月DD日(ddd)') }}</td>
            @if (isset($attendances[$date->format('Y-m-d')]))
              <td>{{ $attendances[$date->format('Y-m-d')]->start_time === null ? '--:--' : $attendances[$date->format('Y-m-d')]->start_time }}</td>
              <td>{{ $attendances[$date->format('Y-m-d')]->end_time === null ? '--:--' : $attendances[$date->format('Y-m-d')]->end_time }}</td>
              <td>{{ $attendances[$date->format('Y-m-d')]->total_break_time }}</td>
              <td>{{ $attendances[$date->format('Y-m-d')]->total_work_time === null ? '--:--' : $attendances[$date->format('Y-m-d')]->total_work_time }}</td>
              @if (auth('admin')->check())
                <td><a href="{{ route('admin-attendance.show', $attendances[$date->format('Y-m-d')]->id) }}">詳細</a></td>
              @else
                <td><a href="{{ route('attendance.show', $attendances[$date->format('Y-m-d')]->id) }}">詳細</a></td>
              @endif
            @else
              <td>--:--</td>
              <td>--:--</td>
              <td>--:--</td>
              <td>--:--</td>
              @if (auth('admin')->check())
                <td><a href="{{ route('admin-attendance.create', ['year' => $date->format('Y'), 'month' => $date->format('m'), 'day' => $date->format('d'), 'id' => $user->id ]) }}">作成</a></td>
              @else
                <td>--</td>
              @endif
            @endif
          </tr>
          @endfor
        </table>
        @if (auth('admin')->check())
          <form action="{{ route('admin-attendance.export-csv', ['year' => $date->format('Y'), 'month' => $date->format('m'), 'id' => $user->id]) }}" method="get">
            @csrf
            <button class="button c-btn c-btn--black c-btn--attendance-list" type="submit">CSV出力</button>
          </form>
        @else
          <div class="margin"></div>
        @endif
      @endif
    </div>
  </div>
@endsection