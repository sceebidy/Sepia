<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<title>SEPIA — Dashboard Analitik</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green:        #1a5c2e;
  --green-2:      #2e7d4a;
  --green-3:      #2e8a55;
  --green-light:  #edfaf3;
  --green-mid:    #c6edd9;
  --green-border: #8fd4b1;

  --text:         #0f172a;
  --text-muted:   #64748b;
  --text-faint:   #94a3b8;
  --border:       #e2e8f0;
  --border-light: #f1f5f9;
  --bg:           #ffffff;
  --bg-2:         #f8fafc;
  --bg-3:         #f1f5f9;

  --c-ideologi:       #6d28d9;
  --c-ideologi-l:     #f5f3ff;
  --c-ideologi-b:     #ddd6fe;
  --c-ideologi-glow:  rgba(109,40,217,0.12);

  --c-politik:        #0369a1;
  --c-politik-l:      #f0f9ff;
  --c-politik-b:      #bae6fd;
  --c-politik-glow:   rgba(3,105,161,0.12);

  --c-ekonomi:        #047857;
  --c-ekonomi-l:      #ecfdf5;
  --c-ekonomi-b:      #a7f3d0;
  --c-ekonomi-glow:   rgba(4,120,87,0.12);

  --c-sosbud:         #b45309;
  --c-sosbud-l:       #fffbeb;
  --c-sosbud-b:       #fde68a;
  --c-sosbud-glow:    rgba(180,83,9,0.12);

  --c-hankam:         #be123c;
  --c-hankam-l:       #fff1f2;
  --c-hankam-b:       #fecdd3;
  --c-hankam-glow:    rgba(190,18,60,0.12);

  --c-profil:         #0f766e;
  --c-profil-l:       #f0fdfa;
  --c-profil-b:       #99f6e4;
  --c-profil-glow:    rgba(15,118,110,0.12);

  --nav-width: 220px;
  --topbar-h:  56px;
}

html, body { height: 100%; }
body {
  font-family: 'Sora', sans-serif;
  background: var(--bg-3);
  color: var(--text);
  display: flex;
  overflow: hidden;
  font-size: 13px;
}

/* ── SIDENAV ── */
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
.user-row { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 8px; cursor: pointer; }
.user-row:hover { background: rgba(255,255,255,0.1); }
.user-avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); color: #fff; font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.user-name { font-size: 12px; font-weight: 500; color: #fff; }
.user-role { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 1px; }

/* ── MAIN ── */
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: var(--topbar-h); background: #fff; border-bottom: 1px solid var(--border); flex-shrink: 0; gap: 12px; }
.topbar-left { display: flex; align-items: center; gap: 14px; }
.page-title { font-size: 14.5px; font-weight: 700; letter-spacing: -0.01em; }
.topbar-date { font-size: 11.5px; color: var(--text-muted); background: var(--bg-3); padding: 4px 10px; border-radius: 20px; font-family: 'JetBrains Mono', monospace; }
.topbar-right { display: flex; align-items: center; gap: 6px; }
.tb-btn { padding: 7px 13px; font-size: 11.5px; border: 1px solid var(--border); border-radius: 8px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'Sora', sans-serif; font-weight: 500; transition: all 0.13s; display: flex; align-items: center; gap: 5px; }
.tb-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.tb-btn.primary { background: var(--green); color: #fff; border-color: var(--green); }
.tb-btn.primary:hover { background: var(--green-2); }
.notif-wrap { position: relative; }
.notif-wrap::after { content: ''; position: absolute; top: 5px; right: 10px; width: 6px; height: 6px; border-radius: 50%; background: #ef4444; border: 1.5px solid #fff; }

/* ── CONTENT ── */
.content { flex: 1; overflow-y: auto; padding: 20px 24px 36px; display: flex; flex-direction: column; gap: 20px; }
.content::-webkit-scrollbar { width: 5px; }
.content::-webkit-scrollbar-track { background: transparent; }
.content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

/* ── STAT CARDS ── */
.stat-row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
.stat-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 14px 16px; transition: box-shadow 0.18s, transform 0.18s; cursor: default; animation: fadeInUp 0.4s ease both; }
.stat-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.07); transform: translateY(-1px); }
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.10s; }
.stat-card:nth-child(3) { animation-delay: 0.15s; }
.stat-card:nth-child(4) { animation-delay: 0.20s; }
.stat-card:nth-child(5) { animation-delay: 0.25s; }
.stat-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.stat-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.stat-trend { font-size: 10px; padding: 3px 7px; border-radius: 20px; font-weight: 600; font-family: 'JetBrains Mono', monospace; }
.trend-up   { background: #dcfce7; color: #15803d; }
.trend-down { background: #fef2f2; color: #dc2626; }
.trend-neu  { background: var(--bg-3); color: var(--text-muted); }
.stat-value { font-size: 26px; font-weight: 800; line-height: 1; letter-spacing: -0.02em; }
.stat-label { font-size: 10.5px; color: var(--text-muted); margin-top: 4px; font-weight: 500; }
.stat-bar-wrap { margin-top: 10px; height: 3px; background: var(--bg-3); border-radius: 99px; overflow: hidden; }
.stat-bar-fill { height: 100%; border-radius: 99px; transition: width 1.2s cubic-bezier(.4,0,.2,1); width: 0%; }

/* ── SECTION HEAD ── */
.section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px; }
.section-head h2 { font-size: 10px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-muted); }
.section-head a { font-size: 11.5px; color: var(--green-2); text-decoration: none; font-weight: 500; }

