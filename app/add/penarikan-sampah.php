<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('penarikan-sampah', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$isPengajuan = false;
$kodePenarikanSampah = $statusPenarikanSampah = "";

if(isset($_SESSION['tipe']) && !empty($_SESSION['tipe']) && $_SESSION['tipe'] == "Pelanggan") {
	$idSession = $_SESSION['id'];

	$queryHistoryPelanggan = mysqli_query($conn, "SELECT * FROM t_penarikan_sampah WHERE m_pelanggan_id = '$idSession' AND status IN('Pending', 'Proses')");
	$countHistoryPelanggan = mysqli_num_rows($queryHistoryPelanggan);
	
	if($countHistoryPelanggan > 0) {
		$rowHistoryPelanggan = mysqli_fetch_array($queryHistoryPelanggan, MYSQLI_ASSOC);
		$isPengajuan = true;
		$kodePenarikanSampah = $rowHistoryPelanggan['kode'];
		$statusPenarikanSampah = $rowHistoryPelanggan['status'];
	}
}

if($_SESSION['tipe'] == "Petugas") {
	$isPengajuan = true;
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
							<h1 class="m-0">Tambah Penarikan Sampah</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../penarikan-sampah">Penarikan Sampah</a></li>
								<li class="breadcrumb-item active">Tambah Penarikan Sampah</li>
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
										<a href="../penarikan-sampah" alt="Kembali" class="btn btn-sm btn-danger">
											<i class="fas fa-arrow-left fa-sm"></i>&nbsp; Kembali
										</a>
									</h3>
								</div>
								<div class="card-body">
									<?php if($isPengajuan == true) { ?>
										<div class="row">
											<div class="col-lg-6 col-md-12">
												<div class="alert alert-danger">
													<h5>
														<i class="icon fas fa-ban"></i> Peringatan!
													</h5>
													<?php if($_SESSION['tipe'] == "Petugas") { ?>
														<p>Anda tidak dapat mengakses menu ini, hanya pelanggan atau admin saja yang dapat mengakses menu ini.</p>
													<?php }else { ?>
														<?php if($statusPenarikanSampah == "Pending") { ?>
															<p>Anda masih memiliki transaksi penarikan sampah dengan kode <strong><?= $kodePenarikanSampah;?></strong> yang masih dalam status pending. Harap menunggu untuk di proses atau Anda dapat menghapus lalu mengajukan penarikan sampah lagi.</p>
														<?php }else if($statusPenarikanSampah == "Proses") { ?>
															<p>Anda masih memiliki transaksi penarikan sampah dengan kode <strong><?= $kodePenarikanSampah;?></strong> yang masih dalam proses. Harap menunggu terlebih dahulu sebelum melakukan pengajuan penarikan sampah lagi.</p>
														<?php } ?>
													<?php } ?>
												</div>
											</div>
										</div>
									<?php }else { ?>
										<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
											<div class="row">
												<div class="col-lg-4 col-md-12">
													<div class="form-group">
														<label for="m_pelanggan_id" class="input-required">Pelanggan</label>
														<select class="form-control select2 width-100" name="m_pelanggan_id" id="m_pelanggan_id" required="">
															<option value="">Pilih Pelanggan</option>
															<?php
															$arrPelanggan = [];
															$queryDataPelanggan = mysqli_query($conn, "SELECT * FROM t_penarikan_sampah WHERE status IN('Pending', 'Proses') AND is_deleted = 0");
															while($rowDataPelanggan = mysqli_fetch_array($queryDataPelanggan, MYSQLI_ASSOC)) {
																if(isset($rowDataPelanggan['m_pelanggan_id']) && !empty($rowDataPelanggan['m_pelanggan_id'])) {
																	$arrPelanggan[$rowDataPelanggan['m_pelanggan_id']] = $rowDataPelanggan['m_pelanggan_id'];
																}
															}
															$arrPelanggan = array_values($arrPelanggan);

															if($_SESSION['tipe'] == "Pengguna") {
																if(isset($arrPelanggan) && !empty($arrPelanggan)) {
																	$implodePelanggan = implode(", ", $arrPelanggan);
																	$queryGet = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = 0 AND id NOT IN(".$implodePelanggan.") ORDER BY nama ASC");
																}else {
																	$queryGet = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = 0 ORDER BY nama ASC");
																}
															}else {
																$idPelanggan = $_SESSION['id'];
																$queryGet = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = 0 AND id = '$idPelanggan' ORDER BY nama ASC");
															}
															while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
																if($_SESSION['tipe'] == "Pengguna") {
																	echo "<option value='".$rowGet['id']."'>".$rowGet['nama']."</option>";
																}else {
																	$selected = isSelected($rowGet['id'], $_SESSION['id']);
																	echo "<option value='".$rowGet['id']."' ".$selected.">".$rowGet['nama']."</option>";
																}
															}
															?>
														</select>
													</div>
												</div>
												<?php if($_SESSION['tipe'] == "Pengguna") { ?>
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="m_petugas_id" class="input-required">Petugas</label>
															<select class="form-control select2 width-100" name="m_petugas_id" id="m_petugas_id" required="">
																<option value="">Pilih Petugas</option>
																<?php
																$queryGet = mysqli_query($conn, "SELECT * FROM m_petugas WHERE is_deleted = 0 ORDER BY nama ASC");
																while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
																	echo "<option value='".$rowGet['id']."'>".$rowGet['nama']."</option>";
																}
																?>
															</select>
														</div>
													</div>
												<?php } ?>
												<div class="col-lg-4 col-md-12">
													<div class="form-group">
														<label for="tanggal" class="input-required">Tanggal</label>
														<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" autofocus="" value="<?= date('Y-m-d');?>" <?php if($_SESSION['tipe'] == "Pelanggan") {echo "readonly=''";}?>>
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
									<?php } ?>
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
						url: "../../routes/web/penarikan-sampah.php",
						data: data,
						processData: false,
						contentType: false,
						success: function(response) {
							var result = JSON.parse(response);
							if(result.status_code == 200) {
								toastr.success(result.message);
								setTimeout(() => {
									window.location.href = '../penarikan-sampah';
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