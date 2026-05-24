@extends('layouts.app')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-[#0a0f1e] px-4 py-10">
    <div class="w-full max-w-md rounded-lg border border-white/10 bg-[#101827] p-6 shadow-2xl">
        <span class="section-label">Admin</span>
        <h1 class="mt-3 text-3xl font-extrabold text-white">Personal Website yönetim girişi</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">Admin paneli CSRF korumalı Laravel session auth ile çalışır. Varsayılan geliştirme kullanıcısı: admin@personal-website.local / password.</p>

        @if($errors->any())
            <div class="mt-5 rounded-md border border-red-400/30 bg-red-400/10 p-3 text-sm text-red-200">
                Email veya şifre hatalı.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 grid gap-4">
            @csrf
            <label class="grid gap-2 text-sm font-semibold text-slate-300">
                Email
                <input name="email" type="email" value="{{ old('email', 'admin@personal-website.local') }}" class="admin-input" autocomplete="email">
            </label>
            <label class="grid gap-2 text-sm font-semibold text-slate-300">
                Şifre
                <input name="password" type="password" value="password" class="admin-input" autocomplete="current-password">
            </label>
            <button class="btn-primary mt-2 w-full">Admin paneline gir</button>
        </form>
    </div>
</div>
@endsection
