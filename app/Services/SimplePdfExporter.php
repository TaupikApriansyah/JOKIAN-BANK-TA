<?php

namespace App\Services;

/** Dependency-free landscape A4 PDF table generator for direct download reports. */
class SimplePdfExporter
{
    /** @param array<int,string> $headers @param array<int,array<int,scalar|null>> $rows */
    public function createTablePdf(string $title, array $headers, array $rows): string
    {
        $rowsPerPage = 21;
        $pages = array_chunk($rows, $rowsPerPage) ?: [[]];
        $pageObjects = [];
        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = ''; // Filled after page object numbers are known.
        $next = 3;

        foreach ($pages as $pageNumber => $pageRows) {
            $pageObject = $next++; $contentObject = $next++;
            $pageObjects[] = $pageObject;
            $objects[$pageObject] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 '.(count($pages)*2+3).' 0 R >> >> /Contents '.$contentObject.' 0 R >>';
            $content = $this->pageContent($title, $headers, $pageRows, $pageNumber+1, count($pages));
            $objects[$contentObject] = '<< /Length '.strlen($content).' >>' . "\nstream\n".$content."\nendstream";
        }
        $fontObject = $next;
        $objects[$fontObject] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $kids = implode(' ', array_map(fn (int $id) => $id.' 0 R', $pageObjects));
        $objects[2] = '<< /Type /Pages /Kids ['.$kids.'] /Count '.count($pageObjects).' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n"; $offsets=[0];
        foreach ($objects as $id=>$object) { $offsets[$id]=strlen($pdf); $pdf.=$id." 0 obj\n".$object."\nendobj\n"; }
        $xref=strlen($pdf); $max=max(array_keys($objects));
        $pdf.='xref' . "\n0 ".($max+1)."\n0000000000 65535 f \n";
        for($i=1;$i<=$max;$i++) { $pdf.=sprintf('%010d 00000 n ', $offsets[$i] ?? 0)."\n"; }
        $pdf.='trailer << /Size '.($max+1).' /Root 1 0 R >>' . "\nstartxref\n".$xref."\n%%EOF";
        return $pdf;
    }
    /** @param array<int,string> $headers @param array<int,array<int,scalar|null>> $rows */
    private function pageContent(string $title, array $headers, array $rows, int $page, int $total): string
    {
        $x=30; $widths=[95,80,145,120,75,75,90,92]; $tableWidth=array_sum($widths); $y=518; $rowHeight=20; $content='';
        $content .= $this->text(30, 565, 16, 'BANK X — '.$title);
        $content .= $this->text(30, 546, 8.5, 'Dicetak otomatis: '.now()->format('d/m/Y H:i').' WIB');
        $content .= $this->text(710, 546, 8.5, 'Halaman '.$page.' / '.$total);
        $content .= "q\n0.063 0.231 0.388 rg\n30 518 ".$tableWidth." 22 re f\nQ\n";
        $cursor=$x; foreach($headers as $i=>$header){$content.=$this->text($cursor+4,525,8.1,$header,true);$cursor+=$widths[$i]??80;}
        foreach($rows as $ri=>$row){$rowY=$y-22-(($ri)*$rowHeight); if($ri%2===1)$content.="q\n0.96 0.98 1 rg\n30 {$rowY} {$tableWidth} {$rowHeight} re f\nQ\n"; $cursor=$x; foreach($headers as $ci=>$_){$content.=$this->text($cursor+4,$rowY+6,7.3,$this->short((string)($row[$ci]??''),$widths[$ci]??80));$cursor+=$widths[$ci]??80;} }
        $bottom=$y-22-(count($rows)*$rowHeight); $content.="q\n0.79 0.86 0.92 RG\n0.45 w\n"; $content.="30 {$bottom} {$tableWidth} ".(540-$bottom)." re S\n"; $cursor=$x; foreach($widths as $w){$content.="{$cursor} {$bottom} m {$cursor} 540 l S\n";$cursor+=$w;} $content.="{$cursor} {$bottom} m {$cursor} 540 l S\n"; for($i=0;$i<=count($rows)+1;$i++){ $lineY=540-($i===0?0:22+(($i-1)*$rowHeight)); if($lineY>=$bottom)$content.="30 {$lineY} m ".(30+$tableWidth)." {$lineY} l S\n"; } $content.="Q\n";
        return $content;
    }
    private function text(float $x,float $y,float $size,string $text,bool $white=false): string { $color=$white?'1 1 1 rg':'0.12 0.18 0.24 rg'; return "BT\n{$color}\n/F1 {$size} Tf\n1 0 0 1 {$x} {$y} Tm\n(".$this->escape($text).") Tj\nET\n"; }
    private function short(string $value,int $width): string { $max=max(9,(int)floor($width/5.15)); if (function_exists('mb_strimwidth')) { return mb_strimwidth($value,0,$max,'…','UTF-8'); } return strlen($value) > $max ? substr($value, 0, max(1, $max - 3)).'...' : $value; }
    private function escape(string $value): string { $value=iconv('UTF-8','Windows-1252//TRANSLIT//IGNORE',$value) ?: $value; return str_replace(['\\','(',')',"\r","\n"],['\\\\','\\(', '\\)', '', ' '],$value); }
}
