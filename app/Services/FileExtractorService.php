<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileExtractorService
{
    /**
     * Ekstrak teks dari file berdasarkan tipe/ekstensi
     * Mengembalikan teks bersih atau null jika gagal
     */
    public function extract(string $filePath, string $fileNama): ?string
    {
        $ext = strtolower(pathinfo($fileNama, PATHINFO_EXTENSION));
        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            // Coba path langsung
            $fullPath = storage_path('app/' . $filePath);
            if (!file_exists($fullPath)) {
                return null;
            }
        }

        try {
            return match($ext) {
                'pdf'          => $this->extractPdf($fullPath),
                'docx'         => $this->extractDocx($fullPath),
                'doc'          => $this->extractDoc($fullPath),
                'xlsx', 'xls'  => $this->extractExcel($fullPath),
                'txt', 'csv'   => $this->extractText($fullPath),
                default        => null,
            };
        } catch (\Exception $e) {
            \Log::warning("FileExtractor gagal untuk {$fileNama}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Ekstrak teks dari PDF
     */
    private function extractPdf(string $path): ?string
    {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($path);
        $teks   = $pdf->getText();

        return $this->bersihkan($teks);
    }

    /**
     * Ekstrak teks dari DOCX
     */
    private function extractDocx(string $path): ?string
    {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
        $teks    = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $teks .= $this->ambilTeksElement($element);
            }
        }

        return $this->bersihkan($teks);
    }

    /**
     * Rekursif ambil teks dari elemen PhpWord
     */
    private function ambilTeksElement($element): string
    {
        $teks = '';

        if (method_exists($element, 'getText')) {
            $teks .= $element->getText() . ' ';
        } elseif (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $teks .= $this->ambilTeksElement($child);
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $child) {
                $teks .= $this->ambilTeksElement($child);
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $cellEl) {
                        $teks .= $this->ambilTeksElement($cellEl) . ' | ';
                    }
                }
                $teks .= "\n";
            }
        }

        return $teks;
    }

    /**
     * Ekstrak teks dari DOC (format lama — pakai antiword jika tersedia, fallback regex)
     */
    private function extractDoc(string $path): ?string
    {
        // Coba baca sebagai binary dan ekstrak teks
        $content = file_get_contents($path);
        if ($content === false) return null;

        // Ambil teks ASCII yang readable
        $teks = '';
        $len  = strlen($content);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($content[$i]);
            if (($c >= 32 && $c <= 126) || $c === 10 || $c === 13) {
                $teks .= $content[$i];
            }
        }

        // Filter baris yang meaningful (panjang > 5 karakter)
        $lines  = explode("\n", $teks);
        $result = array_filter($lines, fn($l) => strlen(trim($l)) > 5);

        return $this->bersihkan(implode("\n", $result));
    }

    /**
     * Ekstrak teks dari Excel (XLSX/XLS)
     */
    private function extractExcel(string $path): ?string
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $teks        = '';

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $teks .= "=== Sheet: " . $sheet->getTitle() . " ===\n";

            $highRow = $sheet->getHighestRow();
            $highCol = $sheet->getHighestColumn();

            for ($row = 1; $row <= min($highRow, 500); $row++) {
                $rowData = [];
                for ($col = 'A'; $col <= $highCol; $col++) {
                    $val = $sheet->getCell($col . $row)->getFormattedValue();
                    if (trim($val) !== '') {
                        $rowData[] = $val;
                    }
                }
                if (!empty($rowData)) {
                    $teks .= implode(' | ', $rowData) . "\n";
                }
            }
        }

        return $this->bersihkan($teks);
    }

    /**
     * Baca file teks biasa
     */
    private function extractText(string $path): ?string
    {
        $konten = file_get_contents($path);
        if ($konten === false) return null;
        return $this->bersihkan($konten);
    }

    /**
     * Bersihkan teks — hapus karakter aneh, normalisasi spasi, batasi panjang
     */
    private function bersihkan(string $teks, int $maxChars = 8000): string
    {
        // Normalisasi newline
        $teks = str_replace(["\r\n", "\r"], "\n", $teks);

        // Hapus karakter non-printable kecuali newline dan tab
        $teks = preg_replace('/[^\x09\x0A\x20-\x7E\xA0-\xFF]/u', ' ', $teks);

        // Hapus baris kosong berlebihan (max 2 baris kosong berturut)
        $teks = preg_replace('/\n{3,}/', "\n\n", $teks);

        // Normalisasi spasi berlebihan
        $teks = preg_replace('/[ \t]+/', ' ', $teks);

        $teks = trim($teks);

        // Batasi panjang
        if (strlen($teks) > $maxChars) {
            $teks = substr($teks, 0, $maxChars) . "\n\n[... dokumen dipotong untuk efisiensi analisis ...]";
        }

        return $teks;
    }
}