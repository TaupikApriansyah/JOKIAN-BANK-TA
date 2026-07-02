<?php

namespace App\Services;


/**
 * Small XLSX writer for fixed tabular operational reports.
 * Keeps the project dependency-light; no external spreadsheet package is needed.
 */
class SpreadsheetExporter
{
    /** @param array<int, string> $headers @param array<int, array<int, scalar|null>> $rows */
    public function createXlsx(array $headers, array $rows, string $temporaryName): string
    {
        $directory = storage_path('app/exports');
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Folder temporary export tidak dapat dibuat.');
        }

        $path = $directory.'/'.$temporaryName;
        $xlsx = $this->zip([
            '[Content_Types].xml' => $this->contentTypes(),
            '_rels/.rels' => $this->rootRelationships(),
            'docProps/app.xml' => $this->appProperties(),
            'docProps/core.xml' => $this->coreProperties(),
            'xl/workbook.xml' => $this->workbook(),
            'xl/_rels/workbook.xml.rels' => $this->workbookRelationships(),
            'xl/styles.xml' => $this->styles(),
            'xl/worksheets/sheet1.xml' => $this->sheet($headers, $rows),
        ]);
        if (file_put_contents($path, $xlsx) === false) {
            throw new \RuntimeException('File Excel tidak dapat dibuat.');
        }

        return $path;
    }

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            .'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            .'</Types>';
    }
    private function rootRelationships(): string { return '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/></Relationships>'; }
    private function appProperties(): string { return '<?xml version="1.0" encoding="UTF-8"?><Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"><Application>Bank X Administration</Application></Properties>'; }
    private function coreProperties(): string { $date=now()->utc()->format('Y-m-d\\TH:i:s\\Z'); return '<?xml version="1.0" encoding="UTF-8"?><cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><dc:title>Laporan Transaksi Administrasi</dc:title><dc:creator>Bank X</dc:creator><dcterms:created xsi:type="dcterms:W3CDTF">'.$date.'</dcterms:created></cp:coreProperties>'; }
    private function workbook(): string { return '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Transaksi" sheetId="1" r:id="rId1"/></sheets></workbook>'; }
    private function workbookRelationships(): string { return '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>'; }
    private function styles(): string { return '<?xml version="1.0" encoding="UTF-8"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="10"/><color rgb="FF1E293B"/><name val="Calibri"/></font><font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font></fonts><fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF103B63"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="1"><border><left/><right/><top/><bottom/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="1" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs></styleSheet>'; }
    /** @param array<int,string> $headers @param array<int,array<int,scalar|null>> $rows */
    private function sheet(array $headers, array $rows): string
    {
        $lastColumn = $this->columnName(max(1, count($headers)));
        $xml = '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><dimension ref="A1:'.$lastColumn.(count($rows)+1).'"/><sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews><cols>';
        foreach ($headers as $index => $header) { $xml .= '<col min="'.($index+1).'" max="'.($index+1).'" width="'.max(13, min(32, strlen($header)+6)).'" customWidth="1"/>'; }
        $xml .= '</cols><sheetData><row r="1" ht="22" customHeight="1">';
        foreach ($headers as $index => $header) { $xml .= $this->cell($this->columnName($index+1).'1', $header, 1); }
        $xml .= '</row>';
        foreach ($rows as $rowIndex => $row) { $excelRow=$rowIndex+2; $xml.='<row r="'.$excelRow.'">'; foreach ($headers as $columnIndex => $_) { $xml.=$this->cell($this->columnName($columnIndex+1).$excelRow, (string)($row[$columnIndex] ?? ''), 0); } $xml.='</row>'; }
        return $xml.'</sheetData><autoFilter ref="A1:'.$lastColumn.(count($rows)+1).'"/></worksheet>';
    }
    private function cell(string $reference, string $value, int $style): string { $escaped=htmlspecialchars($value, ENT_QUOTES|ENT_XML1, 'UTF-8'); return '<c r="'.$reference.'" t="inlineStr" s="'.$style.'"><is><t xml:space="preserve">'.$escaped.'</t></is></c>'; }
    private function columnName(int $index): string { $name=''; while($index>0){$index--; $name=chr(65+($index%26)).$name; $index=intdiv($index,26);} return $name; }

    /** @param array<string,string> $entries */
    private function zip(array $entries): string
    {
        $data = '';
        $central = '';
        $offset = 0;
        $count = 0;

        foreach ($entries as $name => $contents) {
            $name = str_replace('\\', '/', $name);
            $crc = crc32($contents);
            $size = strlen($contents);
            $nameLength = strlen($name);
            $local = pack('VvvvvvVVVvv', 0x04034b50, 20, 0, 0, 0, 0, $crc, $size, $size, $nameLength, 0)
                .$name.$contents;
            $data .= $local;
            $central .= pack('VvvvvvvVVVvvvvvVV',
                0x02014b50, 20, 20, 0, 0, 0, 0, $crc, $size, $size,
                $nameLength, 0, 0, 0, 0, 0, $offset
            ).$name;
            $offset += strlen($local);
            $count++;
        }

        return $data.$central.pack('VvvvvVVv', 0x06054b50, 0, 0, $count, $count, strlen($central), strlen($data), 0);
    }

}
