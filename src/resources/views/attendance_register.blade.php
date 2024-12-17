@extends('layouts.base')
@section('title', '勤怠管理システム')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="attendance-register" class="cmn-page">
    <p id="label" class="c-label label">{{ $attendance->work_status }}</p>
    <p id="date" class="date">{{ $now->isoFormat('YYYY年MM月DD日(ddd)'); }}</p>
    <p id="time" class="time">{{ $now->format('H:i') }}</p>
    <div id="buttons" class="buttons">
      <button id="on-duty-button" class="c-btn c-btn--black c-btn--attendance-register {{ $attendance->status == \App\Models\Attendance::BF_WORK ? '' : 'js-hidden' }}">出勤</button>
      <div id="leave-buttons" class="buttons-leave {{ $attendance->status == \App\Models\Attendance::ON_DUTY ? '' : 'js-hidden' }}">
        <button id="off-duty-button" class="c-btn c-btn--black c-btn--attendance-register">退勤</button>
        <button id="break-start-button" class="c-btn c-btn--white c-btn--attendance-register">休憩入</button>
      </div>
      <button id="break-end-button" class="c-btn c-btn--white c-btn--attendance-register buttons-back-to-work {{ $attendance->status == \App\Models\Attendance::BREAK ? '' : 'js-hidden' }}">休憩終</button>
    </div>
    <p id="message" class="message {{ $attendance->status == \App\Models\Attendance::OFF_DUTY ? '' : 'js-hidden' }}">退勤しました。お疲れ様でした！</p>
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

        // ----------------------------------------------

        // いいねボタンの参考
        // fetch(url, {
        //   method: 'POST',
        //   headers: {
        //     'Content-Type': 'application/json',
        //     'X-CSRF-TOKEN': csrf}
        // }).then(response =>  {
        //     if (!response.ok) {
        //       throw new Error('Network response was not OK');
        //     }
        //     return response.json();
        // }).then(data => {
        //     const likeIcon = document.getElementById('like-icon');
        //     const likes = document.getElementById('number-of-likes');
        //     if (data.likeIt) {
        //       likes.textContent = parseInt(likes.textContent) + 1;  // いいねの数を増やす
        //       likeIcon.classList.add('filled'); // 星の色を黄色に変更
        //     } else {
        //       likes.textContent = parseInt(likes.textContent) - 1;  // いいねの数を減らす
        //       likeIcon.classList.remove('filled'); // 星の色を白色に変更
        //     }
        // }).catch(error => {
        //     console.error('There has been a problem with your fetch operation:', error);
        // }).finally(() => {
        //   // 重複処理抑止用2
        //   icon.classList.remove('js-processing');
        // });
        // ----------------------------------------------
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
      label.textContent = '退勤済';
      toggleClass(buttons);  // 非表示
      toggleClass(message);  // 表示
      time.style.opacity = 0.5;
    });

  </script>
@endsection