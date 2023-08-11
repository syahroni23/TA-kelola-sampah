<?php
include '../../config/autoloads.php';

$id = $_POST['id'];
$query = mysqli_query($conn, "SELECT * FROM t_pengisian_saldo WHERE id = '$id'");
$row = mysqli_fetch_array($query);
?>
<div class="row">
	<div class="col-lg-12 col-md-12" align="center">
		<?php if(isset($row['bukti_transfer']) && !empty($row['bukti_transfer'])) { ?>
			<div class="form-group">
				<div id="image-preview" class="image-preview" style="background-image: url('../assets/img/bukti-transfer/<?= $row['bukti_transfer'];?>'); background-size: cover; background-position: center center;"></div>
			</div>
		<?php }else { ?>
			<input type="hidden" name="id" value="<?= $row['id'];?>">
			<div class="form-group">
				<div id="image-preview" class="image-preview">
					<label for="image-upload" id="image-label">Pilih File</label>
					<input type="file" name="bukti_transfer" id="image-upload" required="" accept=".jpg, .jpeg, .png" title="Tidak ada file dipilih" style="max-width: 100%;" />
				</div>
			</div>
		<?php } ?>
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