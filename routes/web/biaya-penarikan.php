<?php
require "../../config/autoloads.php";
require "../../vendor/tcpdf/tcpdf.php";
require '../../vendor/phpoffice/vendor/autoload.php';
require "../../vendor/excelreader/excel_reader2.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

call_user_func($_POST['function'], $conn);

function getData($conn) {
	$output = array();
	$sql = "SELECT t_biaya_penarikan.*, m_pelanggan.nama AS nama_pelanggan, m_petugas.nama AS nama_petugas FROM t_biaya_penarikan LEFT JOIN t_penarikan_sampah ON t_biaya_penarikan.t_penarikan_sampah_id = t_penarikan_sampah.id LEFT JOIN m_pelanggan ON t_penarikan_sampah.m_pelanggan_id = m_pelanggan.id LEFT JOIN m_petugas ON t_penarikan_sampah.m_petugas_id = m_petugas.id";

	$queryAll = mysqli_query($conn, $sql);
	$total_all_rows = mysqli_num_rows($queryAll);
	$total_filter_rows = $total_all_rows;

	$columns = array(
		0	=>	't_biaya_penarikan.id',
		1	=>	't_biaya_penarikan.kode',
		2	=>	'm_pelanggan.nama',
		3	=>	'm_petugas.nama',
		4	=>	't_biaya_penarikan.biaya',
		5	=>	't_biaya_penarikan.status',
		6	=>	't_biaya_penarikan.modified_at'
	);

	if(isset($_POST['isDeleted'])) {
		$sql .= " WHERE t_biaya_penarikan.is_deleted = ".$_POST['isDeleted'];
	}

	if(isset($_SESSION['tipe']) && !empty($_SESSION['tipe']) && $_SESSION['tipe'] == "Petugas") {
		$idSession = $_SESSION['id'];
		$sql .= " AND t_penarikan_sampah.m_petugas_id = '$idSession'";
	}

	if(isset($_POST['isStatusTransaksi']) && $_POST['isStatusTransaksi'] != "Semua") {
		$sql .= " AND t_biaya_penarikan.status = '".$_POST['isStatusTransaksi']."'";
	}

	if(isset($_POST['search']['value'])) {
		$search_value = $_POST['search']['value'];
		$sql .= " AND (t_biaya_penarikan.kode LIKE '%".$search_value."%'";
		$sql .= " OR m_pelanggan.nama LIKE '%".$search_value."%'";
		$sql .= " OR m_petugas.nama LIKE '%".$search_value."%'";
		$sql .= " OR t_biaya_penarikan.biaya LIKE '%".$search_value."%'";
		$sql .= " OR t_biaya_penarikan.status LIKE '%".$search_value."%')";
	}

	if(isset($_POST['order'])) {
		$order_name = $_POST['order'][0]['column'];
		$order_value = $_POST['order'][0]['dir'];
		$sql .= " ORDER BY ".$columns[$order_name]." ".$order_value."";
	}else {
		$sql .= " ORDER BY t_biaya_penarikan.id DESC";
	}

	$queryFilter = mysqli_query($conn, $sql);
	$total_filter_rows = mysqli_num_rows($queryFilter);

	if($_POST['length'] != -1) {
		$start_value = $_POST['start'];
		$length_value = $_POST['length'];
		$sql .= " LIMIT ".$start_value.", ".$length_value;
	}

	$no = 1;
	$query = mysqli_query($conn, $sql);
	$data = array();

	while($row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC))) {
		$isDisabledEdit = false;
		$isDisabledDelete = false;
		if($row['status'] == "Selesai") {
			$isDisabledEdit = true;
			$isDisabledDelete = true;
		}
		if($row['is_deleted'] == 0) {
			if($row['status'] == "Proses" && $_SESSION['tipe'] == "Petugas") {
				$button = "<div title='Setuju' class='d-inline-block'>
				<a class='btn btn-warning btn-sm btn-action mr-1 cursor-pointer accepted' data-id='".$row['id']."'><i class='fas fa-check fa-sm'></i></a>
				</div>
				<div title='Hapus' class='d-inline-block'>
				<a class='btn btn-danger btn-sm btn-action mr-1 cursor-pointer trashed".isDisabledHref($isDisabledDelete)."' data-id='".$row['id']."'><i class='fas fa-trash fa-sm'></i></a>
				</div>";
			}else {
				$button = "<div title='Ubah' class='d-inline-block'>
				<a href='edit/biaya-penarikan/".$row['id']."' class='btn btn-primary btn-sm btn-action mr-1 cursor-pointer".isDisabledHref($isDisabledEdit)."'><i class='fas fa-edit fa-sm'></i></a>
				</div>
				<div title='Hapus' class='d-inline-block'>
				<a class='btn btn-danger btn-sm btn-action mr-1 cursor-pointer trashed".isDisabledHref($isDisabledDelete)."' data-id='".$row['id']."'><i class='fas fa-trash fa-sm'></i></a>
				</div>";
			}
		}else {
			$button = "<div title='Pulihkan' class='d-inline-block'>
			<a class='btn btn-warning btn-sm btn-action mr-1 cursor-pointer restored' data-id='".$row['id']."'><i class='fas fa-recycle fa-sm'></i></a>
			</div>
			<div title='Hapus' class='d-inline-block'>
			<a class='btn btn-danger btn-sm btn-action cursor-pointer deleted' data-id='".$row['id']."'><i class='fas fa-minus-circle fa-sm'></i></a>
			</div>";
		}
		if($row['status'] == "Proses") {
			$badgeStatus = "<span class='badge badge-warning' style='color: #FFF;'>".$row['status']."</span>";
		}else if($row['status'] == "Selesai") {
			$badgeStatus = "<span class='badge badge-success'>".$row['status']."</span>";
		}else {
			$badgeStatus = "<span class='badge badge-primary'>".$row['status']."</span>";
		}
		$sub_array = array();
		$sub_array[] = null;
		$sub_array[] = "<a href='view/biaya-penarikan/".$row['id']."' class='link'>".$row['kode']."</a>" . "<br><small>" . formatDateIndonesia(date('d M Y', strtotime($row['tanggal']))) . "</small>";
		$sub_array[] = $row['nama_pelanggan'];
		$sub_array[] = $row['nama_petugas'];
		$sub_array[] = "Rp. " . number_format($row['biaya']);
		$sub_array[] = $badgeStatus;
		$sub_array[] = formatDateIndonesia(date('d M Y, H:i', strtotime($row['modified_at'])));
		$sub_array[] = $button;
		$sub_array[] = $row['id'];
		$data[] = $sub_array;
	}

	$output = array(
		'draw' => intval($_POST['draw']),
		'recordsTotal' => $total_all_rows,
		'recordsFiltered' => $total_filter_rows,
		'data' => $data
	);

	echo json_encode($output);
}

