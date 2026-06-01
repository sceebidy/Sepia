<a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
  <div class="nav-icon">📊</div><div class="nav-item-text">Dashboard</div>
</a>
<a class="nav-item {{ request()->routeIs('datapool.*') ? 'active' : '' }}" href="{{ route('datapool.index') }}">
  <div class="nav-icon">📋</div><div class="nav-item-text">RPI</div>
</a>
<a class="nav-item {{ request()->routeIs('penjabaran-strategis') ? 'active' : '' }}" href="{{ route('penjabaran-strategis') }}">
  <div class="nav-icon">🎯</div><div class="nav-item-text">Penjabaran Strategis</div>
</a>
<a class="nav-item {{ request()->routeIs('laporan-informasi') ? 'active' : '' }}" href="{{ route('laporan-informasi') }}">
  <div class="nav-icon">📄</div><div class="nav-item-text">Laporan Informasi</div>
</a>
<a class="nav-item {{ request()->routeIs('laporan-intelijen') ? 'active' : '' }}" href="{{ route('laporan-intelijen') }}">
  <div class="nav-icon">🔍</div><div class="nav-item-text">Laporan Intelijen</div>
</a>
<a class="nav-item {{ request()->routeIs('infografis-intelijen') ? 'active' : '' }}" href="{{ route('infografis-intelijen') }}">
  <div class="nav-icon">📈</div><div class="nav-item-text">Infografis Intelijen</div>
</a>
<a class="nav-item {{ request()->routeIs('profiling-subjek') ? 'active' : '' }}" href="{{ route('profiling-subjek') }}">
  <div class="nav-icon">👤</div><div class="nav-item-text">Profiling Subjek</div>
</a>
<a class="nav-item {{ request()->routeIs('presentasi-intelijen') ? 'active' : '' }}" href="{{ route('presentasi-intelijen') }}">
  <div class="nav-icon">🎞️</div><div class="nav-item-text">Presentasi Intelijen</div>
</a>