@extends('layouts.app')

@section('title', '404 - Không tìm thấy trang')

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-orange" style="color: #ff672a; font-size: 8rem;">404 <br> Not Found</h1>
    </div>
</div>

<style>
    body {
        background-color: #f8f9fa;
    }
    .text-orange {
        text-shadow: 2px 4px 10px rgba(255,103,42,0.2);
    }
</style>
@endsection
