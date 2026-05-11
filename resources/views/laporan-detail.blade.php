<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<title>SEPIA — {{ $laporan->nomorFormatted() }}</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green: #1a5c2e; --green-2: #2e7d4a; --green-light: #edfaf3;
  --green-border: #8fd4b1; --text: #0f172a; --text-muted: #64748b;
  --text-faint: #94a3b8; --border: #e2e8f0; --border-light: #f1f5f9;
  --bg: #ffffff; --bg-2: #f8fafc; --bg-3: #f1f5f9;
  --nav-width: 220px;
}
html, body { height: 100%; }
body { font-family: 'Sora', sans-serif; background: var(--bg-3); color: var(--text); display: flex; overflow: hidden; font-size: 13px; }

.sidenav { width: var(--nav-width); background: var(--green); display: flex; flex-direction: column; flex-shrink: 0; }
.sidenav-brand { padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.brand-logo { font-size: 18px; font-weight: 700; letter-spacing: 0.14em; color: #fff; }
.brand-sub { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 2px; letter-spacing: 0.05em; text-transform: uppercase; }
.sidenav-section { padding: 18px 12px 8px; }
.sidenav-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.4); padding: 0 8px; margin-bottom: 6px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; cursor: pointer; margin-bottom: 2px; text-decoration: none; transition: background 0.12s; border: 1px solid transparent; color: rgba(255,255,255,0.72); font-size: 13px; font-weight: 500; }
.nav-item:hover { background: rgba(255,255,255,0.1); color: #fff; }
.nav-item.active { background: rgba(255,255,255,0.14); border-color: rgba(255,255,255,0.18); color: #fff; }
.nav-item .nav-icon { width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; background: rgba(255,255,255,0.1); }
.nav-item.active .nav-icon { background: rgba(255,255,255,0.2); }
.nav-item-text { flex: 1; }
.nav-badge { font-size: 10px; background: rgba(255,255,255,0.2); color: #fff; padding: 2px 7px; border-radius: 20px; }
.nav-badge.alert { background: #ef4444; }
.sidenav-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 10px 12px; }
.sidenav-bottom { margin-top: auto; padding: 14px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
.user-row { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 8px; }
.user-avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); color: #fff; font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; }
.user-name { font-size: 12px; font-weight: 500; color: #fff; }
.user-role { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 1px; }

.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 56px; background: #fff; border-bottom: 1px solid var(--border); flex-shrink: 0; gap: 12px; }
.topbar-left { display: flex; align-items: center; gap: 14px; }
.page-title { font-size: 14px; font-weight: 700; }
.topbar-right { display: flex; align-items: center; gap: 6px; }
.tb-btn { padding: 7px 13px; font-size: 11.5px; border: 1px solid var(--border); border-radius: 8px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'Sora', sans-serif; font-weight: 500; transition: all 0.13s; display: flex; align-items: center; gap: 5px; text-decoration: none; }
.tb-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.tb-btn.primary { background: var(--green); color: #fff; border-color: var(--green); }
.tb-btn.primary:hover { background: var(--green-2); }

.content { flex: 1; overflow-y: auto; padding: 24px 28px 40px; display: flex; flex-direction: column; gap: 18px; }
.content::-webkit-scrollbar { width: 5px; }
.content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

/* ── HEADER ── */
.header-card { background: #fff; border: 1.5px solid var(--border); border-radius: 16px; overflow: hidden; }
.header-stripe { height: 5px; }
.header-body { padding: 22px 26px; display: flex; align-items: flex-start; gap: 20px; }
.header-icon { width: 56px; height: 56px; border-radius: 14px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; font-size: 28px; flex-shrink: 0; }
.header-info { flex: 1; }
.header-nomor { font-size: 11px; color: var(--text-faint); font-family: 'JetBrains Mono', monospace; margin-bottom: 5px; letter-spacing: 0.05em; }
.header-judul { font-size: 20px; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 10px; }
.header-chips { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.chip { font-size: 11px; color: var(--text-muted); background: var(--bg-3); padding: 4px 10px; border-radius: 20px; border: 1px solid var(--border); }
.header-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; flex-shrink: 0; }
.risiko-badge { padding: 5px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.06em; border: 1px solid transparent; }
.risk-high { background: #fef2f2; color: #be123c; border-color: #fecdd3; }
.risk-med  { background: #fffbeb; color: #a16207; border-color: #fde68a; }
.risk-low  { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.risiko-score { font-size: 32px; font-weight: 800; letter-spacing: -0.03em; font-family: 'JetBrains Mono', monospace; line-height: 1; }
.risiko-score-sub { font-size: 10px; color: var(--text-faint); margin-top: 2px; text-align: right; }

/* ── STAT ROW ── */
.stat-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.stat-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 14px 16px; }
.stat-icon { font-size: 20px; margin-bottom: 8px; }
.stat-val { font-size: 24px; font-weight: 800; letter-spacing: -0.02em; font-family: 'JetBrains Mono', monospace; }
.stat-lbl { font-size: 11px; color: var(--text-muted); margin-top: 3px; font-weight: 500; }

.section-label { font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px; }
.card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 18px 20px; }

/* ── SWOT ── */
.swot-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.swot-cell { border-radius: 12px; padding: 14px 16px; }
.swot-title { font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
.swot-list { display: flex; flex-direction: column; gap: 6px; }
.swot-item { font-size: 12px; line-height: 1.6; padding: 7px 10px; border-radius: 8px; }

/* ── AKTOR ── */
.aktor-list { display: flex; flex-direction: column; gap: 8px; }
.aktor-row { display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: var(--bg-2); border: 1px solid var(--border-light); border-radius: 10px; }
.aktor-av { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0; }
.aktor-info { flex: 1; }
.aktor-nama { font-size: 13px; font-weight: 600; }
.aktor-meta { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.status-badge { font-size: 10px; padding: 3px 9px; border-radius: 20px; border: 1px solid; white-space: nowrap; font-weight: 600; }
.status-tersangka { background: #fef2f2; color: #be123c; border-color: #fecdd3; }
.status-saksi     { background: #fffbeb; color: #a16207; border-color: #fde68a; }
.status-dpo       { background: #1a1a2e; color: #fff; border-color: #333; }

/* ── TIMELINE ── */
.timeline-list { display: flex; flex-direction: column; position: relative; padding-left: 20px; gap: 0; }
.timeline-list::before { content: ''; position: absolute; left: 6px; top: 8px; bottom: 8px; width: 2px; background: var(--border); border-radius: 99px; }
.timeline-item { position: relative; padding: 0 0 16px 20px; }
.timeline-item:last-child { padding-bottom: 0; }
.timeline-dot { position: absolute; left: -20px; top: 4px; width: 12px; height: 12px; border-radius: 50%; border: 2px solid #fff; }
.timeline-date { font-size: 10.5px; color: var(--text-faint); font-family: 'JetBrains Mono', monospace; margin-bottom: 5px; }
.timeline-ket { font-size: 12.5px; color: var(--text); line-height: 1.65; }

/* ── RISK ── */
.risk-list { display: flex; flex-direction: column; gap: 8px; }
.risk-row { display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: var(--bg-2); border: 1px solid var(--border-light); border-radius: 10px; }
.risk-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.risk-info { flex: 1; }
.risk-label { font-size: 13px; font-weight: 600; }
.risk-ket { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
.risk-score { font-size: 12px; font-weight: 700; font-family: 'JetBrains Mono', monospace; padding: 4px 10px; border-radius: 8px; flex-shrink: 0; }

/* ── REKOMENDASI ── */
.rek-list { display: flex; flex-direction: column; gap: 8px; }
.rek-row { display: flex; align-items: flex-start; gap: 12px; padding: 13px 16px; background: var(--bg-2); border: 1px solid var(--border-light); border-radius: 10px; }
.rek-num { width: 24px; height: 24px; border-radius: 50%; background: var(--green); color: #fff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
.rek-body { flex: 1; }
.rek-judul { font-size: 13px; font-weight: 600; margin-bottom: 3px; }
.rek-desc { font-size: 11.5px; color: var(--text-muted); line-height: 1.6; }
.rek-prioritas { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; white-space: nowrap; flex-shrink: 0; }
.prio-tinggi { background: #fef2f2; color: #be123c; border: 1px solid #fecdd3; }
.prio-sedang  { background: #fffbeb; color: #a16207; border: 1px solid #fde68a; }
.prio-rendah  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

/* ── CONFIDENCE ── */
.conf-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.conf-cell { text-align: center; padding: 14px 10px; background: var(--bg-2); border: 1px solid var(--border-light); border-radius: 10px; }
.conf-val { font-size: 22px; font-weight: 800; font-family: 'JetBrains Mono', monospace; }
.conf-lbl { font-size: 10px; color: var(--text-muted); margin-top: 4px; font-weight: 500; }
.conf-bar { height: 4px; background: var(--bg-3); border-radius: 99px; overflow: hidden; margin-top: 8px; }
.conf-fill { height: 100%; border-radius: 99px; background: var(--green); }

@media print {
  .sidenav, .topbar { display: none !important; }
  .main { overflow: visible; }
  .content { overflow: visible; padding: 0; }
  body { overflow: visible; }
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
    <div class="sidenav-label">Menu Utama</div>
    <a class="nav-item" href="{{ route('dashboard') }}"><div class="nav-icon">📊</div><div class="nav-item-text">Dashboard</div></a>
    <a class="nav-item active" href="{{ route('datapool.index') }}"><div class="nav-icon">📋</div><div class="nav-item-text">RPI</div></a>
    <a class="nav-item" href="#"><div class="nav-icon">🗄️</div><div class="nav-item-text">Data Pool</div></a>
    <a class="nav-item" href="#"><div class="nav-icon">🎨</div><div class="nav-item-text">Personalisasi</div></a>
    <a class="nav-item" href="#"><div class="nav-icon">📅</div><div class="nav-item-text">Daily Report</div><span class="nav-badge alert">!</span></a>
  </div>
  <div class="sidenav-divider"></div>
  <div class="sidenav-section">
    <div class="sidenav-label">Sistem</div>
    <a class="nav-item" href="#"><div class="nav-icon">⚙️</div><div class="nav-item-text">Settings</div></a>
    <a class="nav-item" href="#"><div class="nav-icon">🔒</div><div class="nav-item-text">Akses & Izin</div></a>
  </div>
  <div class="sidenav-bottom">
    <div class="user-row">
      <div class="user-avatar">CR</div>
      <div><div class="user-name">C. Rasyid</div><div class="user-role">Analis Senior</div></div>
    </div>
  </div>
</nav>

<div class="main">
  <div class="topbar">
    <div class="topbar-left">
      <a href="{{ route('laporan.index') }}" style="color:var(--text-muted);font-size:13px;text-decoration:none">← Semua Laporan</a>
      <div class="page-title">{{ $laporan->nomorFormatted() }}</div>
    </div>
    <div class="topbar-right">
      <a href="{{ route('datapool.show', $laporan->folder) }}" class="tb-btn">📁 Buka Folder</a>
      @if($laporan->analisis)
      <a href="{{ route('analisis.show', [$laporan->folder, $laporan->analisis]) }}" class="tb-btn">⚡ Lihat Analisis</a>
      @endif
      <button class="tb-btn primary" onclick="window.print()">🖨 Cetak</button>
    </div>
  </div>

  <div class="content">

    @if(session('success'))
    <div style="padding:11px 16px;border-radius:10px;font-size:12.5px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0">
      ✓ {{ session('success') }}
    </div>
    @endif

    {{-- ══ HEADER ══ --}}
    @php
      $risikoLabel = $laporan->risikoLabel();
      $risikoClass = 'risk-' . ($risikoLabel === 'tinggi' ? 'high' : ($risikoLabel === 'sedang' ? 'med' : 'low'));
      $risikoColor = $laporan->tingkat_risiko >= 7 ? '#be123c' : ($laporan->tingkat_risiko >= 4 ? '#b45309' : '#15803d');
      $stripeColor = $laporan->tingkat_risiko >= 7
        ? 'linear-gradient(90deg,#be123c,#f43f5e)'
        : ($laporan->tingkat_risiko >= 4
          ? 'linear-gradient(90deg,#b45309,#f59e0b)'
          : 'linear-gradient(90deg,#1a5c2e,#10b981)');
    @endphp

    <div class="header-card">
      <div class="header-stripe" style="background:{{ $stripeColor }}"></div>
      <div class="header-body">
        <div class="header-icon">📋</div>
        <div class="header-info">
          <div class="header-nomor">{{ $laporan->nomorFormatted() }}</div>
          <div class="header-judul">{{ $laporan->judul }}</div>
          <div class="header-chips">
            <span class="chip">📁 {{ $laporan->folder->nama ?? '—' }}</span>
            <span class="chip">👤 {{ $laporan->dibuat_oleh }}</span>
            <span class="chip">📅 {{ $laporan->created_at->format('d M Y, H:i') }}</span>
            @if($laporan->analisis)
            <span class="chip">🤖 {{ $laporan->analisis->model_versi ?? 'SEPIA AI' }}</span>
            @endif
          </div>
        </div>
        <div class="header-right">
          <span class="risiko-badge {{ $risikoClass }}">● RISIKO {{ strtoupper($risikoLabel) }}</span>
          <div>
            <div class="risiko-score" style="color:{{ $risikoColor }}">{{ $laporan->tingkat_risiko }}</div>
            <div class="risiko-score-sub">dari 10</div>
          </div>
        </div>
      </div>
    </div>

    {{-- ══ STAT CARDS ══ --}}
    <div class="stat-row">
      <div class="stat-card">
        <div class="stat-icon">📎</div>
        <div class="stat-val">{{ $laporan->jumlah_sumber ?? '—' }}</div>
        <div class="stat-lbl">Sumber Data</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-val">{{ $laporan->jumlah_aktor ?? '—' }}</div>
        <div class="stat-lbl">Aktor Teridentifikasi</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-val">{{ $laporan->jumlah_rekomendasi ?? '—' }}</div>
        <div class="stat-lbl">Rekomendasi</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">⚖️</div>
        <div class="stat-val" style="font-size:14px;font-weight:700;margin-top:6px;line-height:1.4">{{ $laporan->prediksi_vonis ?? '—' }}</div>
        <div class="stat-lbl">Prediksi Vonis</div>
      </div>
    </div>

    @if($laporan->analisis)
    @php $analisis = $laporan->analisis; @endphp

    {{-- ══ SWOT ══ --}}
    {{-- tipe: S/W/O/T, isi: konten --}}
    @if($analisis->swotItems->isNotEmpty())
    <div>
      <div class="section-label">Analisis SWOT</div>
      <div class="swot-grid">
        @php
          $swotConfig = [
            'S' => ['label' => 'Kekuatan',  'emoji' => '💪', 'bg' => '#f0fdf4', 'item_bg' => '#dcfce7', 'color' => '#15803d'],
            'W' => ['label' => 'Kelemahan', 'emoji' => '⚠️', 'bg' => '#fffbeb', 'item_bg' => '#fef9c3', 'color' => '#a16207'],
            'O' => ['label' => 'Peluang',   'emoji' => '🎯', 'bg' => '#f0f9ff', 'item_bg' => '#e0f2fe', 'color' => '#0369a1'],
            'T' => ['label' => 'Ancaman',   'emoji' => '🚨', 'bg' => '#fef2f2', 'item_bg' => '#fee2e2', 'color' => '#be123c'],
          ];
        @endphp
        @foreach($swotConfig as $tipe => $cfg)
        @php $items = $analisis->swotItems->where('tipe', $tipe); @endphp
        <div class="swot-cell" style="background:{{ $cfg['bg'] }}">
          <div class="swot-title" style="color:{{ $cfg['color'] }}">
            {{ $cfg['emoji'] }} {{ $cfg['label'] }}
            <span style="font-size:10px;background:rgba(0,0,0,0.06);padding:2px 7px;border-radius:20px;font-weight:500">{{ $items->count() }}</span>
          </div>
          <div class="swot-list">
            @forelse($items as $item)
            <div class="swot-item" style="background:{{ $cfg['item_bg'] }};color:{{ $cfg['color'] }}">{{ $item->isi }}</div>
            @empty
            <div style="font-size:11.5px;color:var(--text-faint);font-style:italic">Tidak ada data</div>
            @endforelse
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- ══ AKTOR ══ --}}
    {{-- kolom: nama, inisial, peran, status(tersangka/saksi/dpo), warna_avatar --}}
    @if($analisis->aktor->isNotEmpty())
    <div>
      <div class="section-label">Peta Aktor ({{ $analisis->aktor->count() }})</div>
      <div class="aktor-list">
        @foreach($analisis->aktor as $aktor)
        <div class="aktor-row">
          <div class="aktor-av" style="background:{{ $aktor->warna_avatar ?? '#1a5c2e' }}">{{ $aktor->inisial }}</div>
          <div class="aktor-info">
            <div class="aktor-nama">{{ $aktor->nama }}</div>
            <div class="aktor-meta">{{ $aktor->peran ?? '—' }}</div>
          </div>
          @if($aktor->status)
          <span class="status-badge status-{{ $aktor->status }}">{{ strtoupper($aktor->status) }}</span>
          @endif
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- ══ TIMELINE ══ --}}
    {{-- kolom: tanggal, keterangan, warna_dot --}}
    @if($timeline->isNotEmpty())
    <div>
      <div class="section-label">Timeline Kejadian</div>
      <div class="card">
        <div class="timeline-list">
          @foreach($timeline as $tl)
          <div class="timeline-item">
            <div class="timeline-dot" style="background:{{ $tl->warna_dot ?? '#16a34a' }}"></div>
            <div class="timeline-date">{{ $tl->tanggal }}</div>
            <div class="timeline-ket">{{ $tl->keterangan }}</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    @endif

    {{-- ══ RISK ASSESSMENT ══ --}}
    {{-- kolom: label, nilai, warna, keterangan --}}
    @if($riskItems->isNotEmpty())
    <div>
      <div class="section-label">Penilaian Risiko</div>
      <div class="risk-list">
        @foreach($riskItems as $risk)
        @php
          $rNilai = $risk->nilai ?? 0;
          $rColor = $risk->warna ?? '#dc2626';
          $rBg    = $rNilai >= 7 ? '#fef2f2' : ($rNilai >= 4 ? '#fffbeb' : '#f0fdf4');
        @endphp
        <div class="risk-row">
          <div class="risk-dot" style="background:{{ $rColor }}"></div>
          <div class="risk-info">
            <div class="risk-label">{{ $risk->label }}</div>
            @if($risk->keterangan)
            <div class="risk-ket">{{ $risk->keterangan }}</div>
            @endif
          </div>
          <div class="risk-score" style="background:{{ $rBg }};color:{{ $rColor }}">{{ $rNilai }}/10</div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- ══ REKOMENDASI ══ --}}
    {{-- kolom: judul, deskripsi, prioritas --}}
    @if($rekomendasi->isNotEmpty())
    <div>
      <div class="section-label">Rekomendasi Tindak Lanjut</div>
      <div class="rek-list">
        @foreach($rekomendasi as $i => $rek)
        @php $prio = $rek->prioritas ?? 'sedang'; @endphp
        <div class="rek-row">
          <div class="rek-num">{{ $i + 1 }}</div>
          <div class="rek-body">
            <div class="rek-judul">{{ $rek->judul }}</div>
            @if($rek->deskripsi)
            <div class="rek-desc">{{ $rek->deskripsi }}</div>
            @endif
          </div>
          <span class="rek-prioritas prio-{{ $prio }}">{{ strtoupper($prio) }}</span>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- ══ CONFIDENCE ══ --}}
    @if($confidence)
    <div>
      <div class="section-label">Tingkat Kepercayaan Analisis</div>
      <div class="card">
        <div class="conf-grid">
          @php
            $confItems = [
              ['label' => 'Kelengkapan Data',   'val' => $confidence->kelengkapan_data   ?? 0],
              ['label' => 'Konsistensi Sumber', 'val' => $confidence->konsistensi_sumber ?? 0],
              ['label' => 'Kualitas Dokumen',   'val' => $confidence->kualitas_dokumen   ?? 0],
              ['label' => 'Kedalaman Analisis', 'val' => $confidence->kedalaman_analisis ?? 0],
            ];
            $avgConf = round(collect($confItems)->avg('val'));
          @endphp
          @foreach($confItems as $ci)
          <div class="conf-cell">
            <div class="conf-val" style="color:var(--green)">{{ $ci['val'] }}%</div>
            <div class="conf-lbl">{{ $ci['label'] }}</div>
            <div class="conf-bar"><div class="conf-fill" style="width:{{ $ci['val'] }}%"></div></div>
          </div>
          @endforeach
        </div>
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border-light);display:flex;align-items:center;justify-content:space-between">
          <span style="font-size:12px;color:var(--text-muted)">Rata-rata Confidence</span>
          <span style="font-size:18px;font-weight:800;color:var(--green);font-family:'JetBrains Mono',monospace">{{ $avgConf }}%</span>
        </div>
      </div>
    </div>
    @endif

    @else
    <div style="text-align:center;padding:40px;background:#fff;border:1.5px dashed var(--border);border-radius:14px;color:var(--text-muted)">
      <div style="font-size:32px;margin-bottom:10px">⚠️</div>
      <div style="font-size:13px;font-weight:600">Data analisis tidak tersedia</div>
      <div style="font-size:11.5px;margin-top:4px">Analisis mungkin telah dihapus atau belum selesai diproses.</div>
    </div>
    @endif

    {{-- ══ FOOTER ══ --}}
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between">
      <div style="font-size:11.5px;color:var(--text-muted)">
        Dibuat otomatis oleh sistem SEPIA pada
        <strong style="color:var(--text)">{{ $laporan->created_at->format('d M Y, H:i') }}</strong>
        oleh <strong style="color:var(--text)">{{ $laporan->dibuat_oleh }}</strong>.
      </div>
      <div style="font-size:10px;color:var(--text-faint);font-family:'JetBrains Mono',monospace">
        {{ $laporan->nomorFormatted() }}
      </div>
    </div>

  </div>
</div>

</body>
</html>