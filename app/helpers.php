<?php

use Carbon\Carbon;
use Illuminate\Support\Str;

if (! function_exists('tag_items')) {
    function tag_items(array $slugs): array
    {
        $lookup = array_map(fn ($slug): string => strtolower((string) $slug), $slugs);

        return collect(config('content.tags'))
            ->filter(fn (array $tag): bool => in_array(strtolower($tag['slug']), $lookup, true))
            ->values()
            ->all();
    }
}

if (! function_exists('tr_date')) {
    function tr_date(?string $date): string
    {
        return $date ? Carbon::parse($date)->locale('en')->translatedFormat('F j, Y') : '';
    }
}

if (! function_exists('normalize_external_url')) {
    function normalize_external_url(?string $url, string $fallback = ''): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return $fallback;
        }

        if (str_starts_with($url, '#') || str_starts_with($url, '/') || preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)) {
            return $url;
        }

        return 'https://'.ltrim($url, '/');
    }
}

if (! function_exists('normalize_telegram_url')) {
    function normalize_telegram_url(?string $url, string $fallback = ''): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return $fallback;
        }

        if (str_starts_with($url, '@')) {
            return 'https://t.me/'.ltrim($url, '@');
        }

        if (! preg_match('/^[a-z][a-z0-9+.-]*:/i', $url) && ! str_contains($url, '/') && ! str_contains($url, '.')) {
            return 'https://t.me/'.$url;
        }

        return normalize_external_url($url, $fallback);
    }
}

if (! function_exists('normalize_site_links')) {
    function normalize_site_links(array $site): array
    {
        $links = $site['links'] ?? [];
        $email = trim((string) ($links['email'] ?? ''));

        if ($email !== '' && ! str_starts_with($email, 'mailto:')) {
            $email = 'mailto:'.$email;
        }

        $links['email'] = $email;
        $links['github'] = normalize_external_url($links['github'] ?? '', config('content.site.links.github', ''));
        $links['linkedin'] = normalize_external_url($links['linkedin'] ?? '', config('content.site.links.linkedin', ''));
        $links['telegram'] = normalize_telegram_url($links['telegram'] ?? '', config('content.site.links.telegram', ''));
        $links['cv'] = $links['cv'] ?? '/cv/pdf';
        $site['links'] = $links;

        return $site;
    }
}

