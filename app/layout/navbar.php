<ul class="navbar-nav">
	<li class="nav-item">
		<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
	</li>
	<li class="nav-item d-none d-sm-inline-block">
		<a href="<?= getBaseURL();?>app/hak-akses" class="nav-link<?= menuActive(array('hak-akses'), $rowAkses, 'active');?>">Hak Akses</a>
	</li>
</ul>
<ul class="navbar-nav ml-auto">
	<li class="nav-item">
		<a class="nav-link" data-widget="fullscreen" href="#" role="button">
			<i class="fas fa-expand-arrows-alt"></i>
		</a>
	</li>
	<?php if($_SESSION['tipe'] != "Pengguna") { ?>
		<li class="nav-item dropdown">
			<a class="nav-link" data-toggle="dropdown" href="#">
				<i class="fas fa-info-circle"></i>
			</a>
			<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
				<span class="dropdown-item dropdown-header">Informasi <?= $_SESSION['tipe'];?></span>
				<div class="dropdown-divider"></div>
				<a href="#" class="dropdown-item">
					<i class="fas fa-clock mr-2"></i> Log Masuk
					<span class="float-right text-muted text-sm">
						<?= formatDateIndonesia(date('d M Y, H:i', strtotime($online['log'])));?>
					</span>
				</a>
				<div class="dropdown-divider"></div>
				<a href="#" class="dropdown-item">
					<i class="fas fa-wallet mr-2"></i> Saldo
					<span class="float-right text-muted text-sm">
						<?= "Rp. " . number_format($online['saldo']);?>
					</span>
				</a>
				<div class="dropdown-divider"></div>
				<a href="javascript:void(0)" class="dropdown-item">
					<i class="fas fa-user-shield mr-2"></i> Level
					<span class="float-right text-muted text-sm">
						<?= $_SESSION['tipe'];?>
					</span>
				</a>
				<div class="dropdown-divider"></div>
			</div>
		</li>
	<?php } ?>
</ul>