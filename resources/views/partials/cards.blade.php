@once
    @php
        if (! function_exists('tag_items')) {
            function tag_items(array $slugs): array {
                $lookup = array_map(fn ($slug): string => strtolower((string) $slug), $slugs);

                return collect(config('content.tags'))
                    ->filter(fn (array $tag): bool => in_array(strtolower($tag['slug']), $lookup, true))
                    ->values()
                    ->all();
            }
        }
        if (! function_exists('tr_date')) {
            function tr_date(?string $date): string {
                return $date ? \Carbon\Carbon::parse($date)->locale('tr')->translatedFormat('d F Y') : '';
            }
        }
    @endphp
@endonce
