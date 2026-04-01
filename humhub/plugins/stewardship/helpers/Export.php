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
            case 'xls':
                $response->headers->set('Content-Type', 'application/vnd.ms-excel');
                $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.xls"');
                $lines = [implode("\t", $headers)];
                foreach ($rows as $row) {
                    $lines[] = implode("\t", $row);
                }
                $response->content = "\xEF\xBB\xBF" . implode("\n", $lines);
                break;

            case 'html':
                $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
                $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.html"');
                $h = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . htmlspecialchars($title) . '</title>';
                $h .= '<style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:6px 10px;text-align:left}th{background:#f5f5f5}</style>';
                $h .= '</head><body><h2>' . htmlspecialchars($title) . '</h2>';
                $h .= '<p>Generated: ' . date('Y-m-d H:i:s') . '</p><table><thead><tr>';
                foreach ($headers as $hdr) {
                    $h .= '<th>' . htmlspecialchars($hdr) . '</th>';
                }
                $h .= '</tr></thead><tbody>';
                foreach ($rows as $row) {
                    $h .= '<tr>';
                    foreach ($row as $cell) {
                        $h .= '<td>' . htmlspecialchars((string) $cell) . '</td>';
                    }
                    $h .= '</tr>';
                }
                $h .= '</tbody></table></body></html>';
                $response->content = $h;
                break;

            default: // csv
                $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
                $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');
                $output = fopen('php://temp', 'r+');
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, $headers);
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
                rewind($output);
                $response->content = stream_get_contents($output);
                fclose($output);
                break;
        }

        return $response;
    }
}
