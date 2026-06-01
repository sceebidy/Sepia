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
  --danger: #dc2626; --danger-light: #fff5f5; --danger-border: #fca5a5;
}
body { font-family: 'DM Sans', sans-serif; background: var(--bg-tertiary); color: var(--text); height: 100vh; display: flex; overflow: hidden; }

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
.nav-item-badge { font-size: 10px; background: rgba(255,255,255,0.2); color: #fff; padding: 2px 7px; border-radius: 20px; }
.nav-item-badge.alert { background: #ef4444; }
.sidenav-divider { height: 1px; background: rgba(255,255,255,0.08); margin: 10px 12px; }
.sidenav-bottom { margin-top: auto; padding: 14px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
.user-row { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 8px; }
.user-avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.2); color: #fff; font-size: 12px; font-weight: 600; display: flex; align-items: center; justify-content: center; }
.user-name { font-size: 12px; font-weight: 500; color: #fff; }
.user-role { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 1px; }

/* ── MAIN ── */
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

/* ── CONTENT ── */
.content { flex: 1; overflow-y: auto; padding: 28px; }
.page-hero { margin-bottom: 20px; }
.page-hero-title { font-size: 20px; font-weight: 600; margin-bottom: 4px; }
.page-hero-sub { font-size: 13px; color: var(--text-muted); }
.page-hero-sub span { color: var(--green); font-weight: 500; }

/* ── ALERT ── */
.alert { padding: 12px 16px; border-radius: 10px; font-size: 12.5px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
.alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.alert-error   { background: #fff5f5; color: #dc2626; border: 1px solid #fca5a5; }

/* ── TOOLBAR ── */
.toolbar { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px; pointer-events: none; }
.search-input { width: 100%; padding: 8px 12px 8px 32px; border: 1px solid var(--border); border-radius: 8px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: var(--text); outline: none; background: #fff; transition: border-color 0.12s; }
.search-input:focus { border-color: var(--green-border); }
.filter-tabs { display: flex; gap: 4px; }
.filter-tab { padding: 7px 13px; font-size: 12px; border: 1px solid var(--border); border-radius: 20px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.12s; white-space: nowrap; }
.filter-tab:hover, .filter-tab.active { border-color: var(--green-border); color: var(--green); background: var(--green-light); font-weight: 500; }

/* ── FOLDER GRID ── */
.folder-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.folder-card { background: #fff; border: 1.5px solid var(--border); border-radius: 14px; overflow: hidden; text-decoration: none; display: block; transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s; position: relative; }
.folder-card:hover { border-color: var(--green-border); box-shadow: 0 6px 24px rgba(26,92,46,0.1); transform: translateY(-2px); }
.folder-stripe { height: 4px; }
.folder-body { padding: 20px 22px 18px; }
.folder-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; }
.folder-emoji { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
.folder-actions { display: flex; gap: 4px; opacity: 0; transition: opacity 0.15s; }
.folder-card:hover .folder-actions { opacity: 1; }
.act-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid var(--border); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; color: var(--text-muted); transition: all 0.12s; text-decoration: none; position: relative; z-index: 2; }
.act-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.act-btn.danger:hover { border-color: var(--danger-border); color: var(--danger); background: var(--danger-light); }
.folder-name { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 6px; line-height: 1.35; }
.folder-desc { font-size: 11px; color: var(--text-muted); line-height: 1.6; margin-bottom: 16px; }
.folder-footer { display: flex; align-items: center; justify-content: space-between; }
.folder-count { font-size: 11px; color: var(--text-muted); }
.folder-count-num { font-size: 14px; font-weight: 600; color: var(--text); font-family: 'DM Mono', monospace; }
.folder-tag { font-size: 10px; padding: 3px 10px; border-radius: 20px; border: 1px solid; font-weight: 500; }
/* Link area di belakang tombol */
.folder-link { position: absolute; inset: 0; z-index: 1; }

/* ── EMPTY STATE ── */
.empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
.empty-state-icon { font-size: 48px; margin-bottom: 16px; }
.empty-state-title { font-size: 16px; font-weight: 600; color: var(--text); margin-bottom: 8px; }
.empty-state-desc { font-size: 13px; margin-bottom: 20px; }

/* ── MODAL BASE ── */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 100; display: none; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal { background: #fff; border-radius: 16px; padding: 28px; width: 480px; max-width: 95vw; position: relative; }
.modal-sm { width: 400px; }
.modal-title { font-size: 16px; font-weight: 600; margin-bottom: 20px; }
.modal-title.danger { color: var(--danger); }

/* ── FORM ── */
.form-group { margin-bottom: 16px; }
.form-label { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-bottom: 6px; display: block; }
.form-input { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; transition: border-color 0.12s; }
.form-input:focus { border-color: var(--green-border); }
.form-select { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; background: #fff; }
.form-textarea { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; resize: vertical; min-height: 80px; }
.form-textarea:focus { border-color: var(--green-border); }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; }
.btn-cancel { padding: 8px 16px; border: 1px solid var(--border); border-radius: 8px; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; cursor: pointer; color: var(--text-muted); }
.btn-submit { padding: 8px 20px; border: none; border-radius: 8px; background: var(--green); color: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; }
.btn-submit:hover { background: #14482a; }
.btn-danger { padding: 8px 20px; border: none; border-radius: 8px; background: var(--danger); color: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; }
.btn-danger:hover { background: #b91c1c; }

/* ── EMOJI & COLOR PICKER ── */
.emoji-row { display: flex; flex-wrap: wrap; gap: 8px; }
.emoji-opt { font-size: 22px; cursor: pointer; padding: 4px; border-radius: 6px; border: 2px solid transparent; transition: border-color 0.1s; }
.emoji-opt:hover, .emoji-opt.selected { border-color: var(--green); }
.color-row { display: flex; gap: 8px; flex-wrap: wrap; }
.color-opt { width: 28px; height: 28px; border-radius: 50%; cursor: pointer; border: 3px solid transparent; outline: 2px solid transparent; transition: outline 0.1s; }
.color-opt.selected { outline: 2px solid var(--green); outline-offset: 2px; }

/* ── DELETE WARNING ── */
.del-body { font-size: 13.5px; color: var(--text-muted); line-height: 1.7; }
.del-folder-name { font-weight: 600; color: var(--text); font-size: 14px; display: block; margin-top: 4px; }
.del-warning { margin-top: 14px; padding: 11px 14px; background: var(--danger-light); border: 1px solid var(--danger-border); border-radius: 8px; font-size: 12px; color: var(--danger); line-height: 1.6; }

/* ── VALIDATION ERROR ── */
.field-error { font-size: 11px; color: var(--danger); margin-top: 4px; }
.form-input.error, .form-textarea.error { border-color: var(--danger-border); }
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

{{-- ══ MAIN ══ --}}
<div class="main">

  {{-- TOPBAR --}}
  <div class="topbar">
    <div class="topbar-left">
      <div class="page-title">RPI — Folder Kasus</div>
      <div class="page-breadcrumb">
        <span>SEPIA</span><span style="opacity:0.4">›</span><span style="color:var(--text)">Repositori Kasus</span>
      </div>
    </div>
    <div class="topbar-right">
      <button class="tb-btn primary" onclick="openModal('modal-create')">+ Buat Folder Baru</button>
    </div>
  </div>

  {{-- CONTENT --}}
  <div class="content">

    {{-- Flash messages --}}
    @if(session('success'))
      <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">✕ {{ session('error') }}</div>
    @endif

    {{-- Page hero --}}
    <div class="page-hero">
      <div class="page-hero-title">Semua Folder Kasus</div>
      <div class="page-hero-sub">
        <span>{{ $folders->count() }} folder</span> · {{ $totalItem }} sumber tersimpan
      </div>
    </div>

    {{-- Toolbar: search + filter --}}
    <div class="toolbar">
      <div class="search-wrap">
        <span class="search-icon">🔍</span>
        <input
          class="search-input"
          type="text"
          id="searchInput"
          placeholder="Cari nama folder..."
          oninput="filterFolders()"
        />
      </div>
      <div class="filter-tabs">
        <button class="filter-tab active" onclick="setFilter('semua', this)">Semua</button>
        <button class="filter-tab" onclick="setFilter('baru', this)">Baru</button>
        <button class="filter-tab" onclick="setFilter('aktif', this)">Aktif</button>
        <button class="filter-tab" onclick="setFilter('penyidikan', this)">Penyidikan</button>
        <button class="filter-tab" onclick="setFilter('penuntutan', this)">Penuntutan</button>
        <button class="filter-tab" onclick="setFilter('inkracht', this)">Inkracht</button>
      </div>
    </div>

    {{-- Folder grid --}}
    @if($folders->isEmpty())
      <div class="empty-state">
        <div class="empty-state-icon">📂</div>
        <div class="empty-state-title">Belum ada folder</div>
        <div class="empty-state-desc">Buat folder pertama untuk mulai mengorganisir sumber data kasus.</div>
        <button class="tb-btn primary" onclick="openModal('modal-create')">+ Buat Folder Baru</button>
      </div>
    @else
      <div class="folder-grid" id="folderGrid">
        @foreach($folders as $folder)
          @php $sc = $folder->statusColor(); @endphp
          <div class="folder-card"
               data-status="{{ $folder->status }}"
               data-name="{{ strtolower($folder->nama) }}">

            {{-- Link utama ke detail (di belakang tombol) --}}
            <a class="folder-link" href="{{ route('datapool.show', $folder) }}" aria-label="{{ $folder->nama }}"></a>

            <div class="folder-stripe" style="background:{{ $folder->warna_stripe }}"></div>
            <div class="folder-body">
              <div class="folder-top">
                <div class="folder-emoji" style="background:{{ $sc['bg'] }}">{{ $folder->emoji }}</div>
                <div class="folder-actions">
                  {{-- Tombol Edit --}}
                  <button class="act-btn"
                          title="Edit folder"
                          onclick="openEdit(
                            {{ $folder->id }},
                            '{{ addslashes($folder->nama) }}',
                            '{{ addslashes($folder->deskripsi ?? '') }}',
                            '{{ $folder->emoji }}',
                            '{{ $folder->warna_stripe }}',
                            '{{ $folder->status }}'
                          )">✏️</button>

                  {{-- Tombol Hapus --}}
                  <button class="act-btn danger"
                          title="Hapus folder"
                          onclick="openDelete(
                            {{ $folder->id }},
                            '{{ addslashes($folder->nama) }}',
                            {{ $folder->items_count }}
                          )">🗑️</button>
                </div>
              </div>
              <div class="folder-name">{{ $folder->nama }}</div>
              <div class="folder-desc">{{ $folder->deskripsi ?? 'Tidak ada deskripsi.' }}</div>
              <div class="folder-footer">
                <div class="folder-count">
                  <span class="folder-count-num">{{ $folder->items_count }}</span> sumber
                </div>
                <span class="folder-tag"
                      style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border-color:{{ $sc['border'] }}">
                  {{ ucfirst($folder->status) }}
                </span>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- No result state (dari filter/search) --}}
      <div id="noResult" style="display:none" class="empty-state">
        <div class="empty-state-icon">🔍</div>
        <div class="empty-state-title">Tidak ditemukan</div>
        <div class="empty-state-desc">Coba kata kunci atau filter yang berbeda.</div>
      </div>
    @endif

  </div>
</div>


{{-- ══════════════════════════════════ --}}
{{--  MODAL 1 — BUAT FOLDER BARU       --}}
{{-- ══════════════════════════════════ --}}
<div class="modal-overlay" id="modal-create" onclick="closeBg(event, 'modal-create')">
  <div class="modal">
    <div class="modal-title">📁 Buat Folder Baru</div>
    <form method="POST" action="{{ route('datapool.store') }}" id="form-create">
      @csrf

      <div class="form-group">
        <label class="form-label">Nama Folder <span style="color:var(--danger)">*</span></label>
        <input class="form-input {{ $errors->has('nama') ? 'error' : '' }}"
               type="text" name="nama"
               value="{{ old('nama') }}"
               placeholder="cth: Korupsi Pengadaan IT Kemenkominfo"
               required />
        @error('nama')<div class="field-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-textarea" name="deskripsi"
                  placeholder="Ringkasan singkat kasus...">{{ old('deskripsi') }}</textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Ikon Folder</label>
        <div class="emoji-row" id="emoji-row-create">
          @php $emojis = ['🏛️','🌾','🛢️','⛏️','🏫','🏥','💰','🔍','📊','🛡️','🏗️','📋','⚖️','🌐','🏦','📡']; @endphp
          @foreach($emojis as $e)
            <span class="emoji-opt {{ (old('emoji', '🏛️') === $e) ? 'selected' : '' }}"
                  onclick="selectEmoji('create', '{{ $e }}', this)">{{ $e }}</span>
          @endforeach
        </div>
        <input type="hidden" name="emoji" id="emoji-input-create" value="{{ old('emoji', '🏛️') }}" />
      </div>

      <div class="form-group">
        <label class="form-label">Warna Stripe</label>
        <div class="color-row">
          @php $colors = ['#1a5c2e','#d97706','#9d174d','#1e6fa3','#065f46','#6b7280','#7c3aed','#dc2626','#0e7490','#b45309']; @endphp
          @foreach($colors as $c)
            <div class="color-opt {{ (old('warna_stripe', '#1a5c2e') === $c) ? 'selected' : '' }}"
                 style="background:{{ $c }}"
                 onclick="selectColor('create', '{{ $c }}', this)"></div>
          @endforeach
        </div>
        <input type="hidden" name="warna_stripe" id="color-input-create" value="{{ old('warna_stripe', '#1a5c2e') }}" />
      </div>

      <div class="form-group">
        <label class="form-label">Status Awal</label>
        <select class="form-select" name="status">
          @foreach(['baru'=>'Baru','aktif'=>'Aktif','penyidikan'=>'Penyidikan','penuntutan'=>'Penuntutan','inkracht'=>'Inkracht'] as $val => $label)
            <option value="{{ $val }}" {{ old('status','baru') === $val ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-create')">Batal</button>
        <button type="submit" class="btn-submit">Buat Folder</button>
      </div>
    </form>
  </div>
</div>


{{-- ══════════════════════════════════ --}}
{{--  MODAL 2 — EDIT FOLDER            --}}
{{-- ══════════════════════════════════ --}}
<div class="modal-overlay" id="modal-edit" onclick="closeBg(event, 'modal-edit')">
  <div class="modal">
    <div class="modal-title">✏️ Edit Folder</div>
    <form method="POST" id="form-edit" action="">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label class="form-label">Nama Folder <span style="color:var(--danger)">*</span></label>
        <input class="form-input" type="text" name="nama" id="edit-nama" required />
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-textarea" name="deskripsi" id="edit-deskripsi"></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Ikon Folder</label>
        <div class="emoji-row" id="emoji-row-edit">
          @php $emojis = ['🏛️','🌾','🛢️','⛏️','🏫','🏥','💰','🔍','📊','🛡️','🏗️','📋','⚖️','🌐','🏦','📡']; @endphp
          @foreach($emojis as $e)
            <span class="emoji-opt" onclick="selectEmoji('edit', '{{ $e }}', this)">{{ $e }}</span>
          @endforeach
        </div>
        <input type="hidden" name="emoji" id="emoji-input-edit" value="" />
      </div>

      <div class="form-group">
        <label class="form-label">Warna Stripe</label>
        <div class="color-row">
          @php $colors = ['#1a5c2e','#d97706','#9d174d','#1e6fa3','#065f46','#6b7280','#7c3aed','#dc2626','#0e7490','#b45309']; @endphp
          @foreach($colors as $c)
            <div class="color-opt"
                 style="background:{{ $c }}"
                 onclick="selectColor('edit', '{{ $c }}', this)"
                 data-color="{{ $c }}"></div>
          @endforeach
        </div>
        <input type="hidden" name="warna_stripe" id="color-input-edit" value="" />
      </div>

      <div class="form-group">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" id="edit-status">
          <option value="baru">Baru</option>
          <option value="aktif">Aktif</option>
          <option value="penyidikan">Penyidikan</option>
          <option value="penuntutan">Penuntutan</option>
          <option value="inkracht">Inkracht</option>
        </select>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-edit')">Batal</button>
        <button type="submit" class="btn-submit">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>


{{-- ══════════════════════════════════ --}}
{{--  MODAL 3 — KONFIRMASI HAPUS       --}}
{{-- ══════════════════════════════════ --}}
<div class="modal-overlay" id="modal-delete" onclick="closeBg(event, 'modal-delete')">
  <div class="modal modal-sm">
    <div class="modal-title danger">🗑️ Hapus Folder</div>
    <div class="del-body">
      Anda akan menghapus folder:<br>
      <span class="del-folder-name" id="delete-folder-name">—</span>
    </div>
    <div class="del-warning" id="delete-warning">
      ⚠️ Folder ini beserta seluruh item di dalamnya akan dihapus permanen dan <strong>tidak dapat dipulihkan</strong>.
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeModal('modal-delete')">Batal</button>
      <form method="POST" id="form-delete" action="">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Ya, Hapus Permanen</button>
      </form>
    </div>
  </div>
</div>


<script>
// ── Filter & Search ──────────────────────────
let activeFilter = 'semua';

function setFilter(status, el) {
  activeFilter = status;
  document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  filterFolders();
}

function filterFolders() {
  const q = document.getElementById('searchInput').value.toLowerCase().trim();
  const cards = document.querySelectorAll('.folder-card');
  let visible = 0;

  cards.forEach(card => {
    const nameMatch = card.dataset.name.includes(q);
    const statusMatch = activeFilter === 'semua' || card.dataset.status === activeFilter;
    const show = nameMatch && statusMatch;
    card.style.display = show ? 'block' : 'none';
    if (show) visible++;
  });

  const noResult = document.getElementById('noResult');
  if (noResult) noResult.style.display = visible === 0 ? 'block' : 'none';
}

// ── Modal helpers ────────────────────────────
function openModal(id) {
  document.getElementById(id).classList.add('open');
}
function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}
function closeBg(e, id) {
  if (e.target === document.getElementById(id)) closeModal(id);
}

// ── Modal Edit ───────────────────────────────
function openEdit(id, nama, deskripsi, emoji, warna, status) {
  // Isi form
  document.getElementById('edit-nama').value       = nama;
  document.getElementById('edit-deskripsi').value  = deskripsi;
  document.getElementById('edit-status').value     = status;
  document.getElementById('emoji-input-edit').value = emoji;
  document.getElementById('color-input-edit').value = warna;

  // Set action URL
  document.getElementById('form-edit').action =
    '{{ url("datapool") }}/' + id;

  // Sync emoji picker
  document.querySelectorAll('#emoji-row-edit .emoji-opt').forEach(el => {
    el.classList.toggle('selected', el.textContent.trim() === emoji);
  });

  // Sync color picker
  document.querySelectorAll('#modal-edit .color-opt').forEach(el => {
    el.classList.toggle('selected', el.dataset.color === warna);
  });

  openModal('modal-edit');
}

// ── Modal Delete ─────────────────────────────
function openDelete(id, nama, itemCount) {
  document.getElementById('delete-folder-name').textContent = nama;

  // Sesuaikan pesan warning berdasarkan jumlah item
  const warn = document.getElementById('delete-warning');
  warn.innerHTML = itemCount > 0
    ? `⚠️ Folder ini berisi <strong>${itemCount} item</strong>. Semua item akan dihapus permanen dan <strong>tidak dapat dipulihkan</strong>.`
    : `⚠️ Folder ini beserta seluruh data terkait akan dihapus permanen dan <strong>tidak dapat dipulihkan</strong>.`;

  // Set action URL form delete
  document.getElementById('form-delete').action =
    '{{ url("datapool") }}/' + id;

  openModal('modal-delete');
}

// ── Emoji Picker ─────────────────────────────
function selectEmoji(scope, val, el) {
  document.querySelectorAll('#emoji-row-' + scope + ' .emoji-opt')
    .forEach(e => e.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('emoji-input-' + scope).value = val;
}

// ── Color Picker ─────────────────────────────
function selectColor(scope, val, el) {
  const parent = el.closest('.color-row') || el.parentElement;
  parent.querySelectorAll('.color-opt').forEach(e => e.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('color-input-' + scope).value = val;
}

// ── Auto-buka modal create jika ada validation error ──
@if($errors->any())
  document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>

</body>
</html>