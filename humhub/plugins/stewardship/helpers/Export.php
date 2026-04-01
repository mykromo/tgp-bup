<?php

namespace humhub\modules\stewardship\helpers;

use Yii;

class Export
{
    /**
     * Send a CSV download response.
     */
    public static function csv(string $filename, array $headers, array $rows): void
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://temp', 'r+');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $response->content = stream_get_contents($output);
        fclose($output);
        $response->send();
        Yii::$app->end();
    }

    /**
     * Send a tab-delimited XLS download response.
     */
    public static function xls(string $filename, array $headers, array $rows): void
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.xls"');

        $lines = [];
        $lines[] = implode("\t", $headers);
        foreach ($rows as $row) {
            $lines[] = implode("\t", $row);
        }
        $response->content = chr(0xEF) . chr(0xBB) . chr(0xBF) . implode("\n", $lines);
        $response->send();
        Yii::$app->end();
    }

    /**
     * Send an HTML table download response.
     */
    public static function html(string $filename, string $title, array $headers, array $rows): void
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'text/html; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.html"');

        $h = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . htmlspecialchars($title) . '</title>';
        $h .= '<style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #ccc;padding:6px 10px;text-align:left}th{background:#f5f5f5}</style>';
        $h .= '</head><body><h2>' . htmlspecialchars($title) . '</h2>';
        $h .= '<p>Generated: ' . date('Y-m-d H:i:s') . '</p>';
        $h .= '<table><thead><tr>';
        foreach ($headers as $hdr) {
            $h .= '<th>' . htmlspecialchars($hdr) . '</th>';
        }
        $h .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $h .= '<tr>';
            foreach ($row as $cell) {
                $h .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $h .= '</tr>';
        }
        $h .= '</tbody></table></body></html>';

        $response->content = $h;
        $response->send();
        Yii::$app->end();
    }

    /**
     * Dispatch to the right format.
     */
    public static function download(string $format, string $filename, string $title, array $headers, array $rows): void
    {
        switch ($format) {
            case 'xls':
                static::xls($filename, $headers, $rows);
                break;
            case 'html':
                static::html($filename, $title, $headers, $rows);
                break;
            default:
                static::csv($filename, $headers, $rows);
        }
    }
}
