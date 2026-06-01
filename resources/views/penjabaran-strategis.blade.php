<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=1280" />
<title>Penjabaran Strategis — SEPIA</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
  --green: #1a5c2e; --green-2: #2e7d4a; --green-light: #edfaf3;
  --green-border: #8fd4b1; --text: #0f172a; --text-muted: #64748b;
  --text-faint: #94a3b8; --border: #e2e8f0; --border-light: #f1f5f9;
  --bg: #ffffff; --bg-2: #f8fafc; --bg-3: #f1f5f9;
  --nav-width: 220px; --accent: #1a5c2e;
  --surface: #ffffff; --text-primary: #0f172a;
}
html, body { height: 100%; }
body { font-family: 'Sora', sans-serif; background: var(--bg-3); color: var(--text); display: flex; overflow: hidden; font-size: 13px; }
.sidenav { width: var(--nav-width); background: var(--green); display: flex; flex-direction: column; flex-shrink: 0; }
.sidenav-brand { padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.brand-logo { font-size: 18px; font-weight: 700; letter-spacing: 0.14em; color: #fff; }
.brand-sub { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 2px; letter-spacing: 0.05em; text-transform: uppercase; }
.sidenav-section { padding: 18px 12px 8px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; cursor: pointer; margin-bottom: 2px; text-decoration: none; transition: background 0.12s; border: 1px solid transparent; color: rgba(255,255,255,0.72); font-size: 13px; font-weight: 500; }
.nav-item:hover { background: rgba(255,255,255,0.1); color: #fff; }
.nav-item.active { background: rgba(255,255,255,0.14); border-color: rgba(255,255,255,0.18); color: #fff; }
.nav-item .nav-icon { width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; background: rgba(255,255,255,0.1); }
.nav-item.active .nav-icon { background: rgba(255,255,255,0.2); }
.nav-item-text { flex: 1; }
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }
.topbar { display: flex; align-items: center; padding: 0 24px; height: 56px; background: #fff; border-bottom: 1px solid var(--border); flex-shrink: 0; gap: 12px; }
.page-title { font-size: 14.5px; font-weight: 700; }
.content { flex: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 6px; }
.content::-webkit-scrollbar { width: 5px; }
.content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
.page-header { margin-bottom: 20px; }
.page-header h1 { font-size: 18px; font-weight: 700; color: var(--text); }
.page-header p { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
.gen-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start; }
.gen-card { background: #fff; border: 1px solid var(--border); border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; }
.gen-card-output { min-height: 400px; }
.gen-card-header { display: flex; align-items: center; gap: 0.5rem; padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); background: var(--bg-2); }
.gen-card-icon { font-size: 1rem; }
.gen-card-title { font-size: 0.875rem; font-weight: 600; color: var(--text); flex: 1; }
.btn-copy { font-size: 0.75rem; color: var(--text-muted); background: transparent; border: 1px solid var(--border); border-radius: 6px; padding: 0.25rem 0.6rem; cursor: pointer; transition: all 0.15s ease; }
.btn-copy:hover { color: var(--accent); border-color: var(--accent); }
.gen-card-body { padding: 1.25rem; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.form-label { font-size: 12px; font-weight: 600; color: var(--text-muted); }
.form-input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: 8px; font-size: 13px; font-family: 'Sora', sans-serif; color: var(--text); background: var(--bg-2); outline: none; transition: border 0.15s; }
.form-input:focus { border-color: var(--green-border); background: #fff; }
.form-select { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: 8px; font-size: 13px; font-family: 'Sora', sans-serif; color: var(--text); background: var(--bg-2); outline: none; }
.form-textarea { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: 8px; font-size: 13px; font-family: 'Sora', sans-serif; color: var(--text); background: var(--bg-2); outline: none; resize: vertical; min-height: 120px; transition: border 0.15s; }
.form-textarea:focus { border-color: var(--green-border); background: #fff; }
.gen-card-footer { padding: 1rem 1.25rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; }
.btn-generate { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.4rem; background: var(--green); color: #fff; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; font-family: 'Sora', sans-serif; cursor: pointer; transition: opacity 0.15s, transform 0.1s; }
.btn-generate:hover { opacity: 0.88; transform: translateY(-1px); }
.output-placeholder { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 3rem 1.5rem; color: var(--text-muted); gap: 0.75rem; min-height: 280px; }
.output-placeholder-icon { font-size: 2.5rem; opacity: 0.35; }
.output-placeholder p { font-size: 12px; line-height: 1.6; max-width: 260px; }
.output-loading { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 1rem; padding: 3rem; min-height: 280px; color: var(--text-muted); font-size: 12px; }
.spinner { width: 28px; height: 28px; border: 2px solid var(--border); border-top-color: var(--green); border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.output-result { display: flex; flex-direction: column; gap: 1.25rem; }
.output-section { border-left: 3px solid var(--green); padding-left: 1rem; }
.output-section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--green); margin-bottom: 0.5rem; }
.output-section p { font-size: 12.5px; line-height: 1.7; color: var(--text); }
.output-list { margin: 0; padding-left: 1.25rem; display: flex; flex-direction: column; gap: 0.35rem; }
.output-list li { font-size: 12.5px; line-height: 1.6; color: var(--text); }
</style>
</head>
<body>

<nav class="sidenav">
  <div class="sidenav-brand">
    <div class="brand-logo">SEPIA</div>
    <div class="brand-sub">Sistem Analitik Intelijen</div>
  </div>
  <div class="sidenav-section">
    @include('partials.sidenav')
  </div>
</nav>

<div class="main">
  <div class="topbar">
    <div class="page-title">🎯 Penjabaran Strategis</div>
  </div>
  <div class="content">
    <div class="page-header">
      <h1>Penjabaran Strategis</h1>
      <p>Generate penjabaran strategis berdasarkan konteks intelijen</p>
    </div>
    <div class="gen-layout">
      <div class="gen-card">
        <div class="gen-card-header">
          <span class="gen-card-icon">✍️</span>
          <span class="gen-card-title">Input Data</span>
        </div>
        <div class="gen-card-body">
          <div class="form-group">
            <label class="form-label">Judul / Topik Strategis</label>
            <input type="text" class="form-input" id="ps-judul" placeholder="cth: Stabilitas Sosial Politik Kota Medan Q2 2025" />
          </div>
          <div class="form-group">
            <label class="form-label">Konteks Situasi</label>
            <textarea class="form-textarea" id="ps-konteks" placeholder="Uraikan situasi, kondisi lapangan, atau data intelijen yang relevan..."></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Fokus Analisis</label>
            <select class="form-select" id="ps-fokus">
              <option value="">— Pilih Fokus —</option>
              <option>Politik</option>
              <option>Ekonomi</option>
              <option>Sosial</option>
              <option>Keamanan</option>
              <option>Hukum</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Periode</label>
            <input type="text" class="form-input" id="ps-periode" placeholder="cth: Januari – Juni 2025" />
          </div>
        </div>
        <div class="gen-card-footer">
          <button class="btn-generate" onclick="doGenerate('ps-output','Penjabaran Strategis')">⚡ Generate</button>
        </div>
      </div>
      <div class="gen-card gen-card-output">
        <div class="gen-card-header">
          <span class="gen-card-icon">📋</span>
          <span class="gen-card-title">Hasil Generate</span>
          <button class="btn-copy" onclick="doCopy('ps-output',this)">⧉ Salin</button>
        </div>
        <div class="gen-card-body" id="ps-output">
          <div class="output-placeholder">
            <div class="output-placeholder-icon">🎯</div>
            <p>Hasil penjabaran strategis akan muncul di sini setelah menekan <strong>Generate</strong>.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function doGenerate(id, label) {
  const el = document.getElementById(id);
  el.innerHTML = `<div class="output-loading"><div class="spinner"></div><span>Memproses...</span></div>`;
  setTimeout(() => {
    el.innerHTML = `
      <div class="output-result">
        <div class="output-section">
          <div class="output-section-title">📌 Ringkasan Eksekutif</div>
          <p>Ini adalah placeholder hasil generate <strong>${label}</strong>. Koneksi ke AI akan diaktifkan pada tahap berikutnya.</p>
        </div>
        <div class="output-section">
          <div class="output-section-title">📊 Penjabaran Utama</div>
          <p>Sistem akan menghasilkan analisis mendalam berdasarkan data yang diinputkan, mencakup proyeksi situasi dan pemetaan faktor strategis.</p>
        </div>
        <div class="output-section">
          <div class="output-section-title">✅ Rekomendasi</div>
          <ul class="output-list">
            <li>Rekomendasi strategis pertama berdasarkan analisis situasi.</li>
            <li>Rekomendasi strategis kedua untuk tindak lanjut operasional.</li>
            <li>Rekomendasi strategis ketiga sebagai langkah preventif.</li>
          </ul>
        </div>
      </div>`;
  }, 1200);
}
function doCopy(id, btn) {
  navigator.clipboard.writeText(document.getElementById(id).innerText).then(() => {
    btn.textContent = '✓ Tersalin';
    setTimeout(() => btn.textContent = '⧉ Salin', 2000);
  });
}
</script>
</body>
</html>