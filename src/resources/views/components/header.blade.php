<div id="header">
  <img class="logo" src="{{ asset('img/logo.svg') }}" alt="carmeriのロゴ">
  @if (auth('admin')->check() ||  auth('web')->check())
    <nav class="nav">

      {{-- 管理者用 --}}
      @if (auth('admin')->check())
        <a class="nav-link" href="{{ route('admin-attendance.show-daily-list', ['year' =>  request()->year, 'month' =>  request()->month, 'day' =>  request()->day]) }}">勤怠一覧</a>
        <a class="nav-link" href="{{ route('admin-staff.show-list') }}">スタッフ一覧</a>
        <a class="nav-link" href="{{ route('admin-stamp-correction-request.index') }}">申請一覧</a>
        <form action="{{ route('admin-logout') }}" method="post">
          @csrf
          <button class="nav-link" type="submit">ログアウト</button>
        </form>
      @endif

      {{-- ユーザ用 --}}
      @if (auth('web')->check())
        <a class="nav-link" href="{{ route('attendance.register') }}">勤怠</a>
        <a class="nav-link" href="{{ route('attendance.show-list', ['year' => request()->year, 'month' => request()->month]) }}">勤怠一覧</a>
        <a class="nav-link" href="{{ route('stamp-correction-request.index') }}">申請</a>
        <form action="{{ route('logout') }}" method="post">
          @csrf
          <button class="nav-link" type="submit">ログアウト</button>
        </form>
      @endif

    </nav>
    <nav class="nav-hamburger">
      <div id="svg" class="nav-hamburger-svg"></div>
      <div id="menu" class="nav-hamburger-menu js-hidden">

        {{-- 管理者用 --}}
        @if (auth('admin')->check())
          <a class="nav-link" href="{{ route('admin-attendance.show-daily-list', ['year' =>  request()->year, 'month' =>  request()->month, 'day' =>  request()->day]) }}">勤怠一覧</a>
          <a class="nav-link" href="{{ route('admin-staff.show-list') }}">スタッフ一覧</a>
          <a class="nav-link" href="{{ route('admin-stamp-correction-request.index') }}">申請一覧</a>
          <form action="{{ route('admin-logout') }}" method="post">
            @csrf
            <button class="nav-link" type="submit">ログアウト</button>
          </form>
        @endif

        {{-- ユーザ用 --}}
        @if (auth('web')->check())
          <a class="nav-link" href="{{ route('attendance.register') }}">勤怠</a>
          <a class="nav-link" href="{{ route('attendance.show-list', ['year' => request()->year, 'month' => request()->month]) }}">勤怠一覧</a>
          <a class="nav-link" href="{{ route('stamp-correction-request.index') }}">申請</a>
          <form action="{{ route('logout') }}" method="post">
            @csrf
            <button class="nav-link" type="submit">ログアウト</button>
          </form>
        @endif

      </div>
    </nav>
  @endif
</div>

{{-- ハンバーガーメニュー --}}
<script>
  const svg = document.getElementById('svg');
  const menu = document.getElementById('menu');

  if (svg) {
    svg.addEventListener('click', () => {
      if (menu.classList.contains('js-hidden')) {
        menu.classList.remove('js-hidden');
      } else {
        menu.classList.add('js-hidden');
      }
    });

    window.addEventListener('click', (e) => {
      if (!menu.contains(e.target) && !svg.contains(e.target)) {
        menu.classList.add('js-hidden');
      }
    });
  }
</script>
