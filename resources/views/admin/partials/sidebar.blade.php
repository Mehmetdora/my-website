<div class="sticky top-0 z-50 border-b border-white/10 bg-[#0a0f1e]/95 px-4 py-3 backdrop-blur lg:hidden">
    <div class="flex items-center justify-between gap-4">
        <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#5DF8D8] text-sm font-black text-[#07101f]">{{ $site['initials'] }}</span>
            <span class="truncate font-bold text-white">Personal Website Admin</span>
        </a>
        <button type="button" class="btn-outline min-h-10 px-4" data-admin-sidebar-toggle aria-controls="admin-sidebar" aria-expanded="false">
            Menu
        </button>
    </div>
</div>

<div class="fixed inset-0 z-50 hidden bg-black/65 backdrop-blur-sm lg:hidden" data-admin-sidebar-overlay></div>

<aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-[60] flex w-80 max-w-[calc(100vw-2rem)] -translate-x-full flex-col border-r border-white/10 bg-[#101827] p-6 shadow-2xl shadow-black/40 transition-transform duration-200 lg:z-40 lg:w-72 lg:max-w-none lg:translate-x-0 lg:shadow-none" data-admin-sidebar>
    <div class="flex items-center justify-between gap-4">
        <a href="/" class="flex min-w-0 items-center gap-3">
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#5DF8D8] text-sm font-black text-[#07101f]">{{ $site['initials'] }}</span>
            <span class="truncate font-bold text-white">Personal Website Admin</span>
        </a>
        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-white/10 bg-white/5 text-xl leading-none text-slate-200 transition hover:border-[#5DF8D8] hover:text-[#5DF8D8] lg:hidden" data-admin-sidebar-close aria-label="Close admin menu">
            &times;
        </button>
    </div>

    <nav class="mt-10 grid flex-1 content-start gap-2 overflow-y-auto pr-1 text-sm font-semibold text-slate-300">
        <a href="{{ route('admin.dashboard') }}#profile" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'dashboard' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">General settings</a>
        <a href="{{ route('admin.dashboard') }}#home" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8]">Homepage</a>
        <a href="{{ route('admin.dashboard') }}#hobbies" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8]">Hobbies / personal side</a>
        <a href="{{ route('admin.dashboard') }}#about-stats" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8]">About stats</a>
        <a href="{{ route('admin.dashboard') }}#education" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8]">Education</a>
        <a href="{{ route('admin.dashboard') }}#tags" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8]">Shared tag list</a>
        <div class="my-3 border-t border-white/10"></div>
        <a href="{{ route('admin.projects.index') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'projects' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">Projects</a>
        <a href="{{ route('admin.projects.create') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'project-create' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">New project</a>
        <a href="{{ route('admin.blog.index') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'blog' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">Blogs</a>
        <a href="{{ route('admin.blog.create') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'blog-create' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">New blog</a>
        <a href="{{ route('admin.life.index') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'life' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">My Life</a>
        <a href="{{ route('admin.life.create') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'life-create' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">New Life post</a>
    </nav>

    <form method="POST" action="{{ route('admin.logout') }}" class="mt-6 border-t border-white/10 pt-6">
        @csrf
        <button class="btn-outline w-full">Logout</button>
    </form>
</aside>
