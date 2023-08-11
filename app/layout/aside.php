<a href="<?= getBaseURL() . "app/beranda";?>" class="brand-link">
	<img src="<?= pathToFile('assets/img/logo/putiki.png?'.$dataSEO['logo'].'');?>" alt="<?= $rowSeo['nama_website'];?> Logo" class="brand-image img-circle elevation-8" style="opacity: .9">
	<span class="brand-text font-weight-light-bold">PUTIKI SAMPAH<?= $rowSeo['#'];?></span>
</a>
<div class="sidebar">
	<div class="user-panel mt-3 pb-3 mb-3 d-flex<?= menuActive(array('akun'), $rowAkses, 'active');?>">
		<div class="image">
			<img src="<?= pathToFile('assets/img/avatar/'.$online['avatar'].'');?>" class="img-circle elevation-2" alt="Avatar">
		</div>
		<div class="info">
			<a href="<?= getBaseURL() . "app/akun";?>" class="d-block"><?= strtok($online['nama'], " ");?></a>
		</div>
	</div>
	<nav class="mt-2">
		<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
			<li class="nav-header<?= menuActive(array('beranda', 'bank', 'pelanggan', 'pengguna', 'petugas', 'biaya-penarikan', 'penarikan-sampah', 'pengambilan-saldo', 'pengisian-saldo'), $rowAkses, 'active');?>">Umum</li>
			<li class="nav-item<?= menuActive(array('beranda'), $rowAkses, 'menu-open');?>">
				<a href="<?= getBaseURL();?>app/beranda" class="nav-link<?= menuActive(array('beranda'), $rowAkses, 'active');?>">
					<i class="nav-icon fas fa-home"></i>
					<p>
						Beranda
					</p>
				</a>
			</li>
			<li class="nav-item<?= menuActive(array('bank', 'pelanggan', 'pengguna', 'petugas'), $rowAkses, 'menu-open');?>">
				<a href="#" class="nav-link<?= menuActive(array('bank', 'pelanggan', 'pengguna', 'petugas'), $rowAkses, 'active');?>">
					<i class="nav-icon fas fa-columns"></i>
					<p>
						Master
						<i class="fas fa-angle-left right"></i>
					</p>
				</a>
				<ul class="nav nav-treeview">
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/bank" class="nav-link<?= menuActive(array('bank'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Bank</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/pelanggan" class="nav-link<?= menuActive(array('pelanggan'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Pelanggan</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/pengguna" class="nav-link<?= menuActive(array('pengguna'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Pengguna</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/petugas" class="nav-link<?= menuActive(array('petugas'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Petugas</p>
						</a>
					</li>
				</ul>
			</li>
			<li class="nav-item<?= menuActive(array('biaya-penarikan', 'penarikan-sampah', 'pengambilan-saldo', 'pengisian-saldo', 'perhitungan-tsp'), $rowAkses, 'menu-open');?>">
				<a href="#" class="nav-link<?= menuActive(array('biaya-penarikan', 'penarikan-sampah', 'pengambilan-saldo', 'pengisian-saldo', 'perhitungan-tsp'), $rowAkses, 'active');?>">
					<i class="nav-icon fas fa-chart-pie"></i>
					<p>
						Transaksi
						<i class="fas fa-angle-left right"></i>
					</p>
				</a>
				<ul class="nav nav-treeview">
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/biaya-penarikan" class="nav-link<?= menuActive(array('biaya-penarikan'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Biaya Penarikan</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/penarikan-sampah" class="nav-link<?= menuActive(array('penarikan-sampah'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Penarikan Sampah</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/pengambilan-saldo" class="nav-link<?= menuActive(array('pengambilan-saldo'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Pengambilan Saldo</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/pengisian-saldo" class="nav-link<?= menuActive(array('pengisian-saldo'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Pengisian Saldo</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/perhitungan-tsp" class="nav-link<?= menuActive(array('perhitungan-tsp'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Perhitungan TSP</p>
						</a>
					</li>
				</ul>
			</li>
			<li class="nav-header<?= menuActive(array('laporan-biaya-penarikan', 'laporan-pelanggan', 'laporan-penarikan-sampah', 'laporan-pengambilan-saldo', 'laporan-pengisian-saldo', 'laporan-petugas', 'jurnal-umum'), $rowAkses, 'active');?>">Data Laporan</li>
			<li class="nav-item<?= menuActive(array('laporan-biaya-penarikan', 'laporan-pelanggan', 'laporan-penarikan-sampah', 'laporan-pengambilan-saldo', 'laporan-pengisian-saldo', 'laporan-petugas'), $rowAkses, 'menu-open');?>">
				<a href="#" class="nav-link<?= menuActive(array('laporan-biaya-penarikan', 'laporan-pelanggan', 'laporan-penarikan-sampah', 'laporan-pengambilan-saldo', 'laporan-pengisian-saldo', 'laporan-petugas'), $rowAkses, 'active');?>">
					<i class="nav-icon fas fa-print"></i>
					<p>
						Laporan
						<i class="fas fa-angle-left right"></i>
					</p>
				</a>
				<ul class="nav nav-treeview">
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/laporan-biaya-penarikan" class="nav-link<?= menuActive(array('laporan-biaya-penarikan'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Biaya Penarikan</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/laporan-pelanggan" class="nav-link<?= menuActive(array('laporan-pelanggan'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Pelanggan</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/laporan-penarikan-sampah" class="nav-link<?= menuActive(array('laporan-penarikan-sampah'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Penarikan Sampah</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/laporan-pengambilan-saldo" class="nav-link<?= menuActive(array('laporan-pengambilan-saldo'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Pengambilan Saldo</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/laporan-pengisian-saldo" class="nav-link<?= menuActive(array('laporan-pengisian-saldo'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Pengisian Saldo</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/laporan-petugas" class="nav-link<?= menuActive(array('laporan-petugas'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Petugas</p>
						</a>
					</li>
				</ul>
			</li>
			<li class="nav-item<?= menuActive(array('jurnal-umum'), $rowAkses, 'menu-open');?>">
				<a href="<?= getBaseURL();?>app/jurnal-umum" class="nav-link<?= menuActive(array('jurnal-umum'), $rowAkses, 'active');?>">
					<i class="nav-icon fas fa-receipt"></i>
					<p>
						Jurnal Umum
					</p>
				</a>
			</li>
			<li class="nav-header<?= menuActive(array('konfigurasi-mailer', 'konfigurasi-seo', 'konfigurasi-umum'), $rowAkses, 'active');?>">Konfigurasi Sistem</li>
			<li class="nav-item<?= menuActive(array('konfigurasi-mailer', 'konfigurasi-seo', 'konfigurasi-umum'), $rowAkses, 'menu-open');?>">
				<a href="#" class="nav-link<?= menuActive(array('konfigurasi-mailer', 'konfigurasi-seo', 'konfigurasi-umum'), $rowAkses, 'active');?>">
					<i class="nav-icon fas fa-cogs"></i>
					<p>
						Pengaturan
						<i class="right fas fa-angle-left"></i>
					</p>
				</a>
				<ul class="nav nav-treeview">
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/konfigurasi-mailer" class="nav-link<?= menuActive(array('konfigurasi-mailer'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Mailer</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/konfigurasi-seo" class="nav-link<?= menuActive(array('konfigurasi-seo'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>SEO</p>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?= getBaseURL();?>app/konfigurasi-umum" class="nav-link<?= menuActive(array('konfigurasi-umum'), $rowAkses, 'active');?>">
							<i class="far fa-circle nav-icon"></i>
							<p>Umum</p>
						</a>
					</li>
				</ul>
			</li>
			<li class="nav-header<?= menuActive(array('aktivitas-log'), $rowAkses, 'active');?>">Tambahan</li>
			<li class="nav-item<?= menuActive(array('aktivitas-log'), $rowAkses, 'menu-open');?>">
				<a href="<?= getBaseURL();?>app/aktivitas-log" class="nav-link<?= menuActive(array('aktivitas-log'), $rowAkses, 'active');?>">
					<i class="nav-icon fas fa-calendar-alt"></i>
					<p>
						Aktivitas Log
					</p>
				</a>
			</li>
			<li class="nav-item">
				<a href="#" id="user-logout" class="nav-link">
					<i class="nav-icon fas fa-sign-out-alt"></i>
					<p>
						Keluar
					</p>
				</a>
			</li>
		</ul>
	</nav>
</div>