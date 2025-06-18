@extends('layouts.app')

@section('title', 'Profil')

@section('content')

{{-- Pesan dari controller 'with()' --}}
@if(session('edit'))
<div class="alert alert-success">
    {{ session('edit') }}
</div>
@endif

<div class="container-fluid p-0">
    <h1 class="h3 mb-3">Profil Pengguna</h1>
    <div class="row">            
        <div class="col-md-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" value="{{ auth()->user()->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" value="{{ auth()->user()->username }}" {{ auth()->user()->username == 'gurupiket' ? 'disabled' : '' }} required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="passwordInput">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="align-middle" data-feather="eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password_confirmation" id="confirmPasswordInput">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="align-middle" data-feather="eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Perbarui Profil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection