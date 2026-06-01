<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SEPIA — Laporan: {{ $analisis->judul }}</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet" />
@php
  $risikoVal    = (float) $analisis->tingkat_risiko;
  $klasifikasi  = $analisis->klasifikasi_dokumen ?? ($risikoVal >= 7 ? 'RAHASIA' : ($risikoVal >= 4 ? 'TERBATAS' : 'BIASA'));
  $perihal      = $analisis->perihal ?? 'Perkembangan Situasi ' . $analisis->judul;
  $wilayah      = $analisis->wilayah ?? $analisis->judul;
  $periode      = $analisis->periode ?? now()->format('d M Y');
  $statusWaspada = $risikoVal >= 8 ? 'WASPADA MERAH' : ($risikoVal >= 6 ? 'WASPADA KUNING' : 'KONDUSIF');
  $statusColor  = $risikoVal >= 8 ? '#dc2626' : ($risikoVal >= 6 ? '#d97706' : '#16a34a');
  $faktaFakta   = $analisis->fakta_fakta ? json_decode($analisis->fakta_fakta, true) : [];
  if (is_string($faktaFakta)) $faktaFakta = json_decode($faktaFakta, true) ?? [];
  $jabatanRek   = $analisis->jabatan_rekomendasi ? json_decode($analisis->jabatan_rekomendasi, true) : [];
  if (is_string($jabatanRek)) $jabatanRek = json_decode($jabatanRek, true) ?? [];
  $earlyWarning = $analisis->early_warning ? json_decode($analisis->early_warning, true) : [];
  if (is_string($earlyWarning)) $earlyWarning = json_decode($earlyWarning, true) ?? [];
  $swotGroups   = $analisis->swotItems->groupBy('tipe');
  $analisisIntelijen = $analisis->analisis_intelijen ?? null;
  $nomorLap     = 'SEPIA/' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $analisis->judul), 0, 6)) . '/' . date('MY') . '/' . str_pad($analisis->id, 3, '0', STR_PAD_LEFT);
@endphp
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green: #1a5c2e; --green-light: #edfaf3; --green-border: #8fd4b1;
  --text: #0f172a; --text-muted: #64748b; --border: #e2e8f0;
  --bg: #f1f5f9; --nav-width: 220px;
}
html, body { height: 100%; }
body { font-family: 'Sora', sans-serif; background: var(--bg); color: var(--text); display: flex; overflow: hidden; font-size: 13px; }

