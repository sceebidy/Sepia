<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SEPIA — {{ $folder->nama }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green: #1a5c2e; --green-2: #2e7d4a; --green-light: #f0f7f2;
  --green-border: #b6d9c3; --text: #1a1a1a; --text-muted: #6b7280;
  --border: #e5e7eb; --bg: #ffffff; --bg-secondary: #f9fafb; --bg-tertiary: #f3f4f6;
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
.tb-btn { padding: 7px 14px; font-size: 12px; border: 1px solid var(--border); border-radius: 8px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.12s; font-weight: 500; }
.tb-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.tb-btn.primary { background: var(--green); color: #fff; border-color: var(--green); }
.tb-btn.primary:hover { background: #14482a; }
.tb-btn.danger { color: #dc2626; border-color: #fecaca; }
.tb-btn.danger:hover { background: #fef2f2; }

.content { flex: 1; overflow-y: auto; padding: 28px; display: flex; flex-direction: column; gap: 20px; }

/* ── ALERT ── */
.alert { padding: 12px 16px; border-radius: 10px; font-size: 12.5px; }
.alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

/* ── FOLDER HEADER CARD ── */
.folder-header-card { background: #fff; border: 1.5px solid var(--border); border-radius: 14px; overflow: hidden; }
.folder-header-stripe { height: 5px; }
.folder-header-body { padding: 20px 24px; display: flex; align-items: center; gap: 18px; }
.folder-header-emoji { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 28px; flex-shrink: 0; }
.folder-header-info { flex: 1; }
.folder-header-name { font-size: 18px; font-weight: 600; margin-bottom: 4px; }
.folder-header-desc { font-size: 12.5px; color: var(--text-muted); line-height: 1.6; }
.folder-header-meta { display: flex; align-items: center; gap: 12px; margin-top: 10px; }
.folder-meta-chip { font-size: 11px; color: var(--text-muted); background: var(--bg-tertiary); padding: 3px 10px; border-radius: 20px; border: 1px solid var(--border); }
.folder-tag { font-size: 10px; padding: 3px 10px; border-radius: 20px; border: 1px solid; font-weight: 600; }

/* ── SECTION ── */
.section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.section-title { font-size: 13px; font-weight: 600; }

/* ── ITEM LIST ── */
.item-list { display: flex; flex-direction: column; gap: 8px; }
.item-card { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; transition: border-color 0.13s; }
.item-card:hover { border-color: var(--green-border); }
.item-icon { font-size: 20px; flex-shrink: 0; margin-top: 1px; }
.item-info { flex: 1; min-width: 0; }
.item-title { font-size: 13px; font-weight: 500; margin-bottom: 3px; }
.item-meta { font-size: 11px; color: var(--text-muted); }
.item-meta a { color: var(--green); text-decoration: none; }
.item-meta a:hover { text-decoration: underline; }
.item-konten { font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.6; background: var(--bg-tertiary); padding: 8px 10px; border-radius: 6px; }
.item-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.item-badge { font-size: 10px; padding: 2px 8px; border-radius: 20px; border: 1px solid; }
.badge-file    { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
.badge-link    { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.badge-catatan { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.badge-processed { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }

/* ── EMPTY STATE ── */
.empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); background: #fff; border: 1.5px dashed var(--border); border-radius: 14px; }
.empty-state-icon { font-size: 36px; margin-bottom: 12px; }
.empty-state-title { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 6px; }

/* ── MODAL ── */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 100; display: none; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal { background: #fff; border-radius: 16px; padding: 28px; width: 500px; max-width: 95vw; }
.modal-title { font-size: 16px; font-weight: 600; margin-bottom: 20px; }
.form-group { margin-bottom: 14px; }
.form-label { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-bottom: 6px; display: block; }
.form-input { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; transition: border-color 0.12s; }
.form-input:focus { border-color: var(--green-border); }
.form-select { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; background: #fff; }
.form-textarea { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; resize: vertical; min-height: 80px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; }
.btn-cancel { padding: 8px 16px; border: 1px solid var(--border); border-radius: 8px; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; cursor: pointer; color: var(--text-muted); }
.btn-submit { padding: 8px 20px; border: none; border-radius: 8px; background: var(--green); color: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; }
.btn-submit:hover { background: #14482a; }

/* ── TIPE SWITCHER ── */
.tipe-row { display: flex; gap: 8px; }
.tipe-btn { flex: 1; padding: 8px; border: 1.5px solid var(--border); border-radius: 8px; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 12px; cursor: pointer; text-align: center; transition: all 0.12s; color: var(--text-muted); }
.tipe-btn.active { border-color: var(--green); background: var(--green-light); color: var(--green); font-weight: 600; }
.field-group { display: none; }
.field-group.visible { display: block; }
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
      <a href="{{ route('datapool.index') }}" style="color:var(--text-muted);font-size:13px;text-decoration:none">← Kembali</a>
      <div class="page-title">{{ $folder->nama }}</div>
    </div>
    <div class="topbar-right">
      <button class="tb-btn primary" onclick="openModal()">+ Tambah Sumber</button>
      <form method="POST" action="{{ route('datapool.destroy', $folder) }}" onsubmit="return confirm('Hapus folder ini beserta semua isinya?')">
        @csrf @method('DELETE')
        <button type="submit" class="tb-btn danger">🗑 Hapus Folder</button>
      </form>
    </div>
  </div>

  <div class="content">

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    {{-- ── INFO FOLDER ── --}}
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
            <span class="folder-meta-chip">👤 {{ $folder->dibuat_oleh }}</span>
            <span class="folder-meta-chip">📅 {{ $folder->created_at->format('d M Y') }}</span>
            <span class="folder-tag" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border-color:{{ $sc['border'] }}">
              {{ ucfirst($folder->status) }}
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- ── 3 SECTION CARDS ── --}}
    <div>
      <div class="section-head">
        <div class="section-title">Aksi Folder</div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">

        {{-- ① ANALISIS --}}
        <div onclick="startAnalysis({{ $folder->id }})" style="cursor:pointer;background:#fff;border:1.5px solid #e5e7eb;border-radius:14px;overflow:hidden;transition:border-color 0.13s" onmouseover="this.style.borderColor='#1e6fa3'" onmouseout="this.style.borderColor='#e5e7eb'">
          <div style="height:4px;background:#1e6fa3"></div>
          <div style="padding:18px 20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
              <div style="width:36px;height:36px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px">⚡</div>
              <span style="font-size:13px;color:#9ca3af">→</span>
            </div>
            <div style="font-size:13px;font-weight:600;margin-bottom:6px">Analisis Prediksi</div>
            <div style="font-size:11.5px;color:#6b7280;line-height:1.6;margin-bottom:14px">Analisis otomatis kasus menggunakan AI — ekstrak SWOT, peta aktor, timeline, dan rekomendasi.</div>
            <div style="display:flex;align-items:center;justify-content:space-between">
              @if($analisis)
                <span style="font-size:11px;color:#6b7280">Risiko <strong>{{ $analisis->tingkat_risiko }}/10</strong></span>
                <span style="font-size:11px;background:#eff6ff;color:#1e6fa3;border:1px solid #bfdbfe;padding:3px 10px;border-radius:20px">Lihat →</span>
              @else
                <span style="font-size:11px;color:#6b7280">Belum ada analisis</span>
                <span style="font-size:11px;background:#eff6ff;color:#1e6fa3;border:1px solid #bfdbfe;padding:3px 10px;border-radius:20px">Mulai →</span>
              @endif
            </div>
          </div>
        </div>

        {{-- ② DISTRIBUSI --}}
        <div style="cursor:not-allowed;background:#fff;border:1.5px solid #e5e7eb;border-radius:14px;overflow:hidden;opacity:0.7">
          <div style="height:4px;background:#d97706"></div>
          <div style="padding:18px 20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
              <div style="width:36px;height:36px;background:#fffbeb;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px">📤</div>
              <span style="font-size:13px;color:#9ca3af">→</span>
            </div>
            <div style="font-size:13px;font-weight:600;margin-bottom:6px">Distribusi</div>
            <div style="font-size:11.5px;color:#6b7280;line-height:1.6;margin-bottom:14px">Kirim laporan dan nota dinas ke instansi terkait — Kejagung, KPK, dan lembaga pengawas.</div>
            <div style="display:flex;align-items:center;justify-content:space-between">
              <span style="font-size:11px;color:#6b7280">Belum tersedia</span>
              <span style="font-size:11px;background:#fffbeb;color:#b45309;border:1px solid #fde68a;padding:3px 10px;border-radius:20px">Segera →</span>
            </div>
          </div>
        </div>

        {{-- ③ LAPORAN --}}
        <div style="cursor:not-allowed;background:#fff;border:1.5px solid #e5e7eb;border-radius:14px;overflow:hidden;opacity:0.7">
          <div style="height:4px;background:#065f46"></div>
          <div style="padding:18px 20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
              <div style="width:36px;height:36px;background:#ecfdf5;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px">📋</div>
              <span style="font-size:13px;color:#9ca3af">→</span>
            </div>
            <div style="font-size:13px;font-weight:600;margin-bottom:6px">Laporan</div>
            <div style="font-size:11.5px;color:#6b7280;line-height:1.6;margin-bottom:14px">Buat dan ekspor laporan analisis kasus — ringkasan eksekutif, daftar aktor, dan rekomendasi.</div>
            <div style="display:flex;align-items:center;justify-content:space-between">
              <span style="font-size:11px;color:#6b7280">Belum tersedia</span>
              <span style="font-size:11px;background:#ecfdf5;color:#065f46;border:1px solid #99f6e4;padding:3px 10px;border-radius:20px">Segera →</span>
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- ── DAFTAR ITEMS ── --}}
    <div>
      <div class="section-head">
        <div class="section-title">Sumber Data ({{ $items->count() }})</div>
      </div>

      @if($items->isEmpty())
      <div class="empty-state">
        <div class="empty-state-icon">📭</div>
        <div class="empty-state-title">Folder masih kosong</div>
        <div style="font-size:12px;margin-top:4px">Tambahkan file, link, atau catatan ke folder ini.</div>
      </div>
      @else
      <div class="item-list">
        @foreach($items as $item)
        <div class="item-card">
          <div class="item-icon">{{ $item->tipeIcon() }}</div>
          <div class="item-info">
            <div class="item-title">{{ $item->judul }}</div>
            <div class="item-meta">
              Ditambahkan oleh {{ $item->ditambahkan_oleh }} · {{ $item->created_at->diffForHumans() }}
              @if($item->tipe === 'file')
                · {{ $item->ukuranFormatted() }} · {{ $item->file_nama }}
              @endif
              @if($item->tipe === 'link')
                · <a href="{{ $item->konten }}" target="_blank">{{ $item->konten }}</a>
              @endif
            </div>
            @if($item->tipe === 'catatan' && $item->konten)
            <div class="item-konten">{{ $item->konten }}</div>
            @endif
            @if($item->hasil_rangkuman)
            <div class="item-konten" style="border-left:3px solid #6d28d9;padding-left:10px">
              <strong style="font-size:10px;color:#6d28d9">RANGKUMAN AI:</strong><br>
              {{ $item->hasil_rangkuman }}
            </div>
            @endif
          </div>
          <div class="item-actions">
            <span class="item-badge badge-{{ $item->tipe }}">{{ strtoupper($item->tipe) }}</span>
            @if($item->processed)
            <span class="item-badge badge-processed">✓ AI</span>
            @endif
            @if($item->tipe === 'file' && $item->file_path)
            <a href="{{ Storage::url($item->file_path) }}" target="_blank" class="tb-btn" style="padding:4px 10px;font-size:11px">⬇</a>
            @endif
            <form method="POST" action="{{ route('datapool.items.delete', $item) }}" onsubmit="return confirm('Hapus item ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="tb-btn danger" style="padding:4px 10px;font-size:11px">✕</button>
            </form>
          </div>
        </div>
        @endforeach
      </div>
      @endif
    </div>

  </div>
</div>

{{-- ══ MODAL TAMBAH ITEM ══ --}}
<div class="modal-overlay" id="modal-overlay" onclick="closeModal(event)">
  <div class="modal">
    <div class="modal-title">➕ Tambah Sumber ke Folder</div>
    <form method="POST" action="{{ route('datapool.items.store', $folder) }}" enctype="multipart/form-data">
      @csrf

      {{-- Pilih tipe --}}
      <div class="form-group">
        <label class="form-label">Tipe Sumber</label>
        <div class="tipe-row">
          <button type="button" class="tipe-btn active" onclick="switchTipe('file', this)">📄 File</button>
          <button type="button" class="tipe-btn" onclick="switchTipe('link', this)">🔗 Link</button>
          <button type="button" class="tipe-btn" onclick="switchTipe('catatan', this)">📝 Catatan</button>
        </div>
        <input type="hidden" name="tipe" id="tipe-input" value="file" />
      </div>

      <div class="form-group">
        <label class="form-label">Judul *</label>
        <input class="form-input" type="text" name="judul" placeholder="Nama atau judul sumber ini" required />
      </div>

      {{-- Field: FILE --}}
      <div class="field-group visible" id="field-file">
        <div class="form-group">
          <label class="form-label">Upload File (PDF, Word, Excel — maks 20MB)</label>
          <input class="form-input" type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt" />
        </div>
      </div>

      {{-- Field: LINK --}}
      <div class="field-group" id="field-link">
        <div class="form-group">
          <label class="form-label">URL *</label>
          <input class="form-input" type="url" name="konten_link" placeholder="https://..." />
        </div>
      </div>

      {{-- Field: CATATAN --}}
      <div class="field-group" id="field-catatan">
        <div class="form-group">
          <label class="form-label">Isi Catatan *</label>
          <textarea class="form-textarea" name="konten_catatan" placeholder="Tulis catatan..."></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn-submit">Tambahkan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal() { document.getElementById('modal-overlay').classList.add('open'); }
function closeModal(e) {
  if (!e || e.target === document.getElementById('modal-overlay')) {
    document.getElementById('modal-overlay').classList.remove('open');
  }
}
function switchTipe(tipe, btn) {
  document.querySelectorAll('.tipe-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tipe-input').value = tipe;
  document.querySelectorAll('.field-group').forEach(g => g.classList.remove('visible'));
  document.getElementById('field-' + tipe).classList.add('visible');
}
</script>

{{-- ══ MODAL LOADING ANALISIS ══ --}}
<div id="modal-analisis-loading" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:200;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:18px;padding:36px 40px;width:380px;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.2)">
    <div style="font-size:44px;margin-bottom:16px">⚙️</div>
    <div style="font-size:16px;font-weight:600;color:#1a5c2e;margin-bottom:8px">Sedang Menganalisis...</div>
    <div style="font-size:12px;color:#6b7280;line-height:1.7;margin-bottom:20px">
      AI sedang membaca dan menganalisis sumber data folder ini.<br>
      <span style="font-size:11px">SWOT • Peta Aktor • Timeline • Risiko • Rekomendasi</span>
    </div>
    <div style="display:flex;align-items:center;justify-content:center;gap:8px">
      <div style="width:8px;height:8px;border-radius:50%;background:#1a5c2e;animation:dotPulse 1.4s infinite"></div>
      <div style="width:8px;height:8px;border-radius:50%;background:#1a5c2e;animation:dotPulse 1.4s 0.25s infinite"></div>
      <div style="width:8px;height:8px;border-radius:50%;background:#1a5c2e;animation:dotPulse 1.4s 0.5s infinite"></div>
    </div>
  </div>
</div>

<style>
@keyframes dotPulse {
  0%, 100% { opacity: 0.2; transform: scale(0.8); }
  50% { opacity: 1; transform: scale(1); }
}
</style>

<script>
function startAnalysis(folderId) {
  // Kalau sudah ada analisis, langsung redirect ke hasil
  @if($analisis)
    window.location.href = '/datapool/{{ $folder->id }}/analisis/{{ $analisis->id }}';
    return;
  @endif

  // Tampilkan modal loading
  const modal = document.getElementById('modal-analisis-loading');
  modal.style.display = 'flex';

  // POST ke Laravel untuk trigger analisis
  fetch('/datapool/' + folderId + '/analisis', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({}),
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      pollAnalysisResult(data.folder_id, data.analisis_id, 0);
    } else {
      modal.style.display = 'none';
      alert('Error: ' + (data.error || 'Terjadi kesalahan'));
    }
  })
  .catch(err => {
    console.error('Fetch error:', err);
    modal.style.display = 'none';
    alert('Tidak dapat terhubung ke server');
  });
}

function pollAnalysisResult(folderId, analisisId, attempts) {
  if (attempts > 60) {
    document.getElementById('modal-analisis-loading').style.display = 'none';
    alert('Analisis memakan waktu terlalu lama. Silakan coba lagi.');
    return;
  }

  setTimeout(() => {
    fetch('/datapool/' + folderId + '/analisis/' + analisisId, {
      headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
      if (data.analisis && data.analisis.tingkat_risiko > 0) {
        window.location.href = '/datapool/' + folderId + '/analisis/' + analisisId;
      } else {
        pollAnalysisResult(folderId, analisisId, attempts + 1);
      }
    })
    .catch(() => pollAnalysisResult(folderId, analisisId, attempts + 1));
  }, 2000);
}
</script>
</body>
</html>