@php
    $role = auth('web')->user()->karyawan->id_jabatan;
    $isAdmin = in_array($role, ['001']);
    $isGudang = in_array($role, ['002']);
@endphp

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="/beranda-admin">
    <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-laugh-wink"></i>
    </div>
    <div class="sidebar-brand-text mx-3">Admin SIKS</div>
</a>

@if ($isAdmin)
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('beranda*') ? 'active' : '' }}">
        <a class="nav-link" href="/beranda-admin">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Beranda Admin</span>
        </a>
    </li>
@endif

<!-- Nav Item - Akun Collapse Menu -->
@php
    $isMasterData = request()->is([
        'master/bahan-baku*',
        'master/pelanggan*',
        'master/toko*',
        'master/supplier*',
        'master/produk*',
        'master/akun*',
        'master/karyawan*',
        'master/jabatan*',
        'master/pengaturan-sistem*',
    ]);
@endphp

<li class="nav-item {{ $isMasterData ? 'active' : '' }}">
    <a class="nav-link {{ $isMasterData ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
        data-target="#collapseMasterData" aria-expanded="{{ $isMasterData ? 'true' : 'false' }}"
        aria-controls="collapseMasterData">
        <i class="fas fa-fw fa-user-cog"></i>
        <span>Master Data</span>
    </a>

    <div id="collapseMasterData" class="collapse {{ $isMasterData ? 'show' : '' }}" aria-labelledby="headingMasterData"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Master Data</h6>
            @if ($isAdmin)
                <a class="collapse-item {{ request()->is('master/jabatan*') ? 'active' : '' }}"
                    href="{{ route('master.daftarJabatan') }}">
                    <i class="fas fa-fw fa-briefcase mr-2 text-gray-600"></i>Jabatan
                </a>
                <a class="collapse-item {{ request()->is('master/karyawan*') ? 'active' : '' }}"
                    href="{{ route('master.karyawan') }}">
                    <i class="fas fa-fw fa-user-friends mr-2 text-gray-600"></i>Karyawan
                </a>
                <a class="collapse-item {{ request()->is('master/akun*') ? 'active' : '' }}"
                    href="{{ route('master.daftarAkun') }}">
                    <i class="fas fa-fw fa-user-shield mr-2 text-gray-600"></i>Akun
                </a>
            @endif
            @if ($isAdmin || $isGudang)
                <a class="collapse-item {{ request()->is('master/bahan-baku*') ? 'active' : '' }}"
                    href="{{ route('master.daftarbahan-baku') }}">
                    <i class="fas fa-fw fa-briefcase mr-2 text-gray-600"></i>Bahan Baku
                </a>
                <a class="collapse-item {{ request()->is('master/produk*') ? 'active' : '' }}"
                    href="{{ route('master.daftar-produk') }}">
                    <i class="fas fa-fw fa-briefcase mr-2 text-gray-600"></i>Produk
                </a>
            @endif
            @if ($isAdmin)
                <a class="collapse-item {{ request()->is('master/pelanggan*') ? 'active' : '' }}"
                    href="{{ route('master.daftarPelanggan') }}">
                    <i class="fas fa-fw fa-briefcase mr-2 text-gray-600"></i>Pelanggan
                </a>
                <a class="collapse-item {{ request()->is('master/toko*') ? 'active' : '' }}"
                    href="{{ route('master.daftarToko') }}">
                    <i class="fas fa-fw fa-briefcase mr-2 text-gray-600"></i>Toko
                </a>
            @endif
            @if ($isAdmin || $isGudang)
                <a class="collapse-item {{ request()->is('master/supplier*') ? 'active' : '' }}"
                    href="{{ route('master.daftarSupplier') }}">
                    <i class="fas fa-fw fa-briefcase mr-2 text-gray-600"></i>Supplier
                </a>
            @endif
            @if ($isAdmin)
                <a class="collapse-item {{ request()->is('master/pengaturan-sistem*') ? 'active' : '' }}"
                    href="{{ route('master.daftarPengaturanSistem') }}">
                    <i class="fas fa-fw fa-briefcase mr-2 text-gray-600"></i>Pengaturan Sistem
                </a>
            @endif
        </div>
    </div>
