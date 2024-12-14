@extends('layouts.base')
@section('title', 'test')
@section('header')
@include('components.header')
@endsection
@section('content')
<pre>
@php
  dump(session()->all());
@endphp
</pre>
<p>管理者のホームだよー</p>
<pre>
@php
  dump(auth()->user());
@endphp
</pre>
@endsection