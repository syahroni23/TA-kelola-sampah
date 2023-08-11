<?php
include '../../config/autoloads.php';

$queryUmum = mysqli_query($conn, "SELECT * FROM p_konfigurasi_umum ORDER BY id ASC LIMIT 1");
$rowUmum = allRowValidateHTML(mysqli_fetch_array($queryUmum, MYSQLI_ASSOC));

$latitudeUmum = $rowUmum['latitude'];
$longitudeUmum = $rowUmum['longitude'];

$id = $_POST['id'];
$query = mysqli_query($conn, "SELECT m_petugas.*, CONCAT(ROUND(ST_Distance_Sphere(POINT(".$longitudeUmum.", ".$latitudeUmum."), POINT(m_petugas.longitude, m_petugas.latitude)), 2), 'm') AS jarak FROM m_petugas WHERE m_petugas.id = '$id'");
$row = mysqli_fetch_array($query);

$latitudePelanggan = $row['latitude'];
$longitudePelanggan = $row['longitude'];

$jarakDitentukan = calculateDistance($longitudeUmum, $latitudeUmum, $longitudePelanggan, $latitudePelanggan);
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<?php if( issetEmpty($row['latitude']) && issetEmpty($row['longitude']) && ($row['latitude'] != "" && $row['longitude'] != "") && ($row['latitude'] != "-" && $row['longitude'] != "-") ) { ?>
			<input type="hidden" name="latitude_umum" id="latitude_umum" value="<?= $rowUmum['latitude'];?>">
			<input type="hidden" name="longitude_umum" id="longitude_umum" value="<?= $rowUmum['longitude'];?>">
			<input type="hidden" name="nama_alamat_umum" id="nama_alamat_umum" value="<?= $rowUmum['nama_alamat'];?>">
			<div class="row">
				<div class="col-lg-6 col-md-12">
					<div class="form-group">
						<label for="alamat">Alamat</label>
						<input type="text" name="alamat" id="alamat" class="form-control" autocomplete="off" placeholder="Masukkan Alamat" disabled="" minlength="1" value="<?= $row['alamat'];?>">
						<input type="hidden" name="place_id" id="place_id" class="form-control" autocomplete="off" value="<?= $row['place_id'];?>">
					</div>
				</div>
				<div class="col-lg-3 col-md-12">
					<div class="form-group">
						<label for="latitude">Latitude</label>
						<input type="text" name="latitude" id="latitude" class="form-control" autocomplete="off" placeholder="Masukkan Latitude" disabled="" value="<?= $row['latitude'];?>">
					</div>
				</div>
				<div class="col-lg-3 col-md-12">
					<div class="form-group">
						<label for="longitude">Longitude</label>
						<input type="text" name="longitude" id="longitude" class="form-control" autocomplete="off" placeholder="Masukkan Longitude" disabled="" value="<?= $row['longitude'];?>">
					</div>
				</div>
				<div class="col-lg-12 col-md-12">
					<div class="form-group">
						<div id="simple-map" style="height: 400px;"></div>
					</div>
				</div>
				<div class="col-lg-6 col-md-12">
					<div class="form-group">
						<label for="jarak">Jarak ke <?= $rowUmum['nama_alamat'];?></label>
						<input type="text" name="jarak" id="jarak" class="form-control" autocomplete="off" placeholder="Masukkan Jarak" disabled="" value="<?= $jarakDitentukan;?>">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12 col-md-12">
					<div class="alert alert-info">
						<h5>
							<i class="icon fas fa-info-circle"></i> Pemberitahuan!
						</h5>
						<ol type="number">
							<li>Titik Lokasi A merupakan lokasi petugas <?= $row['nama'];?>.</li>
							<li>Titik Lokasi B merupakan lokasi <?= $rowUmum['nama_alamat'];?>.</li>
						</ol>
					</div>
				</div>
			</div>
		<?php }else { ?>
			<div class="row">
				<div class="col-lg-12 col-md-12">
					<div class="alert alert-danger">
						<h5>
							<i class="icon fas fa-ban"></i> Peringatan!
						</h5>
						<p>Anda masih belum mengatur lokasi / alamat rumah Anda, mohon untuk mengatur lokasi terlebih dahulu agar dapat menampilkan peta.</p>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
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

		var nama_alamat_umum_ = $('#nama_alamat_umum').val();
		nama_alamat_umum_ = (typeof nama_alamat_umum_ != "undefined" && nama_alamat_umum_ != "" && nama_alamat_umum_ != "-") ? nama_alamat_umum_ : "Lokasi TPS";

		var latitude_umum_ = $('#latitude_umum').val();
		var longitude_umum_ = $('#longitude_umum').val();

		if(typeof latitude_umum_ != "undefined" && typeof longitude_umum_ != "undefined" && latitude_umum_ != "" && longitude_umum_ != "" && latitude_umum_ != "-" && longitude_umum_ != "-") {
			defaultLatLongUmum = {
				lat: parseFloat(latitude_umum_),
				lng: parseFloat(longitude_umum_)
			};
		} else {
			defaultLatLongUmum = {
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

		var directionsService = new google.maps.DirectionsService();
		var directionsRenderer = new google.maps.DirectionsRenderer({
			map: map
		});

		var start = new google.maps.LatLng(defaultLatLong.lat, defaultLatLong.lng);
		var end = new google.maps.LatLng(defaultLatLongUmum.lat, defaultLatLongUmum.lng);

		var request = {
			origin: start,
			destination: end,
			travelMode: google.maps.TravelMode.DRIVING
		};

		directionsService.route(request, function(result, status) {
			if (status === google.maps.DirectionsStatus.OK) {
				directionsRenderer.setDirections(result);
			}
		});
	}
</script>