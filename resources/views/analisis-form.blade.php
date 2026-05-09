<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<title>SEPIA — Input Analisis: {{ $folder->nama }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green:#1a5c2e; --green-2:#2e7d4a; --green-light:#f0f7f2;
  --green-border:#b6d9c3; --text:#1a1a1a; --text-muted:#6b7280;
  --border:#e5e7eb; --bg:#fff; --bg-2:#f9fafb; --bg-3:#f3f4f6;
  --nav-width:220px; --danger:#dc2626;
  --s-bg:#f0fdf4;--s-border:#86efac;--s-text:#166534;
  --w-bg:#fff7ed;--w-border:#fdba74;--w-text:#9a3412;
  --o-bg:#eff6ff;--o-border:#93c5fd;--o-text:#1e40af;
  --t-bg:#fef2f2;--t-border:#fca5a5;--t-text:#991b1b;
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

.main { flex:1; display:flex; flex-direction:column; overflow:hidden; }
.topbar { display:flex; align-items:center; justify-content:space-between; padding:0 24px; height:56px; background:#fff; border-bottom:1px solid var(--border); flex-shrink:0; }
.topbar-left { display:flex; align-items:center; gap:12px; }
.back-link { font-size:13px; color:var(--text-muted); text-decoration:none; }
.back-link:hover { color:var(--green); }
.page-title { font-size:15px; font-weight:600; }
.topbar-right { display:flex; gap:8px; }
.tb-btn { padding:7px 16px; font-size:12px; border:1px solid var(--border); border-radius:8px; background:#fff; color:var(--text-muted); cursor:pointer; font-family:'DM Sans',sans-serif; font-weight:500; transition:all 0.12s; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.tb-btn:hover { border-color:var(--green-border); color:var(--green); background:var(--green-light); }
.tb-btn.primary { background:var(--green); color:#fff; border-color:var(--green); }
.tb-btn.primary:hover { background:#14482a; }

.content { flex:1; overflow-y:auto; padding:28px 28px 30px; }

.form-section { background:#fff; border:1.5px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:16px; }
.form-section-head { padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
.form-section-icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; }
.form-section-title { font-size:14px; font-weight:600; }
.form-section-sub { font-size:11px; color:var(--text-muted); margin-top:1px; }
.form-section-body { padding:20px; }

.form-row { display:grid; gap:14px; margin-bottom:14px; }
.form-row.col-2 { grid-template-columns:1fr 1fr; }
.form-row.col-3 { grid-template-columns:1fr 1fr 1fr; }
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-label { font-size:11.5px; font-weight:500; color:var(--text-muted); }
.form-label span { color:var(--danger); }
.form-input, .form-select, .form-textarea {
  padding:8px 12px; border:1px solid var(--border); border-radius:8px;
  font-family:'DM Sans',sans-serif; font-size:13px; color:var(--text);
  outline:none; transition:border-color 0.12s; background:#fff;
}
.form-input:focus, .form-select:focus, .form-textarea:focus { border-color:var(--green-border); }
.form-textarea { resize:vertical; min-height:70px; }
.range-row { display:flex; align-items:center; gap:10px; }
.range-row input[type=range] { flex:1; }
.range-val { font-size:13px; font-weight:700; color:var(--green); font-family:'DM Mono',monospace; min-width:40px; text-align:right; }

.swot-form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.swot-form-card { border-radius:12px; border:1.5px solid; padding:16px; }
.swot-form-card.s { background:var(--s-bg); border-color:var(--s-border); }
.swot-form-card.w { background:var(--w-bg); border-color:var(--w-border); }
.swot-form-card.o { background:var(--o-bg); border-color:var(--o-border); }
.swot-form-card.t { background:var(--t-bg); border-color:var(--t-border); }
.swot-form-label { font-size:12px; font-weight:700; margin-bottom:10px; display:flex; align-items:center; gap:8px; }
.swot-form-card.s .swot-form-label { color:var(--s-text); }
.swot-form-card.w .swot-form-label { color:var(--w-text); }
.swot-form-card.o .swot-form-label { color:var(--o-text); }
.swot-form-card.t .swot-form-label { color:var(--t-text); }
.swot-letter-badge { width:24px; height:24px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }
.s .swot-letter-badge { background:#16a34a; }
.w .swot-letter-badge { background:#ea580c; }
.o .swot-letter-badge { background:#2563eb; }
.t .swot-letter-badge { background:#dc2626; }
.swot-item-inputs { display:flex; flex-direction:column; gap:6px; }
.swot-item-row { display:flex; align-items:center; gap:6px; }
.swot-item-row input { flex:1; padding:7px 10px; border:1px solid var(--border); border-radius:7px; font-size:12px; font-family:'DM Sans',sans-serif; outline:none; background:#fff; }
.swot-item-row input:focus { border-color:var(--green-border); }
.btn-remove { width:26px; height:26px; border-radius:6px; border:1px solid var(--border); background:#fff; cursor:pointer; font-size:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--text-muted); }
.btn-remove:hover { background:#fef2f2; border-color:#fca5a5; color:var(--danger); }
.btn-add-item { margin-top:8px; font-size:11px; color:var(--text-muted); cursor:pointer; background:none; border:1px dashed var(--border); border-radius:7px; padding:5px 10px; width:100%; font-family:'DM Sans',sans-serif; transition:all 0.12s; }
.btn-add-item:hover { border-color:var(--green-border); color:var(--green); background:var(--green-light); }

.repeater-row { display:grid; gap:8px; padding:14px; padding-right:44px; background:var(--bg-2); border:1px solid var(--border); border-radius:9px; margin-bottom:8px; position:relative; }
.repeater-row .btn-remove { position:absolute; top:12px; right:12px; }
.btn-add-repeater { width:100%; padding:9px; border:1.5px dashed var(--border); border-radius:9px; background:#fff; font-family:'DM Sans',sans-serif; font-size:12px; color:var(--text-muted); cursor:pointer; transition:all 0.12s; margin-top:4px; }
.btn-add-repeater:hover { border-color:var(--green-border); color:var(--green); background:var(--green-light); }

.color-picker-row { display:flex; gap:5px; flex-wrap:wrap; }
.color-dot { width:22px; height:22px; border-radius:50%; cursor:pointer; outline:2px solid transparent; transition:outline 0.1s; }
.color-dot.selected { outline:2px solid var(--green); outline-offset:2px; }

.bottom-bar { display:flex; align-items:center; justify-content:space-between; padding:14px 0 0; }
.submit-info { font-size:12px; color:var(--text-muted); }
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
      <a class="back-link" href="{{ route('datapool.show', $folder) }}">← Kembali</a>
      <div style="width:1px;height:20px;background:var(--border)"></div>
      <div class="page-title">Input Analisis — {{ $folder->nama }}</div>
    </div>
    <div class="topbar-right">
      <button type="submit" form="form-analisis" class="tb-btn primary">💾 Simpan Analisis</button>
    </div>
  </div>

  <div class="content">
    <form id="form-analisis" method="POST" action="{{ route('analisis.store', $folder) }}">
    @csrf

    <div class="form-section">
      <div class="form-section-head">
        <div class="form-section-icon" style="background:#f0f7f2">📋</div>
        <div>
          <div class="form-section-title">Informasi Utama</div>
          <div class="form-section-sub">Data dasar analisis kasus</div>
        </div>
      </div>
      <div class="form-section-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Judul Analisis <span>*</span></label>
            <input class="form-input" type="text" name="judul" value="{{ old('judul', $existing->judul ?? $folder->nama) }}" required />
          </div>
        </div>
        <div class="form-row col-3">
          <div class="form-group">
            <label class="form-label">Tingkat Risiko (0–10) <span>*</span></label>
            <div class="range-row">
              <input type="range" name="tingkat_risiko" min="0" max="10" step="0.1"
                value="{{ old('tingkat_risiko', $existing->tingkat_risiko ?? 5) }}"
                oninput="document.getElementById('risk-val').textContent=this.value" />
              <span class="range-val" id="risk-val">{{ old('tingkat_risiko', $existing->tingkat_risiko ?? 5) }}</span>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Prediksi Vonis</label>
            <input class="form-input" type="text" name="prediksi_vonis" value="{{ old('prediksi_vonis', $existing->prediksi_vonis ?? '') }}" placeholder="cth: 12–15 thn" />
          </div>
          <div class="form-group">
            <label class="form-label">Jumlah Sumber <span>*</span></label>
            <input class="form-input" type="number" name="jumlah_sumber" value="{{ old('jumlah_sumber', $existing->jumlah_sumber ?? $folder->items()->count()) }}" min="0" />
          </div>
        </div>
      </div>
    </div>

    <div class="form-section">
      <div class="form-section-head">
        <div class="form-section-icon" style="background:#f5f3ff">🔲</div>
        <div>
          <div class="form-section-title">Analisis SWOT</div>
          <div class="form-section-sub">Klik "+ Tambah poin" untuk menambah item per kuadran</div>
        </div>
      </div>
      <div class="form-section-body">
        <div class="swot-form-grid">
          @foreach([['s','S','Strengths','Kekuatan'],['w','W','Weaknesses','Kelemahan'],['o','O','Opportunities','Peluang'],['t','T','Threats','Ancaman']] as [$cls,$ltr,$j,$s])
          <div class="swot-form-card {{ $cls }}">
            <div class="swot-form-label">
              <div class="swot-letter-badge">{{ $ltr }}</div>
              {{ $j }} — {{ $s }}
            </div>
            <div class="swot-item-inputs" id="swot-{{ $cls }}-list">
              <div class="swot-item-row">
                <input type="text" name="swot_{{ $cls }}[]" placeholder="Tulis poin..." />
                <button type="button" class="btn-remove" onclick="removeRow(this)">✕</button>
              </div>
            </div>
            <button type="button" class="btn-add-item" onclick="addSwotItem('{{ $cls }}')">+ Tambah poin</button>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="form-section">
      <div class="form-section-head">
        <div class="form-section-icon" style="background:#fdf4ff">👥</div>
        <div>
          <div class="form-section-title">Peta Aktor</div>
          <div class="form-section-sub">Tersangka, saksi, dan pihak terkait</div>
        </div>
      </div>
      <div class="form-section-body">
        <div id="aktor-list">
          <div class="repeater-row" style="grid-template-columns:1fr 80px 1fr 120px 1fr">
            <div class="form-group"><label class="form-label">Nama</label><input class="form-input" type="text" name="aktor_nama[]" /></div>
            <div class="form-group"><label class="form-label">Inisial</label><input class="form-input" type="text" name="aktor_inisial[]" maxlength="3" /></div>
            <div class="form-group"><label class="form-label">Peran</label><input class="form-input" type="text" name="aktor_peran[]" /></div>
            <div class="form-group">
              <label class="form-label">Status</label>
              <select class="form-select" name="aktor_status[]">
                <option value="tersangka">Tersangka</option>
                <option value="saksi">Saksi</option>
                <option value="dpo">DPO</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Warna</label>
              <div class="color-picker-row">
                @foreach(['#1a5c2e','#1e6fa3','#86198f','#d97706','#374151','#dc2626','#0891b2','#7c3aed'] as $c)
                <div class="color-dot {{ $c === '#1a5c2e' ? 'selected' : '' }}" style="background:{{ $c }}" onclick="selectColor(this, '{{ $c }}')"></div>
                @endforeach
                <input type="hidden" name="aktor_warna[]" value="#1a5c2e" class="color-val-input" />
              </div>
            </div>
            <button type="button" class="btn-remove" onclick="removeRepeater(this)">✕</button>
          </div>
        </div>
        <button type="button" class="btn-add-repeater" onclick="addAktor()">+ Tambah Aktor</button>
      </div>
    </div>

    <div class="form-section">
      <div class="form-section-head">
        <div class="form-section-icon" style="background:#eff6ff">📅</div>
        <div>
          <div class="form-section-title">Kronologi / Timeline</div>
          <div class="form-section-sub">Urutan kejadian penting</div>
        </div>
      </div>
      <div class="form-section-body">
        <div id="timeline-list">
          <div class="repeater-row" style="grid-template-columns:160px 1fr 150px">
            <div class="form-group"><label class="form-label">Tanggal</label><input class="form-input" type="text" name="tl_tanggal[]" placeholder="cth: Jan 2021" /></div>
            <div class="form-group"><label class="form-label">Keterangan</label><input class="form-input" type="text" name="tl_keterangan[]" /></div>
            <div class="form-group">
              <label class="form-label">Warna</label>
              <div class="color-picker-row">
                @foreach(['#16a34a','#d97706','#dc2626','#7c3aed','#0891b2','#1a5c2e'] as $c)
                <div class="color-dot {{ $c === '#16a34a' ? 'selected' : '' }}" style="background:{{ $c }}" onclick="selectColor(this, '{{ $c }}')"></div>
                @endforeach
                <input type="hidden" name="tl_warna[]" value="#16a34a" class="color-val-input" />
              </div>
            </div>
            <button type="button" class="btn-remove" onclick="removeRepeater(this)">✕</button>
          </div>
        </div>
        <button type="button" class="btn-add-repeater" onclick="addTimeline()">+ Tambah Kejadian</button>
      </div>
    </div>

    <div class="form-section">
      <div class="form-section-head">
        <div class="form-section-icon" style="background:#fef2f2">⚠️</div>
        <div>
          <div class="form-section-title">Penilaian Risiko</div>
          <div class="form-section-sub">Persentase risiko spesifik</div>
        </div>
      </div>
      <div class="form-section-body">
        <div id="risk-list">
          <div class="repeater-row" style="grid-template-columns:1fr 100px 130px 1fr">
            <div class="form-group"><label class="form-label">Label</label><input class="form-input" type="text" name="risk_label[]" placeholder="cth: Risiko Vonis Bebas" /></div>
            <div class="form-group"><label class="form-label">Nilai (%)</label><input class="form-input" type="number" name="risk_nilai[]" min="0" max="100" /></div>
            <div class="form-group">
              <label class="form-label">Warna</label>
              <div class="color-picker-row">
                @foreach(['#dc2626','#d97706','#16a34a','#2563eb','#7c3aed'] as $c)
                <div class="color-dot {{ $c === '#dc2626' ? 'selected' : '' }}" style="background:{{ $c }}" onclick="selectColor(this, '{{ $c }}')"></div>
                @endforeach
                <input type="hidden" name="risk_warna[]" value="#dc2626" class="color-val-input" />
              </div>
            </div>
            <div class="form-group"><label class="form-label">Keterangan</label><input class="form-input" type="text" name="risk_keterangan[]" placeholder="cth: Rp 211 M" /></div>
            <button type="button" class="btn-remove" onclick="removeRepeater(this)">✕</button>
          </div>
        </div>
        <button type="button" class="btn-add-repeater" onclick="addRisk()">+ Tambah Item Risiko</button>
      </div>
    </div>

    <div class="form-section">
      <div class="form-section-head">
        <div class="form-section-icon" style="background:#f0f7f2">✅</div>
        <div>
          <div class="form-section-title">Rekomendasi Tindak Lanjut</div>
          <div class="form-section-sub">Langkah yang disarankan</div>
        </div>
      </div>
      <div class="form-section-body">
        <div id="reko-list">
          <div class="repeater-row" style="grid-template-columns:1fr 1fr 130px">
            <div class="form-group"><label class="form-label">Judul</label><input class="form-input" type="text" name="reko_judul[]" /></div>
            <div class="form-group"><label class="form-label">Deskripsi</label><input class="form-input" type="text" name="reko_deskripsi[]" /></div>
            <div class="form-group">
              <label class="form-label">Prioritas</label>
              <select class="form-select" name="reko_prioritas[]">
                <option value="tinggi">Tinggi</option>
                <option value="sedang" selected>Sedang</option>
                <option value="rendah">Rendah</option>
              </select>
            </div>
            <button type="button" class="btn-remove" onclick="removeRepeater(this)">✕</button>
          </div>
        </div>
        <button type="button" class="btn-add-repeater" onclick="addReko()">+ Tambah Rekomendasi</button>
      </div>
    </div>

    <div class="form-section">
      <div class="form-section-head">
        <div class="form-section-icon" style="background:#fefce8">📊</div>
        <div>
          <div class="form-section-title">Kepercayaan Analisis</div>
          <div class="form-section-sub">Skor validasi kualitas data (0–100)</div>
        </div>
      </div>
      <div class="form-section-body">
        <div class="form-row col-2">
          @foreach([['conf_kelengkapan','Kelengkapan Data','klg-val'],['conf_konsistensi','Konsistensi Sumber','kss-val'],['conf_kualitas','Kualitas Dokumen','kld-val'],['conf_kedalaman','Kedalaman Analisis','kdl-val']] as [$n,$l,$id])
          <div class="form-group">
            <label class="form-label">{{ $l }}</label>
            <div class="range-row">
              <input type="range" name="{{ $n }}" min="0" max="100" step="1" value="50"
                oninput="document.getElementById('{{ $id }}').textContent=this.value+'%'" />
              <span class="range-val" id="{{ $id }}">50%</span>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="bottom-bar">
      <div class="submit-info">Data akan disimpan ke folder <strong>{{ $folder->nama }}</strong></div>
      <div style="display:flex;gap:8px">
        <a href="{{ route('datapool.show', $folder) }}" class="tb-btn">Batal</a>
        <button type="submit" class="tb-btn primary">💾 Simpan & Lihat Analisis</button>
      </div>
    </div>

    </form>
  </div>
</div>

<script>
function addSwotItem(t) {
  const list = document.getElementById('swot-' + t + '-list');
  const row = document.createElement('div');
  row.className = 'swot-item-row';
  row.innerHTML = `<input type="text" name="swot_${t}[]" placeholder="Tulis poin..." /><button type="button" class="btn-remove" onclick="removeRow(this)">✕</button>`;
  list.appendChild(row);
}
function removeRow(b) { b.closest('.swot-item-row').remove(); }
function removeRepeater(b) { b.closest('.repeater-row').remove(); }

function selectColor(el, val) {
  el.closest('.color-picker-row').querySelectorAll('.color-dot').forEach(d => d.classList.remove('selected'));
  el.classList.add('selected');
  el.closest('.color-picker-row').querySelector('.color-val-input').value = val;
}

function buildColorPicker(name, colors, def) {
  return colors.map(c => `<div class="color-dot ${c===def?'selected':''}" style="background:${c}" onclick="selectColor(this,'${c}')"></div>`).join('') +
    `<input type="hidden" name="${name}[]" value="${def}" class="color-val-input" />`;
}

function addAktor() {
  const list = document.getElementById('aktor-list');
  const row = document.createElement('div');
  row.className = 'repeater-row';
  row.style = 'grid-template-columns:1fr 80px 1fr 120px 1fr';
  row.innerHTML = `
    <div class="form-group"><label class="form-label">Nama</label><input class="form-input" type="text" name="aktor_nama[]" /></div>
    <div class="form-group"><label class="form-label">Inisial</label><input class="form-input" type="text" name="aktor_inisial[]" maxlength="3" /></div>
    <div class="form-group"><label class="form-label">Peran</label><input class="form-input" type="text" name="aktor_peran[]" /></div>
    <div class="form-group"><label class="form-label">Status</label><select class="form-select" name="aktor_status[]"><option value="tersangka">Tersangka</option><option value="saksi">Saksi</option><option value="dpo">DPO</option></select></div>
    <div class="form-group"><label class="form-label">Warna</label><div class="color-picker-row">${buildColorPicker('aktor_warna',['#1a5c2e','#1e6fa3','#86198f','#d97706','#374151','#dc2626','#0891b2','#7c3aed'],'#1a5c2e')}</div></div>
    <button type="button" class="btn-remove" onclick="removeRepeater(this)">✕</button>`;
  list.appendChild(row);
}

function addTimeline() {
  const list = document.getElementById('timeline-list');
  const row = document.createElement('div');
  row.className = 'repeater-row';
  row.style = 'grid-template-columns:160px 1fr 150px';
  row.innerHTML = `
    <div class="form-group"><label class="form-label">Tanggal</label><input class="form-input" type="text" name="tl_tanggal[]" /></div>
    <div class="form-group"><label class="form-label">Keterangan</label><input class="form-input" type="text" name="tl_keterangan[]" /></div>
    <div class="form-group"><label class="form-label">Warna</label><div class="color-picker-row">${buildColorPicker('tl_warna',['#16a34a','#d97706','#dc2626','#7c3aed','#0891b2','#1a5c2e'],'#16a34a')}</div></div>
    <button type="button" class="btn-remove" onclick="removeRepeater(this)">✕</button>`;
  list.appendChild(row);
}

function addRisk() {
  const list = document.getElementById('risk-list');
  const row = document.createElement('div');
  row.className = 'repeater-row';
  row.style = 'grid-template-columns:1fr 100px 130px 1fr';
  row.innerHTML = `
    <div class="form-group"><label class="form-label">Label</label><input class="form-input" type="text" name="risk_label[]" /></div>
    <div class="form-group"><label class="form-label">Nilai (%)</label><input class="form-input" type="number" name="risk_nilai[]" min="0" max="100" /></div>
    <div class="form-group"><label class="form-label">Warna</label><div class="color-picker-row">${buildColorPicker('risk_warna',['#dc2626','#d97706','#16a34a','#2563eb','#7c3aed'],'#dc2626')}</div></div>
    <div class="form-group"><label class="form-label">Keterangan</label><input class="form-input" type="text" name="risk_keterangan[]" /></div>
    <button type="button" class="btn-remove" onclick="removeRepeater(this)">✕</button>`;
  list.appendChild(row);
}

function addReko() {
  const list = document.getElementById('reko-list');
  const row = document.createElement('div');
  row.className = 'repeater-row';
  row.style = 'grid-template-columns:1fr 1fr 130px';
  row.innerHTML = `
    <div class="form-group"><label class="form-label">Judul</label><input class="form-input" type="text" name="reko_judul[]" /></div>
    <div class="form-group"><label class="form-label">Deskripsi</label><input class="form-input" type="text" name="reko_deskripsi[]" /></div>
    <div class="form-group"><label class="form-label">Prioritas</label><select class="form-select" name="reko_prioritas[]"><option value="tinggi">Tinggi</option><option value="sedang" selected>Sedang</option><option value="rendah">Rendah</option></select></div>
    <button type="button" class="btn-remove" onclick="removeRepeater(this)">✕</button>`;
  list.appendChild(row);
}
</script>
</body>
</html>