<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('hak-akses', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include "../layout/head.php";?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed layout-navbar-fixed">
	<div class="wrapper">
		<div class="preloader flex-column justify-content-center align-items-center">
			<img class="animation__shake" src="<?= pathToFile('assets/img/logo/'.$dataSEO['logo'].'');?>" alt="<?= $rowSeo['nama_website'];?> Logo" height="60" width="60">
		</div>

		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<?php include "../layout/navbar.php";?>
		</nav>

		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<?php include "../layout/aside.php";?>
		</aside>

		<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0">Tambah Hak Akses</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../hak-akses">Hak Akses</a></li>
								<li class="breadcrumb-item active">Tambah Hak Akses</li>
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
									<h3 class="card-title margin-top-five">
										<a href="../hak-akses" alt="Kembali" class="btn btn-sm btn-danger">
											<i class="fas fa-arrow-left fa-sm"></i>&nbsp; Kembali
										</a>
									</h3>
								</div>
								<div class="card-body">
									<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
										<div class="row">
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="nama" class="input-required">Nama Hak Akses</label>
													<input type="text" name="nama" id="nama" class="form-control" autocomplete="off" placeholder="Masukkan Nama Hak Akses" required="" minlength="1" maxlength="100" autofocus="">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="status_akses" class="input-required">Status Akses <i class="fas fa-question-circle" title="Mempunyai akses ketika aplikasi sedang maintenance"></i></label>
													<select class="form-control select2 width-100" name="status_akses" id="status_akses" required="">
														<option value="">Pilih Status Akses</option>
														<option value="Dapat Akses">Dapat Akses</option>
														<option value="Tidak Dapat Akses">Tidak Dapat Akses</option>
													</select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-3 col-md-12">
												<div class="card card-primary">
													<div class="card-header">
														<h4 class="card-title">Master</h4>
														<div class="card-tools">
															<button type="button" class="btn btn-tool" data-card-widget="collapse">
																<i class="fas fa-minus"></i>
															</button>
														</div>
													</div>
													<div class="card-body">
														<ul class="list-group list-group-flush">
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="bank" value="bank">
																	<label for="bank">Bank</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="pelanggan" value="pelanggan">
																	<label for="pelanggan">Pelanggan</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="pengguna" value="pengguna">
																	<label for="pengguna">Pengguna</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="petugas" value="petugas">
																	<label for="petugas">Petugas</label>
																</div>
															</li>
														</ul>
													</div>
												</div>
												<div class="card card-primary">
													<div class="card-header">
														<h4 class="card-title">Umum</h4>
														<div class="card-tools">
															<button type="button" class="btn btn-tool" data-card-widget="collapse">
																<i class="fas fa-minus"></i>
															</button>
														</div>
													</div>
													<div class="card-body">
														<ul class="list-group list-group-flush">
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="aktivitas-log" value="aktivitas-log">
																	<label for="aktivitas-log">Aktivitas Log</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="akun" value="akun">
																	<label for="akun">Akun</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="beranda" value="beranda">
																	<label for="beranda">Beranda</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="hak-akses" value="hak-akses">
																	<label for="hak-akses">Hak Akses</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="jurnal-umum" value="jurnal-umum">
																	<label for="jurnal-umum">Jurnal Umum</label>
																</div>
															</li>
														</ul>
													</div>
												</div>
											</div>
											<div class="col-lg-3 col-md-12">
												<div class="card card-primary">
													<div class="card-header">
														<h4 class="card-title">Transaksi</h4>
														<div class="card-tools">
															<button type="button" class="btn btn-tool" data-card-widget="collapse">
																<i class="fas fa-minus"></i>
															</button>
														</div>
													</div>
													<div class="card-body">
														<ul class="list-group list-group-flush">
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="biaya-penarikan" value="biaya-penarikan">
																	<label for="biaya-penarikan">Biaya Penarikan</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="penarikan-sampah" value="penarikan-sampah">
																	<label for="penarikan-sampah">Penarikan Sampah</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="pengambilan-saldo" value="pengambilan-saldo">
																	<label for="pengambilan-saldo">Pengambilan Saldo</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="pengisian-saldo" value="pengisian-saldo">
																	<label for="pengisian-saldo">Pengisian Saldo</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="perhitungan-tsp" value="perhitungan-tsp">
																	<label for="perhitungan-tsp">Perhitungan TSP</label>
																</div>
															</li>
														</ul>
													</div>
												</div>
											</div>
											<div class="col-lg-3 col-md-12">
												<div class="card card-primary">
													<div class="card-header">
														<h4 class="card-title">Pengaturan</h4>
														<div class="card-tools">
															<button type="button" class="btn btn-tool" data-card-widget="collapse">
																<i class="fas fa-minus"></i>
															</button>
														</div>
													</div>
													<div class="card-body">
														<ul class="list-group list-group-flush">
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="konfigurasi-mailer" value="konfigurasi-mailer">
																	<label for="konfigurasi-mailer">Konfigurasi Mailer</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="konfigurasi-seo" value="konfigurasi-seo">
																	<label for="konfigurasi-seo">Konfigurasi SEO</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="konfigurasi-umum" value="konfigurasi-umum">
																	<label for="konfigurasi-umum">Konfigurasi Umum</label>
																</div>
															</li>
														</ul>
													</div>
												</div>
											</div>
											<div class="col-lg-3 col-md-12">
												<div class="card card-primary">
													<div class="card-header">
														<h4 class="card-title">Laporan</h4>
														<div class="card-tools">
															<button type="button" class="btn btn-tool" data-card-widget="collapse">
																<i class="fas fa-minus"></i>
															</button>
														</div>
													</div>
													<div class="card-body">
														<ul class="list-group list-group-flush">
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="laporan-biaya-penarikan" value="laporan-biaya-penarikan">
																	<label for="laporan-biaya-penarikan">Biaya Penarikan</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="laporan-pelanggan" value="laporan-pelanggan">
																	<label for="laporan-pelanggan">Pelanggan</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="laporan-penarikan-sampah" value="laporan-penarikan-sampah">
																	<label for="laporan-penarikan-sampah">Penarikan Sampah</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="laporan-pengambilan-saldo" value="laporan-pengambilan-saldo">
																	<label for="laporan-pengambilan-saldo">Pengambilan Saldo</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="laporan-pengisian-saldo" value="laporan-pengisian-saldo">
																	<label for="laporan-pengisian-saldo">Pengisian Saldo</label>
																</div>
															</li>
															<li class="list-group-item pr-0 pl-0">
																<div class="icheck-primary d-inline">
																	<input type="checkbox" name="akses[]" id="laporan-petugas" value="laporan-petugas">
																	<label for="laporan-petugas">Petugas</label>
																</div>
															</li>
														</ul>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12 col-md-12" align="right">
												<div class="form-group">
													<button type="submit" class="btn btn-sm btn-primary btn-save">
														<i class="fas fa-save"></i>&nbsp; Simpan
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
			<?php include "../layout/footer.php";?>
		</footer>

		<aside class="control-sidebar control-sidebar-dark"></aside>
	</div>
	<?php include "../layout/script.php";?>

	<!-- JS Manual -->
	<script type="text/javascript">
		$("#data-form").submit(function(e) {
			e.preventDefault();

			var data = new FormData(this);
			data.append('function', 'addData');

			$('button[type=submit]', this).attr('disabled', 'disabled');
			let save_button = $(this).find('.btn-save'),
			that = this,
			card = $('.loading-card');

			let card_progress = $.cardProgress(card, {
				spinner: false
			});
			save_button.addClass('btn-progress');

			setTimeout(function() {
				card_progress.dismiss(function() {
					$('html, body').animate({
						scrollTop: 0
					});

					$.ajax({
						type: "POST",
						url: "../../routes/web/hak-akses.php",
						data: data,
						processData: false,
						contentType: false,
						success: function(response) {
							var result = JSON.parse(response);
							if(result.status_code == 200) {
								toastr.success(result.message);
								setTimeout(() => {
									window.location.href = '../hak-akses';
								}, 1500);
							}else {
								toastr.error(result.message);
							}
							save_button.removeClass('btn-progress');
							$('button[type=submit]', that).removeAttr('disabled');
						}
					});
				});
			}, 1000);
			return false;
		});
	</script>
</body>
</html>