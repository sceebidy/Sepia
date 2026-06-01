<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<title>SEPIA — Daftar Laporan</title>
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
.page-title { font-size: 14.5px; font-weight: 700; }
.topbar-right { display: flex; align-items: center; gap: 6px; }
.tb-btn { padding: 7px 13px; font-size: 11.5px; border: 1px solid var(--border); border-radius: 8px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'Sora', sans-serif; font-weight: 500; transition: all 0.13s; display: flex; align-items: center; gap: 5px; text-decoration: none; }
.tb-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }

.content { flex: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 16px; }
.content::-webkit-scrollbar { width: 5px; }
.content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

.risk-badge { padding: 3px 9px; border-radius: 20px; font-size: 9.5px; font-weight: 700; letter-spacing: 0.05em; white-space: nowrap; border: 1px solid transparent; }
.risk-high { background: #fef2f2; color: #be123c; border-color: #fecdd3; }
.risk-med  { background: #fffbeb; color: #a16207; border-color: #fde68a; }
.risk-low  { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }

.stat-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.stat-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 16px 18px; }
.stat-val { font-size: 28px; font-weight: 800; letter-spacing: -0.02em; font-family: 'JetBrains Mono', monospace; }
.stat-lbl { font-size: 11px; color: var(--text-muted); margin-top: 4px; font-weight: 500; }

.laporan-list { display: flex; flex-direction: column; gap: 8px; }
.laporan-row { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 16px; text-decoration: none; color: inherit; transition: all 0.13s; }
.laporan-row:hover { border-color: var(--green-border); background: var(--green-light); transform: translateX(2px); }
.lap-icon { width: 42px; height: 42px; border-radius: 10px; background: #f0f9ff; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.lap-body { flex: 1; min-width: 0; }
.lap-judul { font-size: 13.5px; font-weight: 600; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.lap-meta { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.lap-meta-dot { color: var(--text-faint); }
.lap-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.lap-risiko-bar { width: 60px; height: 4px; background: var(--bg-3); border-radius: 99px; overflow: hidden; }
.lap-risiko-fill { height: 100%; border-radius: 99px; }

.empty-state { text-align: center; padding: 60px 20px; background: #fff; border: 1.5px dashed var(--border); border-radius: 14px; color: var(--text-muted); }
.empty-state-icon { font-size: 40px; margin-bottom: 12px; }

.pagination-wrap { display: flex; justify-content: center; margin-top: 8px; }
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
      <a href="{{ route('dashboard') }}" style="color:var(--text-muted);font-size:13px;text-decoration:none">← Dashboard</a>
      <div class="page-title">Daftar Laporan</div>
    </div>
    <div class="topbar-right">
      <a href="{{ route('datapool.index') }}" class="tb-btn">+ Buat dari RPI</a>
    </div>
  </div>

  <div class="content">

    @if(session('success'))
    <div style="padding:11px 16px;border-radius:10px;font-size:12.5px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0">
      ✓ {{ session('success') }}
    </div>
    @endif

    {{-- STAT CARDS --}}
    <div class="stat-row">
      <div class="stat-card">
        <div class="stat-val">{{ $laporanList->total() }}</div>
        <div class="stat-lbl">📄 Total Laporan</div>
      </div>
      <div class="stat-card">
        <div class="stat-val" style="color:#be123c">{{ $laporanList->getCollection()->where('tingkat_risiko', '>=', 7)->count() }}</div>
        <div class="stat-lbl">🔴 Risiko Tinggi (halaman ini)</div>
      </div>
      <div class="stat-card">
        <div class="stat-val" style="color:#047857">{{ $laporanList->getCollection()->where('tingkat_risiko', '<', 4)->count() }}</div>
        <div class="stat-lbl">🟢 Risiko Rendah (halaman ini)</div>
      </div>
    </div>

    {{-- LIST --}}
    @if($laporanList->isEmpty())
    <div class="empty-state">
      <div class="empty-state-icon">📋</div>
      <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:6px">Belum ada laporan</div>
      <div style="font-size:12px">Buat laporan dari halaman folder RPI setelah analisis selesai.</div>
    </div>
    @else
    <div class="laporan-list">
      @foreach($laporanList as $lap)
      @php
        $risikoColor = $lap->tingkat_risiko >= 7 ? '#be123c' : ($lap->tingkat_risiko >= 4 ? '#b45309' : '#15803d');
        $risikoLabel = $lap->risikoLabel();
        $risikoClass = 'risk-' . ($risikoLabel === 'tinggi' ? 'high' : ($risikoLabel === 'sedang' ? 'med' : 'low'));
        $risikoBarW   = round(($lap->tingkat_risiko / 10) * 100);
      @endphp
      <a href="{{ route('laporan.show', $lap) }}" class="laporan-row">
        <div class="lap-icon">📄</div>
        <div class="lap-body">
          <div class="lap-judul">{{ $lap->judul }}</div>
          <div class="lap-meta">
            <span>{{ $lap->nomorFormatted() }}</span>
            <span class="lap-meta-dot">·</span>
            <span>{{ $lap->folder->nama ?? '—' }}</span>
            <span class="lap-meta-dot">·</span>
            <span>{{ $lap->dibuat_oleh }}</span>
            <span class="lap-meta-dot">·</span>
            <span>{{ $lap->created_at->diffForHumans() }}</span>
            @if($lap->jumlah_aktor)
            <span class="lap-meta-dot">·</span>
            <span>{{ $lap->jumlah_aktor }} aktor</span>
            @endif
          </div>
        </div>
        <div class="lap-right">
          <div>
            <div class="lap-risiko-bar">
              <div class="lap-risiko-fill" style="width:{{ $risikoBarW }}%;background:{{ $risikoColor }}"></div>
            </div>
            <div style="font-size:10px;color:var(--text-faint);margin-top:3px;text-align:center;font-family:'JetBrains Mono',monospace">{{ $lap->tingkat_risiko }}/10</div>
          </div>
          <span class="risk-badge {{ $risikoClass }}">● {{ strtoupper($risikoLabel) }}</span>
          <span style="color:var(--text-faint);font-size:13px">→</span>
        </div>
      </a>
      @endforeach
    </div>

    @if($laporanList->hasPages())
    <div class="pagination-wrap">
      {{ $laporanList->links() }}
    </div>
    @endif
    @endif

  </div>
</div>

</body>
</html>