@extends('layouts.base')
@section('title', '勤怠管理システム')
@section('header')
  @include('components.user.header')
@endsection
@section('content')
  <div id="attendance-register" class="cmn-page">
    <p id="label" class="c-label label js-off-duty">勤務外</p>
    <p id="date" class="date">2023年11月23日（木）</p>
    <p id="time" class="time"></p>
    <div id="buttons" class="buttons">
      <button id="on-duty-button" class="c-btn c-btn--black c-btn--attendance-register">出勤</button>
      <div id="leave-buttons" class="buttons-leave js-hidden">
        <button id="off-duty-button" class="c-btn c-btn--black c-btn--attendance-register">退勤</button>
        <button id="break-start-button" class="c-btn c-btn--white c-btn--attendance-register">休憩入</button>
      </div>
      <button id="break-end-button" class="c-btn c-btn--white c-btn--attendance-register buttons-back-to-work js-hidden">休憩終</button>
    </div>
    <p id="message" class="message js-hidden">退勤しました。お疲れ様でした！</p>
  </div>
  <script>
    /* ------------------ */
    /* 関数
    /* ------------------ */

    // js-hiddenの付け替え用関数
    function toggleClass(element) {
      if (element.classList.contains('js-hidden')) {
        element.classList.remove('js-hidden');
      } else {
        element.classList.add('js-hidden');
      }
    }

    /* ------------------ */
    /* メイン
    /* ------------------ */

    // 日付の表示
    const date = document.getElementById('date');
    date.textContent = new Date().toLocaleDateString('ja-JP', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      weekday: 'short'
    });

    // 時刻の表示
    const time = document.getElementById('time');
    function updateTime() {
      const now = new Date();
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');
      document.querySelector('.time').textContent = `${hours}:${minutes}:${seconds}`;
    }

    updateTime();
    const timer = setInterval(updateTime, 1000);

    /* ------------------ */
    /* EventListener
    /* ------------------ */

    // ページ移動時にタイマーを停止
    window.addEventListener('unload', () => {
      clearInterval(timer);
    });

    const label = document.getElementById('label');
    const buttons = document.getElementById('buttons')
    const onDutyButton = document.getElementById('on-duty-button');
    const offDutyButton = document.getElementById('off-duty-button');
    const leaveButtons = document.getElementById('leave-buttons');
    const breakStartButton = document.getElementById('break-start-button');
    const breakEndButton = document.getElementById('break-end-button');
    const message = document.getElementById('message');

    // 出勤ボタンのクリックイベント
    onDutyButton.addEventListener('click', () => {
        label.textContent = '勤務中';
        toggleClass(onDutyButton); // 非表示
        toggleClass(leaveButtons); // 表示
    });

    // 休憩入ボタンのクリックイベント
    breakStartButton.addEventListener('click', () => {
      label.textContent = '休憩中';
      toggleClass(leaveButtons);   // 非表示
      toggleClass(breakEndButton); // 表示
    });

    // 休憩終ボタンのクリックイベント
    breakEndButton.addEventListener('click', () => {
      label.textContent = '勤務中';
      toggleClass(breakEndButton);   // 非表示
      toggleClass(leaveButtons);     // 表示
    });

    // 退勤ボタンのクリックイベント
    offDutyButton.addEventListener('click', () => {
      clearInterval(timer);
      label.textContent = '退勤済';
      toggleClass(buttons);  // 非表示
      toggleClass(message);  // 表示
      time.style.opacity = 0.5;
    });

  </script>
@endsection