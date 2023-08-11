<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('pengisian-saldo', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$isPengajuan = false;
$kodePengisianSaldo = $statusPengisianSaldo = "";

if(isset($_SESSION['tipe']) && !empty($_SESSION['tipe']) && ($_SESSION['tipe'] == "Pelanggan" || $_SESSION['tipe'] == "Petugas")) {
	$idSession = $_SESSION['id'];

	if($_SESSION['tipe'] == "Pelanggan") {
		$queryHistoryPelanggan = mysqli_query($conn, "SELECT * FROM t_pengisian_saldo WHERE m_pelanggan_id = '$idSession' AND status IN ('Pengajuan', 'Proses')");
		$countHistoryPelanggan = mysqli_num_rows($queryHistoryPelanggan);
		
		if($countHistoryPelanggan > 0) {
			$rowHistoryPelanggan = mysqli_fetch_array($queryHistoryPelanggan, MYSQLI_ASSOC);
			$isPengajuan = true;
			$kodePengisianSaldo = $rowHistoryPelanggan['kode'];
			$statusPengisianSaldo = $rowHistoryPelanggan['status'];
		}
	}else {
		$queryHistoryPetugas = mysqli_query($conn, "SELECT * FROM t_pengisian_saldo WHERE m_petugas_id = '$idSession' AND status IN ('Pengajuan', 'Proses')");
		$countHistoryPetugas = mysqli_num_rows($queryHistoryPetugas);
		
		if($countHistoryPetugas > 0) {
			$rowHistoryPetugas = mysqli_fetch_array($queryHistoryPetugas, MYSQLI_ASSOC);
			$isPengajuan = true;
			$kodePengisianSaldo = $rowHistoryPetugas['kode'];
			$statusPengisianSaldo = $rowHistoryPetugas['status'];
		}
	}
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
							<h1 class="m-0">Tambah Pengisian Saldo</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../pengisian-saldo">Pengisian Saldo</a></li>
								<li class="breadcrumb-item active">Tambah Pengisian Saldo</li>
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
										<a href="../pengisian-saldo" alt="Kembali" class="btn btn-sm btn-danger">
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
													<?php if($statusPengisianSaldo == "Pengajuan") { ?>
														<p>Anda masih memiliki transaksi pengisian saldo dengan kode <strong><?= $kodePengisianSaldo;?></strong> yang masih dalam pengajuan. Harap untuk melakukan pembayaran terlebih dahulu dan mengirim bukti transfer agar dapat dilakukan pengecekan oleh admin. </p>
													<?php }else if($statusPengisianSaldo == "Proses") { ?>
														<p>Anda masih memiliki transaksi pengisian saldo dengan kode <strong><?= $kodePengisianSaldo;?></strong> yang masih dalam proses. Harap menunggu terlebih dahulu sebelum melakukan pengajuan pengisian saldo lagi. </p>
													<?php } ?>
												</div>
											</div>
										</div>
									<?php }else { ?>
										<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
											<input type="hidden" name="tipe" value="<?= $_SESSION['tipe'];?>">

											<?php if($_SESSION['tipe'] == "Pengguna") { ?>
												<div class="row">
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="pelanggan_petugas" class="input-required">Pelanggan / Petugas</label>
															<select class="form-control select2" name="pelanggan_petugas" id="pelanggan_petugas" required="">
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
																$sortByName = array_column($arrPelangganPetugas, 'nama');
																array_multisort($sortByName, SORT_ASC, $arrPelangganPetugas);
																foreach ($arrPelangganPetugas as $keys => $values) {
																	$values = allRowValidateHTML($values);
																	echo "<option value='".$values['kode']."'>".$values['nama']."</option>";
																}
																?>
															</select>
														</div>
													</div>
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="tanggal" class="input-required">Tanggal</label>
															<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" autofocus="" value="<?= date('Y-m-d');?>">
														</div>
													</div>
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="nominal" class="input-required">Nominal</label>
															<input type="text" name="nominal" id="nominal" class="form-control currency" autocomplete="off" placeholder="Masukkan Nominal" required="" minlength="1" maxlength="14" autofocus="">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-3 col-md-12">
														<div class="form-group">
															<label for="bukti_transfer" class="input-required">Bukti Transfer</label>
															<div id="image-preview" class="image-preview">
																<label for="image-upload" id="image-label">Pilih File</label>
																<input type="file" name="bukti_transfer" id="image-upload" required="" accept=".jpg, .jpeg, .png" title="Tidak ada file dipilih" style="max-width: 100%;" />
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
											<?php }else if($_SESSION['tipe'] == "Pelanggan") { ?>
												<input type="hidden" name="m_pelanggan_id" value="<?= $_SESSION['id'];?>">
												<div class="row">
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="tanggal" class="input-required">Tanggal</label>
															<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" autofocus="" value="<?= date('Y-m-d');?>">
														</div>
													</div>
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="nominal" class="input-required">Nominal</label>
															<input type="text" name="nominal" id="nominal" class="form-control currency" autocomplete="off" placeholder="Masukkan Nominal" required="" minlength="1" maxlength="14" autofocus="">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-2 col-md-12">
														<div class="form-group text-center">
															<img src="../../assets/img/default/logo-bri.png" alt="Logo BRI" class="img-fluid" width="100" style="margin-top: 9px; margin-bottom: 6.5px">
															<h5>014301010219532</h5>
														</div>
													</div>
													<div class="col-lg-2 col-md-12">
														<div class="form-group text-center">
															<img src="../../assets/img/default/logo-dana.jpg" alt="Logo Dana" class="img-fluid" width="100">
															<h5>085348110452</h5>
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
											<?php }else if($_SESSION['tipe'] == "Petugas") { ?>
												<input type="hidden" name="m_petugas_id" value="<?= $_SESSION['id'];?>">
												<div class="row">
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="tanggal" class="input-required">Tanggal</label>
															<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" autofocus="" value="<?= date('Y-m-d');?>">
														</div>
													</div>
													<div class="col-lg-4 col-md-12">
														<div class="form-group">
															<label for="nominal" class="input-required">Nominal</label>
															<input type="text" name="nominal" id="nominal" class="form-control currency" autocomplete="off" placeholder="Masukkan Nominal" required="" minlength="1" maxlength="14" autofocus="">
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-2 col-md-12">
														<div class="form-group text-center">
															<img src="../../assets/img/default/logo-bri.png" alt="Logo BRI" class="img-fluid" width="100" style="margin-top: 9px; margin-bottom: 6.5px">
															<h5>014301010219532</h5>
														</div>
													</div>
													<div class="col-lg-2 col-md-12">
														<div class="form-group text-center">
															<img src="../../assets/img/default/logo-dana.jpg" alt="Logo Dana" class="img-fluid" width="100">
															<h5>085348110452</h5>
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
		$.uploadPreview({
			input_field: "#image-upload",
			preview_box: "#image-preview",
			label_field: "#image-label",
			label_default: "Pilih File",
			label_selected: "Ubah File",
			no_label: false
		});

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
						url: "../../routes/web/pengisian-saldo.php",
						data: data,
						processData: false,
						contentType: false,
						success: function(response) {
							var result = JSON.parse(response);
							if(result.status_code == 200) {
								toastr.success(result.message);
								setTimeout(() => {
									window.location.href = '../pengisian-saldo';
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