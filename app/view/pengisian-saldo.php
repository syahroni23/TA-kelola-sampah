<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('pengisian-saldo', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
$query = mysqli_query($conn, "SELECT t_pengisian_saldo.*, m_pelanggan.kode AS kode_pelanggan, m_petugas.kode AS kode_petugas FROM t_pengisian_saldo LEFT JOIN m_pelanggan ON t_pengisian_saldo.m_pelanggan_id = m_pelanggan.id LEFT JOIN m_petugas ON t_pengisian_saldo.m_petugas_id = m_petugas.id WHERE t_pengisian_saldo.id = '$id'");
$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
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
							<h1 class="m-0">Detail Pengisian Saldo</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../../pengisian-saldo">Pengisian Saldo</a></li>
								<li class="breadcrumb-item active">Detail Pengisian Saldo</li>
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
										<a href="../../pengisian-saldo" alt="Kembali" class="btn btn-sm btn-danger">
											<i class="fas fa-arrow-left fa-sm"></i>&nbsp; Kembali
										</a>
									</h3>
								</div>
								<?php if(issetEmpty($row)) { ?>
									<?php
									$row['kode_user'] = isset($row['kode_pelanggan']) && !empty($row['kode_pelanggan']) ? $row['kode_pelanggan'] : $row['kode_petugas'];
									?>
									<div class="card-body">
										<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
											<input type="hidden" name="id" value="<?= $row['id'];?>">
											<div class="row">
												<div class="col-lg-6 col-md-12">
													<div class="form-group">
														<label for="pelanggan_petugas">Pelanggan / Petugas</label>
														<select class="form-control select2" name="pelanggan_petugas" id="pelanggan_petugas" disabled="">
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
														<label for="tanggal">Tanggal</label>
														<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" disabled="" value="<?= date('Y-m-d', strtotime($row['tanggal']));?>">
													</div>
												</div>
												<div class="col-lg-6 col-md-12">
													<div class="form-group">
														<label for="nominal">Nominal</label>
														<input type="text" name="nominal" id="nominal" class="form-control currency" autocomplete="off" placeholder="Masukkan Nominal" disabled="" minlength="1" maxlength="14" value="<?= $row['nominal'];?>">
													</div>
												</div>
												<div class="col-lg-6 col-md-12">
													<div class="form-group">
														<label for="status">Status</label>
														<select class="form-control select2" name="status" id="status" disabled="">
															<option value="">Pilih Status</option>
															<option value="Proses" <?= isSelected($row['status'], "Proses");?>>Proses</option>
															<option value="Disetujui" <?= isSelected($row['status'], "Disetujui");?>>Disetujui</option>
															<option value="Ditolak" <?= isSelected($row['status'], "Ditolak");?>>Ditolak</option>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-3 col-md-12">
													<div class="form-group">
														<label for="bukti_transfer">Bukti Transfer <small class="text-muted">(Opsional)</small></label>
														<div id="image-preview" class="image-preview" style="background-image: url('../../../assets/img/bukti-transfer/<?= $row['bukti_transfer'];?>'); background-size: cover; background-position: center center;"></div>
													</div>
												</div>
											</div>
										</form>
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
													<a href="../../add/pengisian-saldo" class="btn btn-primary mt-4">Buat Baru</a>
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
</body>
</html>