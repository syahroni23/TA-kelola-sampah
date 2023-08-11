<?php include '../config/autoloads.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
	<meta content="<?= $dataSEO['description'];?>" name="description">
	<meta content="<?= $dataSEO['keyword'];?>" name="keywords">
	<meta content="<?= $dataSEO['author'];?>" name="author">
	<meta content="<?= $dataSEO['robots'];?>" name="robots">
	<meta content="article" property="og:type">
	<meta content="<?= $dataSEO['url'];?>" property="og:url">
	<meta content="<?= $dataSEO['title'];?>" property="og:title">
	<meta content="<?= $dataSEO['url'];?>assets/img/logo/<?= $dataSEO['logo'];?>" property="og:image">
	<meta content="image/jpeg" property="og:image:type">
	<meta content="<?= $dataSEO['title'];?>" property="og:image:alt">
	<meta content="<?= $dataSEO['title'];?>" property="og:image:title">
	<meta content="800" property="og:image:width">
	<meta content="800" property="og:image:height">
	<meta content="<?= $dataSEO['description'];?>" property="og:description">
	<meta content="summary_large_image" name="twitter:card">
	<meta content="<?= $dataSEO['description'];?>" name="twitter:description">
	<meta content="<?= $dataSEO['url'];?>assets/img/logo/<?= $dataSEO['logo'];?>" name="twitter:image">
	<meta content="<?= $dataSEO['url'];?>assets/img/logo/<?= $dataSEO['logo'];?>" name="twitter:image:src">
	<meta content="<?= $dataSEO['title'];?>" name="twitter:title">
	<title><?= $dataSEO['title'];?></title>
	<link href="<?= $dataSEO['url'];?>assets/img/logo/<?= $dataSEO['logo'];?>" rel="image_src">
	<link href="<?= $dataSEO['url'];?>" rel="canonical">
	<link rel="shortcut icon" type="image/x-icon" sizes="96x96" href="<?= pathToFile('assets/img/logo/'.$dataSEO['logo']);?>">
	<!-- General CSS Files -->
	<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('assets/fonts/sourcesanspro.css'));?>">
	<!-- CSS Libraries -->
	<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/fontawesome-free/css/all.min.css'));?>">
	<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/icheck-bootstrap/icheck-bootstrap.min.css'));?>">
	<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css'));?>">
	<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/toastr/toastr.min.css'));?>">
	<!-- Template CSS -->
	<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('assets/css/adminlte.min.css'));?>">
	<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('assets/css/custom.css'));?>">
</head>
<body class="hold-transition lockscreen">
	<div class="lockscreen-wrapper">
		<div class="lockscreen-logo">
			<a href="<?= getBaseURL() . "app/";?>"><?= $rowSeo['nama_website'];?></a>
		</div>
		<div class="lockscreen-name">500</div>
		<div class="lockscreen-item">
			<div class="lockscreen-image">
				<img src="../assets/img/logo/<?= $dataSEO['logo'];?>" alt="logo" width="120">
			</div>
			<form class="lockscreen-credentials" action="">
				<div class="input-group">
					<input type="text" class="form-control" placeholder="Cari Sesuatu">
					<div class="input-group-append">
						<button type="button" class="btn">
							<i class="fas fa-arrow-right text-muted"></i>
						</button>
					</div>
				</div>
			</form>
		</div>
		<div class="help-block text-center">
			Oops! Terjadi masalah
		</div>
		<div class="text-center">
			<a href="<?= getBaseURL() . "app/";?>">Kembali ke Halaman Masuk</a>
		</div>
		<div class="lockscreen-footer text-center">
			Hak Cipta &copy; <b><a href="<?= getBaseURL() . "app/";?>" class="text-black"><?= $dataSEO['copyright'];?></a></b><br>
		</div>
	</div>
	
	<!-- JS Libraries -->
	<script src="<?= clearCacheFile(pathToFile('vendor/jquery/jquery.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/jquery-ui/jquery-ui.min.js'));?>"></script>
	<script>
		$.widget.bridge('uibutton', $.ui.button)
	</script>
	<script src="<?= clearCacheFile(pathToFile('vendor/bootstrap/js/bootstrap.bundle.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/sweetalert2/sweetalert2.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/toastr/toastr.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/cleave.js/dist/cleave.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/cleave.js/dist/addons/cleave-phone.us.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/cleave.js/dist/addons/cleave-phone.id.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/chart.js/Chart.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/sparklines/sparkline.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/jqvmap/jquery.vmap.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/jqvmap/maps/jquery.vmap.usa.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/jquery-knob/jquery.knob.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/moment/moment.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/daterangepicker/daterangepicker.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/summernote/summernote-bs4.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/datatables/jquery.dataTables.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/datatables-bs4/js/dataTables.bootstrap4.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/datatables-responsive/js/dataTables.responsive.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/datatables-responsive/js/responsive.bootstrap4.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/datatables-buttons/js/dataTables.buttons.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/datatables-buttons/js/buttons.bootstrap4.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/datatables-buttons/js/buttons.html5.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/datatables-buttons/js/buttons.print.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/datatables-buttons/js/buttons.colVis.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('vendor/select2/js/select2.full.min.js'));?>"></script>
	<!-- General JS Files -->
	<script src="<?= clearCacheFile(pathToFile('assets/js/adminlte.min.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('assets/js/custom.js'));?>"></script>
	<script src="<?= clearCacheFile(pathToFile('assets/js/app.js'));?>"></script>
</body>
</html>