/* ── CAT GRID ── */
.cat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
.cat-card { background: #fff; border: 1.5px solid var(--border); border-radius: 18px; overflow: hidden; transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s; cursor: pointer; display: flex; flex-direction: column; animation: fadeInUp 0.45s ease both; }
.cat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(0,0,0,0.09); }
.cat-card:nth-child(1) { animation-delay: 0.15s; }
.cat-card:nth-child(2) { animation-delay: 0.22s; }
.cat-card:nth-child(3) { animation-delay: 0.29s; }
.cat-card:nth-child(4) { animation-delay: 0.36s; }
.cat-card:nth-child(5) { animation-delay: 0.43s; }
.cat-card:nth-child(6) { animation-delay: 0.50s; }
.cat-stripe { height: 4px; flex-shrink: 0; }
.cat-body { padding: 16px 18px 14px; flex: 1; display: flex; flex-direction: column; gap: 13px; }
.cat-header { display: flex; align-items: flex-start; justify-content: space-between; }
.cat-title-row { display: flex; align-items: center; gap: 11px; }
.cat-icon { width: 42px; height: 42px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.cat-name { font-size: 14.5px; font-weight: 700; letter-spacing: -0.01em; }
.cat-desc { font-size: 10.5px; color: var(--text-muted); margin-top: 2px; line-height: 1.45; }
.risk-badge { padding: 4px 9px; border-radius: 20px; font-size: 9.5px; font-weight: 700; letter-spacing: 0.05em; white-space: nowrap; border: 1px solid transparent; }
.risk-high { background: #fef2f2; color: #be123c; border-color: #fecdd3; }
.risk-med  { background: #fffbeb; color: #a16207; border-color: #fde68a; }
.risk-low  { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.cat-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
.cat-stat { background: var(--bg-2); border: 1px solid var(--border-light); border-radius: 9px; padding: 8px 10px; }
.cat-stat-label { font-size: 9.5px; color: var(--text-faint); margin-bottom: 2px; font-weight: 500; }
.cat-stat-value { font-size: 18px; font-weight: 800; letter-spacing: -0.02em; font-family: 'JetBrains Mono', monospace; }
.cat-viz { display: flex; align-items: center; gap: 14px; }
.donut-wrap { flex-shrink: 0; position: relative; width: 72px; height: 72px; }
.donut-wrap svg { width: 72px; height: 72px; transform: rotate(-90deg); }
.donut-center-label { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.donut-center-val { font-size: 14px; font-weight: 800; font-family: 'JetBrains Mono', monospace; line-height: 1; }
.donut-center-sub { font-size: 8px; color: var(--text-faint); margin-top: 1px; font-weight: 500; }
.bar-list { flex: 1; display: flex; flex-direction: column; gap: 6px; }
.bar-item { display: flex; flex-direction: column; gap: 2px; }
.bar-item-top { display: flex; align-items: center; justify-content: space-between; }
.bar-item-label { font-size: 10px; color: var(--text-muted); font-weight: 500; }
.bar-item-val { font-size: 10px; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; }
.bar-track { height: 5px; background: var(--bg-3); border-radius: 99px; overflow: hidden; }
.bar-fill { height: 100%; border-radius: 99px; transition: width 1s cubic-bezier(.4,0,.2,1); width: 0%; }
.spark-label { font-size: 9.5px; color: var(--text-faint); margin-bottom: 6px; font-weight: 500; display: flex; justify-content: space-between; }
.sparkline-wrap canvas { width: 100% !important; height: 36px !important; }
.recent-list { display: flex; flex-direction: column; gap: 5px; }
.recent-item { display: flex; align-items: flex-start; gap: 8px; padding: 7px 10px; background: var(--bg-2); border: 1px solid var(--border-light); border-radius: 8px; transition: border-color 0.13s; }
.recent-item:hover { border-color: var(--border); }
.recent-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
.recent-text { font-size: 10.5px; color: var(--text); flex: 1; line-height: 1.5; font-weight: 400; }
.recent-time { font-size: 9.5px; color: var(--text-faint); white-space: nowrap; font-family: 'JetBrains Mono', monospace; }
.cat-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 10px; border-top: 1px solid var(--border-light); margin-top: auto; }
.cat-footer-meta { font-size: 10.5px; color: var(--text-faint); }
.cat-footer-meta strong { color: var(--text-muted); }
.cat-cta { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 7px; text-decoration: none; transition: opacity 0.13s; border: 1px solid transparent; }
.cat-cta:hover { opacity: 0.8; }

/* ── PROFIL ── */
.profil-list { display: flex; flex-direction: column; gap: 6px; }
.profil-row { display: flex; align-items: center; gap: 10px; padding: 7px 10px; background: var(--bg-2); border: 1px solid var(--border-light); border-radius: 9px; transition: border-color 0.13s; }
.profil-row:hover { border-color: var(--c-profil-b); }
.profil-av { width: 30px; height: 30px; border-radius: 50%; font-size: 11px; font-weight: 700; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.profil-info { flex: 1; min-width: 0; }
.profil-name { font-size: 11.5px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.profil-meta { font-size: 9.5px; color: var(--text-faint); }

/* ── THEMING PER KATEGORI ── */
.c-ideologi .cat-stripe     { background: linear-gradient(90deg, var(--c-ideologi), #9333ea); }
.c-ideologi .cat-icon       { background: var(--c-ideologi-l); }
.c-ideologi .cat-stat-value { color: var(--c-ideologi); }
.c-ideologi .cat-cta        { color: var(--c-ideologi); background: var(--c-ideologi-l); border-color: var(--c-ideologi-b); }
.c-ideologi:hover           { border-color: var(--c-ideologi-b); box-shadow: 0 12px 40px var(--c-ideologi-glow); }
.c-ideologi .recent-dot     { background: var(--c-ideologi); }

.c-politik .cat-stripe     { background: linear-gradient(90deg, var(--c-politik), #0ea5e9); }
.c-politik .cat-icon       { background: var(--c-politik-l); }
.c-politik .cat-stat-value { color: var(--c-politik); }
.c-politik .cat-cta        { color: var(--c-politik); background: var(--c-politik-l); border-color: var(--c-politik-b); }
.c-politik:hover           { border-color: var(--c-politik-b); box-shadow: 0 12px 40px var(--c-politik-glow); }
.c-politik .recent-dot     { background: var(--c-politik); }

.c-ekonomi .cat-stripe     { background: linear-gradient(90deg, var(--c-ekonomi), #10b981); }
.c-ekonomi .cat-icon       { background: var(--c-ekonomi-l); }
.c-ekonomi .cat-stat-value { color: var(--c-ekonomi); }
.c-ekonomi .cat-cta        { color: var(--c-ekonomi); background: var(--c-ekonomi-l); border-color: var(--c-ekonomi-b); }
.c-ekonomi:hover           { border-color: var(--c-ekonomi-b); box-shadow: 0 12px 40px var(--c-ekonomi-glow); }
.c-ekonomi .recent-dot     { background: var(--c-ekonomi); }

.c-sosbud .cat-stripe     { background: linear-gradient(90deg, var(--c-sosbud), #f59e0b); }
.c-sosbud .cat-icon       { background: var(--c-sosbud-l); }
.c-sosbud .cat-stat-value { color: var(--c-sosbud); }
.c-sosbud .cat-cta        { color: var(--c-sosbud); background: var(--c-sosbud-l); border-color: var(--c-sosbud-b); }
.c-sosbud:hover           { border-color: var(--c-sosbud-b); box-shadow: 0 12px 40px var(--c-sosbud-glow); }
.c-sosbud .recent-dot     { background: var(--c-sosbud); }

.c-hankam .cat-stripe     { background: linear-gradient(90deg, var(--c-hankam), #f43f5e); }
.c-hankam .cat-icon       { background: var(--c-hankam-l); }
.c-hankam .cat-stat-value { color: var(--c-hankam); }
.c-hankam .cat-cta        { color: var(--c-hankam); background: var(--c-hankam-l); border-color: var(--c-hankam-b); }
.c-hankam:hover           { border-color: var(--c-hankam-b); box-shadow: 0 12px 40px var(--c-hankam-glow); }
.c-hankam .recent-dot     { background: var(--c-hankam); }

.c-profil .cat-stripe     { background: linear-gradient(90deg, var(--c-profil), #14b8a6); }
.c-profil .cat-icon       { background: var(--c-profil-l); }
.c-profil .cat-stat-value { color: var(--c-profil); }
.c-profil .cat-cta        { color: var(--c-profil); background: var(--c-profil-l); border-color: var(--c-profil-b); }
.c-profil:hover           { border-color: var(--c-profil-b); box-shadow: 0 12px 40px var(--c-profil-glow); }

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(14px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
</head>
<body>

{{-- ══ DATA DARI CONTROLLER (diteruskan ke JS) ══ --}}
<script>
  const TREND_DATA    = @json($trendData);
  const KATEGORI_DATA = @json($kategoriData);
</script>

{{-- ══ SIDENAV ══ --}}
<nav class="sidenav">
  <div class="sidenav-brand">
    <div class="brand-logo">SEPIA</div>
    <div class="brand-sub">Sistem Analitik Intelijen</div>
  </div>
  <div class="sidenav-section">
    <div class="sidenav-label">Menu Utama</div>
    <a class="nav-item active" href="{{ route('dashboard') }}">
      <div class="nav-icon">📊</div><div class="nav-item-text">Dashboard</div>
    </a>
    <a class="nav-item" href="{{ route('datapool.index') }}">
      <div class="nav-icon">📋</div><div class="nav-item-text">RPI</div>
      <span class="nav-badge">3</span>
    </a>
    <a class="nav-item" href="#">
      <div class="nav-icon">🗄️</div><div class="nav-item-text">Data Pool</div>
    </a>
    <a class="nav-item" href="#">
      <div class="nav-icon">🎨</div><div class="nav-item-text">Personalisasi</div>
    </a>
    <a class="nav-item" href="#">
      <div class="nav-icon">📅</div><div class="nav-item-text">Daily Report</div>
      <span class="nav-badge alert">!</span>
    </a>
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

{{-- ══ MAIN ══ --}}
<div class="main">

  {{-- TOPBAR --}}
  <div class="topbar">
    <div class="topbar-left">
      <div class="page-title">Dashboard Pemantauan</div>
      <div class="topbar-date" id="topbar-date"></div>
    </div>
    <div class="topbar-right">
      <div class="notif-wrap">
        <button class="tb-btn">🔔 Notifikasi</button>
      </div>
      <button class="tb-btn">⬇ Ekspor</button>
      <button class="tb-btn primary">＋ Isu Baru</button>
    </div>
  </div>

  {{-- CONTENT --}}
  <div class="content">

    {{-- ══ STAT CARDS ══ --}}
    <div class="stat-row">

      <div class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" style="background:#f5f3ff">🧠</div>
          <span class="stat-trend trend-up">↑ Aktif</span>
        </div>
        <div class="stat-value">{{ $totalIsu }}</div>
        <div class="stat-label">Total Isu Aktif</div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-fill" data-w="71" style="background:#6d28d9"></div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" style="background:#fef2f2">🚨</div>
          <span class="stat-trend trend-down">● Tinggi</span>
        </div>
        <div class="stat-value" style="color:#be123c">{{ $risikoTinggi }}</div>
        <div class="stat-label">Risiko Tinggi</div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-fill" data-w="{{ $totalIsu > 0 ? round(($risikoTinggi / $totalIsu) * 100) : 0 }}" style="background:#be123c"></div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" style="background:#f0f9ff">📄</div>
          <span class="stat-trend trend-up">↑ Laporan</span>
        </div>
        <div class="stat-value">{{ $totalLaporan }}</div>
        <div class="stat-label">Laporan Dibuat</div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-fill" data-w="56" style="background:#0369a1"></div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" style="background:#ecfdf5">🔍</div>
          <span class="stat-trend trend-neu">—</span>
        </div>
        <div class="stat-value">{{ $totalAnalisis }}</div>
        <div class="stat-label">Analisis Diproses</div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-fill" data-w="85" style="background:#047857"></div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" style="background:#f0fdfa">👤</div>
          <span class="stat-trend trend-up">↑ Pantau</span>
        </div>
        <div class="stat-value">{{ $totalTokoh }}</div>
        <div class="stat-label">Tokoh Dipantau</div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-fill" data-w="63" style="background:#0f766e"></div>
        </div>
      </div>

    </div>

    {{-- ══ CATEGORY CARDS ══ --}}
    <div class="section-head">
      <h2>Kategori Pemantauan</h2>
      <a href="#">Lihat semua →</a>
    </div>

    <div class="cat-grid">

      {{-- ① IDEOLOGI --}}
      @php $kat = $kategoriData['ideologi']; $subPct = $kat['sub_persentase']; @endphp
      <div class="cat-card c-ideologi">
        <div class="cat-stripe"></div>
        <div class="cat-body">
          <div class="cat-header">
            <div class="cat-title-row">
              <div class="cat-icon">🧠</div>
              <div>
                <div class="cat-name">Ideologi</div>
                <div class="cat-desc">Radikalisme · Ekstremisme · Separatisme</div>
              </div>
            </div>
            <span class="risk-badge {{ $kat['risiko_dominan'] === 'tinggi' ? 'risk-high' : ($kat['risiko_dominan'] === 'sedang' ? 'risk-med' : 'risk-low') }}">
              ● {{ strtoupper($kat['risiko_dominan']) }}
            </span>
          </div>
          <div class="cat-stats">
            <div class="cat-stat"><div class="cat-stat-label">Total Isu</div><div class="cat-stat-value">{{ $kat['total'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Minggu Ini</div><div class="cat-stat-value">{{ $kat['minggu_ini'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Aktor</div><div class="cat-stat-value">{{ $kat['total_aktor'] }}</div></div>
          </div>
          <div class="cat-viz">
            <div class="donut-wrap">
              <canvas id="donut-ideologi" width="72" height="72"></canvas>
              <div class="donut-center-label">
                <div class="donut-center-val" style="color:#6d28d9">{{ $subPct['radikalisme'] ?? 0 }}%</div>
                <div class="donut-center-sub">Rad.</div>
              </div>
            </div>
            <div class="bar-list">
              @foreach(['radikalisme' => '#6d28d9', 'separatisme' => '#a78bfa', 'ekstremisme' => '#ddd6fe'] as $sub => $color)
              <div class="bar-item">
                <div class="bar-item-top">
                  <span class="bar-item-label">{{ ucfirst($sub) }}</span>
                  <span class="bar-item-val">{{ $subPct[$sub] ?? 0 }}%</span>
                </div>
                <div class="bar-track">
                  <div class="bar-fill" data-w="{{ $subPct[$sub] ?? 0 }}" style="background:{{ $color }}"></div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="sparkline-section">
            <div class="spark-label"><span>Tren 7 hari</span><span style="color:#6d28d9;font-weight:600">Isu per hari</span></div>
            <div class="sparkline-wrap"><canvas id="spark-ideologi" height="36"></canvas></div>
          </div>
          <div class="recent-list">
            @foreach($kat['recent'] as $item)
            <div class="recent-item">
              <div class="recent-dot"></div>
              <div class="recent-text">{{ $item->judul }}</div>
              <div class="recent-time">{{ $item->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
          </div>
          <div class="cat-footer">
            <div class="cat-footer-meta">Total <strong>{{ $kat['total'] }} isu</strong></div>
            <a class="cat-cta" href="#">Buka Kategori →</a>
          </div>
        </div>
      </div>

      {{-- ② POLITIK --}}
      @php $kat = $kategoriData['politik']; $subPct = $kat['sub_persentase']; @endphp
      <div class="cat-card c-politik">
        <div class="cat-stripe"></div>
        <div class="cat-body">
          <div class="cat-header">
            <div class="cat-title-row">
              <div class="cat-icon">🏛️</div>
              <div>
                <div class="cat-name">Politik</div>
                <div class="cat-desc">Stabilitas · Elektoral · Intervensi Asing</div>
              </div>
            </div>
            <span class="risk-badge {{ $kat['risiko_dominan'] === 'tinggi' ? 'risk-high' : ($kat['risiko_dominan'] === 'sedang' ? 'risk-med' : 'risk-low') }}">
              ● {{ strtoupper($kat['risiko_dominan']) }}
            </span>
          </div>
          <div class="cat-stats">
            <div class="cat-stat"><div class="cat-stat-label">Total Isu</div><div class="cat-stat-value">{{ $kat['total'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Minggu Ini</div><div class="cat-stat-value">{{ $kat['minggu_ini'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Aktor</div><div class="cat-stat-value">{{ $kat['total_aktor'] }}</div></div>
          </div>
          <div class="cat-viz">
            <div class="donut-wrap">
              <canvas id="donut-politik" width="72" height="72"></canvas>
              <div class="donut-center-label">
                <div class="donut-center-val" style="color:#0369a1">{{ $subPct['elektoral'] ?? 0 }}%</div>
                <div class="donut-center-sub">Elek.</div>
              </div>
            </div>
            <div class="bar-list">
              @foreach(['elektoral' => '#0369a1', 'intervensi_asing' => '#38bdf8', 'oposisi' => '#bae6fd'] as $sub => $color)
              <div class="bar-item">
                <div class="bar-item-top">
                  <span class="bar-item-label">{{ ucwords(str_replace('_', ' ', $sub)) }}</span>
                  <span class="bar-item-val">{{ $subPct[$sub] ?? 0 }}%</span>
                </div>
                <div class="bar-track">
                  <div class="bar-fill" data-w="{{ $subPct[$sub] ?? 0 }}" style="background:{{ $color }}"></div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="sparkline-section">
            <div class="spark-label"><span>Tren 7 hari</span><span style="color:#0369a1;font-weight:600">Isu per hari</span></div>
            <div class="sparkline-wrap"><canvas id="spark-politik" height="36"></canvas></div>
          </div>
          <div class="recent-list">
            @foreach($kat['recent'] as $item)
            <div class="recent-item">
              <div class="recent-dot"></div>
              <div class="recent-text">{{ $item->judul }}</div>
              <div class="recent-time">{{ $item->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
          </div>
          <div class="cat-footer">
            <div class="cat-footer-meta">Total <strong>{{ $kat['total'] }} isu</strong></div>
            <a class="cat-cta" href="#">Buka Kategori →</a>
          </div>
        </div>
      </div>

      {{-- ③ EKONOMI --}}
      @php $kat = $kategoriData['ekonomi']; $subPct = $kat['sub_persentase']; @endphp
      <div class="cat-card c-ekonomi">
        <div class="cat-stripe"></div>
        <div class="cat-body">
          <div class="cat-header">
            <div class="cat-title-row">
              <div class="cat-icon">📈</div>
              <div>
                <div class="cat-name">Ekonomi</div>
                <div class="cat-desc">Keuangan · Korupsi · Investasi Asing</div>
              </div>
            </div>
            <span class="risk-badge {{ $kat['risiko_dominan'] === 'tinggi' ? 'risk-high' : ($kat['risiko_dominan'] === 'sedang' ? 'risk-med' : 'risk-low') }}">
              ● {{ strtoupper($kat['risiko_dominan']) }}
            </span>
          </div>
          <div class="cat-stats">
            <div class="cat-stat"><div class="cat-stat-label">Total Isu</div><div class="cat-stat-value">{{ $kat['total'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Minggu Ini</div><div class="cat-stat-value">{{ $kat['minggu_ini'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Aktor</div><div class="cat-stat-value">{{ $kat['total_aktor'] }}</div></div>
          </div>
          <div class="cat-viz">
            <div class="donut-wrap">
              <canvas id="donut-ekonomi" width="72" height="72"></canvas>
              <div class="donut-center-label">
                <div class="donut-center-val" style="color:#047857">{{ $subPct['korupsi'] ?? 0 }}%</div>
                <div class="donut-center-sub">Kor.</div>
              </div>
            </div>
            <div class="bar-list">
              @foreach(['korupsi' => '#047857', 'investasi_asing' => '#10b981', 'pencucian_uang' => '#a7f3d0'] as $sub => $color)
              <div class="bar-item">
                <div class="bar-item-top">
                  <span class="bar-item-label">{{ ucwords(str_replace('_', ' ', $sub)) }}</span>
                  <span class="bar-item-val">{{ $subPct[$sub] ?? 0 }}%</span>
                </div>
                <div class="bar-track">
                  <div class="bar-fill" data-w="{{ $subPct[$sub] ?? 0 }}" style="background:{{ $color }}"></div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="sparkline-section">
            <div class="spark-label"><span>Tren 7 hari</span><span style="color:#047857;font-weight:600">Isu per hari</span></div>
            <div class="sparkline-wrap"><canvas id="spark-ekonomi" height="36"></canvas></div>
          </div>
          <div class="recent-list">
            @foreach($kat['recent'] as $item)
            <div class="recent-item">
              <div class="recent-dot"></div>
              <div class="recent-text">{{ $item->judul }}</div>
              <div class="recent-time">{{ $item->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
          </div>
          <div class="cat-footer">
            <div class="cat-footer-meta">Total <strong>{{ $kat['total'] }} isu</strong></div>
            <a class="cat-cta" href="#">Buka Kategori →</a>
          </div>
        </div>
      </div>

      {{-- ④ SOSBUD --}}
      @php $kat = $kategoriData['sosbud']; $subPct = $kat['sub_persentase']; @endphp
      <div class="cat-card c-sosbud">
        <div class="cat-stripe"></div>
        <div class="cat-body">
          <div class="cat-header">
            <div class="cat-title-row">
              <div class="cat-icon">🤝</div>
              <div>
                <div class="cat-name">Sosial Budaya</div>
                <div class="cat-desc">Konflik SARA · Hoaks · Ketegangan Komunal</div>
              </div>
            </div>
            <span class="risk-badge {{ $kat['risiko_dominan'] === 'tinggi' ? 'risk-high' : ($kat['risiko_dominan'] === 'sedang' ? 'risk-med' : 'risk-low') }}">
              ● {{ strtoupper($kat['risiko_dominan']) }}
            </span>
          </div>
          <div class="cat-stats">
            <div class="cat-stat"><div class="cat-stat-label">Total Isu</div><div class="cat-stat-value">{{ $kat['total'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Minggu Ini</div><div class="cat-stat-value">{{ $kat['minggu_ini'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Aktor</div><div class="cat-stat-value">{{ $kat['total_aktor'] }}</div></div>
          </div>
          <div class="cat-viz">
            <div class="donut-wrap">
              <canvas id="donut-sosbud" width="72" height="72"></canvas>
              <div class="donut-center-label">
                <div class="donut-center-val" style="color:#b45309">{{ $subPct['hoaks_sara'] ?? 0 }}%</div>
                <div class="donut-center-sub">Hoaks</div>
              </div>
            </div>
            <div class="bar-list">
              @foreach(['hoaks_sara' => '#b45309', 'komunal' => '#f59e0b', 'budaya' => '#fde68a'] as $sub => $color)
              <div class="bar-item">
                <div class="bar-item-top">
                  <span class="bar-item-label">{{ ucwords(str_replace('_', ' ', $sub)) }}</span>
                  <span class="bar-item-val">{{ $subPct[$sub] ?? 0 }}%</span>
                </div>
                <div class="bar-track">
                  <div class="bar-fill" data-w="{{ $subPct[$sub] ?? 0 }}" style="background:{{ $color }}"></div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="sparkline-section">
            <div class="spark-label"><span>Tren 7 hari</span><span style="color:#b45309;font-weight:600">Isu per hari</span></div>
            <div class="sparkline-wrap"><canvas id="spark-sosbud" height="36"></canvas></div>
          </div>
          <div class="recent-list">
            @foreach($kat['recent'] as $item)
            <div class="recent-item">
              <div class="recent-dot"></div>
              <div class="recent-text">{{ $item->judul }}</div>
              <div class="recent-time">{{ $item->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
          </div>
          <div class="cat-footer">
            <div class="cat-footer-meta">Total <strong>{{ $kat['total'] }} isu</strong></div>
            <a class="cat-cta" href="#">Buka Kategori →</a>
          </div>
        </div>
      </div>

      {{-- ⑤ HANKAM --}}
      @php $kat = $kategoriData['hankam']; $subPct = $kat['sub_persentase']; @endphp
      <div class="cat-card c-hankam">
        <div class="cat-stripe"></div>
        <div class="cat-body">
          <div class="cat-header">
            <div class="cat-title-row">
              <div class="cat-icon">🛡️</div>
              <div>
                <div class="cat-name">Hankam</div>
                <div class="cat-desc">Siber · Terorisme · Pertahanan Nasional</div>
              </div>
            </div>
            <span class="risk-badge {{ $kat['risiko_dominan'] === 'tinggi' ? 'risk-high' : ($kat['risiko_dominan'] === 'sedang' ? 'risk-med' : 'risk-low') }}">
              ● {{ strtoupper($kat['risiko_dominan']) }}
            </span>
          </div>
          <div class="cat-stats">
            <div class="cat-stat"><div class="cat-stat-label">Total Isu</div><div class="cat-stat-value">{{ $kat['total'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Minggu Ini</div><div class="cat-stat-value">{{ $kat['minggu_ini'] }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Aktor</div><div class="cat-stat-value">{{ $kat['total_aktor'] }}</div></div>
          </div>
          <div class="cat-viz">
            <div class="donut-wrap">
              <canvas id="donut-hankam" width="72" height="72"></canvas>
              <div class="donut-center-label">
                <div class="donut-center-val" style="color:#be123c">{{ $subPct['siber'] ?? 0 }}%</div>
                <div class="donut-center-sub">Siber</div>
              </div>
            </div>
            <div class="bar-list">
              @foreach(['siber' => '#be123c', 'terorisme' => '#f43f5e', 'perbatasan' => '#fecdd3'] as $sub => $color)
              <div class="bar-item">
                <div class="bar-item-top">
                  <span class="bar-item-label">{{ ucfirst($sub) }}</span>
                  <span class="bar-item-val">{{ $subPct[$sub] ?? 0 }}%</span>
                </div>
                <div class="bar-track">
                  <div class="bar-fill" data-w="{{ $subPct[$sub] ?? 0 }}" style="background:{{ $color }}"></div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="sparkline-section">
            <div class="spark-label"><span>Tren 7 hari</span><span style="color:#be123c;font-weight:600">Isu per hari</span></div>
            <div class="sparkline-wrap"><canvas id="spark-hankam" height="36"></canvas></div>
          </div>
          <div class="recent-list">
            @foreach($kat['recent'] as $item)
            <div class="recent-item">
              <div class="recent-dot"></div>
              <div class="recent-text">{{ $item->judul }}</div>
              <div class="recent-time">{{ $item->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
          </div>
          <div class="cat-footer">
            <div class="cat-footer-meta">Total <strong>{{ $kat['total'] }} isu</strong></div>
            <a class="cat-cta" href="#">Buka Kategori →</a>
          </div>
        </div>
      </div>

      {{-- ⑥ PROFILING TOKOH --}}
      @php $kat = $kategoriData['ideologi']; @endphp
      <div class="cat-card c-profil">
        <div class="cat-stripe"></div>
        <div class="cat-body">
          <div class="cat-header">
            <div class="cat-title-row">
              <div class="cat-icon">👤</div>
              <div>
                <div class="cat-name">Profiling Tokoh</div>
                <div class="cat-desc">Jaringan · Afiliasi · Riwayat Aktivitas</div>
              </div>
            </div>
            <span class="risk-badge risk-low">● PANTAU</span>
          </div>
          <div class="cat-stats">
            <div class="cat-stat"><div class="cat-stat-label">Total Profil</div><div class="cat-stat-value">{{ $totalTokoh }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Risiko Tinggi</div><div class="cat-stat-value">{{ $tokohPrioritas->count() }}</div></div>
            <div class="cat-stat"><div class="cat-stat-label">Kategori</div><div class="cat-stat-value">5</div></div>
          </div>
          <div>
            <div class="spark-label" style="margin-bottom:8px">
              <span>Tokoh Prioritas Tinggi</span>
              <span style="color:var(--c-profil);font-weight:600">Lihat semua →</span>
            </div>
            <div class="profil-list">
              @foreach($tokohPrioritas as $tokoh)
              @php
                $avatarColors = [
                  'ideologi' => 'linear-gradient(135deg,#6d28d9,#9333ea)',
                  'politik'  => 'linear-gradient(135deg,#0369a1,#0ea5e9)',
                  'hankam'   => 'linear-gradient(135deg,#be123c,#f43f5e)',
                  'ekonomi'  => 'linear-gradient(135deg,#047857,#10b981)',
                  'sosbud'   => 'linear-gradient(135deg,#b45309,#f59e0b)',
                ];
                $avatarBg = $avatarColors[$tokoh->kategori] ?? 'linear-gradient(135deg,#64748b,#94a3b8)';
              @endphp
              <div class="profil-row">
                <div class="profil-av" style="background:{{ $avatarBg }}">{{ $tokoh->inisial }}</div>
                <div class="profil-info">
                  <div class="profil-name">{{ $tokoh->nama }}</div>
                  <div class="profil-meta">{{ $tokoh->peran }} · {{ $tokoh->wilayah }}</div>
                </div>
                <div>
                  <span class="risk-badge {{ $tokoh->risiko === 'tinggi' ? 'risk-high' : ($tokoh->risiko === 'sedang' ? 'risk-med' : 'risk-low') }}">
                    {{ strtoupper($tokoh->risiko) }}
                  </span>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="cat-footer">
            <div class="cat-footer-meta">Total <strong>{{ $totalTokoh }} tokoh</strong></div>
            <a class="cat-cta" href="#">Buka Kategori →</a>
          </div>
        </div>
      </div>
    </div>
{{-- ══ RPI STATS ══ --}}
    <div class="section-head">
      <h2>Analisis RPI — Statistik Risiko</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
      <div class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" style="background:#f0f9ff">📋</div>
          <span class="stat-trend trend-neu">RPI</span>
        </div>
        <div class="stat-value">{{ $rpiStats['total'] }}</div>
        <div class="stat-label">Total Analisis RPI</div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-fill" data-w="100" style="background:#0369a1"></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" style="background:#fef2f2">🔴</div>
          <span class="stat-trend trend-down">Tinggi</span>
        </div>
        <div class="stat-value" style="color:#be123c">{{ $rpiStats['risiko_tinggi'] }}</div>
        <div class="stat-label">Risiko Tinggi (≥7)</div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-fill" data-w="{{ $rpiStats['total'] > 0 ? round(($rpiStats['risiko_tinggi']/$rpiStats['total'])*100) : 0 }}" style="background:#be123c"></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" style="background:#fffbeb">🟡</div>
          <span class="stat-trend trend-neu">Sedang</span>
        </div>
        <div class="stat-value" style="color:#b45309">{{ $rpiStats['risiko_sedang'] }}</div>
        <div class="stat-label">Risiko Sedang (4–7)</div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-fill" data-w="{{ $rpiStats['total'] > 0 ? round(($rpiStats['risiko_sedang']/$rpiStats['total'])*100) : 0 }}" style="background:#b45309"></div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-top">
          <div class="stat-icon" style="background:#f0fdf4">🟢</div>
          <span class="stat-trend trend-up">Rendah</span>
        </div>
        <div class="stat-value" style="color:#15803d">{{ $rpiStats['risiko_rendah'] }}</div>
        <div class="stat-label">Risiko Rendah (&lt;4)</div>
        <div class="stat-bar-wrap">
          <div class="stat-bar-fill" data-w="{{ $rpiStats['total'] > 0 ? round(($rpiStats['risiko_rendah']/$rpiStats['total'])*100) : 0 }}" style="background:#15803d"></div>
        </div>
      </div>
    </div>

    {{-- ══ GRAFIK TREN ANALISIS RPI ══ --}}
    <div class="section-head">
      <h2>Tren Analisis RPI — 7 Hari Terakhir</h2>
    </div>
    <div style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:20px 24px">
      <canvas id="chart-rpi-trend" height="80"></canvas>
    </div>

    {{-- ══ RIWAYAT LAPORAN ══ --}}
    <div class="section-head">
      <h2>Riwayat Laporan RPI</h2>
      <a href="{{ route('laporan.index') }}">Lihat semua →</a>
    </div>

    @if($riwayatLaporan->isEmpty())
    <div style="background:#fff;border:1.5px dashed var(--border);border-radius:14px;padding:32px;text-align:center;color:var(--text-muted)">
      <div style="font-size:32px;margin-bottom:10px">📋</div>
      <div style="font-size:13px;font-weight:600">Belum ada laporan</div>
      <div style="font-size:11.5px;margin-top:4px">Generate laporan dari halaman analisis folder RPI</div>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:8px">
      @foreach($riwayatLaporan as $lap)
      <a href="{{ route('laporan.show', $lap) }}" style="text-decoration:none">
        <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:14px 18px;display:flex;align-items:center;gap:14px;transition:all 0.13s" onmouseover="this.style.borderColor='#8fd4b1';this.style.background='#edfaf3'" onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#fff'">
          <div style="width:40px;height:40px;border-radius:10px;background:#f0f9ff;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">📄</div>
          <div style="flex:1;min-width:0">
            <div style="font-size:13px;font-weight:600;color:#0f172a;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $lap->judul }}</div>
            <div style="font-size:11px;color:#64748b;display:flex;align-items:center;gap:10px">
              <span>{{ $lap->nomor_laporan ?? 'LAP-'.str_pad($lap->id,4,'0',STR_PAD_LEFT).'/'.date('Y',strtotime($lap->created_at)) }}</span>
              <span>·</span>
              <span>{{ $lap->dibuat_oleh }}</span>
              <span>·</span>
              <span>{{ $lap->created_at->diffForHumans() }}</span>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
            @if($lap->tingkat_risiko >= 7)
              <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;background:#fef2f2;color:#be123c;border:1px solid #fecdd3">● TINGGI</span>
            @elseif($lap->tingkat_risiko >= 4)
              <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;background:#fffbeb;color:#a16207;border:1px solid #fde68a">● SEDANG</span>
            @else
              <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0">● RENDAH</span>
            @endif
            <span style="font-size:11px;color:#94a3b8">→</span>
          </div>
        </div>
      </a>
      @endforeach
    </div>
    @endif
    </div>
  </div>{{-- end .content --}}
</div>{{-- end .main --}}

{{-- Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
// ── Topbar date
document.getElementById('topbar-date').textContent =
  new Date().toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

// ── Animate bar fills
window.addEventListener('load', () => {
  setTimeout(() => {
    document.querySelectorAll('.bar-fill[data-w]').forEach(el => {
      el.style.width = el.dataset.w + '%';
    });
    document.querySelectorAll('.stat-bar-fill[data-w]').forEach(el => {
      el.style.width = el.dataset.w + '%';
    });
  }, 180);
});

// ── Config sparkline
const sparkConfig = (data, color) => ({
  type: 'line',
  data: {
    labels: ['6h lalu','5h lalu','4h lalu','3h lalu','2h lalu','1h lalu','Hari ini'],
    datasets: [{
      data,
      borderColor: color,
      backgroundColor: color + '22',
      borderWidth: 2,
      fill: true,
      tension: 0.4,
      pointRadius: 0,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { enabled: false } },
    scales: { x: { display: false }, y: { display: false } },
    animation: { duration: 1000 },
  }
});

// ── Config donut
const donutConfig = (data, colors) => ({
  type: 'doughnut',
  data: {
    datasets: [{
      data,
      backgroundColor: colors,
      borderWidth: 0,
      hoverOffset: 4,
    }]
  },
  options: {
    responsive: false,
    cutout: '65%',
    plugins: { legend: { display: false }, tooltip: { enabled: false } },
    animation: { animateRotate: true, duration: 1000 },
  }
});

// ── Render semua chart dari data PHP
const T = TREND_DATA;
const K = KATEGORI_DATA;

// Sparklines
new Chart(document.getElementById('spark-ideologi'), sparkConfig(T.ideologi, '#6d28d9'));
new Chart(document.getElementById('spark-politik'),  sparkConfig(T.politik,  '#0369a1'));
new Chart(document.getElementById('spark-ekonomi'),  sparkConfig(T.ekonomi,  '#047857'));
new Chart(document.getElementById('spark-sosbud'),   sparkConfig(T.sosbud,   '#b45309'));
new Chart(document.getElementById('spark-hankam'),   sparkConfig(T.hankam,   '#be123c'));

// Donuts — ambil nilai sub_persentase per kategori
const getSubVals = (kat, subs) => subs.map(s => K[kat].sub_persentase[s] || 0);

new Chart(document.getElementById('donut-ideologi'), donutConfig(
  getSubVals('ideologi', ['radikalisme','separatisme','ekstremisme']),
  ['#6d28d9','#a78bfa','#ddd6fe']
));
new Chart(document.getElementById('donut-politik'), donutConfig(
  getSubVals('politik', ['elektoral','intervensi_asing','oposisi']),
  ['#0369a1','#38bdf8','#bae6fd']
));
new Chart(document.getElementById('donut-ekonomi'), donutConfig(
  getSubVals('ekonomi', ['korupsi','investasi_asing','pencucian_uang']),
  ['#047857','#10b981','#a7f3d0']
));
new Chart(document.getElementById('donut-sosbud'), donutConfig(
  getSubVals('sosbud', ['hoaks_sara','komunal','budaya']),
  ['#b45309','#f59e0b','#fde68a']
));
new Chart(document.getElementById('donut-hankam'), donutConfig(
  getSubVals('hankam', ['siber','terorisme','perbatasan']),
  ['#be123c','#f43f5e','#fecdd3']
));
// ── Grafik tren analisis RPI
new Chart(document.getElementById('chart-rpi-trend'), {
  type: 'bar',
  data: {
    labels: @json(collect(range(6,0,-1))->map(fn($i) => \Carbon\Carbon::now()->subDays($i)->format('d M'))),
    datasets: [{
      label: 'Analisis RPI',
      data: @json($analisisTrend),
      backgroundColor: '#1a5c2e22',
      borderColor: '#1a5c2e',
      borderWidth: 2,
      borderRadius: 6,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 } },
      x: { grid: { display: false } }
    }
  }
});
</script>

</body>
</html>