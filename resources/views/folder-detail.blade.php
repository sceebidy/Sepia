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
  --green: #1a5c2e; --green-light: #f0f7f2; --green-border: #b6d9c3;
  --text: #1a1a1a; --text-muted: #6b7280; --border: #e5e7eb;
  --bg-tertiary: #f3f4f6; --nav-width: 220px;
}
body { font-family: 'DM Sans', sans-serif; background: var(--bg-tertiary); color: var(--text); height: 100vh; display: flex; overflow: hidden; }

.sidenav { width: var(--nav-width); background: var(--green); display: flex; flex-direction: column; flex-shrink: 0; }
.sidenav-brand { padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.brand-logo { font-size: 18px; font-weight: 700; letter-spacing: 0.14em; color: #fff; }
.brand-sub { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 2px; letter-spacing: 0.05em; text-transform: uppercase; }
.sidenav-section { padding: 18px 12px 8px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; margin-bottom: 2px; text-decoration: none; transition: background 0.12s; border: 1px solid transparent; color: rgba(255,255,255,0.72); font-size: 13px; font-weight: 500; }
.nav-item:hover { background: rgba(255,255,255,0.1); color: #fff; }
.nav-item.active { background: rgba(255,255,255,0.14); border-color: rgba(255,255,255,0.18); color: #fff; }
.nav-icon { width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; background: rgba(255,255,255,0.1); }
.nav-item.active .nav-icon { background: rgba(255,255,255,0.2); }
.nav-item-text { flex: 1; }

.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
.topbar { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 56px; background: #fff; border-bottom: 1px solid var(--border); flex-shrink: 0; }
.topbar-left { display: flex; align-items: center; gap: 12px; }
.page-title { font-size: 15px; font-weight: 600; }
.topbar-right { display: flex; align-items: center; gap: 8px; }
.tb-btn { padding: 7px 14px; font-size: 12px; border: 1px solid var(--border); border-radius: 8px; background: #fff; color: var(--text-muted); cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.12s; font-weight: 500; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
.tb-btn:hover { border-color: var(--green-border); color: var(--green); background: var(--green-light); }
.tb-btn.primary { background: var(--green); color: #fff; border-color: var(--green); }
.tb-btn.primary:hover { background: #14482a; }
.tb-btn.danger { color: #dc2626; border-color: #fecaca; }
.tb-btn.danger:hover { background: #fef2f2; }

.content { flex: 1; overflow-y: auto; padding: 28px; display: flex; flex-direction: column; gap: 20px; }
.alert { padding: 12px 16px; border-radius: 10px; font-size: 12.5px; }
.alert-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

/* FOLDER HEADER */
.folder-header-card { background: #fff; border: 1.5px solid var(--border); border-radius: 14px; overflow: hidden; }
.folder-header-stripe { height: 5px; }
.folder-header-body { padding: 20px 24px; display: flex; align-items: center; gap: 18px; }
.folder-header-emoji { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 28px; flex-shrink: 0; }
.folder-header-info { flex: 1; }
.folder-header-name { font-size: 18px; font-weight: 600; margin-bottom: 4px; }
.folder-header-desc { font-size: 12.5px; color: var(--text-muted); line-height: 1.6; }
.folder-header-meta { display: flex; align-items: center; gap: 12px; margin-top: 10px; flex-wrap: wrap; }
.folder-meta-chip { font-size: 11px; color: var(--text-muted); background: var(--bg-tertiary); padding: 3px 10px; border-radius: 20px; border: 1px solid var(--border); }
.folder-tag { font-size: 10px; padding: 3px 10px; border-radius: 20px; border: 1px solid; font-weight: 600; }

/* TOMBOL ANALISIS BESAR */
.btn-analisis-wrap { display: flex; align-items: center; justify-content: space-between; background: #fff; border: 1.5px solid var(--border); border-radius: 14px; padding: 16px 20px; }
.btn-analisis-info { display: flex; align-items: center; gap: 12px; }
.btn-analisis-icon { width: 40px; height: 40px; background: #eff6ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.btn-analisis-title { font-size: 13px; font-weight: 600; margin-bottom: 2px; }
.btn-analisis-sub { font-size: 11.5px; color: var(--text-muted); }
.btn-analisis-trigger { padding: 10px 20px; background: #1e6fa3; color: #fff; border: none; border-radius: 10px; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.12s; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
.btn-analisis-trigger:hover { background: #1a5f8f; }
.btn-analisis-trigger:disabled { background: #9ca3af; cursor: not-allowed; }

/* RESULT CARDS */
.result-card { border-radius: 14px; overflow: hidden; border: 1.5px solid var(--border); transition: border-color 0.13s, box-shadow 0.13s; text-decoration: none; display: block; background: #fff; }
.result-card.clickable:hover { border-color: var(--green-border); box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
.result-card.disabled { opacity: 0.5; pointer-events: none; }
.result-card-stripe { height: 4px; }
.result-card-body { padding: 18px 20px; }
.result-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.result-card-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
.result-card-title { font-size: 13px; font-weight: 600; margin-bottom: 4px; color: var(--text); }
.result-card-desc { font-size: 11.5px; color: var(--text-muted); line-height: 1.6; }
.result-card-footer { padding: 10px 20px; border-top: 1px solid var(--border); background: #fafafa; display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: var(--text-muted); }

/* ITEM LIST */
.section-title { font-size: 13px; font-weight: 600; }
.item-list { display: flex; flex-direction: column; gap: 8px; }
.item-card { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; transition: border-color 0.13s; }
.item-card:hover { border-color: var(--green-border); }
.item-icon { font-size: 20px; flex-shrink: 0; margin-top: 1px; }
.item-info { flex: 1; min-width: 0; }
.item-title { font-size: 13px; font-weight: 500; margin-bottom: 3px; }
.item-meta { font-size: 11px; color: var(--text-muted); }
.item-meta a { color: var(--green); text-decoration: none; }
.item-konten { font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.6; background: var(--bg-tertiary); padding: 8px 10px; border-radius: 6px; }
.item-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.item-badge { font-size: 10px; padding: 2px 8px; border-radius: 20px; border: 1px solid; }
.badge-file    { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
.badge-link    { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
.badge-catatan { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.badge-processed { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }

.empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); background: #fff; border: 1.5px dashed var(--border); border-radius: 14px; }
.empty-state-icon { font-size: 36px; margin-bottom: 12px; }
.empty-state-title { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 6px; }

/* MODALS */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 100; display: none; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal { background: #fff; border-radius: 16px; padding: 28px; width: 500px; max-width: 95vw; }
.modal-title { font-size: 16px; font-weight: 600; margin-bottom: 20px; }
.form-group { margin-bottom: 14px; }
.form-label { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-bottom: 6px; display: block; }
.form-input { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; transition: border-color 0.12s; }
.form-input:focus { border-color: var(--green-border); }
.form-textarea { width: 100%; padding: 9px 12px; border: 1px solid var(--border); border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); outline: none; resize: vertical; min-height: 80px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; }
.btn-cancel { padding: 8px 16px; border: 1px solid var(--border); border-radius: 8px; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; cursor: pointer; color: var(--text-muted); }
.btn-submit { padding: 8px 20px; border: none; border-radius: 8px; background: var(--green); color: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; }
.btn-submit:hover { background: #14482a; }
.tipe-row { display: flex; gap: 8px; }
.tipe-btn { flex: 1; padding: 8px; border: 1.5px solid var(--border); border-radius: 8px; background: #fff; font-family: 'DM Sans', sans-serif; font-size: 12px; cursor: pointer; text-align: center; transition: all 0.12s; color: var(--text-muted); }
.tipe-btn.active { border-color: var(--green); background: var(--green-light); color: var(--green); font-weight: 600; }
.field-group { display: none; }
.field-group.visible { display: block; }

.modal-full { position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 200; display: none; align-items: center; justify-content: center; }
.modal-full.open { display: flex; }

@keyframes dotPulse {
  0%, 100% { opacity: 0.2; transform: scale(0.8); }
  50% { opacity: 1; transform: scale(1); }
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
    <a class="nav-item" href="{{ route('dashboard') }}"><div class="nav-icon">📊</div><div class="nav-item-text">Dashboard</div></a>
    <a class="nav-item active" href="{{ route('datapool.index') }}"><div class="nav-icon">📋</div><div class="nav-item-text">RPI</div></a>
  </div>
</nav>

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

    @if(session('success'))
    <div class="alert alert-success">✓ {{ session('success') }}</div>
    @endif

    {{-- INFO FOLDER --}}
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
            <span class="folder-tag" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border-color:{{ $sc['border'] }}">{{ ucfirst($folder->status) }}</span>
          </div>
        </div>
      </div>
    </div>

    {{-- TOMBOL ANALISIS TUNGGAL --}}
    <div class="btn-analisis-wrap">
      <div class="btn-analisis-info">
        <div class="btn-analisis-icon">⚡</div>
        <div>
          <div class="btn-analisis-title">Analisis Prediksi AI</div>
          <div class="btn-analisis-sub">
            @if($analisis)
              Terakhir dianalisis {{ $analisis->created_at->diffForHumans() }} · Risiko <strong>{{ $analisis->tingkat_risiko }}/10</strong>
            @else
              Belum ada analisis — tambahkan sumber data lalu mulai analisis
            @endif
          </div>
        </div>
      </div>
      <button onclick="startAnalysis({{ $folder->id }})" class="btn-analisis-trigger">
        @if($analisis) 🔄 Generate Ulang @else ⚡ Mulai Analisis @endif
      </button>
    </div>

    {{-- RESULT CARDS --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">

      {{-- CARD ANALISIS --}}
      @if($analisis)
          <a href="{{ route('laporan.buat', [$folder, $analisis]) }}" class="result-card clickable">      @else
      <div class="result-card disabled">
      @endif
        <div class="result-card-stripe" style="background:#1e6fa3"></div>
        <div class="result-card-body">
          <div class="result-card-header">
            <div class="result-card-icon" style="background:#eff6ff">📊</div>
            @if($analisis)
            <span style="font-size:10px;background:#eff6ff;color:#1e6fa3;border:1px solid #bfdbfe;padding:2px 8px;border-radius:20px;font-weight:600">
              {{ $analisis->tingkat_risiko >= 8 ? '🔴 WASPADA MERAH' : ($analisis->tingkat_risiko >= 6 ? '🟡 WASPADA KUNING' : '🟢 KONDUSIF') }}
            </span>
            @else
            <span style="font-size:10px;background:#f3f4f6;color:#9ca3af;padding:2px 8px;border-radius:20px">Belum ada</span>
            @endif
          </div>
          <div class="result-card-title">Hasil Analisis</div>
          <div class="result-card-desc">SWOT, peta aktor, timeline, risk assessment, dan rekomendasi per jabatan.</div>
        </div>
        <div class="result-card-footer">
          @if($analisis)
          <span>{{ $analisis->jumlah_sumber }} sumber · {{ $analisis->model_versi }}</span>
          <span style="color:#1e6fa3;font-weight:600">Buka →</span>
          @else
          <span>Jalankan analisis untuk membuka</span>
          @endif
        </div>
      @if($analisis) </a> @else </div> @endif

      {{-- CARD LAPORAN --}}
      @if($analisis)
        <a href="/datapool/{{ $folder->id }}/analisis/{{ $analisis->id }}" class="result-card clickable">      @else
      <div class="result-card disabled">
      @endif
        <div class="result-card-stripe" style="background:#065f46"></div>
        <div class="result-card-body">
          <div class="result-card-header">
            <div class="result-card-icon" style="background:#ecfdf5">📋</div>
            @if($analisis)
            <span style="font-size:10px;background:#ecfdf5;color:#065f46;border:1px solid #99f6e4;padding:2px 8px;border-radius:20px;font-weight:600">Siap</span>
            @else
            <span style="font-size:10px;background:#f3f4f6;color:#9ca3af;padding:2px 8px;border-radius:20px">Belum ada</span>
            @endif
          </div>
          <div class="result-card-title">Laporan Intelijen</div>
          <div class="result-card-desc">Laporan situasional resmi — fakta-fakta, analisis intelijen, dan rekomendasi per jabatan.</div>
        </div>
        <div class="result-card-footer">
          @if($analisis)
          <span>{{ strtoupper(\Carbon\Carbon::parse($analisis->tanggal_analisis)->format('d M Y')) }}</span>
          <span style="color:#065f46;font-weight:600">Buka →</span>
          @else
          <span>Jalankan analisis untuk membuka</span>
          @endif
        </div>
      @if($analisis) </a> @else </div> @endif

    </div>

    {{-- DAFTAR ITEMS --}}
    <div>
      <div style="margin-bottom:12px">
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
              @if($item->tipe === 'file') · {{ $item->ukuranFormatted() }} · {{ $item->file_nama }} @endif
              @if($item->tipe === 'link') · <a href="{{ $item->konten }}" target="_blank">{{ $item->konten }}</a> @endif
            </div>
            @if($item->tipe === 'catatan' && $item->konten)
            <div class="item-konten">{{ $item->konten }}</div>
            @endif
            @if($item->hasil_rangkuman)
            <div class="item-konten" style="border-left:3px solid #6d28d9;padding-left:10px">
              <strong style="font-size:10px;color:#6d28d9">RANGKUMAN AI:</strong><br>{{ $item->hasil_rangkuman }}
            </div>
            @endif
          </div>
          <div class="item-actions">
            <span class="item-badge badge-{{ $item->tipe }}">{{ strtoupper($item->tipe) }}</span>
            @if($item->processed)<span class="item-badge badge-processed">✓ AI</span>@endif
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

{{-- MODAL TAMBAH ITEM --}}
<div class="modal-overlay" id="modal-overlay" onclick="closeModal(event)">
  <div class="modal">
    <div class="modal-title">➕ Tambah Sumber ke Folder</div>
    <form method="POST" action="{{ route('datapool.items.store', $folder) }}" enctype="multipart/form-data">
      @csrf
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
      <div class="field-group visible" id="field-file">
        <div class="form-group">
          <label class="form-label">Upload File (PDF, Word, Excel — maks 20MB)</label>
          <input class="form-input" type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt" />
        </div>
      </div>
      <div class="field-group" id="field-link">
        <div class="form-group">
          <label class="form-label">URL *</label>
          <input class="form-input" type="url" name="konten_link" placeholder="https://..." />
        </div>
      </div>
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

{{-- MODAL KONFIRMASI GENERATE ULANG --}}
<div class="modal-full" id="modal-konfirmasi">
  <div style="background:#fff;border-radius:18px;padding:32px 36px;width:420px;max-width:95vw;box-shadow:0 24px 64px rgba(0,0,0,0.2)">
    <div style="font-size:36px;text-align:center;margin-bottom:14px">🔄</div>
    <div style="font-size:16px;font-weight:600;text-align:center;margin-bottom:8px">Generate Ulang Analisis?</div>
    <div style="font-size:12.5px;color:#6b7280;text-align:center;line-height:1.7;margin-bottom:24px">
      Folder ini sudah memiliki hasil analisis.<br>
      Generate ulang akan menghapus analisis sebelumnya dan membuat yang baru berdasarkan data terkini.
    </div>
    <div style="display:flex;gap:10px">
      <button id="btn-konfirmasi-batal" style="flex:1;padding:11px;border:1.5px solid #e5e7eb;border-radius:10px;background:#fff;font-family:'DM Sans',sans-serif;font-size:13px;cursor:pointer;color:#374151;font-weight:500">
        Batal
      </button>
      <button id="btn-konfirmasi-ulang" style="flex:1;padding:11px;border:none;border-radius:10px;background:#1e6fa3;color:#fff;font-family:'DM Sans',sans-serif;font-size:13px;cursor:pointer;font-weight:600">
        🔄 Generate Ulang
      </button>
    </div>
  </div>
</div>

{{-- MODAL LOADING ANALISIS --}}
<div class="modal-full" id="modal-analisis-loading">
  <div style="background:#fff;border-radius:18px;padding:36px 40px;width:380px;max-width:95vw;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.2)">
    <div style="font-size:44px;margin-bottom:16px">⚙️</div>
    <div style="font-size:16px;font-weight:600;color:#1a5c2e;margin-bottom:8px">Sedang Menganalisis...</div>
    <div style="font-size:12px;color:#6b7280;line-height:1.7;margin-bottom:20px">
      AI sedang membaca dan menganalisis sumber data folder ini.<br>
      <span style="font-size:11px;color:#9ca3af">SWOT • Peta Aktor • Timeline • Risiko • Rekomendasi</span>
    </div>
    <div style="display:flex;align-items:center;justify-content:center;gap:8px">
      <div style="width:8px;height:8px;border-radius:50%;background:#1a5c2e;animation:dotPulse 1.4s infinite"></div>
      <div style="width:8px;height:8px;border-radius:50%;background:#1a5c2e;animation:dotPulse 1.4s 0.25s infinite"></div>
      <div style="width:8px;height:8px;border-radius:50%;background:#1a5c2e;animation:dotPulse 1.4s 0.5s infinite"></div>
    </div>
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

function startAnalysis(folderId) {
  @if($analisis)
    document.getElementById('modal-konfirmasi').classList.add('open');
    document.getElementById('btn-konfirmasi-batal').onclick = function() {
      document.getElementById('modal-konfirmasi').classList.remove('open');
    };
    document.getElementById('btn-konfirmasi-ulang').onclick = function() {
      document.getElementById('modal-konfirmasi').classList.remove('open');
      jalankanAnalisis(folderId);
    };
    return;
  @endif
  jalankanAnalisis(folderId);
}

function jalankanAnalisis(folderId) {
  document.getElementById('modal-analisis-loading').classList.add('open');
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
      window.location.href = '/datapool/' + data.folder_id + '/analisis/' + data.analisis_id;
    } else {
      document.getElementById('modal-analisis-loading').classList.remove('open');
      alert('Error: ' + (data.error || 'Terjadi kesalahan'));
    }
  })
  .catch(() => {
    document.getElementById('modal-analisis-loading').classList.remove('open');
    alert('Tidak dapat terhubung ke server');
  });
}
</script>

</body>
</html>