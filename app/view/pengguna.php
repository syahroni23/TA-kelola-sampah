<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('pengguna', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$id = htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8');
$query = mysqli_query($conn, "SELECT * FROM m_pengguna WHERE id = '$id'");
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
							<h1 class="m-0">Detail Pengguna</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../../pengguna">Pengguna</a></li>
								<li class="breadcrumb-item active">Detail Pengguna</li>
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
										<a href="../../pengguna" alt="Kembali" class="btn btn-sm btn-danger">
											<i class="fas fa-arrow-left fa-sm"></i>&nbsp; Kembali
										</a>
									</h3>
								</div>
								<?php if(issetEmpty($row)) { ?>
									<div class="card-body">
										<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
											<input type="hidden" name="id" value="<?= $row['id'];?>">
											<div class="row">
												<div class="col-lg-4 col-md-12">
													<div class="form-group">
														<label for="nama">Nama Lengkap</label>
														<input type="text" name="nama" id="nama" class="form-control" autocomplete="off" placeholder="Masukkan Nama Lengkap" disabled="" minlength="1" maxlength="100" value="<?= $row['nama'];?>">
													</div>
												</div>
												<div class="col-lg-4 col-md-12">
													<div class="form-group">
														<label for="telepon">Telepon</label>
														<input type="text" name="telepon" id="telepon" class="form-control phone-number" autocomplete="off" placeholder="Masukkan Telepon" minlength="1" maxlength="15" disabled="" value="<?= $row['telepon'];?>">
													</div>
												</div>
												<div class="col-lg-4 col-md-12">
													<div class="form-group">
														<label for="email">E-mail</label>
														<input type="email" name="email" id="email" class="form-control" autocomplete="off" placeholder="Masukkan E-mail" minlength="1" maxlength="100" disabled="" value="<?= $row['email'];?>">
													</div>
												</div>
												<div class="col-lg-4 col-md-12">
													<div class="form-group">
														<label for="password">Kata Sandi <small class="text-muted">(Opsional)</small></label>
														<input type="password" name="password" id="password" class="form-control" autocomplete="off" placeholder="Masukkan Kata Sandi" minlength="1" maxlength="20" disabled="" value="<?= $row['password'];?>">
													</div>
												</div>
												<div class="col-lg-4 col-md-12">
													<div class="form-group">
														<label for="m_hak_akses_id">Hak Akses</label>
														<select class="form-control select2 width-100" name="m_hak_akses_id" id="m_hak_akses_id" disabled="">
															<option value="">Pilih Hak Akses</option>
															<?php
															$queryGet = mysqli_query($conn, "SELECT * FROM m_hak_akses WHERE id NOT IN(2, 3) AND is_deleted = 0 ORDER BY nama ASC");
															while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
																$selected = isSelected($rowGet['id'], $row['m_hak_akses_id']);
																echo "<option value='".$rowGet['id']."' ".$selected.">".$rowGet['nama']."</option>";
															}
															?>
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-3 col-md-12">
													<div class="form-group">
														<label for="avatar">Avatar <small class="text-muted">(Opsional)</small></label>
														<div id="image-preview" class="image-preview" style="background-image: url('../../../assets/img/avatar/<?= $row['avatar'];?>'); background-size: cover; background-position: center center;"></div>
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
													<a href="../../add/pengguna" class="btn btn-primary mt-4">Buat Baru</a>
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