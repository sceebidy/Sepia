<?php

namespace App\Http\Controllers;

use App\Models\AnalisisKasus;
use App\Models\Folder;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\IOFactory;

class ExportDocxController extends Controller
{
    // Bersihkan teks dari karakter tidak valid XML
   private function b(?string $teks): string
{
    if (!$teks) return '';
    $teks = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $teks);
    $teks = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $teks);
    $teks = str_replace("\x00", '', $teks);
    $teks = str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $teks);
    return trim($teks);
}

    
    // Decode JSON yang mungkin single atau double encoded
    private function decodeJson($val): array
    {
        if (is_array($val)) return $val;
        if (!$val) return [];
        $result = json_decode($val, true);
        if (is_string($result)) {
            $result = json_decode($result, true);
        }
        return is_array($result) ? $result : [];
    }

    public function export(Folder $folder, AnalisisKasus $analisis)
    {
        $analisis->load([
            'swotItems',
            'aktor',
            'timeline',
            'rekomendasi',
            'riskItems',
            'confidence',
        ]);

        $risikoVal    = (float) $analisis->tingkat_risiko;
        $klasifikasi  = $this->b($analisis->klasifikasi_dokumen
            ?? ($risikoVal >= 7 ? 'RAHASIA' : ($risikoVal >= 4 ? 'TERBATAS' : 'BIASA')));
        $perihal      = $this->b($analisis->perihal ?? 'Perkembangan Situasi ' . $analisis->judul);
        $wilayah      = $this->b($analisis->wilayah ?? $analisis->judul);
        $nomorLap     = 'SEPIA-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $analisis->judul), 0, 6)) . '-' . date('MY') . '-' . str_pad($analisis->id, 3, '0', STR_PAD_LEFT);
        $statusWaspada = $risikoVal >= 8 ? 'WASPADA MERAH' : ($risikoVal >= 6 ? 'WASPADA KUNING' : 'KONDUSIF');

        $faktaFakta   = $this->decodeJson($analisis->fakta_fakta);
        $jabatanRek   = $this->decodeJson($analisis->jabatan_rekomendasi);
        $earlyWarning = $this->decodeJson($analisis->early_warning);
        $swotGroups   = $analisis->swotItems->groupBy('tipe');

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        $section = $phpWord->addSection([
            'marginTop'    => 1418,
            'marginBottom' => 1134,
            'marginLeft'   => 1701,
            'marginRight'  => 1134,
        ]);

        // KOP
        $section->addText('LAPORAN INTELIJEN SITUASIONAL', ['bold' => true, 'size' => 15, 'name' => 'Times New Roman', 'allCaps' => true], ['align' => Jc::CENTER, 'spaceAfter' => 40]);
        $section->addText('SISTEM ANALITIK INTELIJEN DAN HUKUM - SEPIA', ['bold' => true, 'size' => 11, 'name' => 'Times New Roman'], ['align' => Jc::CENTER, 'spaceAfter' => 30]);
        $section->addText('Dokumen Resmi - Bersifat ' . $klasifikasi, ['size' => 9, 'color' => '64748b'], ['align' => Jc::CENTER, 'spaceAfter' => 80]);

        $kopLine = $section->addTable(['borderSize' => 0]);
        $kopLine->addRow(20);
        $kopLine->addCell(9000, ['bgColor' => '000000'])->addText('', ['size' => 1]);
        $section->addTextBreak(1);

        $section->addText('KLASIFIKASI: ' . $klasifikasi, ['bold' => true, 'size' => 12, 'allCaps' => true], ['align' => Jc::CENTER, 'spaceBefore' => 80, 'spaceAfter' => 80]);

        // INFO HEADER
        $infoTable = $section->addTable(['borderSize' => 0, 'borderColor' => 'ffffff']);
        $tanggalAnalisis = $analisis->tanggal_analisis
            ? strtoupper(\Carbon\Carbon::parse($analisis->tanggal_analisis)->format('d F Y'))
            : strtoupper(now()->format('d F Y'));

        foreach ([
            ['Perihal',          $perihal],
            ['Wilayah',          $wilayah],
            ['Tanggal Analisis', $tanggalAnalisis],
            ['Tingkat Situasi',  $statusWaspada . ' (Risiko ' . $risikoVal . '/10)'],
        ] as [$key, $val]) {
            $infoTable->addRow();
            $infoTable->addCell(2500)->addText($this->b($key), ['bold' => true, 'size' => 11]);
            $infoTable->addCell(500)->addText(':', ['size' => 11]);
            $infoTable->addCell(6000)->addText($this->b($val), ['size' => 11]);
        }

        $section->addTextBreak(1);
        $sepLine = $section->addTable(['borderSize' => 0]);
        $sepLine->addRow(10);
        $sepLine->addCell(9000, ['bgColor' => '000000'])->addText('', ['size' => 1]);
        $section->addTextBreak(1);

        // RINGKASAN EKSEKUTIF
        if ($analisis->ringkasan_eksekutif) {
            $section->addText($this->b($analisis->ringkasan_eksekutif), ['size' => 11, 'italic' => true], ['spaceAfter' => 120, 'lineHeight' => 1.5]);
            $sep2 = $section->addTable(['borderSize' => 0]);
            $sep2->addRow(10);
            $sep2->addCell(9000, ['bgColor' => '000000'])->addText('', ['size' => 1]);
            $section->addTextBreak(1);
        }

        // ANALISIS SWOT
        $section->addText('ANALISIS SWOT', ['bold' => true, 'size' => 12, 'underline' => 'single', 'allCaps' => true], ['spaceBefore' => 100, 'spaceAfter' => 100]);

        foreach ([
            'S' => ['S - Strengths (Kekuatan)',   'Faktor kekuatan internal yang mendukung penanganan.'],
            'W' => ['W - Weaknesses (Kelemahan)',  'Faktor kelemahan internal yang menghambat penanganan.'],
            'O' => ['O - Opportunities (Peluang)', 'Faktor peluang eksternal yang dapat dimanfaatkan.'],
            'T' => ['T - Threats (Ancaman)',       'Faktor ancaman eksternal yang perlu diwaspadai.'],
        ] as $tipe => [$label, $desc]) {
            $section->addText($label, ['bold' => true, 'size' => 11], ['spaceBefore' => 80, 'spaceAfter' => 30]);
            $section->addText($desc, ['size' => 9, 'italic' => true, 'color' => '64748b'], ['spaceAfter' => 40]);
            $items = $swotGroups->get($tipe, collect());
            if ($items->isEmpty()) {
                $section->addText('Tidak ada poin untuk kategori ini.', ['size' => 11, 'italic' => true, 'color' => '94a3b8'], ['spaceAfter' => 40]);
            } else {
                foreach ($items as $item) {
                    $section->addListItem($this->b($item->isi), 0, ['size' => 11], ['spaceAfter' => 30]);
                }
            }
        }

        $section->addTextBreak(1);
        $sep3 = $section->addTable(['borderSize' => 0]);
        $sep3->addRow(10);
        $sep3->addCell(9000, ['bgColor' => '000000'])->addText('', ['size' => 1]);
        $section->addTextBreak(1);

        // I. FAKTA-FAKTA
        $section->addText('I. FAKTA-FAKTA', ['bold' => true, 'size' => 12, 'underline' => 'single', 'allCaps' => true], ['spaceBefore' => 100, 'spaceAfter' => 100]);

        if (!empty($faktaFakta)) {
            foreach ($faktaFakta as $fakta) {
                $section->addText($this->b(($fakta['huruf'] ?? '') . '. ' . ($fakta['judul'] ?? '')), ['bold' => true, 'size' => 11], ['spaceBefore' => 80, 'spaceAfter' => 40]);
                if (!empty($fakta['isi'])) {
                    $section->addText($this->b($fakta['isi']), ['size' => 11], ['spaceAfter' => 80, 'lineHeight' => 1.5]);
                }
            }
        } elseif ($analisis->timeline->isNotEmpty()) {
            foreach ($analisis->timeline as $tl) {
                $section->addText($this->b($tl->tanggal ?? '-'), ['bold' => true, 'size' => 11], ['spaceBefore' => 60, 'spaceAfter' => 30]);
                $section->addText($this->b($tl->keterangan ?? '-'), ['size' => 11], ['spaceAfter' => 60, 'lineHeight' => 1.5]);
            }
        }

        // II. ANALISIS INTELIJEN
        $section->addText('II. ANALISIS INTELIJEN', ['bold' => true, 'size' => 12, 'underline' => 'single', 'allCaps' => true], ['spaceBefore' => 160, 'spaceAfter' => 100]);
        $section->addText(
            $this->b($analisis->analisis_intelijen ?? $analisis->ringkasan_intelijen ?? $analisis->deskripsi ?? '-'),
            ['size' => 11],
            ['spaceAfter' => 80, 'lineHeight' => 1.5]
        );
        if ($analisis->interpretasi) {
            $section->addText($this->b($analisis->interpretasi), ['size' => 11], ['spaceAfter' => 80, 'lineHeight' => 1.5]);
        }

        // III. REKOMENDASI
        $section->addText('III. REKOMENDASI', ['bold' => true, 'size' => 12, 'underline' => 'single', 'allCaps' => true], ['spaceBefore' => 160, 'spaceAfter' => 100]);

        if (!empty($jabatanRek)) {
            $no = 1;
            foreach ($jabatanRek as $jabatan) {
                $section->addText($no . '. ' . $this->b($jabatan['nama_jabatan'] ?? '-'), ['bold' => true, 'size' => 11], ['spaceBefore' => 80, 'spaceAfter' => 40]);
                foreach ($jabatan['poin'] ?? [] as $p) {
    $teks = is_array($p) ? ($p['rekomendasi'] ?? '') : $p;
    $section->addListItem($this->b($teks), 0, ['size' => 11], ['spaceAfter' => 30, 'lineHeight' => 1.5]);
}
                $no++;
            }
        } elseif ($analisis->rekomendasi->isNotEmpty()) {
            $no = 1;
            foreach ($analisis->rekomendasi->groupBy('judul') as $jabatan => $poinList) {
                $section->addText($no . '. ' . $this->b($jabatan), ['bold' => true, 'size' => 11], ['spaceBefore' => 80, 'spaceAfter' => 40]);
                foreach ($poinList as $p) {
                    $section->addListItem($this->b($p->deskripsi), 0, ['size' => 11], ['spaceAfter' => 30]);
                }
                $no++;
            }
        }

        // IV. EARLY WARNING
        if (!empty($earlyWarning)) {
            $section->addText('IV. INDIKATOR PERINGATAN DINI', ['bold' => true, 'size' => 12, 'underline' => 'single', 'allCaps' => true], ['spaceBefore' => 160, 'spaceAfter' => 100]);
                    foreach ($earlyWarning as $i => $ew) {
                $teks = is_array($ew) ? ($ew['indikator'] ?? '') : $ew;
                $section->addText(($i + 1) . '. ' . $this->b($teks), ['size' => 11], ['spaceAfter' => 40, 'lineHeight' => 1.5]);
            }
        }

        // CATATAN ANALIS
        if ($analisis->catatan_analis) {
            $section->addTextBreak(1);
            $catatanTable = $section->addTable(['borderSize' => 12, 'borderColor' => '000000', 'cellMargin' => 100]);
            $catatanTable->addRow();
            $catatanCell = $catatanTable->addCell(9000);
            $catatanCell->addText('CATATAN PERWIRA ANALIS:', ['bold' => true, 'size' => 10, 'allCaps' => true], ['spaceAfter' => 40]);
            $catatanCell->addText('"' . $this->b($analisis->catatan_analis) . '"', ['size' => 11, 'italic' => true], ['lineHeight' => 1.5]);
        }

        // PENUTUP
        $section->addTextBreak(1);
        $section->addText('PENUTUP', ['bold' => true, 'size' => 12, 'underline' => 'single', 'allCaps' => true], ['spaceBefore' => 100, 'spaceAfter' => 80]);

        $proyeksi = $risikoVal >= 7 ? 'berpotensi memanas dan memerlukan penanganan segera'
            : ($risikoVal >= 5 ? 'tetap dinamis dan memerlukan pemantauan intensif'
            : 'tetap kondusif apabila rekomendasi di atas dilaksanakan dengan baik');

        $section->addText('Demikian laporan situasional ini disusun berdasarkan hasil monitoring dan analisis data di wilayah ' . $wilayah . '. Ke depan situasi diperkirakan ' . $proyeksi . '.', ['size' => 11], ['spaceAfter' => 80, 'lineHeight' => 1.5]);
        $section->addText('DEMIKIAN LAPORAN INI DIBUAT UNTUK DITINDAKLANJUTI.', ['bold' => true, 'size' => 12, 'allCaps' => true], ['align' => Jc::CENTER, 'spaceBefore' => 120, 'spaceAfter' => 120]);

        // FOOTER
        $footerLine = $section->addTable(['borderSize' => 0]);
        $footerLine->addRow(20);
        $footerLine->addCell(9000, ['bgColor' => '000000'])->addText('', ['size' => 1]);
        $section->addTextBreak(1);

        $footerTable = $section->addTable(['borderSize' => 0, 'borderColor' => 'ffffff']);
        $footerTable->addRow();
        $footerTable->addCell(3000)->addText($klasifikasi, ['bold' => true, 'size' => 9, 'color' => '555555']);
        $footerTable->addCell(3000)->addText('SEPIA - Sistem Analitik Intelijen Dan Hukum', ['size' => 9, 'color' => '555555'], ['align' => Jc::CENTER]);
        $footerTable->addCell(3000)->addText(now()->format('d M Y'), ['size' => 9, 'color' => '555555'], ['align' => Jc::RIGHT]);

        // GENERATE & DOWNLOAD
        $filename = str_replace(['/', '\\', ' '], '-', $nomorLap) . '-' . date('Ymd') . '.docx';
        $tmpDir   = storage_path('app/temp');
        $tmpPath  = $tmpDir . '/' . $filename;

        if (!file_exists($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tmpPath);

        return response()->download($tmpPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}