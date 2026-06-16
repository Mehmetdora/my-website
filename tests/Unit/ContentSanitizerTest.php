<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ContentSanitizerTest extends TestCase
{
    public function test_it_removes_script_handlers_and_javascript_urls(): void
    {
        $html = '<h2 onclick="alert(1)">Title</h2><script>alert(1)</script><p><a href="javascript:alert(1)">bad</a></p><img src="x" onerror="alert(1)">';

        $clean = sanitize_content_html($html);

        $this->assertStringContainsString('<h2 id="title">Title</h2>', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('<img', $clean);
    }

    public function test_it_keeps_safe_quill_content(): void
    {
        $html = '<h1>Demo</h1><p><strong>Bold</strong> <em>text</em></p><pre>int main(void) {}</pre><a href="https://example.com">link</a>';

        $clean = sanitize_content_html($html);

        $this->assertStringContainsString('<h1 id="demo">Demo</h1>', $clean);
        $this->assertStringContainsString('<strong>Bold</strong>', $clean);
        $this->assertStringContainsString('<pre>int main(void) {}</pre>', $clean);
        $this->assertStringContainsString('href="https://example.com"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }

    public function test_it_converts_quill_two_code_blocks_to_semantic_pre_code(): void
    {
        $html = '<div class="ql-code-block-container" spellcheck="false"><div class="ql-code-block">// FOSC/4 için clock sinyali</div><div class="ql-code-block">#define _XTAL_FREQ 4000000</div><div class="ql-code-block">void __interrupt() ISR(void)</div></div>';

        $clean = sanitize_content_html($html);

        $this->assertStringContainsString('<pre><code>// FOSC/4 için clock sinyali'."\n".'#define _XTAL_FREQ 4000000'."\n".'void __interrupt() ISR(void)</code></pre>', $clean);
        $this->assertStringNotContainsString('ql-code-block', $clean);
        $this->assertStringNotContainsString('spellcheck', $clean);
    }

    public function test_it_restricts_iframe_sources(): void
    {
        $clean = sanitize_content_html('<iframe src="https://evil.example/embed"></iframe><iframe src="https://www.youtube.com/embed/demo"></iframe>');

        $this->assertStringNotContainsString('evil.example', $clean);
        $this->assertStringContainsString('youtube.com/embed/demo', $clean);
        $this->assertStringContainsString('sandbox="allow-scripts allow-same-origin allow-presentation"', $clean);
    }
}
