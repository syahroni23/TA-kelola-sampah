<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('penarikan-sampah', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
$query = mysqli_query($conn, "SELECT t_penarikan_sampah.*, m_pelanggan.kode AS kode_pelanggan, m_petugas.kode AS kode_petugas FROM t_penarikan_sampah LEFT JOIN m_pelanggan ON t_penarikan_sampah.m_pelanggan_id = m_pelanggan.id LEFT JOIN m_petugas ON t_penarikan_sampah.m_petugas_id = m_petugas.id WHERE t_penarikan_sampah.id = '$id' AND t_penarikan_sampah.is_deleted = 0");
$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));

$isCanEdit = false;

if(isset($row['status']) && $row['status'] == "Pending") {
	$isCanEdit = true;
}else if(isset($row['status']) && $row['status'] == "Proses" && $_SESSION['tipe'] == "Petugas") {
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
							<h1 class="m-0">Ubah Penarikan Sampah</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../../penarikan-sampah">Penarikan Sampah</a></li>
								<li class="breadcrumb-item active">Ubah Penarikan Sampah</li>
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
										<a href="../../penarikan-sampah" alt="Kembali" class="btn btn-sm btn-danger">
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
												<?php if($_SESSION['tipe'] == "Petugas") { ?>
													<div class="row">
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="m_pelanggan_id" class="input-required">Pelanggan</label>
																<select class="form-control select2 width-100" name="m_pelanggan_id" id="m_pelanggan_id" required="" disabled="">
																	<option value="">Pilih Pelanggan</option>
																	<?php
																	$idPelanggan = $row['m_pelanggan_id'];
																	$queryGet = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = 0 AND id = '$idPelanggan' ORDER BY nama ASC");
																	while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
																		$selected = isSelected($rowGet['id'], $row['m_pelanggan_id']);
																		echo "<option value='".$rowGet['id']."' ".$selected.">".$rowGet['nama']."</option>";
																	}
																	?>
																</select>
															</div>
														</div>
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="m_petugas_id" class="input-required">Petugas</label>
																<select class="form-control select2 width-100" name="m_petugas_id" id="m_petugas_id" required="" disabled="">
																	<option value="">Pilih Petugas</option>
																	<?php
																	$idPetugas = $row['m_petugas_id'];
																	$queryGet = mysqli_query($conn, "SELECT * FROM m_petugas WHERE is_deleted = 0 AND id = '$idPetugas' ORDER BY nama ASC");
																	while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
																		$selected = isSelected($rowGet['id'], $row['m_petugas_id']);
																		echo "<option value='".$rowGet['id']."' ".$selected.">".$rowGet['nama']."</option>";
																	}
																	?>
																</select>
															</div>
														</div>
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="tanggal" class="input-required">Tanggal</label>
																<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" readonly="" autofocus="" value="<?= date('Y-m-d', strtotime($row['tanggal']));?>">
															</div>
														</div>
														<div class="col-lg-6 col-md-12">
															<div class="form-group">
																<label for="status" class="input-required">Status</label>
																<select class="form-control select2" name="status" id="status" required="">
																	<option value="">Pilih Status</option>
																	<option value="Proses" <?= isSelected($row['status'], "Proses");?>>Proses Ambil</option>
																	<option value="Selesai" <?= isSelected($row['status'], "Selesai");?>>Sudah Diambil</option>
																</select>
															</div>
														</div>
													</div>
													<div class="row" id="isTampilBuktiAmbil" style="display: none;">
														<div class="col-lg-3 col-md-12">
															<div class="form-group">
																<label for="bukti_ambil" class="input-required">Bukti Ambil</label>
																<div id="image-preview" class="image-preview" style="background-image: url('../../../assets/img/bukti-ambil/<?= $row['bukti_ambil'];?>'); background-size: cover; background-position: center center;">
																	<label for="image-upload" id="image-label">Ubah File</label>
																	<input type="file" name="bukti_ambil" id="image-upload" accept=".jpg, .jpeg, .png" title="Tidak ada file dipilih" style="max-width: 100%;" />
																</div>
															</div>
														</div>
													</div>
												<?php }else { ?>
													<div class="row">
														<div class="col-lg-4 col-md-12">
															<div class="form-group">
																<label for="m_pelanggan_id" class="input-required">Pelanggan</label>
																<select class="form-control select2 width-100" name="m_pelanggan_id" id="m_pelanggan_id" required="">
																	<option value="">Pilih Pelanggan</option>
																	<?php
																	$arrPelanggan = [];
																	$queryDataPelanggan = mysqli_query($conn, "SELECT * FROM t_penarikan_sampah WHERE status IN('Pending', 'Proses') AND id != '$id' AND is_deleted = 0");
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
																		$idPelanggan = $row['m_pelanggan_id'];
																		$queryGet = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = 0 AND id = '$idPelanggan' ORDER BY nama ASC");
																	}
																	while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
																		if($_SESSION['tipe'] == "Pengguna") {
																			$selected = isSelected($rowGet['id'], $row['m_pelanggan_id']);
																			echo "<option value='".$rowGet['id']."' ".$selected.">".$rowGet['nama']."</option>";
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
																			$selected = isSelected($rowGet['id'], $row['m_petugas_id']);
																			echo "<option value='".$rowGet['id']."' ".$selected.">".$rowGet['nama']."</option>";
																		}
																		?>
																	</select>
																</div>
															</div>
														<?php } ?>
														<div class="col-lg-4 col-md-12">
															<div class="form-group">
																<label for="tanggal" class="input-required">Tanggal</label>
																<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" autofocus="" value="<?= date('Y-m-d', strtotime($row['tanggal']));?>" <?php if($_SESSION['tipe'] == "Pelanggan") {echo "readonly=''";}?>>
															</div>
														</div>
													</div>
												<?php } ?>
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
														<?php if($_SESSION['tipe'] == "Petugas") { ?>
															<p>Maaf, Anda tidak memiliki akses untuk mengedit data ini. Yang dapat mengedit data ini adalah orang yang bersangkutan saja.</p>
														<?php }else { ?>
															<p>Maaf, Anda tidak memiliki akses untuk mengedit data ini. Silahkan hubungi admin!</p>
														<?php } ?>
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
													<a href="../../add/penarikan-sampah" class="btn btn-primary mt-4">Buat Baru</a>
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
		$.uploadPreview({
			input_field: "#image-upload",
			preview_box: "#image-preview",
			label_field: "#image-label",
			label_default: "Pilih File",
			label_selected: "Ubah File",
			no_label: false
		});

		function tampilkanBuktiAmbil(value) {
			if(value == "Proses") {
				$('#isTampilBuktiAmbil').css('display', 'none');
				$('#image-upload').removeAttr('required');
			}else if(value == "Selesai") {
				$('#isTampilBuktiAmbil').css('display', 'block');
				$('#image-upload').attr('required', '');
			}else {
				$('#isTampilBuktiAmbil').css('display', 'none');
				$('#image-upload').removeAttr('required');
			}
		}
		
		$('#status').on('change', function(e) {
			e.preventDefault();
			tampilkanBuktiAmbil($(this).val());
		});

		tampilkanBuktiAmbil($('#status').val());

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
						url: "../../../routes/web/penarikan-sampah.php",
						data: data,
						processData: false,
						contentType: false,
						success: function(response) {
							var result = JSON.parse(response);
							if(result.status_code == 200) {
								toastr.success(result.message);
								setTimeout(() => {
									window.location.href = "../../penarikan-sampah";
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