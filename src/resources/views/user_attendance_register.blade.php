@extends('layouts.base')
@section('title', '勤怠管理システム')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="attendance-register" class="cmn-page">
    <p id="label" class="c-label label">{{ $attendance->work_status }}</p>
    <p id="date" class="date">{{ $now->isoFormat('YYYY年MM月DD日(ddd)'); }}</p>
    <p id="time" class="time{{ $attendance->status == \App\Models\Attendance::OFF_DUTY[0] ? ' time--opacity' : ''}}">
      {{ $attendance->status == \App\Models\Attendance::OFF_DUTY[0] ? $attendance->timeFormatConvert($attendance->end_time) : $now->format('H:i') }}
    </p>
    <a class="reload" href="{{ route('attendance.register') }}">時間を更新する</a>
    <div id="buttons" class="buttons">
      <button id="on-duty-button" class="c-btn c-btn--black c-btn--attendance-register {{ $attendance->status == \App\Models\Attendance::BF_WORK[0] ? '' : 'js-hidden' }}">出勤</button>
      <div id="leave-buttons" class="buttons-leave {{ $attendance->status == \App\Models\Attendance::ON_DUTY[0] ? '' : 'js-hidden' }}">
        <button id="off-duty-button" class="c-btn c-btn--black c-btn--attendance-register">退勤</button>
        <button id="break-start-button" class="c-btn c-btn--white c-btn--attendance-register">休憩</button>
      </div>
      <button id="break-end-button" class="c-btn c-btn--white c-btn--attendance-register buttons-back-to-work {{ $attendance->status == \App\Models\Attendance::BREAK[0] ? '' : 'js-hidden' }}">休憩戻</button>
    </div>
    <p id="message" class="message {{ $attendance->status == \App\Models\Attendance::OFF_DUTY[0] ? '' : 'js-hidden' }}">退勤しました。お疲れ様でした！</p>
  </div>

  @php
    $attendance = [
        'BF_WORK' => \App\Models\Attendance::BF_WORK,
        'ON_DUTY' => \App\Models\Attendance::ON_DUTY,
        'BREAK' => \App\Models\Attendance::BREAK,
        'OFF_DUTY' => \App\Models\Attendance::OFF_DUTY,
    ];
  @endphp
  <script>
    const attendance = @json($attendance);
    console.log(attendance);

    /* ------------------ */
    /* 関数
    /* ------------------ */

    // js-hiddenの付け替え用関数
    function toggleClass(element, className) {
      if (element.classList.contains(className)) {
        element.classList.remove(className);
      } else {
        element.classList.add(className);
      }
    }

    async function getResponseJson(url, csrf) {
      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf}
        });

        if (!response.ok) {
          throw new Error('Network response was not OK');
        }

        return response.ok;

      } catch (error) {
        console.error('There has been a problem with your fetch operation:', error);
        return false;
      }
    }

    /* ------------------ */
    /* メイン
    /* ------------------ */

    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // URL
    const urlStartWork = '/attendance/startwork';
    const urlEndWork = '/attendance/endwork';
    const urlStartBreak = '/attendance/startbreak';
    const urlEndBreak = '/attendance/endbreak';

    /* ------------------ */
    /* EventListener
    /* ------------------ */

    const label = document.getElementById('label');
    const buttons = document.getElementById('buttons')
    const onDutyButton = document.getElementById('on-duty-button');
    const offDutyButton = document.getElementById('off-duty-button');
    const leaveButtons = document.getElementById('leave-buttons');
    const breakStartButton = document.getElementById('break-start-button');
    const breakEndButton = document.getElementById('break-end-button');
    const message = document.getElementById('message');

    // 出勤ボタンのクリックイベント
    onDutyButton.addEventListener('click', async () => {
      const status = await getResponseJson(urlStartWork, csrf);

      if (status) {
        label.textContent = attendance.ON_DUTY[1];
        toggleClass(onDutyButton, 'js-hidden'); // 非表示
        toggleClass(leaveButtons, 'js-hidden'); // 表示
      } else {

        // ----------------------------------
        // エラーの処理は修正予定（モーダル化）
        //
        alert('出勤時間の登録ができませんでした');
        // ----------------------------------

      }
    });

    // 退勤ボタンのクリックイベント
    offDutyButton.addEventListener('click', async () => {
      const status = await getResponseJson(urlEndWork, csrf);

      if (status) {
        label.textContent = attendance.OFF_DUTY[1];
        toggleClass(leaveButtons, 'js-hidden');  // 非表示
        toggleClass(message, 'js-hidden');  // 表示

        // スタイルの変更
        toggleClass(time, 'time--opacity');
      } else {

        // ----------------------------------
        // エラーの処理は修正予定（モーダル化）
        //
        alert('退勤時間の登録ができませんでした');
        // ----------------------------------

      }
    });

    // 休憩入ボタンのクリックイベント
    breakStartButton.addEventListener('click', async () => {
      const status = await getResponseJson(urlStartBreak, csrf);

      if (status) {
        label.textContent = attendance.BREAK[1];
        toggleClass(leaveButtons, 'js-hidden');   // 非表示
        toggleClass(breakEndButton, 'js-hidden'); // 表示
      } else {

        // ----------------------------------
        // エラーの処理は修正予定（モーダル化）
        //
        alert('休憩開始時間の登録ができませんでした');
        // ----------------------------------

      }
    });

    // 休憩終ボタンのクリックイベント
    breakEndButton.addEventListener('click', async () => {
      const status = await getResponseJson(urlEndBreak, csrf);

      if (status) {
        label.textContent = attendance.ON_DUTY[1];
        toggleClass(breakEndButton, 'js-hidden');   // 非表示
        toggleClass(leaveButtons, 'js-hidden');     // 表示
      } else {

        // ----------------------------------
        // エラーの処理は修正予定（モーダル化）
        //
        alert('休憩終了時間の登録ができませんでした');
        // ----------------------------------

      }
    });
  </script>
@endsection