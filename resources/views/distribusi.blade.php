<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SEPIA — Distribusi: {{ $analisis->judul }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet" />
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green: #1a5c2e; --green-2: #2e7d4a; --green-light: #f0f7f2;
  --green-border: #b6d9c3; --text: #1a1a1a; --text-muted: #6b7280;
  --border: #e5e7eb; --bg: #ffffff; --bg-2: #f9fafb; --bg-3: #f3f4f6;
  --amber: #d97706; --amber-light: #fffbeb; --amber-border: #fde68a;
  --nav-width: 220px;
}
body { font-family: 'DM Sans', sans-serif; background: var(--bg-3); color: var(--text); height: 100vh; display: flex; overflow: hidden; }

/* SIDENAV */
.sidenav { width: var(--nav-width); background: var(--green); display: flex; flex-direction: column; flex-shrink: 0; }
.sidenav-brand { padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.brand-logo { font-size: 18px; font-weight: 700; letter-spacing: 0.14em; color: #fff; }
.brand-sub { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 2px; letter-spacing: 0.05em; text-transform: uppercase; }
.sidenav-section { padding: 18px 12px 8px; }
.sidenav-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.4); padding: 0 8px; margin-bottom: 6px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; margin-bottom: 2px; text-decoration: none; transition: background 0.12s; border: 1px solid transparent; color: rgba(255,255,255,0.72); font-size: 13px; font-weight: 500; }
.nav-item:hover { background: rgba(255,255,255,0.1); color: #fff; }
.nav-item.active { background: rgba(255,255,255,0.14); border-color: rgba(255,255,255,0.18); color: #fff; }
.nav-item .nav-icon { width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; background: rgba(255,255,255,0.1); }
.nav-item-text { flex: 1; }
.nav-item-badge { font-size: 10px; background: rgba(255,255,255,0.2); color: #fff; padding: 2px 7px; border-radius: 20px; }
.nav-item-badge.alert { background: #ef4444; }
.sidenav-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 10px 12px; }
.sidenav-bottom { margin-top: auto; padding: 14px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
.user-row { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 8px; }
.user-avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); color: #fff; font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; }
.user-name { font-size: 12px; font-weight: 500; color: #fff; }
.user-role { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 1px; }

/* MAIN */
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; position: relative; }

/* TOPBAR */
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 56px; background: #fff; border-bottom: 1px solid var(--border); flex-shrink: 0; z-index: 10; }
.topbar-left { display: flex; align-items: center; gap: 10px; }
.back-btn { display: flex; align-items: center; gap: 6px; padding: 6px 12px; border: 1px solid var(--border); border-radius: 20px; font-size: 12px; color: var(--text-muted); text-decoration: none; transition: all 0.12s; }
.back-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.topbar-divider { width: 1px; height: 20px; background: var(--border); }
.page-title { font-size: 15px; font-weight: 600; }
.topbar-right { display: flex; align-items: center; gap: 8px; }
.tb-btn { padding: 7px 14px; font-size: 12px; border: 1px solid var(--border); border-radius: 20px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.12s; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
.tb-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.tb-btn.amber { background: var(--amber); color: #fff; border-color: var(--amber); }
.tb-btn.amber:hover { background: #b45309; }

/* BANNER */
.dist-banner { background: var(--amber); padding: 16px 28px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; position: relative; overflow: hidden; }
.dist-banner::after { content: 'DISTRIBUSI'; position: absolute; right: 180px; top: 50%; transform: translateY(-50%); font-size: 64px; font-weight: 900; letter-spacing: 0.15em; color: rgba(255,255,255,0.06); pointer-events: none; font-family: 'DM Serif Display', serif; }
.banner-left { display: flex; align-items: center; gap: 14px; z-index: 1; }
.banner-emoji { width: 46px; height: 46px; border-radius: 12px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.banner-title { font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 3px; }
.banner-meta { font-size: 11px; color: rgba(255,255,255,0.75); display: flex; align-items: center; gap: 10px; }
.banner-dot { width: 3px; height: 3px; border-radius: 50%; background: rgba(255,255,255,0.5); }
.banner-right { z-index: 1; display: flex; gap: 8px; align-items: center; }
.stat-pill { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 10px; padding: 8px 16px; text-align: center; }
.stat-pill-val { font-size: 20px; font-weight: 700; color: #fff; font-family: 'DM Mono', monospace; line-height: 1; }
.stat-pill-lbl { font-size: 10px; color: rgba(255,255,255,0.65); margin-top: 2px; text-transform: uppercase; letter-spacing: 0.06em; }

/* BODY LAYOUT */
.body-layout { flex: 1; display: flex; overflow: hidden; }

/* EMAIL SIDEBAR */
.email-sidebar { width: 320px; flex-shrink: 0; background: #fff; border-right: 1px solid var(--border); display: flex; flex-direction: column; overflow: hidden; }
.sidebar-head { padding: 14px 16px 10px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
.sidebar-head-title { font-size: 13px; font-weight: 600; }
.sidebar-count { font-size: 11px; color: var(--text-muted); background: var(--bg-3); border: 1px solid var(--border); border-radius: 20px; padding: 2px 9px; }
.email-list { flex: 1; overflow-y: auto; padding: 8px; }
.email-list::-webkit-scrollbar { width: 4px; }
.email-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

/* EMPTY STATE */
.empty-state-sidebar { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; padding: 32px 20px; text-align: center; }
.empty-state-sidebar .icon { font-size: 40px; margin-bottom: 12px; opacity: 0.5; }
.empty-state-sidebar .title { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
.empty-state-sidebar .sub { font-size: 11.5px; color: var(--text-muted); line-height: 1.6; }

/* EMAIL ITEM */
.email-item { padding: 12px 13px; border-radius: 10px; cursor: pointer; border: 1.5px solid transparent; margin-bottom: 5px; transition: all 0.12s; }
.email-item:hover { background: var(--bg-2); border-color: var(--border); }
.email-item.selected { background: var(--amber-light); border-color: #fcd34d; }
.email-item-top { display: flex; align-items: center; gap: 9px; margin-bottom: 6px; }
.inst-avatar { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
.inst-name { font-size: 12px; font-weight: 600; flex: 1; }
.email-status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dot-pending { background: #fbbf24; }
.dot-sent { background: #22c55e; }
.email-subject { font-size: 11px; color: var(--text); margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 500; }
.email-to { font-size: 10px; color: var(--text-muted); font-family: 'DM Mono', monospace; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.email-item-foot { display: flex; align-items: center; justify-content: space-between; margin-top: 7px; }
.email-label { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; }
.email-time { font-size: 10px; color: var(--text-muted); }

/* EMAIL PREVIEW */
.email-preview { flex: 1; overflow-y: auto; background: var(--bg-2); display: flex; flex-direction: column; }
.email-preview::-webkit-scrollbar { width: 5px; }
.email-preview::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

.preview-topbar { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px; background: #fff; border-bottom: 1px solid var(--border); flex-shrink: 0; position: sticky; top: 0; z-index: 5; }
.preview-title { font-size: 14px; font-weight: 600; }
.preview-actions { display: flex; gap: 7px; }
.pv-btn { display: flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; border: 1.5px solid var(--border); background: #fff; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.12s; color: var(--text-muted); }
.pv-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.pv-btn.send-btn { background: var(--green); color: #fff; border-color: var(--green); }
.pv-btn.send-btn:hover { background: #14482a; }
.pv-btn.sent-state { background: #f0fdf4; color: #16a34a; border-color: #86efac; cursor: default; }

.email-card { margin: 20px 24px; background: #fff; border: 1.5px solid var(--border); border-radius: 14px; overflow: hidden; flex-shrink: 0; }
.email-card.editing { border-color: #fcd34d; box-shadow: 0 0 0 3px rgba(252,211,77,0.15); }
.email-card-header { padding: 20px 24px 16px; border-bottom: 1px solid var(--border); }
.email-header-row { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
.inst-avatar-lg { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.email-inst-name { font-size: 14px; font-weight: 700; color: var(--text); }
.email-inst-addr { font-size: 12px; color: var(--text-muted); margin-top: 2px; font-family: 'DM Mono', monospace; }
.email-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.email-meta-row { display: flex; flex-direction: column; gap: 2px; }
.meta-label { font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.07em; }
.meta-value { font-size: 12px; color: var(--text); font-weight: 500; padding: 5px 8px; border-radius: 6px; border: 1.5px solid transparent; background: transparent; font-family: 'DM Sans', sans-serif; width: 100%; transition: all 0.12s; outline: none; }
.meta-value:focus { border-color: #fcd34d; background: var(--amber-light); }
.subject-row { margin-top: 10px; }
.subject-input { width: 100%; font-size: 14px; font-weight: 600; color: var(--text); padding: 6px 8px; border-radius: 7px; border: 1.5px solid transparent; background: transparent; font-family: 'DM Sans', sans-serif; outline: none; transition: all 0.12s; }
.subject-input:focus { border-color: #fcd34d; background: var(--amber-light); }
.email-body-section { padding: 20px 24px; }
.email-body-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 10px; font-weight: 600; }
.email-body-text { font-size: 13px; line-height: 1.75; color: var(--text); width: 100%; min-height: 280px; border: 1.5px solid transparent; border-radius: 10px; padding: 14px 16px; background: var(--bg-2); font-family: 'DM Sans', sans-serif; resize: vertical; outline: none; transition: all 0.12s; }
.email-body-text:focus { border-color: #fcd34d; background: var(--amber-light); }
.email-body-text[readonly] { cursor: default; resize: none; }
.email-card-footer { padding: 14px 24px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--bg-2); }
.attach-chip { display: flex; align-items: center; gap: 6px; padding: 5px 11px; border: 1px solid var(--border); border-radius: 8px; background: #fff; font-size: 11px; color: var(--text-muted); }

/* SENT OVERLAY */
.sent-overlay { margin: 20px 24px 0; background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 14px 18px; display: none; align-items: center; gap: 12px; }
.sent-overlay.visible { display: flex; }
.sent-title { font-size: 13px; font-weight: 600; color: #166534; }
.sent-sub { font-size: 11px; color: #15803d; margin-top: 2px; }
.sent-time { margin-left: auto; font-size: 11px; font-family: 'DM Mono', monospace; color: #16a34a; }

/* GENERATE STATE */
.generate-state { display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; padding: 40px; text-align: center; }
.generate-icon { font-size: 48px; margin-bottom: 16px; }
.generate-title { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
.generate-sub { font-size: 12px; color: var(--text-muted); line-height: 1.7; margin-bottom: 24px; max-width: 360px; }
.generate-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--amber); color: #fff; border: none; border-radius: 12px; font-size: 14px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.15s; }
.generate-btn:hover { background: #b45309; transform: translateY(-1px); }

/* LOADING */
.loading-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; display: none; align-items: center; justify-content: center; }
.loading-overlay.active { display: flex; }
.loading-card { background: #fff; border-radius: 18px; padding: 36px 40px; width: 400px; text-align: center; box-shadow: 0 24px 64px rgba(0,0,0,0.2); }
.loading-icon { font-size: 44px; margin-bottom: 16px; }
.loading-title { font-size: 16px; font-weight: 600; color: var(--amber); margin-bottom: 8px; }
.loading-sub { font-size: 12px; color: var(--text-muted); line-height: 1.7; margin-bottom: 20px; }
.dots { display: flex; align-items: center; justify-content: center; gap: 8px; }
.dot { width: 8px; height: 8px; border-radius: 50%; background: var(--amber); animation: dotPulse 1.4s infinite; }
.dot:nth-child(2) { animation-delay: 0.25s; }
.dot:nth-child(3) { animation-delay: 0.5s; }
@keyframes dotPulse { 0%,100%{opacity:0.2;transform:scale(0.8)} 50%{opacity:1;transform:scale(1)} }

/* FAB */
.fab-sendall { position: absolute; bottom: 28px; right: 28px; display: flex; align-items: center; gap: 10px; padding: 13px 24px; background: var(--green); color: #fff; border: none; border-radius: 40px; font-size: 14px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; box-shadow: 0 6px 28px rgba(26,92,46,0.35); transition: all 0.15s; z-index: 50; display: none; }
.fab-sendall.visible { display: flex; }
.fab-sendall:hover { background: #14482a; transform: translateY(-2px); }

/* LABEL COLORS */
.lbl-polres    { background: #dbeafe; color: #1d4ed8; }
.lbl-kejaksaan { background: #fef9c3; color: #a16207; }
.lbl-pengadilan{ background: #f3e8ff; color: #6d28d9; }
.lbl-kpk       { background: #fee2e2; color: #b91c1c; }
.lbl-kejagung  { background: #fce7f3; color: #9d174d; }
.lbl-bpk       { background: #ecfdf5; color: #166534; }
.lbl-other     { background: #f3f4f6; color: #374151; }
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
    <a class="nav-item" href="#"><div class="nav-icon">📅</div><div class="nav-item-text">Daily Report</div><span class="nav-item-badge alert">!</span></a>
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
      <a class="back-btn" href="{{ route('datapool.show', $folder) }}">← Kembali ke Folder</a>
      <div class="topbar-divider"></div>
      <div class="page-title">Distribusi Laporan</div>
    </div>
    <div class="topbar-right">
      <button class="tb-btn" id="btn-regenerate" onclick="generateDistribusi()" style="display:none">🔄 Generate Ulang</button>
      <button class="tb-btn amber" id="btn-sendall" onclick="sendAll()" style="display:none">📤 Kirim Semua</button>
    </div>
  </div>

  <div class="dist-banner">
    <div class="banner-left">
      <div class="banner-emoji">📤</div>
      <div>
        <div class="banner-title">{{ $analisis->judul }}</div>
        <div class="banner-meta">
          <span>Risiko {{ $analisis->tingkat_risiko }}/10</span>
          <span class="banner-dot"></span>
          <span>{{ $analisis->jumlah_sumber }} sumber · {{ $analisis->aktor->count() }} aktor</span>
          <span class="banner-dot"></span>
          <span>{{ $analisis->tanggal_analisis ? \Carbon\Carbon::parse($analisis->tanggal_analisis)->format('d M Y') : '-' }}</span>
        </div>
      </div>
    </div>
    <div class="banner-right">
      <div class="stat-pill">
        <div class="stat-pill-val" id="sentCount">0</div>
        <div class="stat-pill-lbl">Terkirim</div>
      </div>
      <div class="stat-pill">
        <div class="stat-pill-val" id="totalCount">0</div>
        <div class="stat-pill-lbl">Total</div>
      </div>
    </div>
  </div>

  <div class="body-layout">

    <div class="email-sidebar">
      <div class="sidebar-head">
        <span class="sidebar-head-title">Daftar Email</span>
        <span class="sidebar-count" id="sidebarCount">0 draft</span>
      </div>
      <div class="email-list" id="emailList">
        <div class="empty-state-sidebar">
          <div class="icon">📭</div>
          <div class="title">Belum ada email</div>
          <div class="sub">Klik "Generate Distribusi" untuk membuat draf email otomatis berdasarkan hasil analisis.</div>
        </div>
      </div>
    </div>

    <div class="email-preview" id="emailPreview">
      <div class="generate-state">
        <div class="generate-icon">📤</div>
        <div class="generate-title">Generate Distribusi Laporan</div>
        <div class="generate-sub">
          AI akan menganalisis hasil kasus ini dan menentukan instansi yang perlu dihubungi beserta isi surat yang sesuai — termasuk wilayah, jenis perkara, dan rekomendasi tindak lanjut.
        </div>
        <button class="generate-btn" onclick="generateDistribusi()">
          ⚡ Generate Distribusi Sekarang
        </button>
      </div>
    </div>

  </div>

  <button class="fab-sendall" id="fabSendAll" onclick="sendAll()">📤 Kirim Semua Sekarang</button>

</div>

{{-- LOADING OVERLAY --}}
<div class="loading-overlay" id="loadingOverlay">
  <div class="loading-card">
    <div class="loading-icon">🤖</div>
    <div class="loading-title">AI Sedang Menyusun Email...</div>
    <div class="loading-sub">
      Menganalisis lokasi, jenis perkara, dan aktor yang terlibat<br>
      <span style="font-size:11px">Menentukan instansi yang perlu dihubungi</span>
    </div>
    <div class="dots">
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
    </div>
  </div>
</div>

<script>
// Data analisis dari Laravel
const analisisData = {
  id: {{ $analisis->id }},
  judul: @json($analisis->judul),
  tingkat_risiko: {{ $analisis->tingkat_risiko }},
  prediksi_vonis: @json($analisis->prediksi_vonis),
  jumlah_sumber: {{ $analisis->jumlah_sumber }},
  folder_id: {{ $folder->id }},
  folder_nama: @json($folder->nama),
  swot: {
    S: @json($analisis->swotItems->where('tipe','S')->pluck('isi')),
    W: @json($analisis->swotItems->where('tipe','W')->pluck('isi')),
    O: @json($analisis->swotItems->where('tipe','O')->pluck('isi')),
    T: @json($analisis->swotItems->where('tipe','T')->pluck('isi')),
  },
  aktor: @json($analisis->aktor->map(fn($a) => ['nama'=>$a->nama,'peran'=>$a->peran,'status'=>$a->status])),
  timeline: @json($analisis->timeline->map(fn($t) => ['tanggal'=>$t->tanggal,'keterangan'=>$t->keterangan])),
  rekomendasi: @json($analisis->rekomendasi->map(fn($r) => ['judul'=>$r->judul,'deskripsi'=>$r->deskripsi,'prioritas'=>$r->prioritas])),
};

let emails = [];
let currentId = 0;
let editingId = null;
let sentCount = 0;

function generateDistribusi() {
  document.getElementById('loadingOverlay').classList.add('active');

  // Kirim ke n8n untuk generate email
  fetch('/datapool/{{ $folder->id }}/distribusi/{{ $analisis->id }}/generate', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ analisis: analisisData }),
  })
  .then(res => res.json())
  .then(data => {
    document.getElementById('loadingOverlay').classList.remove('active');
    if (data.success && data.emails) {
      emails = data.emails;
      initEmails();
    } else {
      alert('Gagal generate: ' + (data.error || 'Terjadi kesalahan'));
    }
  })
  .catch(err => {
    document.getElementById('loadingOverlay').classList.remove('active');
    console.error(err);
    alert('Tidak dapat terhubung ke server');
  });
}

function initEmails() {
  sentCount = 0;
  currentId = 0;
  document.getElementById('totalCount').textContent = emails.length;
  document.getElementById('sentCount').textContent = 0;
  document.getElementById('sidebarCount').textContent = emails.length + ' draft';
  document.getElementById('btn-regenerate').style.display = 'flex';
  document.getElementById('btn-sendall').style.display = 'flex';
  document.getElementById('fabSendAll').classList.add('visible');
  renderSidebar();
  renderPreview();
}

function getLabelClass(tipe) {
  const map = {
    'polres': 'lbl-polres', 'polda': 'lbl-polres',
    'kejaksaan': 'lbl-kejaksaan', 'kejari': 'lbl-kejaksaan',
    'pengadilan': 'lbl-pengadilan',
    'kpk': 'lbl-kpk',
    'kejagung': 'lbl-kejagung',
    'bpk': 'lbl-bpk',
  };
  const key = Object.keys(map).find(k => tipe?.toLowerCase().includes(k));
  return key ? map[key] : 'lbl-other';
}

function getIcon(tipe) {
  if (!tipe) return '🏢';
  const t = tipe.toLowerCase();
  if (t.includes('polres') || t.includes('polda')) return '🚔';
  if (t.includes('kejaksaan') || t.includes('kejari')) return '⚖️';
  if (t.includes('pengadilan')) return '🏛️';
  if (t.includes('kpk')) return '🔍';
  if (t.includes('kejagung')) return '🏢';
  if (t.includes('bpk')) return '📋';
  return '🏛️';
}

function getIconBg(tipe) {
  if (!tipe) return '#f3f4f6';
  const t = tipe.toLowerCase();
  if (t.includes('polres') || t.includes('polda')) return '#dbeafe';
  if (t.includes('kejaksaan') || t.includes('kejari')) return '#fef9c3';
  if (t.includes('pengadilan')) return '#f3e8ff';
  if (t.includes('kpk')) return '#fee2e2';
  if (t.includes('kejagung')) return '#fce7f3';
  if (t.includes('bpk')) return '#ecfdf5';
  return '#f3f4f6';
}

function renderSidebar() {
  const list = document.getElementById('emailList');
  if (!emails.length) {
    list.innerHTML = '<div class="empty-state-sidebar"><div class="icon">📭</div><div class="title">Belum ada email</div></div>';
    return;
  }
  list.innerHTML = emails.map((e, i) => `
    <div class="email-item ${i===currentId?'selected':''}" onclick="selectEmail(${i})">
      <div class="email-item-top">
        <div class="inst-avatar" style="background:${getIconBg(e.instansi)}">${getIcon(e.instansi)}</div>
        <div class="inst-name">${e.instansi || '-'}</div>
        <div class="email-status-dot ${e.sent?'dot-sent':'dot-pending'}"></div>
      </div>
      <div class="email-subject">${(e.subject||'').replace(/^\[.*?\] /,'')}</div>
      <div class="email-to">→ ${e.to || '-'}</div>
      <div class="email-item-foot">
        <span class="email-label ${getLabelClass(e.instansi)}">${e.tipe || 'Instansi'}</span>
        <span class="email-time">${e.sent ? '✓ Terkirim' : 'Draft'}</span>
      </div>
    </div>
  `).join('');
}

function selectEmail(id) {
  currentId = id;
  editingId = null;
  renderSidebar();
  renderPreview();
}

function renderPreview() {
  if (!emails.length) return;
  const e = emails[currentId];
  const isEditing = editingId === currentId;
  const ro = !isEditing ? 'readonly' : '';
  const iconBg = getIconBg(e.instansi);
  const icon = getIcon(e.instansi);

  document.getElementById('emailPreview').innerHTML = `
    <div class="preview-topbar">
      <div class="preview-title">${e.instansi || '-'}</div>
      <div class="preview-actions">
        ${e.sent
          ? `<button class="pv-btn sent-state">✓ Terkirim</button>`
          : `
            <button class="pv-btn" onclick="toggleEdit(${currentId})">
              ${isEditing ? '✕ Batal' : '✏️ Edit'}
            </button>
            ${isEditing ? `<button class="pv-btn" onclick="saveEdit(${currentId})" style="border-color:var(--amber);color:var(--amber);">💾 Simpan</button>` : ''}
            <button class="pv-btn" onclick="openInEmail(${currentId})">📧 Buka di Email</button>
            <button class="pv-btn send-btn" onclick="markSent(${currentId})">✓ Tandai Terkirim</button>
          `
        }
      </div>
    </div>

    ${e.sent ? `
    <div class="sent-overlay visible">
      <div style="font-size:22px">✅</div>
      <div>
        <div class="sent-title">Email berhasil ditandai terkirim</div>
        <div class="sent-sub">Terkirim ke ${e.to}</div>
      </div>
      <div class="sent-time">${e.sentTime || ''}</div>
    </div>` : ''}

    <div class="email-card ${isEditing?'editing':''}">
      <div class="email-card-header">
        <div class="email-header-row">
          <div class="inst-avatar-lg" style="background:${iconBg}">${icon}</div>
          <div>
            <div class="email-inst-name">${e.instansi || '-'}</div>
            <div class="email-inst-addr">${e.to || '-'}</div>
          </div>
        </div>
        <div class="email-meta-grid">
          <div class="email-meta-row">
            <span class="meta-label">Kepada (To)</span>
            <input class="meta-value" id="to-${currentId}" value="${e.to||''}" ${ro} />
          </div>
          <div class="email-meta-row">
            <span class="meta-label">Tembusan (CC)</span>
            <input class="meta-value" id="cc-${currentId}" value="${e.cc||''}" ${ro} />
          </div>
          <div class="email-meta-row">
            <span class="meta-label">Dari (From)</span>
            <input class="meta-value" value="sepia-system@bpk.go.id" readonly />
          </div>
          <div class="email-meta-row">
            <span class="meta-label">Tanggal</span>
            <input class="meta-value" value="${new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'})}" readonly />
          </div>
        </div>
        <div class="subject-row">
          <div class="meta-label" style="margin-bottom:6px">Subjek</div>
          <input class="subject-input" id="subj-${currentId}" value="${(e.subject||'').replace(/"/g,'&quot;')}" ${ro} />
        </div>
      </div>

      <div class="email-body-section">
        <div class="email-body-label">Isi Surat</div>
        <textarea class="email-body-text" id="body-${currentId}" ${ro}>${e.body||''}</textarea>
      </div>

      <div class="email-card-footer">
        <div style="display:flex;gap:8px">
          <div class="attach-chip">📎 Ringkasan_Analisis_${analisisData.judul.replace(/\s+/g,'_')}.pdf</div>
        </div>
        <div style="font-size:11px;color:${isEditing?'var(--amber)':'var(--text-muted)'}">
          ${isEditing ? '✏️ Mode Edit Aktif' : ''}
        </div>
      </div>
    </div>
  `;
}

function toggleEdit(id) {
  editingId = editingId === id ? null : id;
  renderPreview();
  if (editingId === id) {
    setTimeout(() => { const b = document.getElementById('body-'+id); if(b) b.focus(); }, 50);
  }
}

function saveEdit(id) {
  emails[id].to      = document.getElementById('to-'+id)?.value || emails[id].to;
  emails[id].cc      = document.getElementById('cc-'+id)?.value || emails[id].cc;
  emails[id].subject = document.getElementById('subj-'+id)?.value || emails[id].subject;
  emails[id].body    = document.getElementById('body-'+id)?.value || emails[id].body;
  editingId = null;
  renderSidebar();
  renderPreview();
}

function openInEmail(id) {
  const e = emails[id];
  const to = e.to || '';
  const cc = e.cc || '';
  const subject = encodeURIComponent(e.subject || '');
  const body = encodeURIComponent(e.body || '');
  const mailtoUrl = `mailto:${to}?cc=${cc}&subject=${subject}&body=${body}`;
  
  // Coba buka mailto
  const link = document.createElement('a');
  link.href = mailtoUrl;
  link.click();
  
  // Fallback: tampilkan info untuk copy manual
  setTimeout(() => {
    if (document.hasFocus()) {
      // Jika tab masih fokus, berarti mailto tidak terbuka
      // Tampilkan modal copy
      showEmailCopy(id);
    }
  }, 1000);
}

function showEmailCopy(id) {
  const e = emails[id];
  const existing = document.getElementById('email-copy-modal');
  if (existing) existing.remove();
  
  const modal = document.createElement('div');
  modal.id = 'email-copy-modal';
  modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:300;display:flex;align-items:center;justify-content:center';
  modal.innerHTML = `
    <div style="background:#fff;border-radius:16px;padding:28px;width:480px;max-width:95vw">
      <div style="font-size:15px;font-weight:600;margin-bottom:16px">📧 Salin Info Email</div>
      <div style="margin-bottom:10px">
        <div style="font-size:10px;color:#6b7280;margin-bottom:4px">KEPADA</div>
        <div style="font-size:13px;font-family:monospace;background:#f3f4f6;padding:8px 10px;border-radius:6px;cursor:pointer" onclick="navigator.clipboard.writeText('${e.to||''}');this.style.background='#f0fdf4'">${e.to||'-'}</div>
      </div>
      <div style="margin-bottom:10px">
        <div style="font-size:10px;color:#6b7280;margin-bottom:4px">SUBJEK</div>
        <div style="font-size:13px;background:#f3f4f6;padding:8px 10px;border-radius:6px;cursor:pointer" onclick="navigator.clipboard.writeText('${(e.subject||'').replace(/'/g,"\\'")}');this.style.background='#f0fdf4'">${e.subject||'-'}</div>
      </div>
      <div style="margin-bottom:20px">
        <div style="font-size:10px;color:#6b7280;margin-bottom:4px">ISI SURAT (klik untuk salin)</div>
        <div style="font-size:12px;background:#f3f4f6;padding:10px;border-radius:6px;max-height:150px;overflow-y:auto;cursor:pointer;white-space:pre-wrap" onclick="navigator.clipboard.writeText(emails[${id}].body);this.style.background='#f0fdf4'">${e.body||'-'}</div>
      </div>
      <div style="display:flex;justify-content:flex-end;gap:8px">
        <button onclick="document.getElementById('email-copy-modal').remove()" style="padding:8px 16px;border:1px solid #e5e7eb;border-radius:8px;background:#fff;cursor:pointer">Tutup</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
  modal.addEventListener('click', (ev) => { if(ev.target===modal) modal.remove(); });
}

function markSent(id) {
  emails[id].sent = true;
  const now = new Date();
  emails[id].sentTime = now.toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit'}) + ' WIB';
  sentCount++;
  document.getElementById('sentCount').textContent = sentCount;
  editingId = null;
  renderSidebar();
  renderPreview();
}

function sendAll() {
  emails.forEach((e, i) => {
    if (!e.sent) {
      setTimeout(() => openInEmail(i), i * 300);
    }
  });
}
</script>
</body>
</html>