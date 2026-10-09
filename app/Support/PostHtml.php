<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Everything blog-post HTML goes through. Content comes from the admin WYSIWYG editor, but it is
 * still sanitized on write with a strict allow-list (no scripts, no inline styles, no event
 * handlers, no javascript: URLs) so a compromised admin account or a pasted payload can't turn
 * the blog into an XSS vector for students - and so stored markup stays in the small set the
 * public stylesheet knows how to style.
 */
class PostHtml
{
    private static ?HtmlSanitizer $sanitizer = null;

    /** Sanitize editor output. Legacy plain text (no tags) is converted to paragraphs first. */
    public static function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '') {
            return '';
        }

        if (! self::looksLikeHtml($html)) {
            $html = self::fromPlainText($html);
        }

        return self::sanitizer()->sanitize($html);
    }

    /** Plain text to HTML: blank lines separate paragraphs, single newlines become <br>. */
    public static function fromPlainText(string $text): string
    {
        $paragraphs = preg_split('/\R{2,}/', trim($text)) ?: [];

        return collect($paragraphs)
            ->map(fn (string $p) => '<p>'.nl2br(e(trim($p)), false).'</p>')
            ->implode("\n");
    }

    /** Readable text with word boundaries preserved (tags replaced by spaces, entities decoded). */
    public static function toText(?string $html): string
    {
        $text = preg_replace('/<[^>]+>/', ' ', (string) $html) ?? '';
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }

    private static function looksLikeHtml(string $value): bool
    {
        return (bool) preg_match('/<\/?(p|h[1-6]|ul|ol|li|blockquote|pre|br|div|img|a|strong|em)\b/i', $value);
    }

    private static function sanitizer(): HtmlSanitizer
    {
        return self::$sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig)
                ->allowElement('p', ['data-align'])
                ->allowElement('h2', ['data-align'])
                ->allowElement('h3', ['data-align'])
                ->allowElement('h4', ['data-align'])
                ->allowElement('br')
                ->allowElement('hr')
                ->allowElement('strong')
                ->allowElement('em')
                ->allowElement('u')
                ->allowElement('s')
                ->allowElement('code')
                ->allowElement('pre')
                ->allowElement('blockquote')
                ->allowElement('ul')
                ->allowElement('ol')
                ->allowElement('li')
                ->allowElement('a', ['href', 'title', 'target'])
                ->allowElement('img', ['src', 'alt', 'title', 'width', 'height'])
                ->allowLinkSchemes(['http', 'https', 'mailto'])
                ->allowMediaSchemes(['http', 'https'])
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->forceAttribute('a', 'rel', 'noopener noreferrer nofollow')
                ->forceAttribute('img', 'loading', 'lazy')
        );
    }
}
