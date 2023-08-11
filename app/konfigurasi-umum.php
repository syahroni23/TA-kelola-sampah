<?php include '../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('konfigurasi-umum', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
?>
<?php
$query = mysqli_query($conn, "SELECT * FROM p_konfigurasi_umum ORDER BY id ASC LIMIT 1");
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
							<h1 class="m-0">Konfigurasi Umum</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="beranda">Beranda</a></li>
								<li class="breadcrumb-item active">Konfigurasi Umum</li>
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
										Konfigurasi Umum
									</h3>
								</div>
								<div class="card-body">
									<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
										<input type="hidden" name="id" value="<?= $row['id'];?>">
										<div class="row">
											<div class="col-lg-3 col-md-12">
												<div class="form-group">
													<label for="nama_website" class="input-required">Nama Website</label>
													<input type="text" name="nama_website" id="nama_website" class="form-control" autocomplete="off" placeholder="Masukkan Nama Website" required="" minlength="5" maxlength="100" autofocus="" value="<?= $row['nama_website'];?>">
												</div>
											</div>
											<div class="col-lg-3 col-md-12">
												<div class="form-group">
													<label for="telepon" class="input-required">Telepon</label>
													<input type="text" name="telepon" id="telepon" class="form-control phone-number" autocomplete="off" placeholder="Masukkan Telepon" required="" minlength="1" maxlength="15" autofocus="" value="<?= $row['telepon'];?>">
												</div>
											</div>
											<div class="col-lg-3 col-md-12">
												<div class="form-group">
													<label for="email" class="input-required">E-mail</label>
													<input type="email" name="email" id="email" class="form-control" autocomplete="off" placeholder="Masukkan E-mail" required="" minlength="7" maxlength="100" autofocus="" value="<?= $row['email'];?>">
												</div>
											</div>
											<div class="col-lg-3 col-md-12">
												<div class="form-group">
													<label for="nama_alamat" class="input-required">Nama Alamat</label>
													<input type="text" name="nama_alamat" id="nama_alamat" class="form-control" autocomplete="off" placeholder="Masukkan Nama Alamat" required="" minlength="5" maxlength="100" autofocus="" value="<?= $row['nama_alamat'];?>">
												</div>
											</div>
											<div class="col-lg-6 col-md-12">
												<div class="form-group">
													<label for="alamat" class="input-required">Alamat <i title="Drag icon yang ada di peta jika ingin mengubah alamat" class='fas fa-exclamation-circle' style='cursor: pointer;'></i></label>
													<input type="text" name="alamat" id="alamat" class="form-control" autocomplete="off" placeholder="Masukkan Alamat" required="" minlength="1" value="<?= $row['alamat'];?>" readonly="">
													<input type="hidden" name="place_id" id="place_id" class="form-control" autocomplete="off" value="<?= $row['place_id'];?>">
												</div>
											</div>
											<div class="col-lg-3 col-md-12">
												<div class="form-group">
													<label for="latitude" class="input-required">Latitude</label>
													<input type="text" name="latitude" id="latitude" class="form-control" autocomplete="off" placeholder="Masukkan Latitude" required="" readonly="" value="<?= $row['latitude'];?>">
												</div>
											</div>
											<div class="col-lg-3 col-md-12">
												<div class="form-group">
													<label for="longitude" class="input-required">Longitude</label>
													<input type="text" name="longitude" id="longitude" class="form-control" autocomplete="off" placeholder="Masukkan Longitude" required="" readonly="" value="<?= $row['longitude'];?>">
												</div>
											</div>
											<div class="col-lg-12 col-md-12">
												<div class="form-group">
													<div id="simple-map" style="height: 400px;"></div>
												</div>
											</div>
											<div class="col-lg-6 col-md-12">
												<div class="form-group">
													<label for="wablas_api_key" class="input-required">Wablas Api Key</label>
													<input type="text" name="wablas_api_key" id="wablas_api_key" class="form-control" autocomplete="off" placeholder="Masukkan Wablas Api Key" required="" autofocus="" value="<?= $row['wablas_api_key'];?>">
												</div>
											</div>
											<div class="col-lg-6 col-md-12">
												<div class="form-group">
													<label for="wablas_domain_url" class="input-required">Wablas Domain URL</label>
													<input type="text" name="wablas_domain_url" id="wablas_domain_url" class="form-control" autocomplete="off" placeholder="Masukkan Wablas Domain URL" required="" autofocus="" value="<?= $row['wablas_domain_url'];?>">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="telegram_token" class="input-required">Telegram Token</label>
													<input type="text" name="telegram_token" id="telegram_token" class="form-control" autocomplete="off" placeholder="Masukkan Telegram Token" required="" autofocus="" value="<?= $row['telegram_token'];?>">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="telegram_chat_id" class="input-required">Telegram Chat ID</label>
													<input type="text" name="telegram_chat_id" id="telegram_chat_id" class="form-control" autocomplete="off" placeholder="Masukkan Telegram Chat ID" required="" autofocus="" value="<?= $row['telegram_chat_id'];?>">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="telegram_thread_id" class="input-required">Telegram Thread ID</label>
													<input type="text" name="telegram_thread_id" id="telegram_thread_id" class="form-control" autocomplete="off" placeholder="Masukkan Telegram Thread ID" required="" autofocus="" value="<?= $row['telegram_thread_id'];?>">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="is_maintenance" class="input-required">Status Maintenance</label>
													<select class="form-control select2" name="is_maintenance" id="is_maintenance" required="">
														<option value="">Pilih Status Maintenance</option>
														<option value="0" <?= isSelected($row['is_maintenance'], 0);?>>Tidak</option>
														<option value="1" <?= isSelected($row['is_maintenance'], 1);?>>Ya</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="biaya_pengambilan" class="input-required">Biaya Pengambilan</label>
													<input type="text" name="biaya_pengambilan" id="biaya_pengambilan" class="form-control currency" autocomplete="off" placeholder="Masukkan Biaya Pengambilan" required="" minlength="1" maxlength="14" autofocus="" value="<?= $row['biaya_pengambilan'];?>">
												</div>
											</div>
											<div class="col-lg-4 col-md-12">
												<div class="form-group">
													<label for="metode_reset_password" class="input-required">Metode Reset Password</label>
													<select class="form-control select2" name="metode_reset_password" id="metode_reset_password" required="">
														<option value="">Pilih Metode Reset Password</option>
														<option value="E-mail" <?= isSelected($row['metode_reset_password'], "E-mail");?>>E-mail</option>
														<option value="Whatsapp" <?= isSelected($row['metode_reset_password'], "Whatsapp");?>>Whatsapp</option>
													</select>
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

		function updateMarkerPosition(latLng) {
			document.getElementById('latitude').value = [latLng.lat()]
			document.getElementById('longitude').value = [latLng.lng()]
		}

		function initMap() {
			var latitude_ = $('#latitude').val();
			var longitude_ = $('#longitude').val();

			if(typeof latitude_ != "undefined" && typeof longitude_ != "undefined" && latitude_ != "" && longitude_ != "" && latitude_ != "-" && longitude_ != "-") {
				defaultLatLong = {
					lat: parseFloat(latitude_),
					lng: parseFloat(longitude_)
				};
			} else {
				defaultLatLong = {
					lat: 40.7127753,
					lng: -74.0059728
				};
			}

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

			var input = document.getElementById('alamat');
			var autocomplete = new google.maps.places.Autocomplete(input);

			autocomplete.bindTo('bounds', map);

			var marker = new google.maps.Marker({
				map: map,
				position: defaultLatLong,
				draggable: true,
				clickable: true
			});

			if( (typeof latitude_ == "undefined" && typeof longitude_ == "undefined") || (latitude_ == "" && longitude_ == "") || (latitude_ == "-" && longitude_ == "-") ) {
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function(position) {
						initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
						map.setCenter(initialLocation);
						marker.setPosition(initialLocation);

						var geocoder = new google.maps.Geocoder;

						latitude = position.coords.latitude;
						longitude = position.coords.longitude
						var latlng = {lat: parseFloat(latitude), lng: parseFloat(longitude)};

						geocoder.geocode({'location': latlng}, function(results, status) {
							if (status === google.maps.GeocoderStatus.OK) {
								if (results[0]) {
									$('#place_id').val(results[0].place_id);
									$('#alamat').val(results[0].formatted_address);
								} else {
									swal('Peringatan', 'Alamat tidak ditemukan', 'error');
								}
							} else {
								swal('Peringatan', 'Gagal mendapatkan geocoder: ' + status, 'error');
							}
						});

						$('#latitude').val(position.coords.latitude);
						$('#longitude').val(position.coords.longitude);
					});
				}
			}

			google.maps.event.addListener(marker, 'dragend', function(marker) {
				updateMarkerPosition(marker.latLng);
				var latLng = marker.latLng;
				currentLatitude = latLng.lat();
				currentLongitude = latLng.lng();
				var latlng = {
					lat: currentLatitude,
					lng: currentLongitude
				};
				var geocoder = new google.maps.Geocoder;
				geocoder.geocode({
					'location': latlng
				}, function(results, status) {
					if (status === 'OK') {
						if (results[0]) {
							$('#place_id').val(results[0].place_id);
							input.value = results[0].formatted_address;
						} else {
							swal('Peringatan', 'Alamat tidak ditemukan', 'error');
						}
					} else {
						swal('Peringatan', 'Gagal mendapatkan geocoder: ' + status, 'error');
					}
				});
			});

			autocomplete.addListener('place_changed', function() {
				var place = autocomplete.getPlace();
				if (!place.geometry) {
					return;
				}
				if (place.geometry.viewport) {
					map.fitBounds(place.geometry.viewport);
				} else {
					map.setCenter(place.geometry.location);
				}

				marker.setPosition(place.geometry.location);

				updateMarkerPosition(place.geometry.location);

				$('#place_id').val(place.place_id);

				currentLatitude = place.geometry.location.lat();
				currentLongitude = place.geometry.location.lng();
			});
		}

		$("#data-form").submit(function(e) {
			e.preventDefault();

			var data = new FormData(this);
			data.append('function', 'updateKonfigurasiUmum');

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
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1MgLuZuyqR_OGY3ob3M52N46TDBRI_9k&libraries=places&callback=initMap&sensor=false"></script>
</body>
</html>