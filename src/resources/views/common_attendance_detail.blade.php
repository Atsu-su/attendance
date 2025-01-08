@extends('layouts.base')
@section('title', '勤怠詳細')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="attendance-detail" class="cmn-page">
    <div class="l-container-60">
      @if ($isApplicableForDate)
        @if ($isApplicable)
        <h1 class="c-title">勤怠詳細<span>（申請）</span></h1>
          @if (auth('admin')->check())
            <form action="{{ route('admin-stamp-correction-request.store', $attendance->id) }}" method="post">
          @else
            <form action="{{ route('stamp-correction-request.store', $attendance->id) }}" method="post">
          @endif
            @csrf
            <table class="c-table-detail edit-table">
              <tr>
                <th>名前</th>
                <td class="name">{{ $attendance->user->family_name }}&ensp;{{ $attendance->user->given_name}}</td>
              </tr>
              <tr>
                <th>日付</th>
                <td class="date">{{ $attendance->date }}</td>
              </tr>
              <tr>
                <th>出勤・退勤</th>
                <td>
                  <input class="input-time" type="text" name="start_time" value="{{ old('start_time', $attendance->start_time) }}" placeholder="09:00"><span class="wave">～</span><input class="input-time" type="text" name="end_time" value="{{ old('end_time', $attendance->end_time) }}" placeholder="18:00">
                  @if ($errors->has('start_time') || $errors->has('end_time'))
                    <p class="c-table-error-message">{{ ($errors->first('start_time') ?: null) ?? $errors->first('end_time') }}</p>
                  @endif
                </td>
              </tr>
              @if ($breakTimes->isEmpty())
                <tr>
                  <th>休憩</th>
                  <td><input class="input-time" type="text" name="break_start_time[]" value="{{ old('break_start_time.0') }}" placeholder="12:00"><span class="wave">～</span><input class="input-time" type="text" name="break_end_time[]" value="{{ old('break_end_time.0') }}" placeholder="13:00">
                  @if ($errors->has('break_start_time.*') || $errors->has('break_end_time.*'))
                    <p class="c-table-error-message">{{ ($errors->first('break_start_time.*') ?: null) ?? $errors->first('break_end_time.*') }}</p>
                  @endif
                  </td>
                </tr>
              @else
                @foreach($breakTimes as $breakTime)
                <tr>
                  <th>休憩{{$loop->iteration == 1 ? '' : $loop->iteration}}</th>
                  <td><input class="input-time" type="text" name="break_start_time[]" value="{{ old('break_start_time.'.$loop->index, $breakTime->start_time) }}" placeholder="12:00"><span class="wave">～</span><input class="input-time" type="text" name="break_end_time[]" value="{{ old('break_end_time.'.$loop->index, $breakTime->end_time) }}" placeholder="13:00">
                    @if ($errors->has('break_start_time.'.$loop->index) || $errors->has('break_end_time.'.$loop->index))
                      <p class="c-table-error-message">{{ ($errors->first('break_start_time.'.$loop->index) ?: null) ?? $errors->first('break_end_time.'.$loop->index) }}</p>
                    @endif
                  </td>
                </tr>
                @endforeach
              @endif
              <tr>
                <th>備考</th>
                <td>
                  <textarea class="remarks-textarea" name="remarks" placeholder="申請理由を記載して下さい">{{ old('remarks') }}</textarea>
                  @if ($errors->has('remarks'))
                    <p class="c-table-error-message">{{ $errors->first('remarks') }}</p>
                  @endif
                </td>
              </tr>
            </table>
            <button class="button c-btn c-btn--black c-btn--attendance-correction" type="submit">修正</button>
            {{-- 確認用モーダル --}}
            {{-- @include('components.modal_confirm') --}}
          </form>
        @else
          <h1 class="c-title">勤怠詳細<span>（承認待ち）</span></h1>
          <table class="c-table-detail request-content-table">
            <tr>
              <th>名前</th>
              <td class="name">{{ $request->user->family_name }}&ensp;{{ $request->user->given_name }}</td>
            </tr>
            <tr>
              <th>日付</th>
              <td class="date">{{ $request->date }}</td>
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
          <p class=request-content-message>*承認待ちのため修正はできません。</p>
        @endif
      @else
        <p class="not-applicable-message">本日申請はできません。明日以降、申請を行ってください。</p>
      @endif
    </div>
  </div>
@endsection