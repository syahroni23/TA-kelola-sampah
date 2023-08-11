<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('biaya-penarikan', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
$query = mysqli_query($conn, "SELECT * FROM t_biaya_penarikan WHERE id = '$id' AND is_deleted = 0");
$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));

$isCanEdit = false;

if(isset($row['status']) && $row['status'] == "Proses" && $_SESSION['tipe'] == "Pengguna") {
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
							<h1 class="m-0">Ubah Biaya Penarikan</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../../biaya-penarikan">Biaya Penarikan</a></li>
								<li class="breadcrumb-item active">Ubah Biaya Penarikan</li>
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
										<a href="../../biaya-penarikan" alt="Kembali" class="btn btn-sm btn-danger">
											<i class="fas fa-arrow-left fa-sm"></i>&nbsp; Kembali
										</a>
									</h3>
								</div>
								<?php if(issetEmpty($row)) { ?>
									<div class="card-body">
										<?php if($isCanEdit == true) { ?>
											<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
												<input type="hidden" name="id" value="<?= $row['id'];?>">
												<div class="row">
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="t_penarikan_sampah_id" class="input-required">Penarikan Sampah</label>
															<select class="form-control select2 width-100" name="t_penarikan_sampah_id" id="t_penarikan_sampah_id" required="">
																<option value="">Pilih Penarikan Sampah</option>
																<?php
																$arrPenarikanSampah = [];
																$tPenarikanSampahId = $row['t_penarikan_sampah_id'];
																$queryDataPenarikanSampah = mysqli_query($conn, "SELECT * FROM t_biaya_penarikan WHERE is_deleted = 0 AND t_penarikan_sampah_id != '$tPenarikanSampahId' GROUP BY t_penarikan_sampah_id");
																while($rowDataPenarikanSampah = mysqli_fetch_array($queryDataPenarikanSampah, MYSQLI_ASSOC)) {
																	if(isset($rowDataPenarikanSampah['t_penarikan_sampah_id']) && !empty($rowDataPenarikanSampah['t_penarikan_sampah_id'])) {
																		$arrPenarikanSampah[$rowDataPenarikanSampah['t_penarikan_sampah_id']] = $rowDataPenarikanSampah['t_penarikan_sampah_id'];
																	}
																}
																$arrPenarikanSampah = array_values($arrPenarikanSampah);

																if(isset($arrPenarikanSampah) && !empty($arrPenarikanSampah)) {
																	$implodePenarikanSampah = implode(", ", $arrPenarikanSampah);
																	$queryGet = mysqli_query($conn, "SELECT t_penarikan_sampah.*, m_pelanggan.nama AS nama_pelanggan FROM t_penarikan_sampah LEFT JOIN m_pelanggan ON t_penarikan_sampah.m_pelanggan_id = m_pelanggan.id WHERE t_penarikan_sampah.is_deleted = 0 AND t_penarikan_sampah.status = 'Selesai' AND t_penarikan_sampah.id NOT IN(".$implodePenarikanSampah.") ORDER BY t_penarikan_sampah.kode ASC");
																}else {
																	$queryGet = mysqli_query($conn, "SELECT t_penarikan_sampah.*, m_pelanggan.nama AS nama_pelanggan FROM t_penarikan_sampah LEFT JOIN m_pelanggan ON t_penarikan_sampah.m_pelanggan_id = m_pelanggan.id WHERE t_penarikan_sampah.is_deleted = 0 AND t_penarikan_sampah.status = 'Selesai' ORDER BY t_penarikan_sampah.kode ASC");
																}
																while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
																	$selected = isSelected($rowGet['id'], $row['t_penarikan_sampah_id']);
																	echo "<option value='".$rowGet['id']."' ".$selected.">".$rowGet['kode']." - ".$rowGet['nama_pelanggan']."</option>";
																}
																?>
															</select>
														</div>
													</div>
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="tanggal" class="input-required">Tanggal</label>
															<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" autofocus="" value="<?= date('Y-m-d', strtotime($row['tanggal']));?>">
														</div>
													</div>
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="biaya" class="input-required">Biaya</label>
															<input type="text" name="biaya" id="biaya" class="form-control currency" autocomplete="off" placeholder="Masukkan Biaya" required="" minlength="1" maxlength="14" autofocus="" value="<?= $row['biaya'];?>">
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
										<?php }else { ?>
											<div class="row">
												<div class="col-lg-6 col-md-12">
													<div class="alert alert-danger">
														<h5>
															<i class="icon fas fa-ban"></i> Peringatan!
														</h5>
														<p>Maaf, Anda tidak memiliki akses untuk mengedit data ini. Silahkan hubungi admin!</p>
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
													<a href="../../add/biaya-penarikan" class="btn btn-primary mt-4">Buat Baru</a>
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
						url: "../../../routes/web/biaya-penarikan.php",
						data: data,
						processData: false,
						contentType: false,
						success: function(response) {
							var result = JSON.parse(response);
							if(result.status_code == 200) {
								toastr.success(result.message);
								setTimeout(() => {
									window.location.href = "../../biaya-penarikan";
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