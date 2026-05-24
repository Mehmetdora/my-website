<aside class="fixed inset-y-0 left-0 hidden w-72 border-r border-white/10 bg-[#101827] p-6 lg:block">
    <a href="/" class="flex items-center gap-3">
        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-[#5DF8D8] text-sm font-black text-[#07101f]">{{ $site['initials'] }}</span>
        <span class="font-bold text-white">Personal Website Admin</span>
    </a>

    <nav class="mt-10 grid gap-2 text-sm font-semibold text-slate-300">
        <a href="{{ route('admin.dashboard') }}#profile" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'dashboard' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">Genel ayarlar</a>
        <a href="{{ route('admin.dashboard') }}#home" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8]">Ana sayfa</a>
        <a href="{{ route('admin.dashboard') }}#hobbies" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8]">Hobiler / kişisel taraf</a>
        <a href="{{ route('admin.dashboard') }}#education" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8]">Education</a>
        <a href="{{ route('admin.dashboard') }}#tags" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8]">Ortak tag listesi</a>
        <div class="my-3 border-t border-white/10"></div>
        <a href="{{ route('admin.projects.index') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'projects' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">Projeler</a>
        <a href="{{ route('admin.projects.create') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'project-create' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">Yeni proje</a>
        <a href="{{ route('admin.blog.index') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'blog' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">Bloglar</a>
        <a href="{{ route('admin.blog.create') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'blog-create' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">Yeni blog</a>
        <a href="{{ route('admin.life.index') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'life' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">My Life</a>
        <a href="{{ route('admin.life.create') }}" class="rounded-md px-3 py-2 hover:bg-white/5 hover:text-[#5DF8D8] {{ ($active ?? '') === 'life-create' ? 'bg-white/5 text-[#5DF8D8]' : '' }}">Yeni Life paylaşımı</a>
    </nav>

    <form method="POST" action="{{ route('admin.logout') }}" class="absolute bottom-6 left-6 right-6">
        @csrf
        <button class="btn-outline w-full">Çıkış yap</button>
    </form>
</aside>
