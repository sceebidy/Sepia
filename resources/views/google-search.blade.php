<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SEPIA — Cari Sumber: {{ $folder->nama }}</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
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

.content { flex: 1; overflow-y: auto; padding: 24px 28px 40px; display: flex; flex-direction: column; gap: 16px; }
.content::-webkit-scrollbar { width: 5px; }
.content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

/* ── SEARCH BOX ── */
.search-box { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 20px 24px; }
.search-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 10px; }
.search-form { display: flex; gap: 10px; }
.search-input { flex: 1; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 10px; font-family: 'Sora', sans-serif; font-size: 13px; color: var(--text); outline: none; transition: border-color 0.13s; }
.search-input:focus { border-color: var(--green-border); }
.search-btn { padding: 10px 20px; background: var(--green); color: #fff; border: none; border-radius: 10px; font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.13s; }
.search-btn:hover { background: var(--green-2); }

/* ── RESULTS ── */
.results-header { display: flex; align-items: center; justify-content: space-between; }
.results-count { font-size: 12px; color: var(--text-muted); }
.save-btn { padding: 9px 18px; background: var(--green); color: #fff; border: none; border-radius: 8px; font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; }
.save-btn:hover { background: var(--green-2); }
.save-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.result-list { display: flex; flex-direction: column; gap: 10px; }
.result-card { background: #fff; border: 1.5px solid var(--border); border-radius: 12px; padding: 16px 18px; display: flex; align-items: flex-start; gap: 14px; transition: all 0.13s; cursor: pointer; }
.result-card:hover { border-color: var(--green-border); background: var(--green-light); }
.result-card.selected { border-color: var(--green); background: var(--green-light); }
.result-check { width: 20px; height: 20px; border-radius: 5px; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; transition: all 0.13s; }
.result-card.selected .result-check { background: var(--green); border-color: var(--green); }
.result-check-icon { color: #fff; font-size: 11px; display: none; }
.result-card.selected .result-check-icon { display: block; }
.result-body { flex: 1; min-width: 0; }
.result-title { font-size: 13.5px; font-weight: 600; color: var(--green); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.result-url { font-size: 11px; color: var(--text-faint); font-family: 'JetBrains Mono', monospace; margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.result-snippet { font-size: 12px; color: var(--text-muted); line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.result-source { margin-left: auto; flex-shrink: 0; }
.source-chip { font-size: 10px; padding: 3px 8px; border-radius: 20px; background: var(--bg-3); color: var(--text-muted); border: 1px solid var(--border); white-space: nowrap; }

.select-all-row { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); cursor: pointer; }
.select-all-row input { cursor: pointer; }

.error-box { background: #fef2f2; border: 1px solid #fecdd3; border-radius: 10px; padding: 14px 18px; color: #be123c; font-size: 12.5px; }
.empty-box { text-align: center; padding: 40px; background: #fff; border: 1.5px dashed var(--border); border-radius: 14px; color: var(--text-muted); }
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
      <a href="{{ route('datapool.show', $folder) }}" style="color:var(--text-muted);font-size:13px;text-decoration:none">← Kembali ke Folder</a>
      <div style="width:1px;height:18px;background:var(--border)"></div>
      <div class="page-title">🔍 Cari Sumber Otomatis — {{ $folder->nama }}</div>
    </div>
  </div>

  <div class="content">

    @if(session('success'))
    <div style="padding:11px 16px;border-radius:10px;font-size:12.5px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0">
      ✓ {{ session('success') }}
    </div>
    @endif

    {{-- SEARCH BOX --}}
    <div class="search-box">
      <div class="search-label">Kata Kunci Pencarian</div>
      <form method="GET" action="{{ route('google.search', $folder) }}" class="search-form">
        <input class="search-input" type="text" name="q" value="{{ $query }}" placeholder="Masukkan kata kunci..." autocomplete="off" />
        <button type="submit" class="search-btn">🔍 Cari</button>
      </form>
      <div style="font-size:11px;color:var(--text-faint);margin-top:8px">
        Hasil pencarian dari Google — pilih link yang relevan lalu simpan ke folder
      </div>
    </div>

    {{-- ERROR --}}
    @if($error)
    <div class="error-box">⚠️ {{ $error }}</div>
    @endif

    {{-- RESULTS --}}
    @if(count($results) > 0)
    <form method="POST" action="{{ route('google.simpan', $folder) }}" id="form-simpan">
      @csrf
      <div style="display:flex;flex-direction:column;gap:12px">

        <div class="results-header">
          <div style="display:flex;align-items:center;gap:12px">
            <div class="results-count">{{ count($results) }} hasil ditemukan untuk "<strong>{{ $query }}</strong>"</div>
            <label class="select-all-row">
              <input type="checkbox" id="select-all" onchange="toggleAll(this)"> Pilih semua
            </label>
          </div>
          <button type="submit" class="save-btn" id="btn-simpan" disabled>
            💾 Simpan ke Folder (<span id="selected-count">0</span>)
          </button>
        </div>

        <div class="result-list">
          @foreach($results as $i => $item)
          @php
            $domain = parse_url($item['link'] ?? '', PHP_URL_HOST) ?? '-';
          @endphp
          <div class="result-card" id="card-{{ $i }}" onclick="toggleCard({{ $i }})">
            <div class="result-check" id="check-{{ $i }}">
              <span class="result-check-icon">✓</span>
            </div>
            <div class="result-body">
              <div class="result-title">{{ $item['title'] ?? 'Tanpa judul' }}</div>
              <div class="result-url">{{ $item['link'] ?? '-' }}</div>
              <div class="result-snippet">{{ $item['snippet'] ?? '-' }}</div>
            </div>
            <div class="result-source">
              <span class="source-chip">{{ $domain }}</span>
            </div>
            {{-- Hidden inputs --}}
            <input type="hidden" name="links[{{ $i }}][judul]" value="{{ $item['title'] ?? 'Sumber dari Google' }}" id="input-judul-{{ $i }}" disabled />
            <input type="hidden" name="links[{{ $i }}][url]" value="{{ $item['link'] ?? '' }}" id="input-url-{{ $i }}" disabled />
          </div>
          @endforeach
        </div>

      </div>
    </form>

    @elseif($query && !$error)
    <div class="empty-box">
      <div style="font-size:32px;margin-bottom:10px">🔍</div>
      <div style="font-size:13px;font-weight:600">Tidak ada hasil ditemukan</div>
      <div style="font-size:12px;margin-top:4px">Coba kata kunci yang berbeda</div>
    </div>
    @endif

  </div>
</div>

<script>
let selected = new Set();

function toggleCard(i) {
  const card  = document.getElementById('card-' + i);
  const inputJ = document.getElementById('input-judul-' + i);
  const inputU = document.getElementById('input-url-' + i);

  if (selected.has(i)) {
    selected.delete(i);
    card.classList.remove('selected');
    inputJ.disabled = true;
    inputU.disabled = true;
  } else {
    selected.add(i);
    card.classList.add('selected');
    inputJ.disabled = false;
    inputU.disabled = false;
  }

  updateCount();
}

function toggleAll(cb) {
  const cards = document.querySelectorAll('.result-card');
  cards.forEach((card, i) => {
    const inputJ = document.getElementById('input-judul-' + i);
    const inputU = document.getElementById('input-url-' + i);
    if (cb.checked) {
      selected.add(i);
      card.classList.add('selected');
      inputJ.disabled = false;
      inputU.disabled = false;
    } else {
      selected.delete(i);
      card.classList.remove('selected');
      inputJ.disabled = true;
      inputU.disabled = true;
    }
  });
  updateCount();
}

function updateCount() {
  document.getElementById('selected-count').textContent = selected.size;
  document.getElementById('btn-simpan').disabled = selected.size === 0;
}
</script>

</body>
</html>