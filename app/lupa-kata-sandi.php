<?php include '../config/autoloads.php';?>
<?php 
$userIp = getUserIP();
$waktuSekarang = date('Y-m-d H:i:s');

$batasWaktu = null;
$isDisabled = false;

$queryHistory = mysqli_query($conn, "SELECT * FROM t_history_reset_password WHERE ip = '$userIp' AND is_used = 0 AND batas_waktu >= '$waktuSekarang'");
$countHistory = mysqli_num_rows($queryHistory);

if($countHistory > 0) {
	$rowHistory = mysqli_fetch_array($queryHistory, MYSQLI_ASSOC);
	$isDisabled = true;
	$batasWaktu = date('Y-m-d H:i:s', strtotime($rowHistory['batas_waktu']));
}
?>
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
<body class="hold-transition login-page">
	<div class="login-box">
		<div class="card card-outline card-primary loading-card">
			<div class="card-header text-center">
				<a href="<?= getBaseURL() . "app/";?>" class="h1">
					<img src="../assets/img/logo/<?= $dataSEO['logo'];?>" alt="logo" width="120">
				</a>
			</div>
			<div class="card-body">
				<p class="login-box-msg">Kami akan mengirimkan sebuah tautan untuk mengatur ulang kata sandi Anda.</p>
				<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
					<div class="input-group mb-3">
						<input type="email" name="email" id="email" class="form-control" autocomplete="off" placeholder="Masukkan E-mail" required="" minlength="15" maxlength="100" autofocus="">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<button type="submit" id="myButton" class="btn btn-sm btn-primary btn-block btn-save">Kirim Tautan</button>
						</div>
					</div>
				</form>
				<p class="mt-3 mb-1">
					<a href="<?= getBaseURL() . "app/";?>">Masuk</a>
				</p>
			</div>
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

	<!-- JS Manual -->
	<script type="text/javascript">
		function countDownButton(batas_waktu) {
			$('#myButton').attr("disabled", "");

			const endTime = new Date(batas_waktu).getTime();
			const countdownInterval = setInterval(tick, 1000);

			function tick() {
				const now = new Date().getTime();
				const distance = endTime - now;

				const hours = Math.floor(distance / (1000 * 60 * 60));
				const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
				const seconds = Math.floor((distance % (1000 * 60)) / 1000);

				var text = hours.toString().padStart(2, '0') + " : " + minutes.toString().padStart(2, '0') + " : " + seconds.toString().padStart(2, '0');
				$('#myButton').text(text);

				if (distance < 0) {
					clearInterval(countdownInterval);
					$('#myButton').removeAttr("disabled");
					$('#myButton').text("Kirim Tautan");
				}
			}
		}

		<?php if($isDisabled == true) { ?> 
			countDownButton("<?= $batasWaktu;?>");
		<?php } ?>

		$("#data-form").submit(function(e) {
			e.preventDefault();

			var data = new FormData(this);
			data.append('function', 'sendLinkToEmail');

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