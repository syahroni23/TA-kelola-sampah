<?php include '../config/autoloads.php';?>
<?php
if(isset($rowAkses)) {
	$getAkses = json_decode($rowAkses['akses'], true);
	checkAccessUser('jurnal-umum', $getAkses);
	checkMaintenance($rowAkses, $rowUmum);
}
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
							<h1 class="m-0">Jurnal Umum</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="beranda">Beranda</a></li>
								<li class="breadcrumb-item active">Jurnal Umum</li>
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
										Jurnal Umum
									</h3>
								</div>
								<div class="card-body">
									<form role="form" action="#" method="POST" enctype="multipart/form-data" id="data-form">
										<div class="row">
											<div class="col-lg-12 col-md-12">
												<div class="table-responsive">
													<table class="table table-striped table-hover" style="width: 100%;">
														<thead>
															<tr>
																<th style="text-align: center; width: 5%;">No</th>
																<th style="text-align: left; width: 10%;">Tanggal</th>
																<th style="text-align: left; width: 17%;">Nomor Jurnal</th>
																<th style="text-align: center; width: 12%;">Tipe</th>
																<th style="text-align: left;">Keterangan</th>
																<th style="text-align: right; width: 12%;">Debit</th>
																<th style="text-align: right; width: 12%;">Kredit</th>
															</tr>
														</thead>
														<tbody>
															<?php
															$totalDebit = $totalKredit = $totalKeseluruhan = 0;
															$no = 1;
															$query = mysqli_query($conn, "SELECT * FROM trans_detail ORDER BY created_at ASC");
															while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                                                                $totalDebit += $row['debit'];
                                                                $totalKredit += $row['kredit'];
                                                                $totalKeseluruhan += ($row['debit'] + $row['kredit']);
																?>
																<tr>
																	<td style="text-align: center;"><?= $no++;?></td>
																	<td style="text-align: left;"><?= formatDateIndonesia(date('d M Y', strtotime($row['created_at'])));?></td>
																	<td style="text-align: left;"><?= $row['nomor_jurnal'];?></td>
																	<td style="text-align: center;"><?= $row['tipe'];?></td>
																	<td style="text-align: left;"><?= $row['keterangan'];?></td>
																	<td style="text-align: right;">Rp. <?= number_format($row['debit']);?></td>
																	<td style="text-align: right;">Rp. <?= number_format($row['kredit']);?></td>
																</tr>
															<?php } ?>
                                                            <tr>
                                                                <td colspan="5">Total</td>
                                                                <td style="text-align: right;">Rp. <?= number_format($totalDebit);?></td>
                                                                <td style="text-align: right;">Rp. <?= number_format($totalKredit);?></td>
                                                            </tr>
														</tbody>
													</table>
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
</body>
</html>