<?php include '../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('laporan-biaya-penarikan', $getAkses);
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
			<img class="animation__shake" src="<?= pathToFile('assets/img/logo/'.$dataSEO['logo'].'');?>" alt="<?= $rowSeo['nama_website'];?> Logo" height="60" width="60">
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
							<h1 class="m-0">Laporan Biaya Penarikan</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="beranda">Beranda</a></li>
								<li class="breadcrumb-item active">Laporan Biaya Penarikan</li>
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
									<h3 class="card-title">
										Laporan Biaya Penarikan
									</h3>
								</div>
								<div class="card-body">
									<form role="form" action="../routes/web/laporan-biaya-penarikan.php" method="POST" enctype="multipart/form-data" id="data-form">
                                        <input type="hidden" name="function" value="exportData">
										<div class="row">
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="kategori_filter">Kategori Filter</label>
													<select class="form-control select2 width-100" name="kategori_filter" id="kategori_filter">
														<option value="Pilihan Otomatis" selected="">Pilihan Otomatis</option>
														<option value="Rentang Tanggal">Rentang Tanggal</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4 col-md-12" id="is_otomatis" style="display: none;">
												<div class="form-group">
													<label for="pilihan">Pilihan</label>
													<select class="form-control select2 width-100" name="pilihan" id="pilihan">
														<option value="Hari Ini" selected="">Hari Ini</option>
														<option value="Kemarin">Kemarin</option>
														<option value="7 Hari Terakhir">7 Hari Terakhir</option>
														<option value="Bulan Ini">Bulan Ini</option>
														<option value="Bulan Kemarin">Bulan Kemarin</option>
														<option value="Tahun Ini">Tahun Ini</option>
														<option value="Tahun Kemarin">Tahun Kemarin</option>
														<option value="Semua">Semua</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4 col-md-12" id="is_rentang" style="display: none;">
												<div class="form-group">
													<label for="periode">Periode</label>
													<input type="text" name="periode" id="periode" class="form-control daterange-cus">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="jenis">Jenis</label>
													<select class="form-control select2 width-100" name="jenis" id="jenis">
														<option value="excel">Excel</option>
														<option value="pdf">PDF</option>
													</select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12 col-md-12" align="right">
												<div class="form-group">
													<button type="submit" class="btn btn-sm btn-primary btn-save">
														<i class="fas fa-print"></i>&nbsp; Cetak
													</button>
												</div>
											</div>
										</div>
									</form>
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
	<script type="text/javascript">
		$('.daterange-cus').daterangepicker({
			locale: {format: 'YYYY-MM-DD'},
			drops: 'down',
			opens: 'right'
		});

		$('#kategori_filter').on('change', function(e) {
			e.preventDefault();

			if($(this).val() == "Pilihan Otomatis") {
				$('#is_otomatis').css('display', 'block');
				$('#is_rentang').css('display', 'none');
			}else {
				$('#is_otomatis').css('display', 'none');
				$('#is_rentang').css('display', 'block');
			}
		});

		if($('#kategori_filter').val() == "Pilihan Otomatis") {
			$('#is_otomatis').css('display', 'block');
			$('#is_rentang').css('display', 'none');
		}else {
			$('#is_otomatis').css('display', 'none');
			$('#is_rentang').css('display', 'block');
		}
	</script>
</body>
</html>