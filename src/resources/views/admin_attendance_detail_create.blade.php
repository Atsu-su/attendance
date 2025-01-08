@extends('layouts.base')
@section('title', '勤怠詳細')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="attendance-detail" class="cmn-page">
    <div class="l-container-60">
      <h1 class="c-title">新規勤怠作成</h1>
      <form action="{{ route('admin-attendance.store') }}" method="post">
        @csrf
        <table class="c-table-detail edit-table">
          <tr>
            <th>名前</th>
            <td class="name">
              {{ $user->family_name }}&ensp;{{ $user->given_name}}
              <input type="hidden" name="user_id" value="{{ $user->id }}">
            </td>
          </tr>
          <tr>
            <th>日付</th>
            <td class="date">
              {{ $date->isoFormat('MM月DD日(ddd)') }}
              <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
            </td>
          </tr>
          <tr>
            <th>出勤・退勤</th>
            <td>
              <input class="input-time" type="text" name="start_time" value="{{ old('start_time') }}" placeholder="09:00"><span class="wave">～</span><input class="input-time" type="text" name="end_time" value="{{ old('end_time') }}" placeholder="18:00">
              @if ($errors->has('start_time') || $errors->has('end_time'))
                <p class="c-table-error-message">{{ ($errors->first('start_time') ?: null) ?? $errors->first('end_time') }}</p>
              @endif
            </td>
          </tr>
          <tr>
            <th>休憩</th>
            <td><input class="input-time" type="text" name="break_start_time[]" value="{{ old('break_start_time.0') }}" placeholder="12:00"><span class="wave">～</span><input class="input-time" type="text" name="break_end_time[]" value="{{ old('break_end_time.0') }}" placeholder="13:00">
            @if ($errors->has('break_start_time.*') || $errors->has('break_end_time.*'))
              <p class="c-table-error-message">{{ ($errors->first('break_start_time.*') ?: null) ?? $errors->first('break_end_time.*') }}</p>
            @endif
            </td>
          </tr>
        </table>
        <button class="button c-btn c-btn--black c-btn--attendance-correction" type="submit">新規作成</button>
      </form>
    </div>
  </div>
@endsection