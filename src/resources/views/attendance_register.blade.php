@extends('layouts.base')
@section('title', '勤怠管理システム')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="attendance-register">
    <p id="label "class="c-label label js-off-duty">勤務外</p>
    <p id="date" class="date">2023年11月23日（木）</p>
    <p id="time" class="time"></p>
    <button id="switch-to-on-duty" class="c-btn c-btn--black c-btn--attendance-register on-duty-button">出勤</button>
    <div id="buttons" class="buttons">
      <button id="switch-to-off-duty" class="c-btn c-btn--black c-btn--attendance-register button">退勤</button>
      <button id="switch-to-break" class="c-btn c-btn--white c-btn--attendance-register button">休憩入</button>
    </div>
  </div>
  <script>
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

    // ページ移動時にタイマーを停止
    window.addEventListener('unload', () => {
      clearInterval(timer);
    });

    // 出勤ボタンのクリックイベント
    const button = document.getElementById('switch-to-on-duty');
    const label = document.getElementById('label');
    const buttons = document.getElementById('buttons');

    button.addEventListener('click', () => {

        // ここでエラー
        label.textContent = '勤務中';

        button.display = 'none';
        buttons.display = 'flex';
    });
  </script>
@endsection