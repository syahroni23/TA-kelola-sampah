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
	$sql = "SELECT t_penarikan_sampah.*, m_pelanggan.alamat, m_pelanggan.latitude, m_pelanggan.longitude, m_pelanggan.nama AS nama_pelanggan, m_petugas.nama AS nama_petugas FROM t_penarikan_sampah LEFT JOIN m_pelanggan ON t_penarikan_sampah.m_pelanggan_id = m_pelanggan.id LEFT JOIN m_petugas ON t_penarikan_sampah.m_petugas_id = m_petugas.id";

	$queryAll = mysqli_query($conn, $sql);
	$total_all_rows = mysqli_num_rows($queryAll);
	$total_filter_rows = $total_all_rows;

	$columns = array(
		0	=>	't_penarikan_sampah.id',
		1	=>	't_penarikan_sampah.kode',
		2	=>	'm_pelanggan.nama',
		3	=>	't_penarikan_sampah.status',
		4	=>	'm_pelanggan.alamat',
		5	=>	't_penarikan_sampah.modified_at'
	);

	if(isset($_POST['isDeleted'])) {
		$sql .= " WHERE t_penarikan_sampah.is_deleted = ".$_POST['isDeleted'];
	}

	if(isset($_SESSION['tipe']) && !empty($_SESSION['tipe']) && ($_SESSION['tipe'] == "Pelanggan" || $_SESSION['tipe'] == "Petugas")) {
		$idSession = $_SESSION['id'];
		if($_SESSION['tipe'] == "Pelanggan") {
			$sql .= " AND t_penarikan_sampah.m_pelanggan_id = '$idSession'";
		}else {
			$sql .= " AND (t_penarikan_sampah.m_petugas_id = '$idSession' OR t_penarikan_sampah.m_petugas_id IS NULL)";
		}
	}

	if(isset($_POST['isStatusTransaksi']) && $_POST['isStatusTransaksi'] != "Semua") {
		$sql .= " AND t_penarikan_sampah.status = '".$_POST['isStatusTransaksi']."'";
	}

	if(isset($_POST['search']['value'])) {
		$search_value = $_POST['search']['value'];
		$sql .= " AND (t_penarikan_sampah.kode LIKE '%".$search_value."%'";
		$sql .= " OR m_pelanggan.nama LIKE '%".$search_value."%'";
		$sql .= " OR t_penarikan_sampah.status LIKE '%".$search_value."%')";
	}

	if(isset($_POST['order'])) {
		$order_name = $_POST['order'][0]['column'];
		$order_value = $_POST['order'][0]['dir'];
		$sql .= " ORDER BY ".$columns[$order_name]." ".$order_value."";
	}else {
		$sql .= " ORDER BY t_penarikan_sampah.id DESC";
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
		$isStatusPending = false;
		if($row['status'] == "Selesai") {
			$isDisabledEdit = true;
			$isDisabledDelete = true;
		}else if($row['status'] == "Proses" && isset($_SESSION['tipe']) && !empty($_SESSION['tipe']) && $_SESSION['tipe'] != "Petugas") {
			$isDisabledEdit = true;
			$isDisabledDelete = true;
		}else if($row['status'] == "Proses" && isset($_SESSION['tipe']) && !empty($_SESSION['tipe']) && $_SESSION['tipe'] == "Petugas") {
			$isDisabledDelete = true;
		}

		if(empty($row['m_petugas_id'])) $isStatusPending = true;
		if($row['is_deleted'] == 0) {
			if($isStatusPending == true && $_SESSION['tipe'] == "Petugas") {
				$button = "<div title='Ambil' class='d-inline-block'>
				<a class='btn btn-warning btn-sm btn-action mr-1 cursor-pointer taked' data-id='".$row['id']."'><i class='fas fa-check fa-sm'></i></a>
				</div>
				<div title='Hapus' class='d-inline-block'>
				<a class='btn btn-danger btn-sm btn-action mr-1 cursor-pointer trashed".isDisabledHref($isDisabledDelete)."' data-id='".$row['id']."'><i class='fas fa-trash fa-sm'></i></a>
				</div>";
			}else {
				$button = "<div title='Ubah' class='d-inline-block'>
				<a href='edit/penarikan-sampah/".$row['id']."' class='btn btn-primary btn-sm btn-action mr-1 cursor-pointer".isDisabledHref($isDisabledEdit)."'><i class='fas fa-edit fa-sm'></i></a>
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
		if($row['status'] == "Pending") {
			$badgeStatus = "<span class='badge badge-secondary'>Minta Diambil</span>";
		}else if($row['status'] == "Proses") {
			$badgeStatus = "<span class='badge badge-warning' style='color: #FFF;'>Proses Ambil</span>";
		}else if($row['status'] == "Selesai") {
			$badgeStatus = "<span class='badge badge-success'>Sudah Diambil</span>";
		}else {
			$badgeStatus = "<span class='badge badge-primary'>Minta Diambil</span>";
		}
		if( (isset($row['latitude']) && !empty($row['latitude'])) && (isset($row['longitude']) && !empty($row['longitude'])) ) {
			$statusMap = "Tampil";
		}else {
			$statusMap = "Tidak Tampil";
		}
		if(isset($row['bukti_ambil']) && !empty($row['bukti_ambil'])) {
			$buttonBuktiAmbil = "<div title='Lihat Bukti Ambil' class='d-inline-block'>
			<a class='btn btn-warning btn-sm btn-action mr-1 cursor-pointer' data-toggle='modal' data-target='#viewDataAmbil' data-upload='tidak' data-id='".$row['id']."'><i class='fas fa-image fa-sm'></i></a>
			</div>";
		}else {
			$buttonBuktiAmbil = "<div title='Tidak Ada Bukti Ambil' class='d-inline-block'>
			<a class='btn btn-danger btn-sm btn-action mr-1 cursor-pointer".isDisabledHref(true)."'><i class='fas fa-image fa-sm'></i></a>
			</div>";
		}
		$namaPetugas = isset($row['nama_petugas']) && !empty($row['nama_petugas']) ? $row['nama_petugas'] : "-";
		$sub_array = array();
		$sub_array[] = null;
		$sub_array[] = "<a href='view/penarikan-sampah/".$row['id']."' class='link'>".$row['kode']."</a>" . "<br><small>" . formatDateIndonesia(date('d M Y', strtotime($row['tanggal']))) . "</small>";
		$sub_array[] = $row['nama_pelanggan'] . "<br><small>Petugas: ".$namaPetugas."</small>";
		$sub_array[] = $buttonBuktiAmbil;
		$sub_array[] = "<div title='Lihat Peta' class='d-inline-block'>
		<a class='btn btn-info btn-sm btn-action mr-1 cursor-pointer' data-toggle='modal' data-target='#viewData' data-status='".$statusMap."' data-id='".$row['id']."'><i class='fas fa-map fa-sm'></i></a>
		</div>";
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

	if($_SESSION['tipe'] == "Pengguna") {
		$formPengguna = [];
		$formPengguna['kode'] = getCodeIncrement($conn, 't_penarikan_sampah', 'kode', "KLS/PS/", 5);
		$formPengguna['m_pelanggan_id'] = $form['m_pelanggan_id'];
		$formPengguna['m_petugas_id'] = $form['m_petugas_id'];
		$formPengguna['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
		$formPengguna['status'] = "Pending";
	}else {
		$formPelanggan = [];
		$formPelanggan['kode'] = getCodeIncrement($conn, 't_penarikan_sampah', 'kode', "KLS/PS/", 5);
		$formPelanggan['m_pelanggan_id'] = $form['m_pelanggan_id'];
		$formPelanggan['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
		$formPelanggan['status'] = "Pending";
	}

	try {
		if($_SESSION['tipe'] == "Pengguna") {
			$models = toInsertData($conn, 't_penarikan_sampah', $formPengguna, true);
		}else {
			$models = toInsertData($conn, 't_penarikan_sampah', $formPelanggan, true);
		}

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
	$getData = toGetDataDetail($conn, 't_penarikan_sampah', $_POST['id']);
	$form = $_POST;

	if($_SESSION['tipe'] == "Pengguna") {
		$formPengguna = [];
		$formPengguna['m_pelanggan_id'] = $form['m_pelanggan_id'];
		$formPengguna['m_petugas_id'] = $form['m_petugas_id'];
		$formPengguna['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
	}else if($_SESSION['tipe'] == "Petugas") {
		$formPetugas = [];
		$formPetugas['status'] = $form['status'];
		$formPetugas['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));

		if(isset($_FILES['bukti_ambil']['name']) && !empty($_FILES['bukti_ambil']['name'])) {
			if(checkFileSize($_FILES['bukti_ambil']['size'], 1048576) == false) {
				echo json_encode([
					'status' => 0,
					'status_code' => 400,
					'message' => "Ukuran file melebihi batas",
					'data' => null
				], JSON_PRETTY_PRINT);
				breakResponse();
			}else if(checkFileType(pathinfo($_FILES['bukti_ambil']['name'], PATHINFO_EXTENSION), array('jpg', 'jpeg', 'png')) == false) {
				echo json_encode([
					'status' => 0,
					'status_code' => 400,
					'message' => "Tipe file tidak sesuai",
					'data' => null
				], JSON_PRETTY_PRINT);
				breakResponse();
			}else {
				$formPetugas['bukti_ambil'] = getFileName($_FILES['bukti_ambil']);
			}
		}
	}else {
		$formPelanggan = [];
		$formPelanggan['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
	}

	try {
		if($_SESSION['tipe'] == "Pengguna") {
			$models = toUpdateData($conn, 't_penarikan_sampah', $formPengguna, $form['id'], null, true);
		}else if($_SESSION['tipe'] == "Petugas") {
			$models = toUpdateData($conn, 't_penarikan_sampah', $formPetugas, $form['id'], null, true);
		}else {
			$models = toUpdateData($conn, 't_penarikan_sampah', $formPelanggan, $form['id'], null, true);
		}

		if($models['status'] == true) {
			if(isset($_FILES['bukti_ambil']['name']) && !empty($_FILES['bukti_ambil']['name'])) {
				if(issetEmpty($getData['bukti_ambil']) && file_exists('../../assets/img/bukti-ambil/'.$getData['bukti_ambil']) && $getData['bukti_ambil'] != "default-bukti-ambil.png") {
					unlinkFile('../../assets/img/bukti-ambil/'.$getData['bukti_ambil']);
				}
				
				uploadFile($_FILES['bukti_ambil'], "../../assets/img/bukti-ambil", $formPetugas['bukti_ambil']);
			}

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
		$query = mysqli_query($conn, "SELECT * FROM t_penarikan_sampah WHERE id = '$id'");
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
		$query = mysqli_query($conn, "SELECT * FROM t_penarikan_sampah WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toTrashData($conn, 't_penarikan_sampah', $formPost['id'], null, true);

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
		$query = mysqli_query($conn, "SELECT * FROM t_penarikan_sampah WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$checkData = checkDataExist($conn, 't_penarikan_sampah', 't_penarikan_sampah_id', $formPost['id']);
		if(issetEmpty($checkData)) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Tidak dapat melakukan proses hapus data karena data sedang digunakan di menu " . $checkData['child_menu_name'],
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}

		$getData = toGetDataDetail($conn, 't_penarikan_sampah', $formPost['id']);

		if(issetEmpty($getData['bukti_ambil']) && file_exists("../../assets/img/bukti-ambil/".$getData['bukti_ambil']) && $getData['bukti_ambil'] != "default-bukti-ambil.png") {
			unlinkFile("../../assets/img/bukti-ambil/".$getData['bukti_ambil']);
		}

		$models = toDeleteData($conn, 't_penarikan_sampah', $formPost['id']);

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
		$query = mysqli_query($conn, "SELECT * FROM t_penarikan_sampah WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toRestoreData($conn, 't_penarikan_sampah', $formPost['id'], null, true);

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
	$getData = toGetDataDetail($conn, 't_penarikan_sampah', $_POST['id']);
	$form = $_POST;

	$formProses = [];
	$formProses['status'] = "Proses";
	$formProses['m_petugas_id'] = $_SESSION['id'];

	try {
		$models = toUpdateData($conn, 't_penarikan_sampah', $formProses, $form['id'], null, true);

		if($models['status'] == true) {
			echo json_encode([
				'status' => 1,
				'status_code' => 200,
				'message' => "Berhasil mengambil data",
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
			'message' => "Gagal mengambil data",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}
?>