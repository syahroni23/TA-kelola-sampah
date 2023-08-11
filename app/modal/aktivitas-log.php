<?php
include '../../config/autoloads.php';

$id = $_POST['id'];
$query = mysqli_query($conn, "SELECT * FROM t_aktivitas_log WHERE id = '$id'");
$row = mysqli_fetch_array($query);
?>
<div class="row">
	<div class="col-lg-12 col-md-12">
		<div class="table-responsive">
			<table class="table table-bordered table-striped">
				<tbody>
					<tr>
						<td width="25%">Tanggal Dibuat</td>
						<td width="75%"><?= formatDateIndonesia(date('d F Y, H:i', strtotime($row['created_at'])));?></td>
					</tr>
					<tr>
						<td>IP Address</td>
						<td><?= $row['ip'];?></td>
					</tr>
					<tr>
						<td>Browser</td>
						<td><?= $row['browser'];?></td>
					</tr>
					<tr>
						<td>Peramban Web</td>
						<td><?= $row['peramban_web'];?></td>
					</tr>
					<tr>
						<td>Sistem Operasi</td>
						<td><?= $row['sistem_operasi'];?></td>
					</tr>
					<tr>
						<td>Keterangan</td>
						<td><?= $row['keterangan'];?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>