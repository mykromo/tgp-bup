<?php

namespace humhub\modules\stewardship\helpers;

use Yii;
use yii\web\Response;

class Export
{
    public static function download(string $format, string $filename, string $title, array $headers, array $rows)
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;

        switch ($format) {
            case 'xlsx':
                return static::xlsx($response, $filename, $title, $headers, $rows);
            case 'html':
                return static::html($response, $filename, $title, $headers, $rows);
            default:
                return static::csv($response, $filename, $headers, $rows);
        }
    }

    private static function csv($response, $filename, $headers, $rows)
    {
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $headers);
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        rewind($out);
        $response->content = stream_get_contents($out);
        fclose($out);
        return $response;
    }

    private static function html($response, $filename, $title, $headers, $rows)
    {
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.html"');
        $h = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . self::esc($title) . '</title>';
        $h .= '<style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:6px 10px;text-align:left}th{background:#f5f5f5}</style>';
        $h .= '</head><body><h2>' . self::esc($title) . '</h2><p>Generated: ' . date('Y-m-d H:i:s') . '</p><table><thead><tr>';
        foreach ($headers as $hdr) $h .= '<th>' . self::esc($hdr) . '</th>';
        $h .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $h .= '<tr>';
            foreach ($row as $cell) $h .= '<td>' . self::esc((string) $cell) . '</td>';
            $h .= '</tr>';
        }
        $h .= '</tbody></table></body></html>';
        $response->content = $h;
        return $response;
    }

    private static function xlsx($response, $filename, $title, $headers, $rows)
    {
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.xlsx"');

        $allRows = array_merge([$headers], $rows);
        $sheetXml = static::buildSheetXml($allRows);

        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new \ZipArchive();
        $zip->open($tmpFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
            . '</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . self::esc($title) . '" sheetId="1" r:id="rId1"/></sheets></workbook>');

        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml['sheet']);
        $zip->addFromString('xl/sharedStrings.xml', $sheetXml['strings']);
        $zip->close();

        $response->content = file_get_contents($tmpFile);
        unlink($tmpFile);
        return $response;
    }

    private static function buildSheetXml(array $allRows): array
    {
        $strings = [];
        $stringIndex = [];
        $sheetRows = '';

        foreach ($allRows as $r => $row) {
            $rowNum = $r + 1;
            $sheetRows .= '<row r="' . $rowNum . '">';
            foreach ($row as $c => $cell) {
                $col = chr(65 + $c);
                $cellRef = $col . $rowNum;
                $val = (string) $cell;

                if (is_numeric(str_replace([',', ' '], '', $val)) && preg_match('/^[\d,]+\.?\d*$/', str_replace(' ', '', $val))) {
                    $num = str_replace(',', '', $val);
                    $sheetRows .= '<c r="' . $cellRef . '"><v>' . $num . '</v></c>';
                } else {
                    if (!isset($stringIndex[$val])) {
                        $stringIndex[$val] = count($strings);
                        $strings[] = $val;
                    }
                    $sheetRows .= '<c r="' . $cellRef . '" t="s"><v>' . $stringIndex[$val] . '</v></c>';
                }
            }
            $sheetRows .= '</row>';
        }

        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $sheetRows . '</sheetData></worksheet>';

        $ss = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($strings) . '" uniqueCount="' . count($strings) . '">';
        foreach ($strings as $s) {
            $ss .= '<si><t>' . self::esc($s) . '</t></si>';
        }
        $ss .= '</sst>';

        return ['sheet' => $sheet, 'strings' => $ss];
    }

    private static function esc(string $val): string
    {
        return htmlspecialchars($val, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
