  <div id="header">
  <img class="logo" src="{{ asset('img/logo.svg') }}" alt="carmeriのロゴ">
  @if (request()->headerType == 'logOut' || request()->headerType == 'logIn')
    <nav class="nav">
      <a class="nav-link" href="">勤怠一覧</a>
      <a class="nav-link" href="">スタッフ一覧</a>
      <a class="nav-link" href="">申請一覧</a>
      @if (request()->headerType == 'logOut')
        <form action="{{ route('logout') }}" method="post">
          @csrf
          <button class="nav-link" type="submit">ログアウト</button>
        </form>
      @else
        <a class="nav-link" href="{{ route('login') }}">ログイン</a>
      @endif
    </nav>
    <nav class="nav-hamburger">
      <div id="svg" class="nav-hamburger-svg"></div>
      <div id="menu" class="nav-hamburger-menu js-hidden">
        <a class="nav-hamburger-menu-link" href="">勤怠一覧</a>
        <a class="nav-hamburger-menu-link" href="">スタッフ一覧</a>
        <a class="nav-hamburger-menu-link" href="">申請一覧</a>
        @if (request()->headerType == 'logOut')
          <form action="{{ route('logout') }}" method="post">
            @csrf
            <button class="nav-link" type="submit">ログアウト</button>
          </form>
        @else
          <a class="nav-link" href="{{ route('login') }}">ログイン</a>
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