function addData($conn) {
	$form = $_POST;
	$form['kode'] = getCodeIncrement($conn, 't_biaya_penarikan', 'kode', "KLS/BP/", 5);
	$form['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
	$form['biaya'] = mysqli_escape_string($conn, str_replace(',', '', $form['biaya']));
	$form['status'] = "Proses";

	$getDataPenarikanSampah = toGetDataDetail($conn, 't_penarikan_sampah', $form['t_penarikan_sampah_id']);

	$idPelanggan = $getDataPenarikanSampah['m_pelanggan_id'];
	$countPelanggan = mysqli_num_rows(
		mysqli_query($conn, "SELECT t_biaya_penarikan.* FROM t_biaya_penarikan LEFT JOIN t_penarikan_sampah ON t_biaya_penarikan.t_penarikan_sampah_id = t_penarikan_sampah.id WHERE t_penarikan_sampah.m_pelanggan_id = '$idPelanggan' AND t_biaya_penarikan.status = 'Proses'")
	);
	if($countPelanggan > 0) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "Penarikan sampah untuk pelanggan yang bersangkutan tidak dapat diproses. Harap menunggu untuk biaya penarikan diterima oleh petugas.",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	$queryGetPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE id = '$idPelanggan'");
	$rowGetPelanggan = mysqli_fetch_array($queryGetPelanggan, MYSQLI_ASSOC);

	$saldoTersimpan = isset($rowGetPelanggan['saldo']) && !empty($rowGetPelanggan['saldo']) ? $rowGetPelanggan['saldo'] : 0;

	if($form['biaya'] > $saldoTersimpan) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "Nominal terlalu besar, saldo tidak mencukupi",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	try {
		$models = toInsertData($conn, 't_biaya_penarikan', $form, true);
		if($models['status'] == true) {
			echo json_encode([
				'status' => 1,
				'status_code' => 200,
				'message' => "Berhasil menambah data",
				"info_error" => null,
				'data' => $models
			], JSON_PRETTY_PRINT);
		}else {
			throw new Exception($models['message'], 1);
		}
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Gagal menambah data",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function updateData($conn) {
	$getData = toGetDataDetail($conn, 't_biaya_penarikan', $_POST['id']);
	$form = $_POST;
	$form['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
	$form['biaya'] = mysqli_escape_string($conn, str_replace(',', '', $form['biaya']));

	$getDataPenarikanSampah = toGetDataDetail($conn, 't_penarikan_sampah', $form['t_penarikan_sampah_id']);

	$id = $form['id'];
	$idPelanggan = $getDataPenarikanSampah['m_pelanggan_id'];
	$countPelanggan = mysqli_num_rows(
		mysqli_query($conn, "SELECT t_biaya_penarikan.* FROM t_biaya_penarikan LEFT JOIN t_penarikan_sampah ON t_biaya_penarikan.t_penarikan_sampah_id = t_penarikan_sampah.id WHERE t_penarikan_sampah.m_pelanggan_id = '$idPelanggan' AND t_biaya_penarikan.status = 'Proses' AND t_biaya_penarikan.id != '$id'")
	);
	if($countPelanggan > 0) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "Penarikan sampah untuk pelanggan yang bersangkutan tidak dapat diproses. Harap menunggu untuk biaya penarikan diterima oleh petugas.",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	$queryGetPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE id = '$idPelanggan'");
	$rowGetPelanggan = mysqli_fetch_array($queryGetPelanggan, MYSQLI_ASSOC);

	$saldoTersimpan = isset($rowGetPelanggan['saldo']) && !empty($rowGetPelanggan['saldo']) ? $rowGetPelanggan['saldo'] : 0;

	if($form['biaya'] > $saldoTersimpan) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "Nominal terlalu besar, saldo tidak mencukupi",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	try {
		$models = toUpdateData($conn, 't_biaya_penarikan', $form, $form['id'], null, true);
		if($models['status'] == true) {
			echo json_encode([
				'status' => 1,
				'status_code' => 200,
				'message' => "Berhasil mengubah data",
				"info_error" => null,
				'data' => $models
			], JSON_PRETTY_PRINT);
		}else {
			throw new Exception($models['message'], 1);
		}
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Gagal mengubah data",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function viewData($conn) {
	$form = $_POST;
	$id = $form['id'];

	try {
		$query = mysqli_query($conn, "SELECT * FROM t_biaya_penarikan WHERE id = '$id'");
		$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));

		echo json_encode([
			'status' => 1,
			'status_code' => 200,
			'message' => "Berhasil menampilkan data",
			"info_error" => null,
			'data' => $row
		], JSON_PRETTY_PRINT);
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Gagal menampilkan data",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function trashData($conn) {
	try {
		$id = $_POST['id'];
		$query = mysqli_query($conn, "SELECT * FROM t_biaya_penarikan WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toTrashData($conn, 't_biaya_penarikan', $formPost['id'], null, true);

		echo json_encode([
			'status' => 1,
			'status_code' => 200,
			'message' => "Berhasil menghapus data",
			"info_error" => null,
			'data' => $models
		], JSON_PRETTY_PRINT);
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Gagal menghapus data",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function deleteData($conn) {
	try {
		$id = $_POST['id'];
		$query = mysqli_query($conn, "SELECT * FROM t_biaya_penarikan WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$checkData = checkDataExist($conn, 't_biaya_penarikan', 't_biaya_penarikan_id', $formPost['id']);
		if(issetEmpty($checkData)) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Tidak dapat melakukan proses hapus data karena data sedang digunakan di menu " . $checkData['child_menu_name'],
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}

		$getData = toGetDataDetail($conn, 't_biaya_penarikan', $formPost['id']);

		$models = toDeleteData($conn, 't_biaya_penarikan', $formPost['id']);

		echo json_encode([
			'status' => 1,
			'status_code' => 200,
			'message' => "Berhasil menghapus data permanen",
			"info_error" => null,
			'data' => $models
		], JSON_PRETTY_PRINT);
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Gagal menghapus data permanen",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function restoreData($conn) {
	try {
		$id = $_POST['id'];
		$query = mysqli_query($conn, "SELECT * FROM t_biaya_penarikan WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toRestoreData($conn, 't_biaya_penarikan', $formPost['id'], null, true);

		echo json_encode([
			'status' => 1,
			'status_code' => 200,
			'message' => "Berhasil memulihkan data",
			"info_error" => null,
			'data' => $models
		], JSON_PRETTY_PRINT);
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Gagal memulihkan data",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function getUpdateData($conn) {
	$getData = toGetDataDetail($conn, 't_biaya_penarikan', $_POST['id']);
	$form = $_POST;

	$formProses = [];
	$formProses['status'] = "Selesai";

	$getDataPenarikanSampah = toGetDataDetail($conn, 't_penarikan_sampah', $getData['t_penarikan_sampah_id']);

	$idPelanggan = $getDataPenarikanSampah['m_pelanggan_id'];
	$queryGetPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE id = '$idPelanggan'");
	$rowGetPelanggan = mysqli_fetch_array($queryGetPelanggan, MYSQLI_ASSOC);

	$saldoTersimpan = isset($rowGetPelanggan['saldo']) && !empty($rowGetPelanggan['saldo']) ? $rowGetPelanggan['saldo'] : 0;

	if($getData['biaya'] > $saldoTersimpan) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "Nominal terlalu besar, saldo tidak mencukupi",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	$idPetugas = $getDataPenarikanSampah['m_petugas_id'];
	$queryGetPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE id = '$idPetugas'");
	$rowGetPetugas = mysqli_fetch_array($queryGetPetugas, MYSQLI_ASSOC);

	try {
		$models = toUpdateData($conn, 't_biaya_penarikan', $formProses, $form['id'], null, true);

		if($models['status'] == true) {
			$dataJurnal = [];
			$dataJurnal['kode'] = getCodeIncrement($conn, 'trans_detail', 'kode', "KLS/TD/", 5);
			$dataJurnal['nomor_jurnal'] = getCodeIncrement($conn, 'trans_detail', 'nomor_jurnal', "KLS/".date('Y')."/".date('m')."/PGL/", 5, true);
			$dataJurnal['tanggal'] = date('Y-m-d');
			$dataJurnal['tipe'] = 'Pengeluaran';
			$dataJurnal['debit'] = 0;
			$dataJurnal['kredit'] = $getData['biaya'];
			$dataJurnal['keterangan'] = "Biaya penarikan sampah sebesar Rp. " . number_format($getData['biaya']) .  " untuk petugas " . $rowGetPetugas['nama'];

			$modelsJurnal = toInsertData($conn, 'trans_detail', $dataJurnal, true);

			$saldoPetugas = $rowGetPetugas['saldo'];
			$saldoPetugasBaru = $saldoPetugas + $getData['biaya'];
			
			$formUpdatePetugas = [];
			$formUpdatePetugas['saldo'] = $saldoPetugasBaru;
			
			toUpdateData($conn, 'm_petugas', $formUpdatePetugas, $rowGetPetugas['id'], null, true);

			$saldoPelanggan = $rowGetPelanggan['saldo'];
			$saldoPelangganBaru = $saldoPelanggan - $getData['biaya'];
			
			$formUpdatePelanggan = [];
			$formUpdatePelanggan['saldo'] = $saldoPelangganBaru;
			
			toUpdateData($conn, 'm_pelanggan', $formUpdatePelanggan, $rowGetPelanggan['id'], null, true);

			echo json_encode([
				'status' => 1,
				'status_code' => 200,
				'message' => "Berhasil menyetujui data",
				"info_error" => null,
				'data' => $models
			], JSON_PRETTY_PRINT);
		}else {
			throw new Exception($models['message'], 1);
		}
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Gagal menyetujui data",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function getDetailPenarikan($conn) {
	$getData = toGetDataDetail($conn, 't_penarikan_sampah', $_POST['id']);
	$form = $_POST;

	try {

		$queryUmum = mysqli_query($conn, "SELECT * FROM p_konfigurasi_umum ORDER BY id ASC LIMIT 1");
		$rowUmum = allRowValidateHTML(mysqli_fetch_array($queryUmum, MYSQLI_ASSOC));
		
		$id = $_POST['id'];
		$queryPenarikanSampah = mysqli_query($conn, "SELECT t_penarikan_sampah.*, m_petugas.nama AS nama_petugas, m_petugas.latitude, m_petugas.longitude FROM t_penarikan_sampah LEFT JOIN m_petugas ON t_penarikan_sampah.m_petugas_id = m_petugas.id WHERE t_penarikan_sampah.id = '$id' AND t_penarikan_sampah.is_deleted = 0");
		$rowPenarikanSampah = mysqli_fetch_array($queryPenarikanSampah);
		
		$latitudeUmum = isset($rowPenarikanSampah['latitude']) && !empty($rowPenarikanSampah['latitude']) ? $rowPenarikanSampah['latitude'] : $rowUmum['latitude'];
		$longitudeUmum = isset($rowPenarikanSampah['longitude']) && !empty($rowPenarikanSampah['longitude']) ? $rowPenarikanSampah['longitude'] : $rowUmum['longitude'];
		$namaAlamatUmum = issetEmpty($rowPenarikanSampah['longitude']) && issetEmpty($rowPenarikanSampah['latitude']) ? $rowPenarikanSampah['nama_petugas'] : $rowUmum['nama_alamat'];
		
		$isPetugasAlamat = false;
		if( issetEmpty($rowPenarikanSampah['longitude']) && issetEmpty($rowPenarikanSampah['latitude']) ) {
			$isPetugasAlamat = true;
		}
		
		$idPelanggan = $rowPenarikanSampah['m_pelanggan_id'];
		$query = mysqli_query($conn, "SELECT m_pelanggan.*, CONCAT(ROUND(ST_Distance_Sphere(POINT(".$longitudeUmum.", ".$latitudeUmum."), POINT(m_pelanggan.longitude, m_pelanggan.latitude)), 2), 'm') AS jarak FROM m_pelanggan WHERE m_pelanggan.id = '$idPelanggan'");
		$row = mysqli_fetch_array($query);
		
		$latitudePelanggan = $row['latitude'];
		$longitudePelanggan = $row['longitude'];
		
		$jarakDitentukan = calculateDistance($longitudeUmum, $latitudeUmum, $longitudePelanggan, $latitudePelanggan);
		$biayaPengambilan = (int) $rowUmum['biaya_pengambilan'];
		$kelipatanJarak = (floor($jarakDitentukan / 3000) + 1);
		$biayaAkhir = isset($kelipatanJarak) && !empty($kelipatanJarak) && $kelipatanJarak != 0 ? $biayaPengambilan * $kelipatanJarak : $biayaPengambilan * 1;

		echo json_encode([
			'status' => 1,
			'status_code' => 200,
			'message' => "Berhasil mendapatkan data",
			"info_error" => null,
			'data' => [
				'jarak' => $jarakDitentukan,
				'biaya_normal' => $biayaPengambilan,
				'kelipatan' => $kelipatanJarak,
				'biaya_akhir' => $biayaAkhir
			]
		], JSON_PRETTY_PRINT);
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Gagal mendapatkan data",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}
?>