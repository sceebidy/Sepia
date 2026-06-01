<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" /><meta name="viewport" content="width=1280" />
<title>Profiling Subjek — SEPIA</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--green:#1a5c2e;--green-border:#8fd4b1;--text:#0f172a;--text-muted:#64748b;--border:#e2e8f0;--bg-2:#f8fafc;--bg-3:#f1f5f9;--nav-width:220px;--accent:#1a5c2e}
html,body{height:100%}
body{font-family:'Sora',sans-serif;background:var(--bg-3);color:var(--text);display:flex;overflow:hidden;font-size:13px}
.sidenav{width:var(--nav-width);background:var(--green);display:flex;flex-direction:column;flex-shrink:0}
.sidenav-brand{padding:22px 20px 18px;border-bottom:1px solid rgba(255,255,255,0.1)}
.brand-logo{font-size:18px;font-weight:700;letter-spacing:.14em;color:#fff}
.brand-sub{font-size:10px;color:rgba(255,255,255,0.5);margin-top:2px;letter-spacing:.05em;text-transform:uppercase}
.sidenav-section{padding:18px 12px 8px}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;margin-bottom:2px;text-decoration:none;transition:background .12s;border:1px solid transparent;color:rgba(255,255,255,0.72);font-size:13px;font-weight:500}
.nav-item:hover{background:rgba(255,255,255,0.1);color:#fff}
.nav-item.active{background:rgba(255,255,255,0.14);border-color:rgba(255,255,255,0.18);color:#fff}
.nav-item .nav-icon{width:30px;height:30px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;background:rgba(255,255,255,0.1)}
.nav-item.active .nav-icon{background:rgba(255,255,255,0.2)}
.nav-item-text{flex:1}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0}
.topbar{display:flex;align-items:center;padding:0 24px;height:56px;background:#fff;border-bottom:1px solid var(--border);flex-shrink:0;font-size:14.5px;font-weight:700}
.content{flex:1;overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:20px}
.content::-webkit-scrollbar{width:5px}.content::-webkit-scrollbar-thumb{background:var(--border);border-radius:99px}
.page-header h1{font-size:18px;font-weight:700}.page-header p{font-size:12px;color:var(--text-muted);margin-top:4px}
.gen-layout{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start}
.gen-card{background:#fff;border:1px solid var(--border);border-radius:12px;display:flex;flex-direction:column;overflow:hidden}
.gen-card-output{min-height:400px}
.gen-card-header{display:flex;align-items:center;gap:.5rem;padding:1rem 1.25rem;border-bottom:1px solid var(--border);background:var(--bg-2)}
.gen-card-title{font-size:.875rem;font-weight:600;flex:1}
.btn-copy{font-size:.75rem;color:var(--text-muted);background:transparent;border:1px solid var(--border);border-radius:6px;padding:.25rem .6rem;cursor:pointer}
.btn-copy:hover{color:var(--accent);border-color:var(--accent)}
.gen-card-body{padding:1.25rem;flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:1rem}
.form-group{display:flex;flex-direction:column;gap:.4rem}
.form-label{font-size:12px;font-weight:600;color:var(--text-muted)}
.form-input,.form-select,.form-textarea{width:100%;padding:.5rem .75rem;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:'Sora',sans-serif;color:var(--text);background:var(--bg-2);outline:none}
.form-textarea{resize:vertical;min-height:100px}
.form-input:focus,.form-textarea:focus{border-color:var(--green-border);background:#fff}
.gen-card-footer{padding:1rem 1.25rem;border-top:1px solid var(--border);display:flex;justify-content:flex-end}
.btn-generate{display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.4rem;background:var(--green);color:#fff;border:none;border-radius:8px;font-size:.875rem;font-weight:600;font-family:'Sora',sans-serif;cursor:pointer;transition:opacity .15s,transform .1s}
.btn-generate:hover{opacity:.88;transform:translateY(-1px)}
.output-placeholder{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:3rem 1.5rem;color:var(--text-muted);gap:.75rem;min-height:280px}
.output-placeholder-icon{font-size:2.5rem;opacity:.35}
.output-placeholder p{font-size:12px;line-height:1.6;max-width:260px}
.output-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1rem;padding:3rem;min-height:280px;color:var(--text-muted);font-size:12px}
.spinner{width:28px;height:28px;border:2px solid var(--border);border-top-color:var(--green);border-radius:50%;animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.output-result{display:flex;flex-direction:column;gap:1.25rem}
.output-section{border-left:3px solid var(--green);padding-left:1rem}
.output-section-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--green);margin-bottom:.5rem}
.output-section p{font-size:12.5px;line-height:1.7}
.output-list{margin:0;padding-left:1.25rem;display:flex;flex-direction:column;gap:.35rem}
.output-list li{font-size:12.5px;line-height:1.6}
</style>
</head>
<body>
<nav class="sidenav">
  <div class="sidenav-brand"><div class="brand-logo">SEPIA</div><div class="brand-sub">Sistem Analitik Intelijen</div></div>
  <div class="sidenav-section">@include('partials.sidenav')</div>
</nav>
<div class="main">
  <div class="topbar">👤 Profiling Subjek</div>
  <div class="content">
    <div class="page-header"><h1>Profiling Subjek</h1><p>Buat profil intelijen subjek secara terstruktur dan komprehensif</p></div>
    <div class="gen-layout">
      <div class="gen-card">
        <div class="gen-card-header"><span>✍️</span><span class="gen-card-title">Input Data Subjek</span></div>
        <div class="gen-card-body">
          <div class="form-group"><label class="form-label">Inisial / Kode Subjek</label><input type="text" class="form-input" placeholder="cth: ABS / Subjek A" /></div>
          <div class="form-group"><label class="form-label">Kategori Subjek</label>
            <select class="form-select"><option value="">— Pilih Kategori —</option><option>Tersangka</option><option>Saksi Kunci</option><option>Aktor Politik</option><option>Anggota Jaringan</option><option>Korban</option></select>
          </div>
          <div class="form-group"><label class="form-label">Latar Belakang</label><textarea class="form-textarea" style="min-height:80px" placeholder="Jabatan, profesi, afiliasi organisasi, latar pendidikan..."></textarea></div>
          <div class="form-group"><label class="form-label">Keterlibatan dalam Kasus</label><textarea class="form-textarea" placeholder="Uraikan peran, keterlibatan, aktivitas, dan hubungan dengan pihak lain..."></textarea></div>
          <div class="form-group"><label class="form-label">Status Hukum</label>
            <select class="form-select"><option>Belum Tersangka</option><option>Tersangka</option><option>Terdakwa</option><option>Terpidana</option><option>DPO</option></select>
          </div>
        </div>
        <div class="gen-card-footer"><button class="btn-generate" onclick="doGenerate('ps2-out','Profiling Subjek')">⚡ Generate</button></div>
      </div>
      <div class="gen-card gen-card-output">
        <div class="gen-card-header"><span>📋</span><span class="gen-card-title">Hasil Generate</span><button class="btn-copy" onclick="doCopy('ps2-out',this)">⧉ Salin</button></div>
        <div class="gen-card-body" id="ps2-out">
          <div class="output-placeholder"><div class="output-placeholder-icon">👤</div><p>Profil intelijen subjek akan muncul di sini setelah menekan <strong>Generate</strong>.</p></div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function doGenerate(id,label){const el=document.getElementById(id);el.innerHTML='<div class="output-loading"><div class="spinner"></div><span>Memproses...</span></div>';setTimeout(()=>{el.innerHTML=`<div class="output-result"><div class="output-section"><div class="output-section-title">🪪 Identitas & Latar Belakang</div><p>Placeholder hasil generate <strong>${label}</strong>. Koneksi ke AI akan diaktifkan pada tahap berikutnya.</p></div><div class="output-section"><div class="output-section-title">🔗 Jaringan & Afiliasi</div><p>Sistem akan memetakan jaringan hubungan subjek, afiliasi organisasi, dan koneksi dengan aktor-aktor terkait lainnya.</p></div><div class="output-section"><div class="output-section-title">⚡ Pola Perilaku & Ancaman</div><ul class="output-list"><li>Pola perilaku yang teridentifikasi dari data yang tersedia.</li><li>Potensi ancaman atau risiko dari aktivitas subjek.</li><li>Proyeksi tindakan subjek ke depan berdasarkan analisis.</li></ul></div><div class="output-section"><div class="output-section-title">📌 Rekomendasi Penanganan</div><p>Rekomendasi penanganan dan monitoring terhadap subjek berdasarkan tingkat ancaman dan status hukum yang berlaku.</p></div></div>`;},1200);}
function doCopy(id,btn){navigator.clipboard.writeText(document.getElementById(id).innerText).then(()=>{btn.textContent='✓ Tersalin';setTimeout(()=>btn.textContent='⧉ Salin',2000);});}
</script>
</body></html>