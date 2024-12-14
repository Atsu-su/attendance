@extends('layouts.base')
@section('title', '申請一覧')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div id="stamp-correction-request" class="cmn-page">
    <div class="container">
      <h1 class="c-title">申請一覧</h1>
      <div class="titles">
        <p id="pending-approval" class="title titles-pending-approval js-active-title" data-tab="first-tab">承認待ち</p>
        <p id="approved" class="title titles-approved" data-tab="second-tab">承認済み</p>
      </div>
      <div class="tabs">
        <div id="first-tab" class="tab first-tab">
          <table class="c-table c-table--stamp-correction-request table">
            <tr class="header">
              <th>状態</th>
              <th>名前</th>
              <th>対象日時</th>
              <th>申請理由</th>
              <th>申請日時</th>
              <th>詳細</th>
            </tr>
            @foreach ($formattedRequests as $request)
              @if ($request->is_approved == 0)
                <tr class="data">
                  <td>{{ $request->approval_status }}</td>
                  <td>{{ $request->user->family_name }}&ensp;{{ $request->user->given_name }}</td>
                  {{-- $request->dateはattendances.date --}}
                  <td>{{ $request->date }}</td>
                  <td>{{ $request->remarks }}</td>
                  <td>{{ $request->request_date}}</td>
                  <td>
                    @if (auth('admin')->check())
                      <a href="{{ route('admin-stamp-correction-request-approve.show', $request->id) }}">詳細</a>
                    @else
                      <a href="{{ route('stamp-correction-request.show', $request->id) }}">詳細</a>
                    @endif
                  </td>
                </tr>
              @endif
            @endforeach
          </table>
        </div>
        <div id="second-tab" class="tab second-tab js-hidden">
          <table class="c-table c-table--stamp-correction-request table">
            <tr class="header">
              <th>状態</th>
              <th>名前</th>
              <th>対象日時</th>
              <th>申請理由</th>
              <th>申請日時</th>
              <th>詳細</th>
            </tr>
            @foreach ($formattedRequests as $request)
              @if ($request->is_approved == 1)
                <tr class="data">
                  <td>{{ $request->approval_status }}</td>
                  <td>{{ $request->user->family_name }}&ensp;{{ $request->user->given_name }}</td>
                  <td>{{ $request->date }}</td>
                  <td>{{ $request->remarks }}</td>
                  <td>{{ $request->request_date}}</td>
                  <td>
                    @if (auth('admin')->check())
                      <a href="{{ route('admin-stamp-correction-request.show', $request->id) }}">詳細</a>
                    @else
                      <a href="{{ route('stamp-correction-request.show', $request->id) }}">詳細</a>
                    @endif
                  </td>
                </tr>
              @endif
            @endforeach
          </table>
        </div>
      </div>
    </div>
  </div>
  <script>
    const titles = document.querySelectorAll('.title');
    const tabs = document.querySelectorAll('.tab');

    titles.forEach((title) => {
      title.addEventListener('click', (e) => {
        // タイトルの切り替え
        if (!e.target.classList.contains('js-active-title')) {
          titles.forEach((title) => {
            title.classList.remove('js-active-title');
          });
          e.target.classList.add('js-active-title');
        }

        // タブの切り替え
        const targetTab = e.target.dataset.tab;

        tabs.forEach((tab) => {
          const isTarget = tab.classList.contains(targetTab);
          const isHidden = tab.classList.contains('js-hidden');

          if (isTarget && isHidden) {
            tab.classList.remove('js-hidden');  // 表示
          } else if (!isTarget && !isHidden) {
            tab.classList.add('js-hidden');     // 非表示
          }
        });
      });
    });
  </script>
@endsection