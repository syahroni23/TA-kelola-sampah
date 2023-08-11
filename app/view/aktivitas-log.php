<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('aktivitas-log', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
$query = mysqli_query($conn, "SELECT * FROM t_aktivitas_log WHERE id = '$id'");
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
							<h1 class="m-0">Detail Aktivitas Log</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../../aktivitas-log">Aktivitas Log</a></li>
								<li class="breadcrumb-item active">Detail Aktivitas Log</li>
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
										<a href="../../aktivitas-log" alt="Kembali" class="btn btn-sm btn-danger">
											<i class="fas fa-arrow-left fa-sm"></i>&nbsp; Kembali
										</a>
									</h3>
								</div>
								<?php if(issetEmpty($row)) { ?>
									<div class="card-body">
										<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
											<div class="row">
												<div class="col-lg-6 col-md-12">
													<div class="form-group">
														<label for="ip">IP Address</label>
														<input type="text" name="ip" id="ip" class="form-control" autocomplete="off" placeholder="Masukkan IP Address" minlength="1" maxlength="100" disabled="" value="<?= $row['ip'];?>">
													</div>
												</div>
												<div class="col-lg-6 col-md-12">
													<div class="form-group">
														<label for="browser">Browser</label>
														<input type="text" name="browser" id="browser" class="form-control" autocomplete="off" placeholder="Masukkan Browser" minlength="1" maxlength="100" disabled="" value="<?= $row['browser'];?>">
													</div>
												</div>
												<div class="col-lg-6 col-md-12">
													<div class="form-group">
														<label for="peramban_web">Peramban Web</label>
														<textarea name="peramban_web" id="peramban_web" class="form-control h-25" rows="4" autocomplete="off" placeholder="Masukkan Peramban Web" disabled=""><?= $row['peramban_web'];?></textarea>
													</div>
												</div>
												<div class="col-lg-6 col-md-12">
													<div class="form-group">
														<label for="sistem_operasi">Sistem Operasi</label>
														<textarea name="sistem_operasi" id="sistem_operasi" class="form-control h-25" rows="4" autocomplete="off" placeholder="Masukkan Sistem Operasi" disabled=""><?= $row['sistem_operasi'];?></textarea>
													</div>
												</div>
												<div class="col-lg-12 col-md-12">
													<div class="form-group">
														<label for="keterangan">Keterangan</label>
														<textarea name="keterangan" id="keterangan" class="form-control h-25" rows="4" autocomplete="off" placeholder="Masukkan Keterangan" disabled=""><?= $row['keterangan'];?></textarea>
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