<?php include '../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('perhitungan-tsp', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$queryUmum = mysqli_query($conn, "SELECT * FROM p_konfigurasi_umum ORDER BY id ASC LIMIT 1");
$rowUmum = allRowValidateHTML(mysqli_fetch_array($queryUmum, MYSQLI_ASSOC));

$latitudeUmum = $rowUmum['latitude'];
$longitudeUmum = $rowUmum['longitude'];

$tanggal = isset($_GET['tanggal']) && !empty($_GET['tanggal']) ? date('Y-m-d', strtotime($_GET['tanggal'])) : date('Y-m-d');

$arrData = $arrNama = $arrDataBobot = $arrMapJS = $arrDataUrutan = [];
$queryGet = mysqli_query($conn, "SELECT t_penarikan_sampah.*, m_pelanggan.nama AS nama_pelanggan, m_pelanggan.latitude, m_pelanggan.longitude, CONCAT(ROUND(ST_Distance_Sphere(POINT(".$longitudeUmum.", ".$latitudeUmum."), POINT(m_pelanggan.longitude, m_pelanggan.latitude)), 2), 'm') AS jarak, ROUND(ST_Distance_Sphere(POINT(".$longitudeUmum.", ".$latitudeUmum."), POINT(m_pelanggan.longitude, m_pelanggan.latitude)), 2) AS jarak_desimal FROM t_penarikan_sampah LEFT JOIN m_pelanggan ON t_penarikan_sampah.m_pelanggan_id = m_pelanggan.id WHERE t_penarikan_sampah.tanggal = '$tanggal' ORDER BY t_penarikan_sampah.created_at ASC");
while($rowGet = mysqli_fetch_array($queryGet, MYSQLI_ASSOC)) {
	$arrData[] = $rowGet;
}

$arrNama[0]['nama'] = $rowUmum['nama_alamat'];
$arrNama[0]['kode'] = "KLS/PS/00000";
$arrNama[0]['gabungan'] = $rowUmum['nama_alamat']."|KLS/PS/00000";
$arrNama[0]['latitude'] = $rowUmum['latitude'];
$arrNama[0]['longitude'] = $rowUmum['longitude'];

$noIndex = 1;
foreach ($arrData as $keys => $values) {
	$values = (array) $values;

	$arrNama[$noIndex]['nama'] = $values['nama_pelanggan'];
	$arrNama[$noIndex]['kode'] = $values['kode'];
	$arrNama[$noIndex]['gabungan'] = $values['nama_pelanggan'] . "|" . $values['kode'];
	$arrNama[$noIndex]['latitude'] = $values['latitude'];
	$arrNama[$noIndex]['longitude'] = $values['longitude'];
	$noIndex++;
}

foreach ($arrNama as $keys => $values) {
	if($values['nama'] == $rowUmum['nama_alamat']) {
		$arrNama[$keys]['jarak'][] = 0;
		$arrNama[$keys]['peta'][] = [
			'longitude'	=>	(float) $values['longitude'],
			'latitude'	=>	(float) $values['latitude']
		];
		foreach ($arrData as $k => $v) {
			$arrNama[$keys]['jarak'][] = ROUND($v['jarak_desimal'], 2);
			$arrNama[$keys]['peta'][] = [
				'longitude'	=>	(float) $v['longitude'],
				'latitude'	=>	(float) $v['latitude']
			];
		}
	}else {
		foreach ($arrData as $k => $v) {
			$gabunganPelanggan = $v['nama_pelanggan'] . "|" . $v['kode'];
			if($values['gabungan'] == $gabunganPelanggan) {
				$arrNama[$keys]['jarak'][] = ROUND($v['jarak_desimal'], 2);
				$arrNama[$keys]['peta'][] = [
					'longitude'	=>	(float) $v['longitude'],
					'latitude'	=>	(float) $v['latitude']
				];
			}
		}
	}
}

foreach ($arrNama as $keys => $values) {
	if($keys > 0) {
		for($i = 1; $i <= count($arrNama[0]['jarak']) - 1; $i++) {
			$arrNama[$keys]['jarak'][] =  ROUND(($arrNama[$keys]['jarak'][0] - $arrNama[0]['jarak'][$i]), 2);
		}
	}
}

foreach ($arrNama as $keys => $values) {
	$arrDataBobot[] = [
		'nama'		=>	$values['nama'],
		'jarak'		=>	$values['jarak'],
		'latitude'	=>	(float) $values['latitude'],
		'longitude'	=>	(float) $values['longitude']
	];
}

