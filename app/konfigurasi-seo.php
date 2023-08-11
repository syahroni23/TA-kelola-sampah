<?php include '../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('konfigurasi-seo', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$query = mysqli_query($conn, "SELECT * FROM p_konfigurasi_seo ORDER BY id ASC LIMIT 1");
$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include "layout/head.php";?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed layout-navbar-fixed">
	<div class="wrapper">
		<div class="preloader flex-column justify-content-center align-items-center">
			<img class="animation__shake" src="<?= pathToFile('assets/img/logo/'.$dataSEO['logo'].'');?>" alt="<?= $rowSeo['nama_website'];?> Logo" height="60" width="60">
		</div>

		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<?php include "layout/navbar.php";?>
		</nav>

		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<?php include "layout/aside.php";?>
		</aside>

		<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0">Konfigurasi SEO</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="beranda">Beranda</a></li>
								<li class="breadcrumb-item active">Konfigurasi SEO</li>
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
									<h3 class="card-title">
										Konfigurasi SEO
									</h3>
								</div>
								<div class="card-body">
									<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
										<input type="hidden" name="id" value="<?= $row['id'];?>">
										<div class="row">
											<div class="col-lg-6 col-md-12">
												<div class="form-group">
													<label for="author" class="input-required">Author</label>
													<input type="text" name="author" id="author" class="form-control" autocomplete="off" placeholder="Masukkan Author" required="" minlength="3" maxlength="100" autofocus="" value="<?= $row['author'];?>">
												</div>
											</div>
											<div class="col-lg-6 col-md-12">
												<div class="form-group">
													<label for="title" class="input-required">Title</label>
													<input type="text" name="title" id="title" class="form-control" autocomplete="off" placeholder="Masukkan Title" required="" minlength="3" autofocus="" value="<?= $row['title'];?>">
												</div>
											</div>
											<div class="col-lg-6 col-md-12">
												<div class="form-group">
													<label for="keyword" class="input-required">Keyword</label>
													<input type="text" name="keyword" id="keyword" class="form-control inputtags" autocomplete="off" placeholder="Masukkan Keyword" required="" autofocus="" value="<?= $row['keyword'];?>">
												</div>
											</div>
											<div class="col-lg-6 col-md-12">
												<div class="form-group">
													<label for="copyright" class="input-required">Copy Right</label>
													<input type="text" name="copyright" id="copyright" class="form-control" autocomplete="off" placeholder="Masukkan Copy Right" required="" minlength="3" autofocus="" value="<?= $row['copyright'];?>">
												</div>
											</div>
											<div class="col-lg-12 col-md-12">
												<div class="form-group">
													<label for="description" class="input-required">Description</label>
													<textarea name="description" id="description" class="form-control height-125" autocomplete="off" placeholder="Masukkan Description" rows="4" required="" autofocus=""><?= $row['description'];?></textarea>
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="publisher" class="input-required">Publisher</label>
													<input type="text" name="publisher" id="publisher" class="form-control" autocomplete="off" placeholder="Masukkan Publisher" required="" minlength="5" autofocus="" value="<?= $row['publisher'];?>">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="robots" class="input-required">Robots</label>
													<input type="text" name="robots" id="robots" class="form-control" autocomplete="off" placeholder="Masukkan Robots" required="" minlength="3" autofocus="" value="<?= $row['robots'];?>">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="url" class="input-required">URL</label>
													<input type="text" name="url" id="url" class="form-control" autocomplete="off" placeholder="Masukkan URL" required="" minlength="5" maxlength="100" autofocus="" value="<?= $row['url'];?>">
												</div>
											</div>
											<div class="col-lg-3 col-md-12">
												<div class="form-group">
													<label for="logo">Logo <small class="text-muted">(opsional)</small></label>
													<div id="image-preview" class="image-preview" style="background-image: url('../assets/img/logo/<?= $row['logo'];?>'); background-size: cover; background-position: center center;">
														<label for="image-upload" id="image-label">Ubah File</label>
														<input type="file" name="logo" id="image-upload" accept=".jpg, .jpeg, .png" title="Tidak ada file dipilih" style="max-width: 100%;" />
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
			<?php include "layout/footer.php";?>
		</footer>

		<aside class="control-sidebar control-sidebar-dark"></aside>
	</div>
	<?php include "layout/script.php";?>

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

		$(".inputtags").tagsinput('items');
	</script>

	<script type="text/javascript">
		$("#data-form").submit(function(e) {
			e.preventDefault();

			var data = new FormData(this);
			data.append('function', 'updateKonfigurasiSEO');

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
						url: "../routes/web/site.php",
						data: data,
						processData: false,
						contentType: false,
						success: function(response) {
							var result = JSON.parse(response);
							if(result.status_code == 200) {
								toastr.success(result.message);
								setTimeout(() => {
									location.reload();
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