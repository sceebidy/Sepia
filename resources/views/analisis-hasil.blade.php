<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<title>SEPIA — Hasil Analisis: {{ $analisis->judul }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet" />
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green:#1a5c2e; --green-2:#2e7d4a; --green-light:#f0f7f2;
  --green-border:#b6d9c3; --text:#1a1a1a; --text-muted:#6b7280;
  --border:#e5e7eb; --bg:#fff; --bg-2:#f9fafb; --bg-3:#f3f4f6;
  --amber:#d97706; --nav-width:220px;
  --s-bg:#f0fdf4;--s-border:#86efac;--s-text:#166534;--s-accent:#16a34a;
  --w-bg:#fff7ed;--w-border:#fdba74;--w-text:#9a3412;--w-accent:#ea580c;
  --o-bg:#eff6ff;--o-border:#93c5fd;--o-text:#1e40af;--o-accent:#2563eb;
  --t-bg:#fef2f2;--t-border:#fca5a5;--t-text:#991b1b;--t-accent:#dc2626;
}
body { font-family:'DM Sans',sans-serif; background:var(--bg-3); color:var(--text); height:100vh; display:flex; overflow:hidden; }

.sidenav { width:var(--nav-width); background:var(--green); display:flex; flex-direction:column; flex-shrink:0; }
.sidenav-brand { padding:22px 20px 18px; border-bottom:1px solid rgba(255,255,255,0.1); }
.brand-logo { font-size:18px; font-weight:700; letter-spacing:0.14em; color:#fff; }
.brand-sub { font-size:10px; color:rgba(255,255,255,0.5); margin-top:2px; letter-spacing:0.05em; text-transform:uppercase; }
.sidenav-section { padding:18px 12px 8px; }
.sidenav-label { font-size:10px; text-transform:uppercase; letter-spacing:0.1em; color:rgba(255,255,255,0.4); padding:0 8px; margin-bottom:6px; }
.nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; margin-bottom:2px; text-decoration:none; transition:background 0.12s; border:1px solid transparent; color:rgba(255,255,255,0.72); font-size:13px; font-weight:500; }
.nav-item:hover { background:rgba(255,255,255,0.1); color:#fff; }
.nav-item.active { background:rgba(255,255,255,0.14); border-color:rgba(255,255,255,0.18); color:#fff; }
.nav-item .nav-icon { width:30px; height:30px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; background:rgba(255,255,255,0.1); }
.nav-item-text { flex:1; }
.nav-item-badge { font-size:10px; background:rgba(255,255,255,0.2); color:#fff; padding:2px 7px; border-radius:20px; }
.nav-item-badge.alert { background:#ef4444; }
.sidenav-divider { height:1px; background:rgba(255,255,255,0.08); margin:10px 12px; }
.sidenav-bottom { margin-top:auto; padding:14px 12px; border-top:1px solid rgba(255,255,255,0.1); }
.user-row { display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:8px; }
.user-avatar { width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,0.2); color:#fff; font-size:12px; font-weight:600; display:flex; align-items:center; justify-content:center; }
.user-name { font-size:12px; font-weight:500; color:#fff; }
.user-role { font-size:10px; color:rgba(255,255,255,0.5); margin-top:1px; }

.main { flex:1; display:flex; flex-direction:column; overflow:hidden; position:relative; }
.topbar { display:flex; align-items:center; justify-content:space-between; padding:0 24px; height:56px; background:#fff; border-bottom:1px solid var(--border); flex-shrink:0; z-index:10; }
.topbar-left { display:flex; align-items:center; gap:10px; }
.back-btn { display:flex; align-items:center; gap:6px; padding:6px 12px; border:1px solid var(--border); border-radius:20px; font-size:12px; color:var(--text-muted); text-decoration:none; transition:all 0.12s; }
.back-btn:hover { border-color:var(--green-border); color:var(--green); background:var(--green-light); }
.topbar-divider { width:1px; height:20px; background:var(--border); }
.page-title { font-size:15px; font-weight:600; }
.topbar-right { display:flex; align-items:center; gap:8px; }
.tb-btn { padding:7px 14px; font-size:12px; border:1px solid var(--border); border-radius:20px; background:#fff; color:var(--text-muted); cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.12s; text-decoration:none; display:flex; align-items:center; gap:5px; }
.tb-btn:hover { border-color:var(--green-border); color:var(--green); background:var(--green-light); }

.result-banner { background:var(--green); padding:16px 28px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; position:relative; overflow:hidden; }
.result-banner::after { content:'ANALISIS'; position:absolute; right:220px; top:50%; transform:translateY(-50%); font-size:72px; font-weight:900; letter-spacing:0.15em; color:rgba(255,255,255,0.04); pointer-events:none; font-family:'DM Serif Display',serif; }
.banner-left { display:flex; align-items:center; gap:14px; z-index:1; }
.banner-emoji { width:46px; height:46px; border-radius:12px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
.banner-title { font-size:15px; font-weight:600; color:#fff; margin-bottom:3px; }
.banner-meta { font-size:11px; color:rgba(255,255,255,0.65); display:flex; align-items:center; gap:10px; }
.banner-dot { width:3px; height:3px; border-radius:50%; background:rgba(255,255,255,0.4); }
.banner-right { display:flex; align-items:center; gap:8px; z-index:1; }
.score-pill { display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); border-radius:10px; padding:8px 16px; }
.score-label { font-size:10px; color:rgba(255,255,255,0.6); text-transform:uppercase; letter-spacing:0.08em; }
.score-val { font-size:22px; font-weight:700; color:#fff; font-family:'DM Mono',monospace; line-height:1; }
.score-unit { font-size:11px; color:rgba(255,255,255,0.5); align-self:flex-end; margin-bottom:2px; }

.content-scroll { flex:1; overflow-y:auto; padding:22px 28px 100px; display:flex; flex-direction:column; gap:20px; }

.sec-title { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.1em; color:var(--text-muted); display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.sec-title::after { content:''; flex:1; height:1px; background:var(--border); }

.swot-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.swot-card { border-radius:14px; padding:18px 20px; border:1.5px solid; }
.swot-card.S { background:var(--s-bg); border-color:var(--s-border); }
.swot-card.W { background:var(--w-bg); border-color:var(--w-border); }
.swot-card.O { background:var(--o-bg); border-color:var(--o-border); }
.swot-card.T { background:var(--t-bg); border-color:var(--t-border); }
.swot-header { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
.swot-letter { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:800; font-family:'DM Serif Display',serif; flex-shrink:0; color:#fff; }
.swot-card.S .swot-letter { background:var(--s-accent); }
.swot-card.W .swot-letter { background:var(--w-accent); }
.swot-card.O .swot-letter { background:var(--o-accent); }
.swot-card.T .swot-letter { background:var(--t-accent); }
.swot-card-title { font-size:13px; font-weight:700; }
.swot-card.S .swot-card-title { color:var(--s-text); }
.swot-card.W .swot-card-title { color:var(--w-text); }
.swot-card.O .swot-card-title { color:var(--o-text); }
.swot-card.T .swot-card-title { color:var(--t-text); }
.swot-card-sub { font-size:10px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; margin-top:1px; }
.swot-items { display:flex; flex-direction:column; gap:7px; }
.swot-item { display:flex; align-items:flex-start; gap:9px; font-size:12px; line-height:1.55; }
.swot-item-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; margin-top:5px; }
.swot-card.S .swot-item-dot { background:var(--s-accent); }
.swot-card.W .swot-item-dot { background:var(--w-accent); }
.swot-card.O .swot-item-dot { background:var(--o-accent); }
.swot-card.T .swot-item-dot { background:var(--t-accent); }
.swot-card.S .swot-item { color:#14532d; }
.swot-card.W .swot-item { color:#7c2d12; }
.swot-card.O .swot-item { color:#1e3a8a; }
.swot-card.T .swot-item { color:#7f1d1d; }

.three-col { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; }
.two-col { display:grid; grid-template-columns:2fr 1fr; gap:10px; }
.analysis-card { background:#fff; border:1.5px solid var(--border); border-radius:14px; overflow:hidden; }
.analysis-card-head { padding:14px 18px 12px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
.analysis-card-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:15px; }
.analysis-card-label { font-size:13px; font-weight:600; }
.analysis-card-sublabel { font-size:10px; color:var(--text-muted); margin-top:1px; }
.analysis-card-body { padding:14px 18px; }

.risk-meter { margin-bottom:12px; }
.risk-label-row { display:flex; justify-content:space-between; margin-bottom:5px; }
.risk-name { font-size:11px; color:var(--text-muted); }
.risk-pct { font-size:11px; font-weight:600; font-family:'DM Mono',monospace; }
.risk-bar { height:6px; background:var(--bg-3); border-radius:10px; overflow:hidden; }
.risk-fill { height:100%; border-radius:10px; }

.timeline { display:flex; flex-direction:column; }
.tl-item { display:flex; gap:12px; position:relative; padding-bottom:14px; }
.tl-item:last-child { padding-bottom:0; }
.tl-left { display:flex; flex-direction:column; align-items:center; flex-shrink:0; width:28px; }
.tl-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; border:2px solid #fff; box-shadow:0 0 0 2px currentColor; margin-top:2px; }
.tl-line { flex:1; width:1.5px; background:var(--border); margin-top:4px; }
.tl-item:last-child .tl-line { display:none; }
.tl-date { font-size:10px; font-family:'DM Mono',monospace; color:var(--text-muted); margin-bottom:2px; }
.tl-text { font-size:12px; color:var(--text); line-height:1.5; }

.actor-list { display:flex; flex-direction:column; gap:8px; }
.actor-item { display:flex; align-items:center; gap:10px; }
.actor-avatar { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; color:#fff; }
.actor-name { font-size:12px; font-weight:500; }
.actor-role { font-size:10px; color:var(--text-muted); margin-top:1px; }
.actor-status { margin-left:auto; font-size:10px; font-weight:600; padding:3px 9px; border-radius:20px; flex-shrink:0; }
.status-tersangka { background:#fee2e2; color:#b91c1c; }
.status-saksi { background:#fef9c3; color:#a16207; }
.status-dpo { background:#fae8ff; color:#86198f; }

.rekomendasi-list { display:flex; flex-direction:column; gap:8px; }
.reko-item { display:flex; align-items:flex-start; gap:12px; padding:12px 14px; border-radius:10px; border:1.5px solid var(--border); background:var(--bg-2); transition:border-color 0.12s; }
.reko-item:hover { border-color:var(--green-border); background:var(--green-light); }
.reko-num { width:26px; height:26px; border-radius:7px; background:var(--green); color:#fff; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-family:'DM Mono',monospace; }
.reko-title { font-size:12px; font-weight:600; margin-bottom:2px; }
.reko-desc { font-size:11px; color:var(--text-muted); line-height:1.5; }
.reko-priority { font-size:10px; font-weight:600; padding:2px 8px; border-radius:20px; margin-top:5px; display:inline-block; }
.prio-tinggi { background:#fee2e2; color:#b91c1c; }
.prio-sedang { background:#fef9c3; color:#a16207; }
.prio-rendah { background:var(--green-light); color:var(--green); }

.confidence-wrap { display:flex; flex-direction:column; align-items:center; padding:20px 0 14px; }
.conf-ring { position:relative; width:110px; height:110px; margin-bottom:14px; }
.conf-ring svg { transform:rotate(-90deg); }
.conf-center { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; }
.conf-pct { font-size:22px; font-weight:800; font-family:'DM Mono',monospace; line-height:1; }
.conf-sub { font-size:9px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.06em; margin-top:2px; }
.conf-labels { display:flex; flex-direction:column; gap:8px; width:100%; }
.conf-label-row { display:flex; justify-content:space-between; align-items:center; }
.conf-label-name { font-size:11px; color:var(--text-muted); }
.conf-label-val { font-size:11px; font-weight:600; font-family:'DM Mono',monospace; }
.conf-bar-sm { height:4px; background:var(--bg-3); border-radius:10px; margin-top:3px; overflow:hidden; }
.conf-fill-sm { height:100%; border-radius:10px; background:var(--green); }

.fab-edit { position:absolute; bottom:28px; left:28px; display:flex; align-items:center; gap:8px; padding:11px 20px; background:#fff; color:var(--green); border:1.5px solid var(--green-border); border-radius:40px; font-size:13px; font-weight:600; cursor:pointer; box-shadow:0 4px 16px rgba(0,0,0,0.08); z-index:50; text-decoration:none; }
.fab-edit:hover { background:var(--green-light); transform:translateY(-2px); }

.empty-msg { font-size:12px; color:var(--text-muted); font-style:italic; }
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
      <a class="back-btn" href="{{ route('datapool.show', $folder) }}">← Kembali</a>
      <div class="topbar-divider"></div>
      <div class="page-title">Hasil Analisis</div>
    </div>
    <div class="topbar-right">
      <div style="display:flex;align-items:center;gap:6px;padding:5px 12px;background:var(--green-light);border:1px solid var(--green-border);border-radius:20px;font-size:11px;font-weight:500;color:var(--green)">
        <div style="width:7px;height:7px;border-radius:50%;background:var(--green-2)"></div>
        Input Manual · {{ $analisis->jumlah_sumber }} sumber
      </div>
      <button class="tb-btn">⬇ Ekspor PDF</button>
    </div>
  </div>

  <div class="result-banner">
    <div class="banner-left">
      <div class="banner-emoji">{{ $folder->emoji }}</div>
      <div>
        <div class="banner-title">{{ $analisis->judul }}</div>
        <div class="banner-meta">
          <span>{{ $analisis->tanggal_analisis ? \Carbon\Carbon::parse($analisis->tanggal_analisis)->format('d M Y, H.i') . ' WIB' : '-' }}</span>
          <span class="banner-dot"></span>
          <span>{{ $analisis->jumlah_sumber }} sumber · {{ $analisis->aktor->count() }} aktor</span>
          <span class="banner-dot"></span>
          <span>{{ $analisis->model_versi }}</span>
        </div>
      </div>
    </div>
    <div class="banner-right">
      <div class="score-pill">
        <div>
          <div class="score-label">Tingkat Risiko</div>
          <div style="display:flex;align-items:flex-end;gap:3px">
            <div class="score-val">{{ $analisis->tingkat_risiko }}</div>
            <div class="score-unit">/ 10</div>
          </div>
        </div>
      </div>
      @if($analisis->prediksi_vonis)
      <div class="score-pill">
        <div>
          <div class="score-label">Prediksi Vonis</div>
          <div style="display:flex;align-items:flex-end;gap:3px">
            <div class="score-val">{{ $analisis->prediksi_vonis }}</div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>

  <div class="content-scroll">

    <div>
      <div class="sec-title">Analisis SWOT</div>
      @php
        $swotGroups = $analisis->swotItems->groupBy('tipe');
        $swotMeta = ['S'=>['Strengths','Kekuatan'],'W'=>['Weaknesses','Kelemahan'],'O'=>['Opportunities','Peluang'],'T'=>['Threats','Ancaman']];
      @endphp
      <div class="swot-grid">
        @foreach($swotMeta as $tipe => [$j, $s])
        <div class="swot-card {{ $tipe }}">
          <div class="swot-header">
            <div class="swot-letter">{{ $tipe }}</div>
            <div>
              <div class="swot-card-title">{{ $j }} — {{ $s }}</div>
              <div class="swot-card-sub">{{ isset($swotGroups[$tipe]) ? $swotGroups[$tipe]->count() : 0 }} poin</div>
            </div>
          </div>
          <div class="swot-items">
            @forelse($swotGroups->get($tipe, collect()) as $item)
            <div class="swot-item"><div class="swot-item-dot"></div><span>{{ $item->isi }}</span></div>
            @empty
            <div class="empty-msg">Tidak ada poin.</div>
            @endforelse
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div>
      <div class="sec-title">Analisis Lanjutan</div>
      <div class="three-col">

        <div class="analysis-card">
          <div class="analysis-card-head">
            <div class="analysis-card-icon" style="background:#fef2f2">⚠️</div>
            <div>
              <div class="analysis-card-label">Penilaian Risiko</div>
              <div class="analysis-card-sublabel">{{ $analisis->riskItems->count() }} indikator</div>
            </div>
          </div>
          <div class="analysis-card-body">
            @forelse($analisis->riskItems as $risk)
            <div class="risk-meter" style="{{ $loop->last ? 'margin-bottom:0' : '' }}">
              <div class="risk-label-row">
                <span class="risk-name">{{ $risk->label }}</span>
                <span class="risk-pct" style="color:{{ $risk->warna }}">{{ $risk->keterangan ?? $risk->nilai . '%' }}</span>
              </div>
              <div class="risk-bar"><div class="risk-fill" style="width:{{ $risk->nilai }}%;background:{{ $risk->warna }}"></div></div>
            </div>
            @empty
            <div class="empty-msg">Belum ada data risiko.</div>
            @endforelse
          </div>
        </div>

        <div class="analysis-card">
          <div class="analysis-card-head">
            <div class="analysis-card-icon" style="background:#eff6ff">📅</div>
            <div>
              <div class="analysis-card-label">Kronologi Kasus</div>
              <div class="analysis-card-sublabel">{{ $analisis->timeline->count() }} kejadian</div>
            </div>
          </div>
          <div class="analysis-card-body">
            <div class="timeline">
              @forelse($analisis->timeline as $tl)
              <div class="tl-item">
                <div class="tl-left"><div class="tl-dot" style="color:{{ $tl->warna_dot }}"></div><div class="tl-line"></div></div>
                <div>
                  <div class="tl-date">{{ $tl->tanggal }}</div>
                  <div class="tl-text">{{ $tl->keterangan }}</div>
                </div>
              </div>
              @empty
              <div class="empty-msg">Belum ada kronologi.</div>
              @endforelse
            </div>
          </div>
        </div>

        <div class="analysis-card">
          <div class="analysis-card-head">
            <div class="analysis-card-icon" style="background:#fdf4ff">👥</div>
            <div>
              <div class="analysis-card-label">Peta Aktor</div>
              <div class="analysis-card-sublabel">{{ $analisis->aktor->count() }} pihak</div>
            </div>
          </div>
          <div class="analysis-card-body">
            <div class="actor-list">
              @forelse($analisis->aktor as $aktor)
              <div class="actor-item">
                <div class="actor-avatar" style="background:{{ $aktor->warna_avatar }}">{{ $aktor->inisial }}</div>
                <div>
                  <div class="actor-name">{{ $aktor->nama }}</div>
                  <div class="actor-role">{{ $aktor->peran }}</div>
                </div>
                <div class="actor-status status-{{ $aktor->status }}">{{ ucfirst($aktor->status) }}</div>
              </div>
              @empty
              <div class="empty-msg">Belum ada aktor.</div>
              @endforelse
            </div>
          </div>
        </div>

      </div>
    </div>

    <div>
      <div class="sec-title">Rekomendasi & Kepercayaan Analisis</div>
      <div class="two-col">

        <div class="analysis-card">
          <div class="analysis-card-head">
            <div class="analysis-card-icon" style="background:#f0f7f2">✅</div>
            <div>
              <div class="analysis-card-label">Rekomendasi Tindak Lanjut</div>
              <div class="analysis-card-sublabel">{{ $analisis->rekomendasi->count() }} rekomendasi</div>
            </div>
          </div>
          <div class="analysis-card-body">
            <div class="rekomendasi-list">
              @forelse($analisis->rekomendasi as $reko)
              <div class="reko-item">
                <div class="reko-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                <div>
                  <div class="reko-title">{{ $reko->judul }}</div>
                  <div class="reko-desc">{{ $reko->deskripsi }}</div>
                  <span class="reko-priority prio-{{ $reko->prioritas }}">Prioritas {{ ucfirst($reko->prioritas) }}</span>
                </div>
              </div>
              @empty
              <div class="empty-msg">Belum ada rekomendasi.</div>
              @endforelse
            </div>
          </div>
        </div>

        <div class="analysis-card">
          <div class="analysis-card-head">
            <div class="analysis-card-icon" style="background:#fefce8">📊</div>
            <div>
              <div class="analysis-card-label">Kepercayaan Analisis</div>
              <div class="analysis-card-sublabel">Validasi kualitas data</div>
            </div>
          </div>
          <div class="analysis-card-body">
            @if($analisis->confidence)
            @php
              $conf = $analisis->confidence;
              $avg = $analisis->avgConfidence();
              $circumference = 283;
              $offset = $circumference - ($avg / 100 * $circumference);
            @endphp
            <div class="confidence-wrap">
              <div class="conf-ring">
                <svg viewBox="0 0 100 100" width="110" height="110">
                  <circle fill="none" stroke="var(--bg-3)" stroke-width="10" cx="50" cy="50" r="45"/>
                  <circle fill="none" stroke="var(--green)" stroke-width="10" stroke-linecap="round"
                    cx="50" cy="50" r="45"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $offset }}"/>
                </svg>
                <div class="conf-center">
                  <div class="conf-pct">{{ $avg }}%</div>
                  <div class="conf-sub">Akurasi</div>
                </div>
              </div>
              <div class="conf-labels">
                @foreach([['Kelengkapan data', $conf->kelengkapan_data],['Konsistensi sumber', $conf->konsistensi_sumber],['Kualitas dokumen', $conf->kualitas_dokumen],['Kedalaman analisis', $conf->kedalaman_analisis]] as [$l, $v])
                <div>
                  <div class="conf-label-row"><span class="conf-label-name">{{ $l }}</span><span class="conf-label-val">{{ $v }}%</span></div>
                  <div class="conf-bar-sm"><div class="conf-fill-sm" style="width:{{ $v }}%"></div></div>
                </div>
                @endforeach
              </div>
            </div>
            @else
            <div class="empty-msg" style="text-align:center;padding:20px 0">Belum ada data kepercayaan.</div>
            @endif
          </div>
        </div>

      </div>
    </div>

  </div>

  <a class="fab-edit" href="{{ route('datapool.show', $folder) }}">← Kembali ke Folder</a>

</div>
</body>
</html>