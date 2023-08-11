<?php include '../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('biaya-penarikan', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include "layout/head.php";?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed layout-navbar-fixed">
	<div class="wrapper">
		<div class="preloader flex-column justify-content-center align-items-center">
			<img class="animation__shake" src="<?= pathToFile('assets/img/logo/putiki.png?'.$dataSEO['logo'].'');?>" alt="<?= $rowSeo['nama_website'];?> Logo" height="150" width="150">
		</div>

		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<?php include "layout/navbar.php";?>
		</nav>

		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<?php include "layout/aside.php";?>
		</aside>

		<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0">Biaya Penarikan</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="beranda">Beranda</a></li>
								<li class="breadcrumb-item active">Biaya Penarikan</li>
							</ol>
						</div>
					</div>
				</div>
			</section>
			<section class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-lg-12 col-md-12">
							<div class="card card-primary card-outline loading-card">
								<div class="card-header">
									<h3 class="card-title margin-top-five">Daftar Biaya Penarikan</h3>
									<div class="card-tools">
										<div class="btn-group">
											<button type="button" class="btn btn-sm btn-default" data-toggle="collapse" data-target="#collapseFilter" aria-expanded="false" aria-controls="collapseFilter">
												<i class="fas fa-filter fa-sm"></i>&nbsp; Filter
											</button>
											<?php if($_SESSION['tipe'] == "Pengguna") { ?>
												<a href="add/biaya-penarikan" alt="Buat Baru" class="btn btn-sm btn-primary">
													<i class="fas fa-plus fa-sm"></i>&nbsp; Buat Baru
												</a>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-lg-12 col-md-12">
											<div class="collapse" id="collapseFilter">
												<div class="row mb-3">
													<div class="col-lg-12 col-md-12">
														<div class="row">
															<div class="col-lg-3 col-md-12">
																<div class="form-group row">
																	<div class="col-sm-12">
																		<select class="form-control select2" name="isDeleted" id="isDeleted" style="width: 100% !important;">
																			<option value="0">Tidak Terhapus</option>
																			<option value="1">Terhapus</option>
																		</select>
																	</div>
																</div>
															</div>
															<div class="col-lg-3 col-md-12">
																<div class="form-group row">
																	<div class="col-sm-12">
																		<select class="form-control select2" name="isStatusTransaksi" id="isStatusTransaksi" style="width: 100% !important;">
																			<option value="Semua">Semua Status Transaksi</option>
																			<option value="Proses">Proses</option>
																			<option value="Selesai">Selesai</option>
																		</select>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12 col-md-12">
											<div class="table-responsive">
												<table class="table table-striped table-hover" width="100%" id="datatables-serverside">
													<thead>
														<tr>
															<th style="text-align: center; width: 5%;">No</th>
															<th style="text-align: left; width: 12%;">Kode</th>
															<th style="text-align: left;">Pelanggan</th>
															<th style="text-align: left;">Petugas</th>
															<th style="text-align: right; width: 12%;">Biaya</th>
															<th style="text-align: center; width: 10%;">Status</th>
															<th style="text-align: left; width: 18%;">Tanggal Diubah</th>
															<th style="text-align: center; width: 10%;">Aksi</th>
														</tr>
													</thead>
													<tbody>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>

		<footer class="main-footer">
			<?php include "layout/footer.php";?>
		</footer>

		<aside class="control-sidebar control-sidebar-dark"></aside>
	</div>
	<?php include "layout/script.php";?>

	<!-- JS Manual -->
	<script type="text/javascript" src="<?= clearCacheFile('page/biaya-penarikan.js');?>"></script>
</body>
</html>