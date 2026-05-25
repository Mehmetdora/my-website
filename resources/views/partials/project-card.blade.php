@php($labels = ['planned' => 'Planned', 'in-progress' => 'In Progress', 'completed' => 'Completed', 'archived' => 'Archived'])
<a href="/projects/{{ $project['slug'] }}" class="group flex h-full min-h-[260px] flex-col rounded-lg border border-[hsl(var(--border))] bg-[hsl(var(--panel))] p-6 shadow-soft transition hover:-translate-y-0.5 hover:border-[hsl(var(--accent))]">
    <div class="flex flex-1 flex-col">
        <div class="flex flex-wrap gap-2">
            <span class="badge border-[hsl(var(--signal)/0.4)] bg-[hsl(var(--signal)/0.1)] text-[hsl(var(--signal))]">{{ $labels[$project['status']] ?? $project['status'] }}</span>
        </div>
        <h2 class="mt-6 text-xl font-semibold tracking-normal text-[hsl(var(--ink))] group-hover:text-[hsl(var(--accent))]">{{ $project['title'] }}</h2>
        <p class="mt-3 line-clamp-4 text-sm leading-6 text-[hsl(var(--soft))]">{{ $project['summary'] }}</p>
        <div class="mt-auto flex flex-wrap gap-2 pt-6">
            @foreach(array_slice($project['technologies'], 0, 4) as $tech)
                <span class="badge">{{ $tech }}</span>
            @endforeach
        </div>
    </div>
</a>
