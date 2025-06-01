<!-- resources/views/errors/404.blade.php -->
@extends('layouts.app') {{-- Atau layout sesuai kebutuhan --}}

@section('title', '404 Not Found')

@section('content')
    <div class="text-center mt-5">
        <h1>404</h1>
        <h3>Halaman tidak ditemukan</h3>
        <p>Maaf, halaman yang kamu cari tidak tersedia.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
@endsection
