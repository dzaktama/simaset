<?php

namespace App\Services;

use ZipArchive;
use Illuminate\Support\Facades\Response;

class SimpleXlsx
{
    /**
     * Generate and download a simple XLSX file from an array of rows.
     * 
     * @param array $rows Array of arrays (data rows)
     * @param string $fileName Filename for download
     */
    public static function download(array $rows, string $fileName = 'export.xlsx')
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return response()->json(['error' => 'Cannot create zip file'], 500);
        }

        // 1. [Content_Types].xml
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');

        // 2. _rels/.rels
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');

        // 3. xl/workbook.xml
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets></workbook>');

        // 4. xl/_rels/workbook.xml.rels
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');

        // 5. xl/worksheets/sheet1.xml - Build the sheet data
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
        
        foreach ($rows as $rowIndex => $row) {
            // Row index is 1-based
            $r = $rowIndex + 1;
            $xml .= '<row r="' . $r . '">';
            $colIndex = 0;
            foreach ($row as $cellValue) {
                // Simplest approach: ALL columns as Inline String (t="inlineStr")
                // This avoids managing a Shared Strings table.
                $val = htmlspecialchars($cellValue ?? '', ENT_XML1, 'UTF-8');
                
                // Column letters (e.g., A, B, ... AA) not strictly required in <c> tag if order is preserved, but good practice.
                // We'll omit 'r' attribute in cell to be faster/simpler, Excel handles it.
                // Or better, just plain <c t="inlineStr"><is><t>...</t></is></c>
                
                $xml .= '<c t="inlineStr"><is><t>' . $val . '</t></is></c>';
                $colIndex++;
            }
            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $xml);

        $zip->close();

        return Response::download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