foreach ($arrDataBobot as $keys => $values) {
	$arrDataBobot[$keys]['hasil_jarak'] = ROUND((array_sum($values['jarak']) - $values['jarak'][0]), 2);
}

$arrDataMap = $arrDataBobot;

$jarakRank = array_column($arrDataMap, 'hasil_jarak');
array_multisort($jarakRank, SORT_ASC, $arrDataMap);

foreach ($arrDataMap as $keys => $values) {
	if($values['nama'] == $rowUmum['nama_alamat']) {
		$arrMapJS[0] = [
			'lat'	=>	$values['latitude'],
			'lng'	=>	$values['longitude']
		];
	}
}
foreach ($arrDataMap as $keys => $values) {
	if($values['nama'] != $rowUmum['nama_alamat']) {
		$arrMapJS[] = [
			'lat'	=>	$values['latitude'],
			'lng'	=>	$values['longitude']
		];
	}
}

foreach ($arrDataMap as $keys => $values) {
	if($values['nama'] == $rowUmum['nama_alamat']) {
		$arrDataUrutan[0] = [
			'lat'			=>	$values['latitude'],
			'lng'			=>	$values['longitude'],
			'nama'			=>	$values['nama'],
			'hasil_jarak'	=>	$values['hasil_jarak']
		];
	}
}
foreach ($arrDataMap as $keys => $values) {
	if($values['nama'] != $rowUmum['nama_alamat']) {
		$arrDataUrutan[] = [
			'lat'			=>	$values['latitude'],
			'lng'			=>	$values['longitude'],
			'nama'			=>	$values['nama'],
			'hasil_jarak'	=>	$values['hasil_jarak']
		];
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php include "layout/head.php";?>
	<style>
		#sidebar {
			flex-basis: 15rem;
			flex-grow: 1;
			padding: 1rem;
			max-width: 30rem;
			height: 100%;
			box-sizing: border-box;
			overflow: auto;
		}
		#floating-panel {
			position: absolute;
			top: 10px;
			left: 25%;
			z-index: 5;
			background-color: #fff;
			padding: 5px;
			border: 1px solid #999;
			text-align: center;
			font-family: "Roboto", "sans-serif";
			line-height: 30px;
			padding-left: 10px;
		}
		#floating-panel {
			background-color: #fff;
			border: 0;
			border-radius: 2px;
			box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
			margin: 10px;
			padding: 0 0.5em;
			font: 400 18px Roboto, Arial, sans-serif;
			overflow: hidden;
			padding: 5px;
			font-size: 14px;
			text-align: center;
			line-height: 30px;
			height: auto;
		}
		#map {
			flex: auto;
		}
		#sidebar {
			flex: 0 1 auto;
			padding: 0;
		}
		#sidebar > div {
			padding: 0.5rem;
		}
	</style>
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
							<h1 class="m-0">Perhitungan TSP</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="beranda">Beranda</a></li>
								<li class="breadcrumb-item active">Perhitungan TSP</li>
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
										Perhitungan TSP
									</h3>
								</div>
								<div class="card-body">
									<form role="form" action="" method="GET" enctype="multipart/form-data" id="data-form">
										<div class="row">
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="tanggal" class="input-required">Tanggal Transaksi Pelanggan</label>
													<input type="date" name="tanggal" id="tanggal" class="form-control" autocomplete="off" placeholder="Masukkan Tanggal" required="" autofocus="" value="<?= $tanggal;?>">
												</div>
											</div>
										</div>
										<?php if(isset($_GET['tanggal']) && !empty($_GET['tanggal'])) { ?>
											<div class="row mt-4">
												<div class="col-lg-12 col-md-12">
													<h2 class="text-center">Perhitungan TSP (<?= formatDateIndonesia(date('d F Y', strtotime($tanggal)));?>)</h2>
													<p class="text-center">Jumlah Data Jarak Pelanggan ke <?= $rowUmum['nama_alamat'];?>.</p>
												</div>
												<div class="col-lg-12 col-md-12 mt-4">
													<div class="table-responsive">
														<table class="table table-striped table-hover" style="width: 100%;">
															<thead>
																<tr>
																	<th>No</th>
																	<th>Nama Pelanggan</th>
																	<th>Jarak ke <?= $rowUmum['nama_alamat'];?></th>
																</tr>
															</thead>
															<tbody>
																<?php
																$noUrutPelanggan = 1;
																foreach ($arrData as $keys => $values) {
																	$values = (array) $values;
																	?>
																	<tr>
																		<td><?= $noUrutPelanggan++;?></td>
																		<td><?= $values['nama_pelanggan'];?></td>
																		<td><?= $values['jarak'];?></td>
																	</tr>
																<?php } ?>
															</tbody>
														</table>
													</div>
												</div>
												<div class="col-lg-12 col-md-12 mt-4">
													<p class="text-center">Bobot</p>
												</div>
												<div class="col-lg-12 col-md-12 mt-4">
													<div class="table-responsive">
														<table class="table table-striped table-hover" style="width: 100%;">
															<thead>
																<tr>
																	<th>No</th>
																	<th>Nama</th>
																	<?php foreach ($arrDataBobot as $keys => $values) { ?>
																		<th><?= $values['nama'];?></th>
																	<?php } ?>
																	<th>Total Jarak</th>
																</tr>
															</thead>
															<tbody>
																<?php
																$noUrutBobot = 1;
																foreach ($arrDataBobot as $keys => $values) {
																	?>
																	<tr>
																		<td><?= $noUrutBobot++;?></td>
																		<td><?= $values['nama'];?></td>
																		<?php foreach ($values['jarak'] as $k => $v) { ?>
																			<td><?= $v;?></td>
																		<?php } ?>
																		<td><?= $values['hasil_jarak'];?></td>
																	</tr>
																<?php } ?>
															</tbody>
														</table>
													</div>
												</div>
												<div class="col-lg-12 col-md-12 mt-4">
													<p class="text-center">Hasil TSP</p>
												</div>
												<div class="col-lg-12 col-md-12 mt-4">
													<div class="form-group">
														<div id="simple-map" style="height: 400px;"></div>
													</div>
												</div>

											</div>
											<div class="row mt-4">
												<div class="col-lg-6 col-md-12">
													<h4>Keterangan</h4>
													<p>Jadi, urutan petugas mendatangi rumah pelanggan adalah sebagai berikut :</p>
													<ol type="number">
														<?php foreach ($arrDataUrutan as $keys => $values) { ?>
															<?php if($values['nama'] != $rowUmum['nama_alamat']) { ?>
																<li><?= $values['nama'];?> dengan jarak <?= $values['hasil_jarak'];?>m.</li>
															<?php } ?>
														<?php } ?>
													</ol>
												</div>
												<div class="col-lg-6 col-md-12">
													<div style="float: right;" id="sidebar"></div>
												</div>
											</div>
										<?php } ?>
										<div class="row">
											<div class="col-lg-12 col-md-12" align="right">
												<div class="form-group">
													<button type="submit" class="btn btn-sm btn-primary btn-save">
														<i class="fas fa-save"></i>&nbsp; Proses
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
		var locations = <?= json_encode($arrMapJS);?>;

		function initMap() {
			defaultLatLong = {
				lat: 40.7127753,
				lng: -74.0059728
			};

			var map = new google.maps.Map(document.getElementById('simple-map'), {
				center: defaultLatLong,
				zoom: 15,
				mapTypeId: "roadmap",
				panControl: true,
				zoomControl: true,
				mapTypeControl: true,
				scaleControl: true,
				streetViewControl: true,
				overviewMapControl: true,
				rotateControl: true,
				fullscreenControl: true
			});

			var directionsService = new google.maps.DirectionsService();
			var directionsRenderer = new google.maps.DirectionsRenderer({
				map: map
			});
			directionsRenderer.setPanel(document.getElementById("sidebar"));

			var waypoints = [];

			for (var i = 1; i < locations.length - 1; i++) {
				waypoints.push({
					location: locations[i],
					stopover: true
				});
			}

			var request = {
				origin: locations[0],
				destination: locations[locations.length - 1],
				optimizeWaypoints: false,
				waypoints: waypoints,
				travelMode: google.maps.TravelMode.DRIVING,
				language: 'id'
			};

			directionsService.route(request, function(result, status) {
				if (status === google.maps.DirectionsStatus.OK) {
					directionsRenderer.setDirections(result);
				}
			});
		}
	</script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1MgLuZuyqR_OGY3ob3M52N46TDBRI_9k&libraries=places&callback=initMap&sensor=false"></script>
</body>
</html>