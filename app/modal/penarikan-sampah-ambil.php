<?php
include '../../config/autoloads.php';

$id = $_POST['id'];
$query = mysqli_query($conn, "SELECT * FROM t_penarikan_sampah WHERE id = '$id'");
$row = mysqli_fetch_array($query);
?>
<div class="row">
	<div class="col-lg-12 col-md-12" align="center">
		<div class="form-group">
			<div id="image-preview" class="image-preview" style="background-image: url('../assets/img/bukti-ambil/<?= $row['bukti_ambil'];?>'); background-size: cover; background-position: center center;"></div>
		</div>
	</div>
</div>

<script>
	$.uploadPreview({
		input_field: "#image-upload",
		preview_box: "#image-preview",
		label_field: "#image-label",
		label_default: "Pilih File",
		label_selected: "Ubah File",
		no_label: false
	});
</script>