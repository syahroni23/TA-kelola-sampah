<?php
if(!isset($_SESSION['login'])) {
	header("Location: ".getBaseURL()."app/");
	exit();
}
if(isset($_SESSION['login'])) {
	$userID = $_SESSION['id'];
	if(isset($_SESSION['tipe']) && !empty($_SESSION['tipe'])) {
		if($_SESSION['tipe'] == "Pengguna") {
			$syntax = mysqli_query($conn, "SELECT m_pengguna.*, m_hak_akses.nama AS nama_hak_akses FROM m_pengguna LEFT JOIN m_hak_akses ON m_pengguna.m_hak_akses_id = m_hak_akses.id WHERE m_pengguna.id = '$userID'");
		}else if($_SESSION['tipe'] == "Pelanggan") {
			$syntax = mysqli_query($conn, "SELECT m_pelanggan.*, m_hak_akses.nama AS nama_hak_akses FROM m_pelanggan LEFT JOIN m_hak_akses ON m_pelanggan.m_hak_akses_id = m_hak_akses.id WHERE m_pelanggan.id = '$userID'");
		}else if($_SESSION['tipe'] == "Petugas") {
			$syntax = mysqli_query($conn, "SELECT m_petugas.*, m_hak_akses.nama AS nama_hak_akses FROM m_petugas LEFT JOIN m_hak_akses ON m_petugas.m_hak_akses_id = m_hak_akses.id WHERE m_petugas.id = '$userID'");
		}else {
			$syntax = mysqli_query($conn, "SELECT m_pengguna.*, m_hak_akses.nama AS nama_hak_akses FROM m_pengguna LEFT JOIN m_hak_akses ON m_pengguna.m_hak_akses_id = m_hak_akses.id WHERE m_pengguna.id = '$userID'");
		}
	}else {
		$syntax = mysqli_query($conn, "SELECT m_pengguna.*, m_hak_akses.nama AS nama_hak_akses FROM m_pengguna LEFT JOIN m_hak_akses ON m_pengguna.m_hak_akses_id = m_hak_akses.id WHERE m_pengguna.id = '$userID'");
	}
	$online = mysqli_fetch_array($syntax, MYSQLI_ASSOC);
}
?>
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
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('assets/css/ionicons-2.0.1/css/ionicons.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/icheck-bootstrap/icheck-bootstrap.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/toastr/toastr.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/jqvmap/jqvmap.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/overlayScrollbars/css/OverlayScrollbars.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/daterangepicker/daterangepicker.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/summernote/summernote-bs4.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/datatables-bs4/css/dataTables.bootstrap4.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/datatables-responsive/css/responsive.bootstrap4.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/datatables-buttons/css/buttons.bootstrap4.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/select2/css/select2.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/bootstrap4-duallistbox/bootstrap-duallistbox.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/bs-stepper/css/bs-stepper.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/dropzone/min/dropzone.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.css'));?>">
<!-- Template CSS -->
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('assets/css/adminlte.min.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('assets/css/custom.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('assets/css/app.css'));?>">
<link rel="stylesheet" href="<?= clearCacheFile(pathToFile('assets/css/slider-captcha.min.css'));?>">
<style>
	.margin-top-five {
		margin-top: 5px !important;
	}
	.select2-container .select2-selection--single {
		height: calc(2.25rem + 2px) !important;
	}
	.select2-container--default .select2-selection--single {
		height: calc(2.25rem + 2px) !important;
		border: 1px solid #ced4da !important;
	}
	.select2-selection__arrow {
		height: 37px !important;
	}
	.select2-container--default.select2-container--disabled .select2-selection--single {
		background-color: #e9ecef !important;
		cursor: default;
	}
</style>