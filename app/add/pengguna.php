<?php include '../../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('pengguna', $getAkses);
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
							<h1 class="m-0">Tambah Pengguna</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="../beranda">Beranda</a></li>
								<li class="breadcrumb-item"><a href="../pengguna">Pengguna</a></li>
								<li class="breadcrumb-item active">Tambah Pengguna</li>
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
										<a href="../pengguna" alt="Kembali" class="btn btn-sm btn-danger">
											<i class="fas fa-arrow-left fa-sm"></i>&nbsp; Kembali
										</a>
									</h3>
								</div>
								<div class="card-body">
									<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
										<div class="row">
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="nama" class="input-required">Nama Lengkap</label>
													<input type="text" name="nama" id="nama" class="form-control" autocomplete="off" placeholder="Masukkan Nama Lengkap" required="" minlength="1" maxlength="100" autofocus="">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="telepon" class="input-required">Telepon</label>
													<input type="text" name="telepon" id="telepon" class="form-control phone-number" autocomplete="off" placeholder="Masukkan Telepon" required="" minlength="1" maxlength="15" autofocus="">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="email" class="input-required">E-mail</label>
													<input type="email" name="email" id="email" class="form-control" autocomplete="off" placeholder="Masukkan E-mail" required="" minlength="1" maxlength="100" autofocus="">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="password" class="input-required">Kata Sandi</label>
													<input type="password" name="password" id="password" class="form-control" autocomplete="off" placeholder="Masukkan Kata Sandi" required="" minlength="1" maxlength="20" autofocus="">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="m_hak_akses_id" class="input-required">Hak Akses</label>
													<select class="form-control select2 width-100" name="m_hak_akses_id" id="m_hak_akses_id" required="">
														<option value="">Pilih Hak Akses</option>
														<?php
														$queryGet = mysqli_query($conn, "SELECT * FROM m_hak_akses WHERE id NOT IN(2, 3) AND is_deleted = 0 ORDER BY nama ASC");
														while($rowGet = allRowValidateHTML(mysqli_fetch_array($queryGet, MYSQLI_ASSOC))) {
															echo "<option value='".$rowGet['id']."'>".$rowGet['nama']."</option>";
														}
														?>
													</select>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-3 col-md-12">
												<div class="form-group">
													<label for="avatar" class="input-required">Avatar</label>
													<div id="image-preview" class="image-preview">
														<label for="image-upload" id="image-label">Pilih File</label>
														<input type="file" name="avatar" id="image-upload" required="" accept=".jpg, .jpeg, .png" title="Tidak ada file dipilih" style="max-width: 100%;" />
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
						url: "../../routes/web/pengguna.php",
						data: data,
						processData: false,
						contentType: false,
						success: function(response) {
							var result = JSON.parse(response);
							if(result.status_code == 200) {
								toastr.success(result.message);
								setTimeout(() => {
									window.location.href = '../pengguna';
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