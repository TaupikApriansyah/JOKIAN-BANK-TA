<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;

/**
 * Pembuat PDF sederhana untuk laporan SIBERKAS.
 * Tidak memakai library tambahan supaya langsung bisa dipakai di shared hosting.
 */
class SimplePdfExport
{
    public function download(string $filename, string $title, string $subtitle, array $summary, array $headers, array $rows): Response
    {
        $pages = [];
        $page = $this->newPage($title, $subtitle);
        $y = 770;

        foreach ($summary as $label => $value) {
            if ($y < 120) {
                $pages[] = $page;
                $page = $this->newPage($title . ' (lanjutan)', $subtitle);
                $y = 770;
            }

            $page[] = $this->text(48, $y, 9, $label . ':', true);
            $page[] = $this->text(220, $y, 9, (string) $value, false);
            $y -= 15;
        }

        $y -= 12;
        if ($y < 120) {
            $pages[] = $page;
            $page = $this->newPage($title . ' (lanjutan)', $subtitle);
            $y = 770;
        }

        $page[] = $this->text(48, $y, 10, 'Detail Data', true);
        $y -= 18;
        $page[] = $this->tableHeader($headers, $y);
        $y -= 14;

        foreach ($rows as $row) {
            if ($y < 72) {
                $pages[] = $page;
                $page = $this->newPage($title . ' (lanjutan)', $subtitle);
                $y = 770;
                $page[] = $this->tableHeader($headers, $y);
                $y -= 14;
            }

            $page[] = $this->tableRow($row, $y);
            $y -= 12;
        }

        if (count($rows) === 0) {
            $page[] = $this->text(48, $y, 9, 'Tidak ada data untuk periode atau filter yang dipilih.', false);
        }

        $pages[] = $page;
        $pdf = $this->buildPdf($pages);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->safeFilename($filename) . '"',
            'Cache-Control' => 'no-store, private',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function newPage(string $title, string $subtitle): array
    {
        return [
            $this->text(48, 806, 16, 'SIBERKAS', true),
            $this->text(48, 787, 11, $title, true),
            $this->text(48, 772, 8, $subtitle, false),
            '0.30 0.55 0.53 RG 48 762 m 547 762 l S',
        ];
    }

    private function tableHeader(array $headers, int $y): string
    {
        return $this->text(48, $y, 7, implode('  |  ', array_map(fn ($header) => strtoupper((string) $header), $headers)), true);
    }

    private function tableRow(array $row, int $y): string
    {
        $parts = [];
        foreach ($row as $value) {
            $parts[] = $this->shorten((string) $value, 23);
        }

        return $this->text(48, $y, 7, implode('  |  ', $parts), false);
    }

    private function text(int $x, int $y, int $size, string $text, bool $bold): string
    {
        $font = $bold ? 'F2' : 'F1';
        $safe = $this->escape($this->latin($text));

        return "BT /{$font} {$size} Tf 0.13 0.25 0.27 rg 1 0 0 1 {$x} {$y} Tm ({$safe}) Tj ET";
    }

    private function buildPdf(array $pages): string
    {
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        $kids = [];
        foreach ($pages as $index => $content) {
            $pageObject = 5 + ($index * 2);
            $streamObject = $pageObject + 1;
            $stream = implode("\n", $content);

            $objects[$pageObject] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents ' . $streamObject . ' 0 R >>';
            $objects[$streamObject] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
            $kids[] = $pageObject . ' 0 R';
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($pages) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $body . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $maxId = max(array_keys($objects));
        $pdf .= 'xref' . "\n0 " . ($maxId + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $maxId; $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i] ?? 0) . "\n";
        }
        $pdf .= 'trailer' . "\n<< /Size " . ($maxId + 1) . " /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }

    private function shorten(string $value, int $length): string
    {
        $value = preg_replace('/\s+/', ' ', trim($value)) ?: '-';
        if (mb_strlen($value) <= $length) {
            return $value;
        }

        return mb_substr($value, 0, max(1, $length - 3)) . '...';
    }

    private function latin(string $text): string
    {
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        return $converted === false ? $text : $converted;
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ' '], $text);
    }

    private function safeFilename(string $filename): string
    {
        return preg_replace('/[^A-Za-z0-9._-]/', '_', $filename) ?: 'laporan-siberkas.pdf';
    }
}
