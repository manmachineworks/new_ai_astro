<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CsvExportService
{
    /**
     * Stream a CSV response for a given query and columns.
     */
    public function streamExport($filename, $query, $columns)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query, $columns) {
            $file = fopen('php://output', 'w');

            // Write Headers
            fputcsv($file, array_values($columns));

            // Chunk and write rows
            $query->chunk(1000, function ($rows) use ($file, $columns) {
                foreach ($rows as $row) {
                    $data = [];
                    foreach (array_keys($columns) as $column) {
                        $data[] = $this->formatValue($row, $column);
                    }
                    fputcsv($file, $data);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function formatValue($row, $column)
    {
        // Handle dots for relations (e.g. 'user.name')
        $value = data_get($row, $column);

        if ($value instanceof \Carbon\Carbon) {
            return $value->setTimezone('Asia/Kolkata')->toDateTimeString();
        }

        if (is_string($value) && preg_match('/_at$/', $column) && strtotime($value) !== false) {
            return \Carbon\Carbon::parse($value, 'UTC')->setTimezone('Asia/Kolkata')->toDateTimeString();
        }

        return $value;
    }
}
