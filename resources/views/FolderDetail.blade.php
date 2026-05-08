<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<title>SEPIA — {{ $folder->nama }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green: #1a5c2e; --green-2: #2e7d4a; --green-light: #f0f7f2;
  --green-border: #b6d9c3; --text: #1a1a1a; --text-muted: #6b7280;
  --border: #e5e7eb; --bg: #ffffff; --bg-secondary: #f9fafb; --bg-tertiary: #f3f4f6;
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
.back-link { color: var(--text-muted); font-size: 12px; text-decoration: none; display: flex; align-items: center; gap: 4px; padding: 5px 10px; border-radius: 6px; border: 1px solid var(--border); transition: all 0.12s; }
.back-link:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.page-title { font-size: 15px; font-weight: 600; max-width: 340px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.topbar-right { display: flex; align-items: center; gap: 8px; }
.tb-btn { padding: 7px 14px; font-size: 12px; border: 1px solid var(--border); border-radius: 8px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.12s; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
.tb-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.tb-btn.primary { background: var(--green); color: #fff; border-color: var(--green); }
.tb-btn.primary:hover { background: #14482a; }
.tb-btn.danger { color: var(--danger); border-color: var(--danger-border); }
.tb-btn.danger:hover { background: var(--danger-light); }

/* ── CONTENT ── */
.content { flex: 1; overflow-y: auto; padding: 28px; display: flex; flex-direction: column; gap: 20px; }

/* ── ALERTS ── */
.alert { padding: 12px 16px; border-radius: 10px; font-size: 12.5px; display: flex; align-items: center; gap: 8px; }
.alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.alert-error   { background: var(--danger-light); color: var(--danger); border: 1px solid var(--danger-border); }

/* ── FOLDER HEADER CARD ── */
.folder-header-card { background: #fff; border: 1.5px solid var(--border); border-radius: 14px; overflow: hidden; }
.folder-header-stripe { height: 5px; }
.folder-header-body { padding: 20px 24px; display: flex; align-items: flex-start; gap: 18px; }
.folder-header-emoji { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 28px; flex-shrink: 0; }
.folder-header-info { flex: 1; }
.folder-header-name { font-size: 18px; font-weight: 600; margin-bottom: 4px; }
.folder-header-desc { font-size: 12.5px; color: var(--text-muted); line-height: 1.6; }
.folder-header-meta { display: flex; align-items: center; gap: 8px; margin-top: 10px; flex-wrap: wrap; }
.folder-meta-chip { font-size: 11px; color: var(--text-muted); background: var(--bg-tertiary); padding: 3px 10px; border-radius: 20px; border: 1px solid var(--border); }
.folder-tag { font-size: 10px; padding: 3px 10px; border-radius: 20px; border: 1px solid; font-weight: 600; }

/* ── SECTION TOOLBAR ── */
.section-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.section-title { font-size: 13px; font-weight: 600; }
.filter-tabs { display: flex; gap: 4px; }
.filter-tab { padding: 5px 12px; font-size: 11px; border: 1px solid var(--border); border-radius: 20px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.12s; }
.filter-tab:hover, .filter-tab.active { border-color: var(--green-border); color: var(--green); background: var(--green-light); font-weight: 500; }

/* ── ITEM LIST ── */
.item-list { display: flex; flex-direction: column; gap: 8px; margin-top: 12px; }
.item-card { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; transition: border-color 0.13s; }
.item-card:hover { border-color: var(--green-border); }
.item-icon { font-size: 20px; flex-shrink: 0; margin-top: 2px; }
.item-info { flex: 1; min-width: 0; }
.item-title { font-size: 13px; font-weight: 600; margin-bottom: 3px; }
.item-meta { font-size: 11px; color: var(--text-muted); line-height: 1.6; }
.item-meta a { color: var(--green); text-decoration: none; word-break: break-all; }
.item-meta a:hover { text-decoration: underline; }
.item-konten { font-size: 12px; color: var(--text-muted); margin-top: 8px; line-height: 1.7; background: var(--bg-tertiary); padding: 10px 12px; border-radius: 6px; }
.item-ai { font-size: 12px; color: var(--text-muted); margin-top: 8px; line-height: 1.7; background: #f5f3ff; padding: 10px 12px; border-radius: 6px; border-left: 3px solid #6d28d9; }
.item-ai-label { font-size: 10px; font-weight: 600; color: #6d28d9; margin-bottom: 4px; }
.item-actions { display: flex; align-items: center; gap: 5px; flex-shrink: 0; }
.item-badge { font-size: 10px; padding: 2px 8px; border-radius: 20px; border: 1px solid; font-weight: 500; }
.badge-file    { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
.badge-link    { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.badge-catatan { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.badge-processed { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
.item-act-btn { width: 28px; height: 28px; border-radius: 7px; border: 1px solid var(--border); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; color: var(--text-muted); transition: all 0.12s; text-decoration: none; }
.item-act-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.item-act-btn.danger:hover { border-color: var(--danger-border); color: var(--danger); background: var(--danger-light); }

/* ── EMPTY STATE ── */
.empty-state { text-align: center; padding: 44px 20px; color: var(--text-muted); background: #fff; border: 1.5px dashed var(--border); border-radius: 14px; }
.empty-state-icon { font-size: 36px; margin-bottom: 12px; }
.empty-state-title { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
.empty-state-desc { font-size: 12px; }

/* ── MODAL ── */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 100; display: none; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal { background: #fff; border-radius: 16px; padding: 28px; width: 500px; max-width: 95vw; }
.modal-sm { width: 400px; }
.modal-title { font-size: 16px; font-weight: 600; margin-bottom: 20px; }
.modal-title.danger { color: var(--danger); }

/* ── FORM ── */
.form-group { margin-bottom: 14px; }
.form-label { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-bottom: 6px; display: block; }
.form-input { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; transition: border-color 0.12s; }
.form-input:focus { border-color: var(--green-border); }
.form-input.error { border-color: var(--danger-border); }
.form-select { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; background: #fff; }
.form-textarea { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; resize: vertical; min-height: 90px; transition: border-color 0.12s; }
.form-textarea:focus { border-color: var(--green-border); }
.field-error { font-size: 11px; color: var(--danger); margin-top: 4px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; }
.btn-cancel { padding: 8px 16px; border: 1px solid var(--border); border-radius: 8px; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; cursor: pointer; color: var(--text-muted); }
.btn-submit { padding: 8px 20px; border: none; border-radius: 8px; background: var(--green); color: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; }
.btn-submit:hover { background: #14482a; }
.btn-danger { padding: 8px 20px; border: none; border-radius: 8px; background: var(--danger); color: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; }
.btn-danger:hover { background: #b91c1c; }

/* ── TIPE SWITCHER ── */
.tipe-row { display: flex; gap: 8px; }
.tipe-btn { flex: 1; padding: 8px; border: 1.5px solid var(--border); border-radius: 8px; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 12px; cursor: pointer; text-align: center; transition: all 0.12s; color: var(--text-muted); }
.tipe-btn.active { border-color: var(--green); background: var(--green-light); color: var(--green); font-weight: 600; }
.field-group { display: none; }
.field-group.visible { display: block; }

/* ── DELETE WARNING ── */
.del-body { font-size: 13.5px; color: var(--text-muted); line-height: 1.7; }
.del-item-name { font-weight: 600; color: var(--text); display: block; margin-top: 4px; }
.del-warning { margin-top: 12px; padding: 10px 14px; background: var(--danger-light); border: 1px solid var(--danger-border); border-radius: 8px; font-size: 12px; color: var(--danger); line-height: 1.6; }
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
    <a class="nav-item" href="{{ route('dashboard') }}">
      <div class="nav-icon">📊</div><div class="nav-item-text">Dashboard</div>
    </a>
    <a class="nav-item active" href="{{ route('datapool.index') }}">
      <div class="nav-icon">📋</div><div class="nav-item-text">RPI</div>
    </a>
    <a class="nav-item" href="#">
      <div class="nav-icon">🗄️</div><div class="nav-item-text">Data Pool</div>
    </a>
    <a class="nav-item" href="#">
      <div class="nav-icon">🎨</div><div class="nav-item-text">Personalisasi</div>
    </a>
    <a class="nav-item" href="#">
      <div class="nav-icon">📅</div><div class="nav-item-text">Daily Report</div>
      <span class="nav-item-badge alert">!</span>
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
      <div>
        <div class="user-name">C. Rasyid</div>
        <div class="user-role">Analis Senior</div>
      </div>
    </div>
  </div>
</nav>

{{-- ══ MAIN ══ --}}
<div class="main">

  {{-- TOPBAR --}}
  <div class="topbar">
    <div class="topbar-left">
      <a href="{{ route('datapool.index') }}" class="back-link">← Kembali</a>
      <div class="page-title">{{ $folder->emoji }} {{ $folder->nama }}</div>
    </div>
    <div class="topbar-right">
      <button class="tb-btn primary" onclick="openModal('modal-tambah')">+ Tambah Sumber</button>
      <button class="tb-btn danger" onclick="openDeleteFolder()">🗑 Hapus Folder</button>
    </div>
  </div>

  {{-- CONTENT --}}
  <div class="content">

    {{-- Flash --}}
    @if(session('success'))
      <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">✕ {{ session('error') }}</div>
    @endif

    {{-- ── HEADER FOLDER ── --}}
    @php $sc = $folder->statusColor(); @endphp
    <div class="folder-header-card">
      <div class="folder-header-stripe" style="background:{{ $folder->warna_stripe }}"></div>
      <div class="folder-header-body">
        <div class="folder-header-emoji" style="background:{{ $sc['bg'] }}">{{ $folder->emoji }}</div>
        <div class="folder-header-info">
          <div class="folder-header-name">{{ $folder->nama }}</div>
          <div class="folder-header-desc">{{ $folder->deskripsi ?? 'Tidak ada deskripsi.' }}</div>
          <div class="folder-header-meta">
            <span class="folder-meta-chip">📎 {{ $items->count() }} sumber</span>
            <span class="folder-meta-chip">📄 {{ $items->where('tipe','file')->count() }} file</span>
            <span class="folder-meta-chip">🔗 {{ $items->where('tipe','link')->count() }} link</span>
            <span class="folder-meta-chip">📝 {{ $items->where('tipe','catatan')->count() }} catatan</span>
            <span class="folder-meta-chip">👤 {{ $folder->dibuat_oleh }}</span>
            <span class="folder-meta-chip">📅 {{ $folder->created_at->format('d M Y') }}</span>
            <span class="folder-tag"
                  style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border-color:{{ $sc['border'] }}">
              {{ ucfirst($folder->status) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- ── DAFTAR ITEM ── --}}
    <div>
      <div class="section-toolbar">
        <div class="section-title">Sumber Data ({{ $items->count() }})</div>
        @if($items->count() > 0)
        <div class="filter-tabs">
          <button class="filter-tab active" onclick="filterItems('semua', this)">Semua</button>
          <button class="filter-tab" onclick="filterItems('file', this)">📄 File</button>
          <button class="filter-tab" onclick="filterItems('link', this)">🔗 Link</button>
          <button class="filter-tab" onclick="filterItems('catatan', this)">📝 Catatan</button>
          @if($items->where('processed', true)->count() > 0)
          <button class="filter-tab" onclick="filterItems('processed', this)">✨ Sudah AI</button>
          @endif
        </div>
        @endif
      </div>

      @if($items->isEmpty())
        <div class="empty-state" style="margin-top:12px">
          <div class="empty-state-icon">📭</div>
          <div class="empty-state-title">Folder masih kosong</div>
          <div class="empty-state-desc">Tambahkan file, link, atau catatan ke folder ini.</div>
        </div>
      @else
        <div class="item-list" id="itemList">
          @foreach($items as $item)
            <div class="item-card"
                 data-tipe="{{ $item->tipe }}"
                 data-processed="{{ $item->processed ? 'true' : 'false' }}">

              <div class="item-icon">{{ $item->tipeIcon() }}</div>

              <div class="item-info">
                <div class="item-title">{{ $item->judul }}</div>
                <div class="item-meta">
                  Ditambahkan oleh <strong>{{ $item->ditambahkan_oleh }}</strong>
                  · {{ $item->created_at->diffForHumans() }}
                  @if($item->tipe === 'file' && $item->file_nama)
                    · {{ $item->file_nama }}
                    · <span style="font-family:'DM Mono',monospace;font-size:10px">{{ $item->ukuranFormatted() }}</span>
                  @endif
                  @if($item->tipe === 'link' && $item->konten)
                    · <a href="{{ $item->konten }}" target="_blank" rel="noopener">
                        {{ Str::limit($item->konten, 60) }}
                      </a>
                  @endif
                </div>

                {{-- Konten catatan --}}
                @if($item->tipe === 'catatan' && $item->konten)
                  <div class="item-konten">{{ $item->konten }}</div>
                @endif

                {{-- Rangkuman AI --}}
                @if($item->hasil_rangkuman)
                  <div class="item-ai">
                    <div class="item-ai-label">✨ RANGKUMAN AI</div>
                    {{ $item->hasil_rangkuman }}
                  </div>
                @endif
              </div>

              {{-- Actions --}}
              <div class="item-actions">
                <span class="item-badge badge-{{ $item->tipe }}">{{ strtoupper($item->tipe) }}</span>
                @if($item->processed)
                  <span class="item-badge badge-processed">✓ AI</span>
                @endif

                {{-- Download file --}}
                @if($item->tipe === 'file' && $item->file_path)
                  <a href="{{ Storage::url($item->file_path) }}"
                     target="_blank"
                     class="item-act-btn"
                     title="Download file">⬇</a>
                @endif

                {{-- Edit item --}}
                <button class="item-act-btn"
                        title="Edit item"
                        onclick="openEditItem(
                          {{ $item->id }},
                          '{{ addslashes($item->judul) }}',
                          '{{ $item->tipe }}',
                          '{{ addslashes($item->konten ?? '') }}'
                        )">✏️</button>

                {{-- Hapus item --}}
                <button class="item-act-btn danger"
                        title="Hapus item"
                        onclick="openDeleteItem(
                          {{ $item->id }},
                          '{{ addslashes($item->judul) }}'
                        )">🗑️</button>
              </div>

            </div>
          @endforeach
        </div>

        {{-- No result dari filter --}}
        <div id="noItemResult" style="display:none;margin-top:12px" class="empty-state">
          <div class="empty-state-icon">🔍</div>
          <div class="empty-state-title">Tidak ada item tipe ini</div>
          <div class="empty-state-desc">Coba pilih filter yang berbeda.</div>
        </div>
      @endif
    </div>

  </div>
</div>


{{-- ══════════════════════════════════ --}}
{{--  MODAL 1 — TAMBAH SUMBER          --}}
{{-- ══════════════════════════════════ --}}
<div class="modal-overlay" id="modal-tambah" onclick="closeBg(event,'modal-tambah')">
  <div class="modal">
    <div class="modal-title">➕ Tambah Sumber</div>
    <form method="POST"
          action="{{ route('datapool.items.store', $folder) }}"
          enctype="multipart/form-data"
          id="form-tambah">
      @csrf

      <div class="form-group">
        <label class="form-label">Tipe Sumber</label>
        <div class="tipe-row">
          <button type="button" class="tipe-btn active" onclick="switchTipe('tambah','file',this)">📄 File</button>
          <button type="button" class="tipe-btn" onclick="switchTipe('tambah','link',this)">🔗 Link</button>
          <button type="button" class="tipe-btn" onclick="switchTipe('tambah','catatan',this)">📝 Catatan</button>
        </div>
        <input type="hidden" name="tipe" id="tipe-tambah" value="{{ old('tipe','file') }}" />
      </div>

      <div class="form-group">
        <label class="form-label">Judul <span style="color:var(--danger)">*</span></label>
        <input class="form-input {{ $errors->has('judul') ? 'error' : '' }}"
               type="text" name="judul"
               value="{{ old('judul') }}"
               placeholder="Nama atau judul sumber ini" required />
        @error('judul')<div class="field-error">{{ $message }}</div>@enderror
      </div>

      {{-- FILE --}}
      <div class="field-group {{ old('tipe','file') === 'file' ? 'visible' : '' }}" id="field-tambah-file">
        <div class="form-group">
          <label class="form-label">Upload File</label>
          <input class="form-input" type="file" name="file"
                 accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.csv,.ppt,.pptx" />
          <div style="font-size:11px;color:var(--text-muted);margin-top:4px">
            Format: PDF, Word, Excel, PPT, TXT, CSV · Maks 20MB
          </div>
          @error('file')<div class="field-error">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- LINK --}}
      <div class="field-group {{ old('tipe') === 'link' ? 'visible' : '' }}" id="field-tambah-link">
        <div class="form-group">
          <label class="form-label">URL <span style="color:var(--danger)">*</span></label>
          <input class="form-input" type="url" name="konten_link"
                 value="{{ old('konten_link') }}"
                 placeholder="https://..." />
          @error('konten_link')<div class="field-error">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- CATATAN --}}
      <div class="field-group {{ old('tipe') === 'catatan' ? 'visible' : '' }}" id="field-tambah-catatan">
        <div class="form-group">
          <label class="form-label">Isi Catatan <span style="color:var(--danger)">*</span></label>
          <textarea class="form-textarea" name="konten_catatan"
                    placeholder="Tulis catatan...">{{ old('konten_catatan') }}</textarea>
          @error('konten_catatan')<div class="field-error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-tambah')">Batal</button>
        <button type="submit" class="btn-submit">Tambahkan</button>
      </div>
    </form>
  </div>
</div>


{{-- ══════════════════════════════════ --}}
{{--  MODAL 2 — EDIT ITEM              --}}
{{-- ══════════════════════════════════ --}}
<div class="modal-overlay" id="modal-edit-item" onclick="closeBg(event,'modal-edit-item')">
  <div class="modal">
    <div class="modal-title">✏️ Edit Item</div>
    <form method="POST" id="form-edit-item" action="">
      @csrf
      @method('PATCH')

      <div class="form-group">
        <label class="form-label">Judul <span style="color:var(--danger)">*</span></label>
        <input class="form-input" type="text" name="judul" id="edit-item-judul" required />
      </div>

      {{-- Field konten (hanya untuk link & catatan, file tidak perlu) --}}
      <div id="edit-item-konten-wrap" class="form-group" style="display:none">
        <label class="form-label" id="edit-item-konten-label">Konten</label>
        <textarea class="form-textarea" name="konten" id="edit-item-konten"></textarea>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('modal-edit-item')">Batal</button>
        <button type="submit" class="btn-submit">Simpan</button>
      </div>
    </form>
  </div>
</div>


{{-- ══════════════════════════════════ --}}
{{--  MODAL 3 — HAPUS ITEM             --}}
{{-- ══════════════════════════════════ --}}
<div class="modal-overlay" id="modal-hapus-item" onclick="closeBg(event,'modal-hapus-item')">
  <div class="modal modal-sm">
    <div class="modal-title danger">🗑️ Hapus Item</div>
    <div class="del-body">
      Anda akan menghapus item:<br>
      <span class="del-item-name" id="hapus-item-nama">—</span>
    </div>
    <div class="del-warning">⚠️ Item ini akan dihapus permanen dan tidak dapat dipulihkan.</div>
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeModal('modal-hapus-item')">Batal</button>
      <form method="POST" id="form-hapus-item" action="">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Ya, Hapus</button>
      </form>
    </div>
  </div>
</div>


{{-- ══════════════════════════════════ --}}
{{--  MODAL 4 — HAPUS FOLDER           --}}
{{-- ══════════════════════════════════ --}}
<div class="modal-overlay" id="modal-hapus-folder" onclick="closeBg(event,'modal-hapus-folder')">
  <div class="modal modal-sm">
    <div class="modal-title danger">🗑️ Hapus Folder</div>
    <div class="del-body">
      Anda akan menghapus folder:<br>
      <span class="del-item-name">{{ $folder->nama }}</span>
    </div>
    <div class="del-warning">
      ⚠️ Folder ini berisi <strong>{{ $items->count() }} item</strong>.
      Semua data akan dihapus permanen dan tidak dapat dipulihkan.
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-cancel" onclick="closeModal('modal-hapus-folder')">Batal</button>
      <form method="POST" action="{{ route('datapool.destroy', $folder) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Ya, Hapus Folder</button>
      </form>
    </div>
  </div>
</div>


<script>
// ── Modal helpers ────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function closeBg(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }

// ── Hapus folder ─────────────────────────────
function openDeleteFolder() { openModal('modal-hapus-folder'); }

// ── Edit item ────────────────────────────────
function openEditItem(id, judul, tipe, konten) {
  document.getElementById('edit-item-judul').value = judul;

  const kontenWrap  = document.getElementById('edit-item-konten-wrap');
  const kontenField = document.getElementById('edit-item-konten');
  const kontenLabel = document.getElementById('edit-item-konten-label');

  if (tipe === 'link') {
    kontenWrap.style.display  = 'block';
    kontenLabel.textContent   = 'URL';
    kontenField.value         = konten;
  } else if (tipe === 'catatan') {
    kontenWrap.style.display  = 'block';
    kontenLabel.textContent   = 'Isi Catatan';
    kontenField.value         = konten;
  } else {
    // file — hanya judul yang bisa diedit
    kontenWrap.style.display  = 'none';
  }

  document.getElementById('form-edit-item').action =
    '{{ url("datapool/items") }}/' + id;

  openModal('modal-edit-item');
}

// ── Hapus item ───────────────────────────────
function openDeleteItem(id, judul) {
  document.getElementById('hapus-item-nama').textContent = judul;
  document.getElementById('form-hapus-item').action =
    '{{ url("datapool/items") }}/' + id;
  openModal('modal-hapus-item');
}

// ── Filter item by tipe ──────────────────────
function filterItems(tipe, el) {
  document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');

  let visible = 0;
  document.querySelectorAll('.item-card').forEach(card => {
    let show = false;
    if (tipe === 'semua')     show = true;
    else if (tipe === 'processed') show = card.dataset.processed === 'true';
    else show = card.dataset.tipe === tipe;

    card.style.display = show ? 'flex' : 'none';
    if (show) visible++;
  });

  const noResult = document.getElementById('noItemResult');
  if (noResult) noResult.style.display = visible === 0 ? 'block' : 'none';
}

// ── Tipe switcher modal tambah ───────────────
function switchTipe(scope, tipe, btn) {
  // Update button aktif
  btn.closest('.tipe-row').querySelectorAll('.tipe-btn')
    .forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  // Update hidden input
  document.getElementById('tipe-' + scope).value = tipe;

  // Toggle field group
  ['file','link','catatan'].forEach(t => {
    const el = document.getElementById('field-' + scope + '-' + t);
    if (el) el.classList.toggle('visible', t === tipe);
  });
}

// ── Auto-buka modal tambah jika ada error ────
@if($errors->any())
  document.addEventListener('DOMContentLoaded', () => {
    openModal('modal-tambah');
    @if(old('tipe') && old('tipe') !== 'file')
      const tipeOld = '{{ old("tipe") }}';
      const btn = document.querySelector(`.tipe-btn[onclick*="${tipeOld}"]`);
      if (btn) switchTipe('tambah', tipeOld, btn);
    @endif
  });
@endif
</script>

</body>
</html>