@extends('layouts.app')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-[#0a0f1e] px-4 py-10">
    <div class="w-full max-w-md rounded-lg border border-white/10 bg-[#101827] p-6 shadow-2xl">
        <a href="{{ route('home') }}" class="section-label">Admin</a>
        <h1 class="mt-3 text-3xl font-extrabold text-white">Personal Website admin login</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">The admin panel uses CSRF-protected Laravel session authentication.</p>

        @if($errors->any())
            <div class="mt-5 rounded-md border border-red-400/30 bg-red-400/10 p-3 text-sm text-red-200">
                Email or password is incorrect.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="mt-6 grid gap-4">
            @csrf
            <label class="grid gap-2 text-sm font-semibold text-slate-300">
                Email
                <input name="email" type="email" value="{{ old('email') }}" class="admin-input" autocomplete="email">
            </label>
            <label class="grid gap-2 text-sm font-semibold text-slate-300">
                Password
                <input name="password" type="password" class="admin-input" autocomplete="current-password">
            </label>
            <button class="btn-primary mt-2 w-full">Admin paneline gir</button>
        </form>
    </div>
</div>
@endsection
