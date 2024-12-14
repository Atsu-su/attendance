@extends('layouts.base')
@section('title', 'ユーザ登録')
@section('header')
  @include('components.header')
@endsection
@section('content')
  <div class="c-default-form" id="register">
    <h1 class="title">会員登録</h1>
    <form class="form" action="{{ route('register') }}" method="post">
      @csrf
      <label class="form-title">ユーザ名</label>
      <div class="form-input-name">
        <input class="form-input" type="text" name="family_name" value="{{ old('family_name') }}" placeholder="姓">
        <input class="form-input" type="text" name="given_name" value="{{ old('given_name') }}" placeholder="名">
      </div>
      @if (!empty($errors->has('family_name')) || !empty($errors->has('given_name')))
        <p class="c-error-message">{{ !empty($errors->first('family_name')) ? $errors->first('family_name') : $errors->first('given_name') }}</p>
      @endif
      <label class="form-title">メールアドレス</label>
      <input class="form-input" type="text" name="email" value="{{ old('email') }}">
      @error('email')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-title">パスワード</label>
      <input class="form-input" type="password" name="password">
      @error('password')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <label class="form-title">確認用パスワード</label>
      <input class="form-input" type="password" name="confirm_password">
      @error('confirm_password')
        <p class="c-error-message">{{ $message }}</p>
      @enderror
      <button class="form-btn c-btn c-btn--black c-btn--auth" type="submit">登録する</button>
    </form>
    <a class="login-link" href="{{ route('login') }}">ログインはこちら</a>
  </div>
@endsection