/* SIDENAV */
.sidenav { width: var(--nav-width); background: var(--green); display: flex; flex-direction: column; flex-shrink: 0; }
.sidenav-brand { padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.brand-logo { font-size: 18px; font-weight: 700; letter-spacing: 0.14em; color: #fff; }
.brand-sub { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 2px; letter-spacing: 0.05em; text-transform: uppercase; }
.sidenav-section { padding: 18px 12px 8px; }
.sidenav-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.4); padding: 0 8px; margin-bottom: 6px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; margin-bottom: 2px; text-decoration: none; border: 1px solid transparent; color: rgba(255,255,255,0.72); font-size: 13px; font-weight: 500; }
.nav-item:hover { background: rgba(255,255,255,0.1); color: #fff; }
.nav-item.active { background: rgba(255,255,255,0.14); border-color: rgba(255,255,255,0.18); color: #fff; }
.nav-icon { width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; background: rgba(255,255,255,0.1); }
.nav-item.active .nav-icon { background: rgba(255,255,255,0.2); }
.nav-item-text { flex: 1; }
.nav-badge { font-size: 10px; background: rgba(255,255,255,0.2); color: #fff; padding: 2px 7px; border-radius: 20px; }
.nav-badge.alert { background: #ef4444; }
.sidenav-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 10px 12px; }
.sidenav-bottom { margin-top: auto; padding: 14px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
.user-row { display: flex; align-items: center; gap: 10px; padding: 8px 10px; }
.user-avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); color: #fff; font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; }
.user-name { font-size: 12px; font-weight: 500; color: #fff; }
.user-role { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 1px; }

/* MAIN */
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 56px; background: #fff; border-bottom: 1px solid var(--border); flex-shrink: 0; }
.topbar-left { display: flex; align-items: center; gap: 12px; }
.topbar-right { display: flex; align-items: center; gap: 6px; }
.tb-btn { padding: 7px 13px; font-size: 11.5px; border: 1px solid var(--border); border-radius: 8px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'Sora', sans-serif; font-weight: 500; transition: all 0.13s; display: flex; align-items: center; gap: 5px; text-decoration: none; }
.tb-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.tb-btn.primary { background: var(--green); color: #fff; border-color: var(--green); }
.tb-btn.warning { background: #fffbeb; color: #b45309; border-color: #fde68a; }

.content { flex: 1; overflow-y: auto; padding: 24px 28px 60px; background: var(--bg); }
.content::-webkit-scrollbar { width: 5px; }
.content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

/* EDIT PANEL */
.edit-panel { background: #fff; border: 1.5px solid var(--green-border); border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; display: none; }
.edit-panel.open { display: block; }
.edit-panel-title { font-size: 12px; font-weight: 600; color: var(--green); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.06em; }
.edit-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.edit-field label { font-size: 11px; color: var(--text-muted); font-weight: 500; display: block; margin-bottom: 4px; }
.edit-field input { width: 100%; padding: 7px 10px; border: 1px solid var(--border); border-radius: 7px; font-family: 'Sora', sans-serif; font-size: 12px; outline: none; }
.edit-field input:focus { border-color: var(--green-border); }
.edit-actions { display: flex; gap: 8px; margin-top: 12px; justify-content: flex-end; }
.save-btn { padding: 7px 16px; background: var(--green); color: #fff; border: none; border-radius: 7px; font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; }

/* ══════════════════════════════
   DOKUMEN — 1:1 dengan Word
   A4: 794px lebar, margin sama
══════════════════════════════ */
.dok-wrap { max-width: 794px; margin: 0 auto; }

.dok-page {
  background: #fff;
  width: 794px;
  min-height: 1123px;
  margin: 0 auto 24px;
  padding: 72px 60px 60px 85px;
  font-family: 'Times New Roman', Times, serif;
  font-size: 12pt;
  line-height: 1.8;
  color: #000;
  box-shadow: 0 2px 16px rgba(0,0,0,0.10);
  position: relative;
}

/* KOP */
.kop { text-align: center; margin-bottom: 4px; }
.kop h1 { font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.04em; margin: 0; }
.kop h2 { font-size: 11pt; font-weight: bold; margin: 2px 0 0; }
.kop p  { font-size: 9pt; color: #444; margin: 3px 0 0; }

/* Garis hitam penuh */
.line-full { width: 100%; height: 4px; background: #000; margin: 10px 0 12px; }
.line-thin { width: 100%; height: 1px; background: #000; margin: 10px 0; }

/* KLASIFIKASI */
.klas { text-align: center; margin-bottom: 12px; }
.klas span { display: inline-block; border: 2.5px solid #000; padding: 3px 20px; font-size: 11pt; font-weight: bold; letter-spacing: 0.2em; }

/* INFO TABLE */
.info-tbl { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
.info-tbl td { font-size: 11.5pt; padding: 1px 0; vertical-align: top; }
.info-tbl .ik { width: 155px; font-weight: bold; }
.info-tbl .is { width: 14px; }
.info-tbl .iv { }

/* RINGKASAN */
.ringkasan { font-style: italic; font-size: 11.5pt; text-align: justify; margin-bottom: 4px; line-height: 1.8; }

/* SECTION */
.sek { margin-bottom: 14px; }
.sek-title { font-size: 12pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-bottom: 8px; }

/* SWOT */
.swot-item { margin-bottom: 12px; }
.swot-head { font-weight: bold; font-size: 11.5pt; margin-bottom: 2px; }
.swot-desc { font-style: italic; font-size: 9.5pt; color: #555; margin-bottom: 4px; }
.swot-list { list-style: disc; padding-left: 22px; font-size: 11.5pt; }
.swot-list li { margin-bottom: 2px; }

/* FAKTA */
.fakta-item { margin-bottom: 12px; }
.fakta-head { font-weight: bold; font-size: 11.5pt; margin-bottom: 3px; }
.fakta-isi { font-size: 11.5pt; text-align: justify; line-height: 1.8; }

/* REKOMENDASI */
.rek-item { margin-bottom: 12px; }
.rek-head { font-weight: bold; font-size: 11.5pt; margin-bottom: 4px; }
.rek-list { list-style: disc; padding-left: 22px; font-size: 11.5pt; }
.rek-list li { margin-bottom: 3px; text-align: justify; line-height: 1.7; }

/* EARLY WARNING */
.ew-list { padding-left: 22px; font-size: 11.5pt; }
.ew-list li { margin-bottom: 4px; line-height: 1.7; }

/* CATATAN */
.catatan { border: 2px solid #000; padding: 8px 12px; margin: 12px 0; }
.catatan-lbl { font-weight: bold; font-size: 10.5pt; text-transform: uppercase; margin-bottom: 2px; }
.catatan-isi { font-size: 11.5pt; font-style: italic; line-height: 1.7; }

/* PENUTUP */
.penutup { font-size: 11.5pt; text-align: justify; line-height: 1.8; margin-bottom: 6px; }
.penutup-bold { font-weight: bold; text-align: center; font-size: 12pt; margin: 14px 0 0; }

/* FOOTER */
.dok-footer { margin-top: 20px; padding-top: 5px; border-top: 2px solid #000; display: flex; justify-content: space-between; font-size: 9pt; color: #555; }

/* Nomor halaman */
.page-num { position: absolute; bottom: 26px; right: 38px; font-size: 9pt; color: #777; font-family: 'Times New Roman', Times, serif; }

/* Mini header halaman 2+ */
.page-mini-hdr { display: flex; justify-content: space-between; font-size: 9.5pt; color: #555; padding-bottom: 7px; border-bottom: 1px solid #000; margin-bottom: 14px; }

/* ── PRINT ── */
@media print {
  .sidenav, .topbar, .edit-panel { display: none !important; }
  html, body { height: auto; overflow: visible; display: block; background: #fff; }
  .main { overflow: visible; display: block; }
  .content { overflow: visible; padding: 0; background: #fff; }
  .dok-wrap { max-width: 100%; }
  .dok-page {
    width: 100%;
    min-height: auto;
    margin: 0;
    padding: 2cm 1.5cm 1.5cm 2.5cm;
    box-shadow: none;
    border: none;
    page-break-after: always;
  }
  .dok-page:last-child { page-break-after: auto; }
  .fakta-item, .rek-item, .swot-item, .catatan { page-break-inside: avoid; }
  @page { size: A4 portrait; margin: 0; }
}
</style>
</head>
<body>

<nav class="sidenav">
  <div class="sidenav-brand">
    <div class="brand-logo">SEPIA</div>
    <div class="brand-sub">Sistem Analitik Intelijen</div>
  </div>
  <div class="sidenav-section">
    <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
      <div class="nav-icon">📊</div>
      <div class="nav-item-text">Dashboard</div>
    </a>
    <a class="nav-item {{ request()->routeIs('datapool.*') ? 'active' : '' }}" href="{{ route('datapool.index') }}">
      <div class="nav-icon">📋</div>
      <div class="nav-item-text">RPI</div>
    </a>
    <a class="nav-item {{ request()->routeIs('penjabaran-strategis') ? 'active' : '' }}" href="{{ route('penjabaran-strategis') }}">
      <div class="nav-icon">🎯</div>
      <div class="nav-item-text">Penjabaran Strategis</div>
    </a>
    <a class="nav-item {{ request()->routeIs('laporan-informasi') ? 'active' : '' }}" href="{{ route('laporan-informasi') }}">
      <div class="nav-icon">📄</div>
      <div class="nav-item-text">Laporan Informasi</div>
    </a>
    <a class="nav-item {{ request()->routeIs('laporan-intelijen') ? 'active' : '' }}" href="{{ route('laporan-intelijen') }}">
      <div class="nav-icon">🔍</div>
      <div class="nav-item-text">Laporan Intelijen</div>
    </a>
    <a class="nav-item {{ request()->routeIs('infografis-intelijen') ? 'active' : '' }}" href="{{ route('infografis-intelijen') }}">
      <div class="nav-icon">📈</div>
      <div class="nav-item-text">Infografis Intelijen</div>
    </a>
    <a class="nav-item {{ request()->routeIs('profiling-subjek') ? 'active' : '' }}" href="{{ route('profiling-subjek') }}">
      <div class="nav-icon">👤</div>
      <div class="nav-item-text">Profiling Subjek</div>
    </a>
    <a class="nav-item {{ request()->routeIs('presentasi-intelijen') ? 'active' : '' }}" href="{{ route('presentasi-intelijen') }}">
      <div class="nav-icon">🎞️</div>
      <div class="nav-item-text">Presentasi Intelijen</div>
    </a>
  </div>
</nav>

<div class="main">
  <div class="topbar">
    <div class="topbar-left">
      <a href="{{ route('datapool.show', $folder) }}" style="color:var(--text-muted);font-size:13px;text-decoration:none">← Kembali</a>
      <div style="width:1px;height:18px;background:var(--border)"></div>
      <div style="font-size:14px;font-weight:700">Laporan Intelijen Situasional</div>
    </div>
    <div class="topbar-right">
      <a href="{{ route('distribusi.show', [$folder, $analisis]) }}" class="tb-btn warning">📤 Distribusi</a>
      <a href="{{ route('analisis.export.docx', [$folder, $analisis]) }}" class="tb-btn">📄 Ekspor Word</a>
      <button class="tb-btn primary" onclick="window.print()">🖨 Cetak</button>
    </div>
  </div>

  <div class="content">

    {{-- EDIT PANEL --}}
    <div class="edit-panel" id="edit-panel">
      <div class="edit-panel-title">✏️ Edit Informasi Laporan</div>
      <div class="edit-grid">
        <div class="edit-field">
          <label>Perihal</label>
          <input type="text" id="edit-perihal" value="{{ $perihal }}" />
        </div>
        <div class="edit-field">
          <label>Wilayah</label>
          <input type="text" id="edit-wilayah" value="{{ $wilayah }}" />
        </div>
      </div>
      <div class="edit-actions">
        <button class="tb-btn" onclick="toggleEdit()">Batal</button>
        <button class="save-btn" onclick="simpanInfo()">💾 Simpan</button>
      </div>
    </div>

    <div class="dok-wrap">

    {{-- ══ HALAMAN 1 ══ --}}
    <div class="dok-page">

      {{-- KOP --}}
      <div class="kop">
        <h1>Laporan Intelijen Situasional</h1>
        <h2>Sistem Analitik Intelijen Dan Hukum - SEPIA</h2>
        <p>Dokumen Resmi - Bersifat {{ $klasifikasi }}</p>
      </div>
      <div class="line-full"></div>

      {{-- KLASIFIKASI --}}
      <div class="klas"><span>KLASIFIKASI: {{ $klasifikasi }}</span></div>

      {{-- INFO --}}
      <table class="info-tbl">
        <tr>
          <td class="ik">Perihal</td>
          <td class="is">:</td>
          <td class="iv" id="dok-perihal">{{ $perihal }}</td>
        </tr>
        <tr>
          <td class="ik">Wilayah</td>
          <td class="is">:</td>
          <td class="iv" id="dok-wilayah">{{ $wilayah }}</td>
        </tr>
        <tr>
          <td class="ik">Tanggal Analisis</td>
          <td class="is">:</td>
          <td class="iv">{{ $analisis->tanggal_analisis ? strtoupper(\Carbon\Carbon::parse($analisis->tanggal_analisis)->format('d F Y')) : strtoupper(now()->format('d F Y')) }}</td>
        </tr>
        <tr>
          <td class="ik">Tingkat Situasi</td>
          <td class="is">:</td>
          <td class="iv"><strong style="color:{{ $statusColor }}">{{ $statusWaspada }}</strong> (Risiko {{ $analisis->tingkat_risiko }}/10)</td>
        </tr>
      </table>

      <div class="line-thin"></div>

      {{-- RINGKASAN EKSEKUTIF --}}
      @if($analisis->ringkasan_eksekutif)
      <p class="ringkasan">{{ $analisis->ringkasan_eksekutif }}</p>
      <div class="line-thin"></div>
      @endif

      {{-- SWOT --}}
      <div class="sek">
        <div class="sek-title">Analisis SWOT</div>

        @php
          $swotMeta = [
            'S' => ['S - Strengths (Kekuatan)',   'Faktor kekuatan internal yang mendukung penanganan.'],
            'W' => ['W - Weaknesses (Kelemahan)',  'Faktor kelemahan internal yang menghambat penanganan.'],
            'O' => ['O - Opportunities (Peluang)', 'Faktor peluang eksternal yang dapat dimanfaatkan.'],
            'T' => ['T - Threats (Ancaman)',       'Faktor ancaman eksternal yang perlu diwaspadai.'],
          ];
        @endphp

        @foreach($swotMeta as $tipe => [$label, $desc])
        <div class="swot-item">
          <div class="swot-head">{{ $label }}</div>
          <div class="swot-desc">{{ $desc }}</div>
          <ul class="swot-list">
            @forelse($swotGroups->get($tipe, collect()) as $item)
            <li>{{ $item->isi }}</li>
            @empty
            <li style="color:#999;font-style:italic">Tidak ada poin.</li>
            @endforelse
          </ul>
        </div>
        @endforeach
      </div>

      <div class="line-thin"></div>

      {{-- I. FAKTA-FAKTA --}}
      <div class="sek">
        <div class="sek-title">I. Fakta-Fakta</div>
        @if(!empty($faktaFakta))
          @foreach($faktaFakta as $fakta)
          <div class="fakta-item">
            <div class="fakta-head">{{ $fakta['huruf'] ?? '' }}. {{ $fakta['judul'] ?? '' }}</div>
            <div class="fakta-isi">{{ $fakta['isi'] ?? '' }}</div>
          </div>
          @endforeach
        @else
          @foreach($analisis->timeline as $tl)
          <div class="fakta-item">
            <div class="fakta-head">{{ $tl->tanggal }}</div>
            <div class="fakta-isi">{{ $tl->keterangan }}</div>
          </div>
          @endforeach
        @endif
      </div>

      <div class="page-num">Halaman 1</div>
    </div>{{-- end page 1 --}}

    {{-- ══ HALAMAN 2 ══ --}}
    <div class="dok-page">

      <div class="page-mini-hdr">
        <span><strong>{{ $perihal }}</strong></span>
        <span style="font-weight:bold;letter-spacing:0.1em">{{ $klasifikasi }}</span>
        <span>Lanjutan — Halaman 2</span>
      </div>

      {{-- II. ANALISIS INTELIJEN (PESTLE) --}}
      <div class="sek">
        <div class="sek-title">II. Analisis Intelijen</div>
        @php
          $pestleMeta = [
            'politik'    => ['label' => 'Politik',    'icon' => '🏛️', 'color' => '#1e40af', 'bg' => '#eff6ff'],
            'ekonomi'    => ['label' => 'Ekonomi',    'icon' => '💰', 'color' => '#166534', 'bg' => '#f0fdf4'],
            'sosial'     => ['label' => 'Sosial',     'icon' => '👥', 'color' => '#7c3aed', 'bg' => '#f5f3ff'],
            'teknologi'  => ['label' => 'Teknologi',  'icon' => '💻', 'color' => '#0369a1', 'bg' => '#f0f9ff'],
            'hukum'      => ['label' => 'Hukum',      'icon' => '⚖️', 'color' => '#b45309', 'bg' => '#fffbeb'],
            'lingkungan' => ['label' => 'Lingkungan', 'icon' => '🌿', 'color' => '#065f46', 'bg' => '#ecfdf5'],
            'budaya'     => ['label' => 'Budaya',     'icon' => '🎭', 'color' => '#9d174d', 'bg' => '#fdf2f8'],
          ];
          $analisisRaw = $analisis->analisis_intelijen ?? null;
          $pestle = null;
          if ($analisisRaw) {
            $decoded = json_decode($analisisRaw, true);
            if (is_array($decoded) && isset($decoded['politik'])) {
              $pestle = $decoded;
            }
          }
        @endphp

        @if($pestle)
          @foreach($pestleMeta as $key => $meta)
          @if(!empty($pestle[$key]))
          <div style="margin-bottom:10px;border-radius:8px;overflow:hidden;border:1px solid #e2e8f0">
            <div style="padding:6px 12px;background:{{ $meta['bg'] }};display:flex;align-items:center;gap:6px;border-bottom:1px solid #e2e8f0">
              <span style="font-size:12pt">{{ $meta['icon'] }}</span>
              <span style="font-size:10.5pt;font-weight:bold;color:{{ $meta['color'] }};text-transform:uppercase;letter-spacing:0.05em">{{ $meta['label'] }}</span>
            </div>
            <div style="padding:8px 12px;font-size:11pt;line-height:1.8;text-align:justify">{{ $pestle[$key] }}</div>
          </div>
          @endif
          @endforeach
        @elseif($analisisRaw)
          <p class="fakta-isi">{{ $analisisRaw }}</p>
        @elseif($analisis->ringkasan_intelijen)
          <p class="fakta-isi">{{ $analisis->ringkasan_intelijen }}</p>
        @elseif($analisis->deskripsi)
          <p class="fakta-isi">{{ $analisis->deskripsi }}</p>
        @endif
      </div>
{{-- III. REKOMENDASI --}}
      @if(!empty($jabatanRek))
      <div class="sek">
        <div class="sek-title">III. Rekomendasi</div>
        @foreach($jabatanRek as $key => $jabatan)
        <div class="rek-item">
          <div class="rek-head">{{ $loop->iteration }}. {{ $jabatan['nama_jabatan'] ?? $key }}</div>
          <ul class="rek-list">
            @foreach($jabatan['poin'] ?? [] as $poin)
            <li>{{ is_array($poin) ? ($poin['rekomendasi'] ?? '') : $poin }}</li>
            @endforeach
          </ul>
        </div>
        @endforeach
      </div>
      @elseif($analisis->rekomendasi->isNotEmpty())
      <div class="sek">
        <div class="sek-title">III. Rekomendasi</div>
        @php $grouped = $analisis->rekomendasi->groupBy('judul'); @endphp
        @foreach($grouped as $jabatan => $poin)
        <div class="rek-item">
          <div class="rek-head">{{ $loop->iteration }}. {{ $jabatan }}</div>
          <ul class="rek-list">
            @foreach($poin as $p)
            <li>{{ $p->deskripsi }}</li>
            @endforeach
          </ul>
        </div>
        @endforeach
      </div>
      @endif

      {{-- IV. EARLY WARNING --}}
      @if(!empty($earlyWarning))
      <div class="sek">
        <div class="sek-title">IV. Indikator Peringatan Dini</div>
        <ol class="ew-list">
          @foreach($earlyWarning as $ew)
          <li>{{ is_array($ew) ? ($ew['indikator'] ?? '') : $ew }}</li>
          @endforeach
        </ol>
      </div>
      @endif

      {{-- CATATAN ANALIS --}}
      @if($analisis->catatan_analis)
      <div class="catatan">
        <div class="catatan-lbl">Catatan Perwira Analis:</div>
        <div class="catatan-isi">"{{ $analisis->catatan_analis }}"</div>
      </div>
      @endif

      {{-- PENUTUP --}}
      <div style="margin-top:16px">
        <p class="penutup">Demikian laporan situasional ini disusun berdasarkan hasil monitoring dan analisis data di wilayah <strong>{{ $wilayah }}</strong>. Ke depan situasi diperkirakan {{ $risikoVal >= 7 ? 'berpotensi memanas dan memerlukan penanganan segera' : ($risikoVal >= 5 ? 'tetap dinamis dan memerlukan pemantauan intensif' : 'tetap kondusif apabila rekomendasi di atas dilaksanakan dengan baik') }}.</p>
        <p class="penutup-bold">DEMIKIAN LAPORAN INI DIBUAT UNTUK DITINDAKLANJUTI.</p>
      </div>

      {{-- FOOTER --}}
      <div class="dok-footer">
        <span>{{ $klasifikasi }}</span>
        <span>SEPIA - Sistem Analitik Intelijen Dan Hukum</span>
        <span>{{ now()->format('d M Y') }}</span>
      </div>

      <div class="page-num">Halaman 2</div>
    </div>{{-- end page 2 --}}

    </div>{{-- end dok-wrap --}}
  </div>
</div>

<script>
function toggleEdit() {
  document.getElementById('edit-panel').classList.toggle('open');
}
function simpanInfo() {
  const perihal = document.getElementById('edit-perihal').value;
  const wilayah = document.getElementById('edit-wilayah').value;
  fetch('{{ route("analisis.update", [$folder, $analisis]) }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ perihal, wilayah, _method: 'PATCH' }),
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById('dok-perihal').textContent = perihal;
      document.getElementById('dok-wilayah').textContent = wilayah;
      toggleEdit();
    } else { alert('Gagal menyimpan'); }
  })
  .catch(() => alert('Error koneksi'));
}
</script>
</body>
</html>