if (! function_exists('is_safe_content_url')) {
    function is_safe_content_url(string $url, array $schemes = ['http', 'https', 'mailto', 'tel']): bool
    {
        $url = trim($url);

        if ($url === '' || str_starts_with($url, '#') || str_starts_with($url, '/')) {
            return true;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        return is_string($scheme) && in_array(strtolower($scheme), $schemes, true);
    }
}

if (! function_exists('is_safe_image_src')) {
    function is_safe_image_src(string $src): bool
    {
        $src = trim($src);

        if (is_safe_content_url($src, ['http', 'https'])) {
            return true;
        }

        return preg_match('/^data:image\/(?:png|jpe?g|gif|webp);base64,[a-z0-9+\/=\s]+$/i', $src) === 1
            && strlen($src) <= 3_000_000;
    }
}

if (! function_exists('is_safe_iframe_src')) {
    function is_safe_iframe_src(string $src): bool
    {
        $host = parse_url($src, PHP_URL_HOST);
        $scheme = parse_url($src, PHP_URL_SCHEME);

        if (! is_string($host) || ! in_array(strtolower((string) $scheme), ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower($host);

        return str_ends_with($host, 'youtube.com')
            || str_ends_with($host, 'youtube-nocookie.com')
            || str_ends_with($host, 'youtu.be')
            || str_ends_with($host, 'vimeo.com');
    }
}

if (! function_exists('sanitize_inline_style')) {
    function sanitize_inline_style(string $style): string
    {
        $allowed = ['color', 'background-color', 'text-align'];
        $safe = [];

        foreach (explode(';', $style) as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);
            $value = trim($value);

            if (! in_array($property, $allowed, true)) {
                continue;
            }

            if (preg_match('/url|expression|javascript|data:/i', $value)) {
                continue;
            }

            if (preg_match('/^[#(),.%\sa-z0-9-]+$/i', $value) !== 1) {
                continue;
            }

            $safe[] = "{$property}: {$value}";
        }

        return implode('; ', $safe);
    }
}

if (! function_exists('sanitize_quill_class')) {
    function sanitize_quill_class(string $class): string
    {
        $allowedPrefixes = [
            'ql-align-',
            'ql-direction-',
            'ql-indent-',
            'ql-size-',
            'ql-font-',
            'ql-syntax',
        ];

        return collect(preg_split('/\s+/', trim($class)) ?: [])
            ->filter(function (string $className) use ($allowedPrefixes): bool {
                return collect($allowedPrefixes)->contains(fn (string $prefix): bool => str_starts_with($className, $prefix));
            })
            ->implode(' ');
    }
}

if (! function_exists('sanitize_content_html')) {
    function sanitize_content_html(?string $html): string
    {
        if (! is_string($html) || trim($html) === '') {
            return '';
        }

        $allowedTags = [
            'p', 'br', 'h1', 'h2', 'h3', 'strong', 'b', 'em', 'i', 'u', 's',
            'a', 'blockquote', 'pre', 'code', 'ol', 'ul', 'li', 'span', 'img', 'iframe',
        ];
        $blockedTags = ['script', 'style', 'object', 'embed', 'svg', 'math', 'meta', 'link'];

        $source = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $source->loadHTML('<?xml encoding="UTF-8"><div id="content-root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $clean = new DOMDocument('1.0', 'UTF-8');
        $wrapper = $clean->createElement('div');
        $clean->appendChild($wrapper);
        $headingIds = [];

        $copyNode = function (DOMNode $node, DOMNode $parent) use (&$copyNode, $clean, $allowedTags, $blockedTags, &$headingIds): void {
            if ($node instanceof DOMText) {
                $parent->appendChild($clean->createTextNode($node->wholeText));

                return;
            }

            if (! $node instanceof DOMElement) {
                foreach ($node->childNodes as $child) {
                    $copyNode($child, $parent);
                }

                return;
            }

            $tag = strtolower($node->tagName);

            if (in_array($tag, $blockedTags, true)) {
                return;
            }

            if (! in_array($tag, $allowedTags, true)) {
                foreach ($node->childNodes as $child) {
                    $copyNode($child, $parent);
                }

                return;
            }

            if ($tag === 'img' && ! is_safe_image_src($node->getAttribute('src'))) {
                return;
            }

            if ($tag === 'iframe' && ! is_safe_iframe_src($node->getAttribute('src'))) {
                return;
            }

            $element = $clean->createElement($tag);

            if (in_array($tag, ['h1', 'h2', 'h3'], true)) {
                $base = Str::slug($node->textContent) ?: 'section';
                $id = $base;
                $index = 2;

                while (in_array($id, $headingIds, true)) {
                    $id = "{$base}-{$index}";
                    $index++;
                }

                $headingIds[] = $id;
                $element->setAttribute('id', $id);
            }

            if ($node->hasAttribute('class')) {
                $class = sanitize_quill_class($node->getAttribute('class'));
                if ($class !== '') {
                    $element->setAttribute('class', $class);
                }
            }

            if ($node->hasAttribute('style')) {
                $style = sanitize_inline_style($node->getAttribute('style'));
                if ($style !== '') {
                    $element->setAttribute('style', $style);
                }
            }

            if ($tag === 'a') {
                $href = $node->getAttribute('href');
                if (is_safe_content_url($href)) {
                    $element->setAttribute('href', $href);
                    $element->setAttribute('rel', 'noopener noreferrer');
                    if (! str_starts_with($href, '#') && ! str_starts_with($href, '/')) {
                        $element->setAttribute('target', '_blank');
                    }
                }
            }

            if ($tag === 'img') {
                $element->setAttribute('src', $node->getAttribute('src'));
                $element->setAttribute('alt', $node->getAttribute('alt') ?: '');
                $element->setAttribute('loading', 'lazy');
                $element->setAttribute('decoding', 'async');
            }

            if ($tag === 'iframe') {
                $element->setAttribute('src', $node->getAttribute('src'));
                $element->setAttribute('loading', 'lazy');
                $element->setAttribute('allowfullscreen', 'allowfullscreen');
                $element->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
                $element->setAttribute('sandbox', 'allow-scripts allow-same-origin allow-presentation');
            }

            foreach ($node->childNodes as $child) {
                $copyNode($child, $element);
            }

            $parent->appendChild($element);
        };

        $sourceRoot = $source->getElementById('content-root') ?: $source->documentElement;

        foreach ($sourceRoot?->childNodes ?? [] as $child) {
            $copyNode($child, $wrapper);
        }

        $output = '';
        foreach ($wrapper->childNodes as $child) {
            $output .= $clean->saveHTML($child);
        }

        return $output;
    }
}

if (! function_exists('content_headings')) {
    function content_headings(array $blocks = [], ?string $html = null): array
    {
        if (is_string($html) && trim($html) !== '') {
            $sanitized = sanitize_content_html($html);
            $document = new DOMDocument('1.0', 'UTF-8');
            libxml_use_internal_errors(true);
            $document->loadHTML('<?xml encoding="UTF-8"><div>'.$sanitized.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $headings = [];
            foreach (['h1', 'h2', 'h3'] as $tag) {
                foreach ($document->getElementsByTagName($tag) as $heading) {
                    $headings[] = [
                        'id' => $heading->getAttribute('id') ?: Str::slug($heading->textContent),
                        'text' => $heading->textContent,
                    ];
                }
            }

            return $headings;
        }

        return collect($blocks)
            ->where('type', 'heading')
            ->map(fn (array $block): array => [
                'id' => Str::slug($block['text'] ?? ''),
                'text' => $block['text'] ?? '',
            ])
            ->values()
            ->all();
    }
}