</li>



@php
    $MenuGudang = request()->is([
        'gudang/bahan-baku-masuk*',
        'gudang/bahan-baku-keluar*',
        'gudang/produk-masuk*',
        'gudang/laporan-harian*',
        'gudang/laporan-produk*',
    ]);
@endphp




<li class="nav-item {{ $MenuGudang ? 'active' : '' }}">
    <a class="nav-link {{ $MenuGudang ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
        data-target="#collapseGudang" aria-expanded="{{ $MenuGudang ? 'true' : 'false' }}"
        aria-controls="collapseGudang">
        <i class="fas fa-fw fa-warehouse"></i>
        <span>Gudang</span>
    </a>
    <div id="collapseGudang" class="collapse {{ $MenuGudang ? 'show' : '' }}" aria-labelledby="headingGudang"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Menu Gudang:</h6>
            <a class="collapse-item {{ request()->is('gudang/bahan-baku-masuk*') ? 'active' : '' }}"
                href="{{ route('gudang.bahan-masuk.index') }}">
                <i class="fas fa-fw fa-sign-in-alt mr-2 text-gray-600"></i>Bahan Masuk
            </a>
            <a class="collapse-item {{ request()->is('gudang/bahan-baku-keluar*') ? 'active' : '' }}"
                href="{{ route('gudang.bahan-keluar.index') }}">
                <i class="fas fa-fw fa-sign-out-alt mr-2 text-gray-600"></i>Bahan Keluar
            </a>
            <a class="collapse-item {{ request()->is('gudang/produk-masuk*') ? 'active' : '' }}"
                href="{{ route('gudang.produk-masuk.index') }}">
                <i class="fas fa-fw fa-sign-out-alt mr-2 text-gray-600"></i>Hasil Produksi
            </a>
            <a class="collapse-item {{ request()->is('gudang/laporan-harian') ? 'active' : '' }}"
                href="{{ route('gudang.laporan-harian') }}">
                <i class="fas fa-fw fa-arrow-circle-down mr-2 text-gray-600"></i>Laporan Bahan Baku
            </a>
            <a class="collapse-item {{ request()->is('gudang/laporan-produk') ? 'active' : '' }}"
                href="{{ route('gudang.laporan-produk') }}">
                <i class="fas fa-fw fa-arrow-circle-down mr-2 text-gray-600"></i>Laporan Produk
            </a>
        </div>
    </div>
</li>

@if ($isAdmin)
    <!-- Nav Item - Penjualan Collapse Menu -->
    @php
        $isSales = request()->is(['penjualan/pesanan*', 'penjualan/pembayaran*', 'penjualan/history*']);
    @endphp

    <li class="nav-item {{ $isSales ? 'active' : '' }}">
        <a class="nav-link {{ $isSales ? '' : 'collapsed' }}" href="#" data-toggle="collapse"
            data-target="#collapseSales" aria-expanded="{{ $isSales ? 'true' : 'false' }}"
            aria-controls="collapseSales">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Penjualan</span>
        </a>
        <div id="collapseSales" class="collapse {{ $isSales ? 'show' : '' }}" aria-labelledby="headingSales"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Menu Penjualan:</h6>
                <a class="collapse-item {{ request()->is('penjualan/pesanan*') ? 'active' : '' }}"
                    href="{{ route('penjualan.pesanan.index') }}">
                    <i class="fas fa-fw fa-clipboard-list mr-2 text-gray-600"></i>Pesanan
                </a>
                <a class="collapse-item {{ request()->is('penjualan/pembayaran*') ? 'active' : '' }}"
                    href="{{ route('penjualan.pembayaran.index') }}">
                    <i class="fas fa-fw fa-money-check-alt mr-2 text-gray-600"></i>Pembayaran
                </a>
                <a class="collapse-item {{ request()->is('penjualan/history*') ? 'active' : '' }}"
                    href="{{ route('penjualan.history.index') }}">
                    <i class="fas fa-fw fa-money-check-alt mr-2 text-gray-600"></i>History
                </a>
            </div>
        </div>
    </li>
@endif


<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>
