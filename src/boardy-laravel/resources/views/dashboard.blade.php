@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <p>Добро пожаловать, {{ auth()->user()->name }}!</p>
    <a href="{{ route('posts.index') }}" class="btn btn-primary">Перейти к постам</a>
@endsection
