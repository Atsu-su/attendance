@extends('layouts.base')
@section('title', 'ログイン')
@section('header')
@include('components.header')
@endsection
@section('content')
<div class="c-default-form" id="login">
  <h1 class="title">管理者ログイン</h1>
  <form class="form" action="{{ route('admin-login') }}" method="post">
    @csrf
    <label class="form-title">メールアドレス</label>
    <input class="form-input" type="text" name="email" value="{{ old('email') }}">
    @error('email')
    <p class="c-error-message">{{ $message }}</p>
    @enderror
    <label class="form-title">パスワード</label>
    <input class="form-input" type="password" name="password" value="{{ old('password') }}">
    @error('password')
    <p class="c-error-message">{{ $message }}</p>
    @enderror
    <button class="form-btn c-btn c-btn--black c-btn--auth" type="submit">ログインする</button>
  </form>
</div>
@endsection