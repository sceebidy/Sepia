<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<title>SEPIA — Data Pool</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green: #1a5c2e; --green-2: #2e7d4a; --green-light: #f0f7f2;
  --green-mid: #ddeee4; --green-border: #b6d9c3;
  --text: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb;
  --bg: #ffffff; --bg-secondary: #f9fafb; --bg-tertiary: #f3f4f6;
  --nav-width: 220px;
}
body { font-family: 'DM Sans', sans-serif; background: var(--bg-tertiary); color: var(--text); height: 100vh; display: flex; overflow: hidden; }

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
.nav-item-badge { font-size: 10px; background: rgba(255,255,255,0.2); color: #fff; padding: 2px 7px; border-radius: 20px; }
.nav-item-badge.alert { background: #ef4444; }
.sidenav-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 10px 12px; }
.sidenav-bottom { margin-top: auto; padding: 14px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
.user-row { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 8px; }
.user-avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); color: #fff; font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; }
.user-name { font-size: 12px; font-weight: 500; color: #fff; }
.user-role { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 1px; }

.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 56px; background: #fff; border-bottom: 1px solid var(--border); flex-shrink: 0; }
.topbar-left { display: flex; align-items: center; gap: 12px; }
.page-title { font-size: 15px; font-weight: 600; }
.page-breadcrumb { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 5px; }
.topbar-right { display: flex; align-items: center; gap: 8px; }
.tb-btn { padding: 7px 14px; font-size: 12px; border: 1px solid var(--border); border-radius: 20px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.12s; }
.tb-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.tb-btn.primary { background: var(--green); color: #fff; border-color: var(--green); border-radius: 8px; padding: 7px 16px; font-weight: 500; }
.tb-btn.primary:hover { background: #14482a; }

.content { flex: 1; overflow-y: auto; padding: 28px; }
.page-hero { margin-bottom: 24px; }
.page-hero-title { font-size: 20px; font-weight: 600; margin-bottom: 4px; }
.page-hero-sub { font-size: 13px; color: var(--text-muted); }
.page-hero-sub span { color: var(--green); font-weight: 500; }

/* ── ALERT ── */
.alert { padding: 12px 16px; border-radius: 10px; font-size: 12.5px; margin-bottom: 20px; }
.alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

/* ── FOLDER GRID ── */
.folder-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.folder-card { background: #fff; border: 1.5px solid var(--border); border-radius: 14px; overflow: hidden; text-decoration: none; display: block; transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s; cursor: pointer; }
.folder-card:hover { border-color: var(--green-border); box-shadow: 0 6px 24px rgba(26,92,46,0.1); transform: translateY(-2px); }
.folder-stripe { height: 4px; }
.folder-body { padding: 20px 22px 18px; }
.folder-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; }
.folder-emoji { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
.folder-arrow { font-size: 16px; color: var(--text-muted); margin-top: 6px; opacity: 0; transition: opacity 0.15s, transform 0.15s; }
.folder-card:hover .folder-arrow { opacity: 1; transform: translateX(3px); }
.folder-name { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 6px; line-height: 1.35; }
.folder-desc { font-size: 11px; color: var(--text-muted); line-height: 1.6; margin-bottom: 16px; }
.folder-footer { display: flex; align-items: center; justify-content: space-between; }
.folder-count { font-size: 11px; color: var(--text-muted); }
.folder-count-num { font-size: 14px; font-weight: 600; color: var(--text); font-family: 'DM Mono', monospace; }
.folder-tag { font-size: 10px; padding: 3px 10px; border-radius: 20px; border: 1px solid; font-weight: 500; }

/* ── EMPTY STATE ── */
.empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
.empty-state-icon { font-size: 48px; margin-bottom: 16px; }
.empty-state-title { font-size: 16px; font-weight: 600; color: var(--text); margin-bottom: 8px; }
.empty-state-desc { font-size: 13px; margin-bottom: 20px; }

/* ── MODAL ── */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 100; display: none; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal { background: #fff; border-radius: 16px; padding: 28px; width: 480px; max-width: 95vw; }
.modal-title { font-size: 16px; font-weight: 600; margin-bottom: 20px; }
.form-group { margin-bottom: 16px; }
.form-label { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-bottom: 6px; display: block; }
.form-input { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; transition: border-color 0.12s; }
.form-input:focus { border-color: var(--green-border); }
.form-select { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; background: #fff; }
.form-textarea { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; resize: vertical; min-height: 80px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; }
.btn-cancel { padding: 8px 16px; border: 1px solid var(--border); border-radius: 8px; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; cursor: pointer; color: var(--text-muted); }
.btn-submit { padding: 8px 20px; border: none; border-radius: 8px; background: var(--green); color: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; }
.btn-submit:hover { background: #14482a; }

/* ── EMOJI PICKER ── */
.emoji-row { display: flex; flex-wrap: wrap; gap: 8px; }
.emoji-opt { font-size: 22px; cursor: pointer; padding: 4px; border-radius: 6px; border: 2px solid transparent; transition: border-color 0.1s; }
.emoji-opt:hover, .emoji-opt.selected { border-color: var(--green); }

/* ── WARNA PICKER ── */
.color-row { display: flex; gap: 8px; flex-wrap: wrap; }
.color-opt { width: 28px; height: 28px; border-radius: 50%; cursor: pointer; border: 3px solid transparent; outline: 2px solid transparent; transition: outline 0.1s; }
.color-opt.selected { outline: 2px solid var(--green); outline-offset: 2px; }
</style>
</head>
<body>

{{-- ══ SIDENAV ══ --}}
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

{{-- ══ MAIN ══ --}}
<div class="main">
  <div class="topbar">
    <div class="topbar-left">
      <div class="page-title">Data Pool</div>
      <div class="page-breadcrumb">
        <span>SEPIA</span><span style="opacity:0.4">›</span><span style="color:var(--text)">Repositori Sumber</span>
      </div>
    </div>
    <div class="topbar-right">
      <button class="tb-btn primary" onclick="openModal()">+ Buat Folder Baru</button>
    </div>
  </div>

  <div class="content">

    {{-- Alert sukses --}}
    @if(session('success'))
    <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    <div class="page-hero">
      <div class="page-hero-title">Semua Folder Kasus</div>
      <div class="page-hero-sub">
        <span>{{ $folders->count() }} folder aktif</span> · {{ $totalItem }} sumber tersimpan
      </div>
    </div>

    {{-- ── FOLDER GRID ── --}}
    @if($folders->isEmpty())
    <div class="empty-state">
      <div class="empty-state-icon">📂</div>
      <div class="empty-state-title">Belum ada folder</div>
      <div class="empty-state-desc">Buat folder pertama untuk mulai mengorganisir sumber data kasus.</div>
      <button class="tb-btn primary" onclick="openModal()">+ Buat Folder Baru</button>
    </div>
    @else
    <div class="folder-grid">
      @foreach($folders as $folder)
      @php $sc = $folder->statusColor(); @endphp
      <a class="folder-card" href="{{ route('datapool.show', $folder) }}">
        <div class="folder-stripe" style="background:{{ $folder->warna_stripe }}"></div>
        <div class="folder-body">
          <div class="folder-top">
            <div class="folder-emoji" style="background:{{ $sc['bg'] }}">{{ $folder->emoji }}</div>
            <div class="folder-arrow">→</div>
          </div>
          <div class="folder-name">{{ $folder->nama }}</div>
          <div class="folder-desc">{{ $folder->deskripsi ?? 'Tidak ada deskripsi.' }}</div>
          <div class="folder-footer">
            <div class="folder-count">
              <span class="folder-count-num">{{ $folder->items_count }}</span> sumber
            </div>
            <span class="folder-tag" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border-color:{{ $sc['border'] }}">
              {{ ucfirst($folder->status) }}
            </span>
          </div>
        </div>
      </a>
      @endforeach
    </div>
    @endif

  </div>
</div>

{{-- ══ MODAL BUAT FOLDER ══ --}}
<div class="modal-overlay" id="modal-overlay" onclick="closeModal(event)">
  <div class="modal">
    <div class="modal-title">📁 Buat Folder Baru</div>
    <form method="POST" action="{{ route('datapool.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label">Nama Folder *</label>
        <input class="form-input" type="text" name="nama" placeholder="cth: Korupsi Pengadaan IT Kemenkominfo" required />
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-textarea" name="deskripsi" placeholder="Ringkasan singkat kasus..."></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Ikon Folder</label>
        <div class="emoji-row" id="emoji-row">
          @foreach(['🏛️','🌾','🛢️','⛏️','🏫','🏥','💰','🔍','📊','🛡️','🏗️','📋'] as $e)
          <span class="emoji-opt {{ $e === '🏛️' ? 'selected' : '' }}" onclick="selectEmoji('{{ $e }}', this)">{{ $e }}</span>
          @endforeach
        </div>
        <input type="hidden" name="emoji" id="emoji-input" value="🏛️" />
      </div>

      <div class="form-group">
        <label class="form-label">Warna Folder</label>
        <div class="color-row">
          @foreach(['#1a5c2e','#d97706','#9d174d','#1e6fa3','#065f46','#6b7280','#7c3aed','#dc2626'] as $c)
          <div class="color-opt {{ $c === '#1a5c2e' ? 'selected' : '' }}"
               style="background:{{ $c }}"
               onclick="selectColor('{{ $c }}', this)"></div>
          @endforeach
        </div>
        <input type="hidden" name="warna_stripe" id="color-input" value="#1a5c2e" />
      </div>

      <div class="form-group">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <option value="baru">Baru</option>
          <option value="aktif">Aktif</option>
          <option value="penyidikan">Penyidikan</option>
          <option value="penuntutan">Penuntutan</option>
          <option value="inkracht">Inkracht</option>
        </select>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn-submit">Buat Folder</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal() {
  document.getElementById('modal-overlay').classList.add('open');
}
function closeModal(e) {
  if (!e || e.target === document.getElementById('modal-overlay')) {
    document.getElementById('modal-overlay').classList.remove('open');
  }
}
function selectEmoji(val, el) {
  document.querySelectorAll('.emoji-opt').forEach(e => e.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('emoji-input').value = val;
}
function selectColor(val, el) {
  document.querySelectorAll('.color-opt').forEach(e => e.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('color-input').value = val;
}
</script>
</body>
</html>