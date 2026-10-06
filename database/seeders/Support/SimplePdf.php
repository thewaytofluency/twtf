<?php

namespace Database\Seeders\Support;

/**
 * Tiny dependency-free PDF writer, just enough to give seeded documents a real, openable file
 * (A4, Helvetica, headings, wrapped paragraphs and bullets, automatic page breaks). ASCII only —
 * the standard Helvetica font here has no encoding for anything else.
 */
class SimplePdf
{
    private const PAGE_WIDTH = 595;
    private const PAGE_HEIGHT = 842;
    private const MARGIN = 56;

    /** @var array<int, string> content stream per page */
    private array $pages = [];

    private string $stream = '';

    private float $y = 0;

    public function __construct(private string $title, private string $subtitle = '')
    {
        $this->newPage();
        $this->text($title, 22, true);
        $this->y -= 6;

        if ($subtitle !== '') {
            $this->text($subtitle, 11, false, 0.4);
        }

        $this->y -= 8;
    }

    public function heading(string $text): static
    {
        $this->ensureSpace(60);
        $this->y -= 10;
        $this->text($text, 14, true);
        $this->y -= 2;

        return $this;
    }

    public function paragraph(string $text): static
    {
        foreach (explode("\n", wordwrap($text, 88, "\n", true)) as $line) {
            $this->text($line, 11);
        }
        $this->y -= 4;

        return $this;
    }

    /** @param  array<int, string>  $items */
    public function bullets(array $items): static
    {
        foreach ($items as $item) {
            $lines = explode("\n", wordwrap($item, 80, "\n", true));
            foreach ($lines as $i => $line) {
                $this->text(($i === 0 ? '-  ' : '   ').$line, 11, false, 0, 12);
            }
        }
        $this->y -= 4;

        return $this;
    }

    public function render(): string
    {
        $this->closePage();

        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $kids = [];
        $pageCount = count($this->pages);
        for ($i = 0; $i < $pageCount; $i++) {
            $kids[] = (5 + $i * 2).' 0 R';
        }
        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $kids).'] /Count '.$pageCount.' >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        foreach ($this->pages as $i => $content) {
            $pageObj = 5 + $i * 2;
            $contentObj = $pageObj + 1;
            $objects[$pageObj] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 '.self::PAGE_WIDTH.' '.self::PAGE_HEIGHT.'] '
                .'/Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents '.$contentObj.' 0 R >>';
            $objects[$contentObj] = '<< /Length '.strlen($content)." >>\nstream\n".$content."\nendstream";
        }

        $out = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $n => $body) {
            $offsets[$n] = strlen($out);
            $out .= "{$n} 0 obj\n{$body}\nendobj\n";
        }

        $xref = strlen($out);
        $out .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $out .= sprintf("%010d 00000 n \n", $offset);
        }
        $out .= 'trailer << /Size '.(count($objects) + 1)." /Root 1 0 R /Info << /Title ({$this->escape($this->title)}) >> >>\n";
        $out .= "startxref\n{$xref}\n%%EOF\n";

        return $out;
    }

    private function text(string $text, int $size, bool $bold = false, float $gray = 0, int $indent = 0): void
    {
        $this->ensureSpace($size + 6);
        $this->y -= $size + 3;
        $x = self::MARGIN + $indent;
        $this->stream .= sprintf(
            "BT /%s %d Tf %.2f g %d %.2f Td (%s) Tj ET\n",
            $bold ? 'F2' : 'F1', $size, $gray, $x, $this->y, $this->escape($text)
        );
    }

    private function ensureSpace(float $needed): void
    {
        if ($this->y - $needed < self::MARGIN) {
            $this->closePage();
            $this->newPage();
        }
    }

    private function newPage(): void
    {
        $this->stream = '';
        $this->y = self::PAGE_HEIGHT - self::MARGIN;
    }

    private function closePage(): void
    {
        if ($this->stream === '') {
            return;
        }

        $footer = sprintf(
            "BT /F1 9 Tf 0.5 g %d 30 Td (%s - page %d) Tj ET\n",
            self::MARGIN, $this->escape('The Way to Fluency'), count($this->pages) + 1
        );
        $this->pages[] = $this->stream.$footer;
        $this->stream = '';
    }

    private function escape(string $text): string
    {
        $text = preg_replace('/[^\x20-\x7E]/', '', $text) ?? '';

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
