<?php include '../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('beranda', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
$dashboard = getDashboardData($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include "layout/head.php";?>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed layout-navbar-fixed">
	<div class="wrapper">
		<div class="preloader flex-column justify-content-center align-items-center">
			<img class="animation__shake" src="<?= pathToFile('assets/img/logo/putiki.png?'.$dataSEO['logo'].'');?>" alt="<?= $rowSeo['nama_website'];?> Logo" height="150" width="150">
		</div>

		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<?php include "layout/navbar.php";?>
		</nav>

		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<?php include "layout/aside.php";?>
		</aside>

		<div class="content-wrapper">
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0">Beranda</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item active">Beranda</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
			<section class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-lg-3 col-md-12">
							<div class="small-box bg-info">
								<div class="inner">
									<h3><?= $dashboard['totalBank'];?></h3>
									<p>Bank</p>
								</div>
								<div class="icon">
									<i class="ion ion-bag"></i>
								</div>
								<?php if($_SESSION['tipe'] == "Pengguna") { ?>
									<a href="<?= getBaseURL();?>app/bank" class="small-box-footer">
										Selengkapnya <i class="fas fa-arrow-circle-right"></i>
									</a>
								<?php } ?>
							</div>
						</div>
						<div class="col-lg-3 col-md-12">
							<div class="small-box bg-success">
								<div class="inner">
									<h3><?= $dashboard['totalPelanggan'];?></h3>
									<p>Pelanggan</p>
								</div>
								<div class="icon">
									<i class="ion ion-stats-bars"></i>
								</div>
								<?php if($_SESSION['tipe'] == "Pengguna") { ?>
									<a href="<?= getBaseURL();?>app/pelanggan" class="small-box-footer">
										Selengkapnya <i class="fas fa-arrow-circle-right"></i>
									</a>
								<?php } ?>
							</div>
						</div>
						<div class="col-lg-3 col-md-12">
							<div class="small-box bg-warning">
								<div class="inner">
									<h3><?= $dashboard['totalPetugas'];?></h3>
									<p>Petugas</p>
								</div>
								<div class="icon">
									<i class="ion ion-person-add"></i>
								</div>
								<?php if($_SESSION['tipe'] == "Pengguna") { ?>
									<a href="<?= getBaseURL();?>app/petugas" class="small-box-footer">
										Selengkapnya <i class="fas fa-arrow-circle-right"></i>
									</a>
								<?php } ?>
							</div>
						</div>
						<div class="col-lg-3 col-md-12">
							<div class="small-box bg-danger">
								<div class="inner">
									<h3><?= $dashboard['totalPenarikanSampah'];?></h3>
									<p>Penarikan Sampah</p>
								</div>
								<div class="icon">
									<i class="ion ion-pie-graph"></i>
								</div>
								<?php if($_SESSION['tipe'] == "Pengguna") { ?>
									<a href="<?= getBaseURL();?>app/penarikan-sampah" class="small-box-footer">
										Selengkapnya <i class="fas fa-arrow-circle-right"></i>
									</a>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php if($_SESSION['tipe'] == "Pengguna") { ?>
						<div class="row">
							<div class="col-lg-8 col-md-12">
								<div class="card">
									<div class="card-header">
										<div class="card-title">Grafik Keuangan Bulan <?= formatDateIndonesia(date('F Y'));?></div>
										<div class="card-tools">
											<button type="button" class="btn btn-tool" data-card-widget="collapse">
												<i class="fas fa-minus"></i>
											</button>
										</div>
									</div>
									<div class="card-body p-0">
										<div class="chart">
											<canvas id="myChart" style="min-height: 563px; height: 563px; max-height: 563px; max-width: 100%;"></canvas>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-4 col-md-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Daftar Pengguna Baru</h3>
										<div class="card-tools">
											<button type="button" class="btn btn-tool" data-card-widget="collapse">
												<i class="fas fa-minus"></i>
											</button>
										</div>
									</div>
									<div class="card-body p-0">
										<ul class="products-list product-list-in-card pl-2 pr-2">
											<?php foreach ($dashboard['dataPenggunaBaru'] as $keys => $values) {?>
												<?php 
												if($values['tipe'] == "Pelanggan") {
													$badge = "<span class='badge badge-success float-right'>".$values['tipe']."</span>";
												}else if($values['tipe'] == "Petugas") {
													$badge = "<span class='badge badge-warning float-right'>".$values['tipe']."</span>";
												}else {
													$badge = "<span class='badge badge-danger float-right'>".$values['tipe']."</span>";
												}
												?>
												<li class="item">
													<div class="product-img">
														<img src="<?= pathToFile('assets/img/avatar/'.$values['avatar'].'');?>" alt="Avatar" class="img-size-50">
													</div>
													<div class="product-info">
														<a href="javascript:void(0)" class="product-title"><?= $values['nama'];?><?= $badge;?></a>
														<span class="product-description">
															Waktu Pendaftaran: <?= formatDateIndonesia(date('d F Y, H:i', strtotime($values['created_at'])));?>
														</span>
													</div>
												</li>
											<?php } ?>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
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
		$.ajax({
			url: '../routes/web/site.php',
			type: 'POST',
			data: {function: 'getChartDashboard'},
			success: function(results){
				var result = JSON.parse(results);

				var ctx = $('#myChart').get(0).getContext('2d');
				var myChart = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: result.data.listName,
						datasets: [{
							label: 'Pemasukan',
							data: result.data.listCountPemasukan,
							backgroundColor: 'rgba(60,141,188,0.9)',
							borderColor: 'rgba(60,141,188,0.8)',
							pointRadius: false,
							pointColor: '#3b8bba',
							pointStrokeColor: 'rgba(60,141,188,1)',
							pointHighlightFill: '#fff',
							pointHighlightStroke: 'rgba(60,141,188,1)'
						},
						{
							label: 'Pengeluaran',
							data: result.data.listCountPengeluaran,
							backgroundColor: 'rgba(210, 214, 222, 1)',
							borderColor: 'rgba(210, 214, 222, 1)',
							pointRadius: false,
							pointColor: 'rgba(210, 214, 222, 1)',
							pointStrokeColor: '#c1c7d1',
							pointHighlightFill: '#fff',
							pointHighlightStroke: 'rgba(220,220,220,1)',
						},
						]
					},
					options: {
						tooltips: {
							callbacks: {
								label: function(tooltipItem, data) {
									var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
									value = value.toString();
									value = value.split(/(?=(?:...)*$)/);
									value = value.join(',');
									return data.datasets[tooltipItem.datasetIndex].label + ": " + value;
								}
							}
						},
						scales: {
							yAxes: [{
								gridLines: {
									drawBorder: false,
									color: '#f2f2f2',
								},
								ticks: {
									userCallback: function(value, index, values) {
										value = value.toString();
										value = value.split(/(?=(?:...)*$)/);
										value = value.join(',');
										return value;
									}
								}
							}],
							xAxes: [{
								gridLines: {
									display: false
								}
							}]
						},
						responsive: true,
						maintainAspectRatio: false,
						datasetFill: false
					}
				});
			}
		});
	</script>
</body>
</html>