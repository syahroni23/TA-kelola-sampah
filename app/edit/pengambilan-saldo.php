<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('pengambilan-saldo', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
$query = mysqli_query($conn, "SELECT t_pengambilan_saldo.*, m_pelanggan.kode AS kode_pelanggan, m_petugas.kode AS kode_petugas FROM t_pengambilan_saldo LEFT JOIN m_pelanggan ON t_pengambilan_saldo.m_pelanggan_id = m_pelanggan.id LEFT JOIN m_petugas ON t_pengambilan_saldo.m_petugas_id = m_petugas.id WHERE t_pengambilan_saldo.id = '$id' AND t_pengambilan_saldo.is_deleted = 0");
$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));

$isCanEdit = false;

if(isset($row['status']) && $row['status'] == "Pengajuan") {
	$isCanEdit = true;
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
							<h1 class="m-0">Ubah Pengambilan Saldo</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../../pengambilan-saldo">Pengambilan Saldo</a></li>
								<li class="breadcrumb-item active">Ubah Pengambilan Saldo</li>
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
										<a href="../../pengambilan-saldo" alt="Kembali" class="btn btn-sm btn-danger">
											<i class="fas fa-arrow-left fa-sm"></i>&nbsp; Kembali
										</a>
									</h3>
								</div>
								<?php if(issetEmpty($row)) { ?>
									<?php
									$row['kode_user'] = isset($row['kode_pelanggan']) && !empty($row['kode_pelanggan']) ? $row['kode_pelanggan'] : $row['kode_petugas'];
									?>
									<div class="card-body">
										<?php if($isCanEdit == true) { ?>
											<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
												<input type="hidden" name="id" value="<?= $row['id'];?>">

												<?php if($_SESSION['tipe'] == "Pengguna") { ?>
													<div class="row">
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="pelanggan_petugas" class="input-required">Pelanggan / Petugas</label>
																<select class="form-control select2" name="pelanggan_petugas" id="pelanggan_petugas" required="" disabled="">
																	<option value="">Pilih Pelanggan / Petugas</option>
																	<?php
																	$arrPelangganPetugas = [];
																	$queryPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = 0 ORDER BY nama ASC");
																	while($rowPelanggan = mysqli_fetch_array($queryPelanggan, MYSQLI_ASSOC)) {
																		$arrPelangganPetugas[] = [
																			'id'		=>	$rowPelanggan['id'],
																			'kode'		=>	$rowPelanggan['kode'],
																			'nama'		=>	$rowPelanggan['nama'],
																			'telepon'	=>	$rowPelanggan['telepon'],
																			'saldo'		=>	$rowPelanggan['saldo'],
																			'tipe'		=>	"Pelanggan"
																		];
																	}
																	$queryPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE is_deleted = 0 ORDER BY nama ASC");
																	while($rowPetugas = mysqli_fetch_array($queryPetugas, MYSQLI_ASSOC)) {
																		$arrPelangganPetugas[] = [
																			'id'		=>	$rowPetugas['id'],
																			'kode'		=>	$rowPetugas['kode'],
																			'nama'		=>	$rowPetugas['nama'],
																			'telepon'	=>	$rowPetugas['telepon'],
																			'saldo'		=>	$rowPetugas['saldo'],
																			'tipe'		=>	"Petugas"
																		];
																	}
																	foreach ($arrPelangganPetugas as $keys => $values) {
																		$values = (array) $values;
																		if($values['kode'] != $row['kode_user']) {
																			unset($arrPelangganPetugas[$keys]);
																		}
																	}
																	$arrPelangganPetugas = array_values($arrPelangganPetugas);
																	$sortByName = array_column($arrPelangganPetugas, 'nama');
																	array_multisort($sortByName, SORT_ASC, $arrPelangganPetugas);
																	foreach ($arrPelangganPetugas as $keys => $values) {
																		$values = allRowValidateHTML($values);
																		$selected = isSelected($values['kode'], $row['kode_user']);
																		echo "<option value='".$values['kode']."' ".$selected.">".$values['nama']."</option>";
																	}
																	?>
																</select>
															</div>
														</div>
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="tanggal" class="input-required">Tanggal</label>
																<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" autofocus="" value="<?= date('Y-m-d', strtotime($row['tanggal']));?>">
															</div>
														</div>
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="nominal" class="input-required">Nominal</label>
																<input type="text" name="nominal" id="nominal" class="form-control currency" autocomplete="off" placeholder="Masukkan Nominal" required="" minlength="1" maxlength="14" autofocus="" value="<?= $row['nominal'];?>">
															</div>
														</div>
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="status" class="input-required">Status</label>
																<select class="form-control select2" name="status" id="status" required="">
																	<option value="">Pilih Status</option>
																	<option value="Disetujui" <?= isSelected($row['status'], "Disetujui");?>>Disetujui</option>
																	<option value="Ditolak" <?= isSelected($row['status'], "Ditolak");?>>Ditolak</option>
																</select>
															</div>
														</div>
														<div class="col-lg-4 col-md-12">
															<div class="form-group">
																<label for="m_bank_id" class="input-required">Bank</label>
																<select class="form-control select2 width-100" name="m_bank_id" id="m_bank_id" required="">
																	<option value="">Pilih Bank</option>
																	<?php
																	$queryGet = mysqli_query($conn, "SELECT * FROM m_bank WHERE is_deleted = 0 ORDER BY nama ASC");
																	while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
																		$selected = isSelected($rowGet['id'], $row['m_bank_id']);
																		echo "<option value='".$rowGet['id']."' ".$selected.">".$rowGet['nama']."</option>";
																	}
																	?>
																</select>
															</div>
														</div>
														<div class="col-lg-4 col-md-12">
															<div class="form-group">
																<label for="nomor_rekening" class="input-required">Nomor Rekening</label>
																<input type="text" name="nomor_rekening" id="nomor_rekening" class="form-control" autocomplete="off" placeholder="Masukkan Nomor Rekening" required="" minlength="1" maxlength="50" autofocus="" onkeypress="return onlyNumber(event);" value="<?= $row['nomor_rekening'];?>">
															</div>
														</div>
														<div class="col-lg-4 col-md-12">
															<div class="form-group">
																<label for="atas_nama" class="input-required">Atas Nama</label>
																<input type="text" name="atas_nama" id="atas_nama" class="form-control" autocomplete="off" placeholder="Masukkan Atas Nama" required="" minlength="1" maxlength="100" autofocus="" value="<?= $row['atas_nama'];?>">
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
												<?php }else { ?>
													<div class="row">
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="tanggal" class="input-required">Tanggal</label>
																<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" autofocus="" value="<?= date('Y-m-d', strtotime($row['tanggal']));?>">
															</div>
														</div>
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="nominal" class="input-required">Nominal</label>
																<input type="text" name="nominal" id="nominal" class="form-control currency" autocomplete="off" placeholder="Masukkan Nominal" required="" minlength="1" maxlength="14" autofocus="" value="<?= $row['nominal'];?>">
															</div>
														</div>
														<div class="col-lg-4 col-md-12">
															<div class="form-group">
																<label for="m_bank_id" class="input-required">Bank</label>
																<select class="form-control select2 width-100" name="m_bank_id" id="m_bank_id" required="">
																	<option value="">Pilih Bank</option>
																	<?php
																	$queryGet = mysqli_query($conn, "SELECT * FROM m_bank ORDER BY nama ASC");
																	while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
																		$selected = isSelected($rowGet['id'], $row['m_bank_id']);
																		echo "<option value='".$rowGet['id']."' ".$selected.">".$rowGet['nama']."</option>";
																	}
																	?>
																</select>
															</div>
														</div>
														<div class="col-lg-4 col-md-12">
															<div class="form-group">
																<label for="nomor_rekening" class="input-required">Nomor Rekening</label>
																<input type="text" name="nomor_rekening" id="nomor_rekening" class="form-control" autocomplete="off" placeholder="Masukkan Nomor Rekening" required="" minlength="1" maxlength="50" autofocus="" onkeypress="return onlyNumber(event);" value="<?= $row['nomor_rekening'];?>">
															</div>
														</div>
														<div class="col-lg-4 col-md-12">
															<div class="form-group">
																<label for="atas_nama" class="input-required">Atas Nama</label>
																<input type="text" name="atas_nama" id="atas_nama" class="form-control" autocomplete="off" placeholder="Masukkan Atas Nama" required="" minlength="1" maxlength="100" autofocus="" value="<?= $row['atas_nama'];?>">
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
												<?php } ?>
											</form>
										<?php }else { ?>
											<div class="row">
												<div class="col-lg-6 col-md-12">
													<div class="alert alert-danger">
														<h5>
															<i class="icon fas fa-ban"></i> Peringatan!
														</h5>
														<p>Maaf, Anda tidak memiliki akses untuk mengedit data ini.</p>
													</div>
												</div>
											</div>
										<?php } ?>
									</div>
								<?php }else { ?>
									<div class="card-body">
										<div class="error-page">
											<h2 class="headline text-warning"> 404</h2>
											<div class="error-content">
												<h3>
													<i class="fas fa-exclamation-triangle text-warning"></i> Oops! Data tidak dapat ditemukan
												</h3>
												<p>Maaf kami tidak dapat menemukan data apa pun, untuk menghilangkan pesan ini, buat setidaknya 1 data.</p>
												<form class="search-form">
													<a href="../../add/pengambilan-saldo" class="btn btn-primary mt-4">Buat Baru</a>
												</form>
											</div>
										</div>
									</div>
								<?php } ?>
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
			data.append('function', 'updateData');

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
						url: "../../../routes/web/pengambilan-saldo.php",
						data: data,
						processData: false,
						contentType: false,
						success: function(response) {
							var result = JSON.parse(response);
							if(result.status_code == 200) {
								toastr.success(result.message);
								setTimeout(() => {
									window.location.href = "../../pengambilan-saldo";
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