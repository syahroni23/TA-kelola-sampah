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
	$sql = "SELECT * FROM m_pengguna";

	$queryAll = mysqli_query($conn, $sql);
	$total_all_rows = mysqli_num_rows($queryAll);
	$total_filter_rows = $total_all_rows;

	$columns = array(
		0	=>	'm_pengguna.id',
		1	=>	'm_pengguna.kode',
		2	=>	'm_pengguna.nama',
		3	=>	'm_pengguna.telepon',
		4	=>	'm_pengguna.email',
		5	=>	'm_pengguna.modified_at'
	);

	if(isset($_POST['isDeleted'])) {
		$sql .= " WHERE m_pengguna.is_deleted = ".$_POST['isDeleted'];
	}

	if(isset($_POST['search']['value'])) {
		$search_value = $_POST['search']['value'];
		$sql .= " AND (m_pengguna.kode LIKE '%".$search_value."%'";
		$sql .= " OR m_pengguna.nama LIKE '%".$search_value."%'";
		$sql .= " OR m_pengguna.telepon LIKE '%".$search_value."%'";
		$sql .= " OR m_pengguna.email LIKE '%".$search_value."%')";
	}

	if(isset($_POST['order'])) {
		$order_name = $_POST['order'][0]['column'];
		$order_value = $_POST['order'][0]['dir'];
		$sql .= " ORDER BY ".$columns[$order_name]." ".$order_value."";
	}else {
		$sql .= " ORDER BY m_pengguna.kode ASC";
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
		if($row['is_deleted'] == 0) {
			$button = "<div title='Ubah' class='d-inline-block'>
			<a href='edit/pengguna/".$row['id']."' class='btn btn-primary btn-sm btn-action mr-1 cursor-pointer'><i class='fas fa-edit fa-sm'></i></a>
			</div>
			<div title='Hapus' class='d-inline-block'>
			<a class='btn btn-danger btn-sm btn-action mr-1 cursor-pointer trashed' data-id='".$row['id']."'><i class='fas fa-trash fa-sm'></i></a>
			</div>";
		}else {
			$button = "<div title='Pulihkan' class='d-inline-block'>
			<a class='btn btn-warning btn-sm btn-action mr-1 cursor-pointer restored' data-id='".$row['id']."'><i class='fas fa-recycle fa-sm'></i></a>
			</div>
			<div title='Hapus' class='d-inline-block'>
			<a class='btn btn-danger btn-sm btn-action cursor-pointer deleted' data-id='".$row['id']."'><i class='fas fa-minus-circle fa-sm'></i></a>
			</div>";
		}
		$sub_array = array();
		$sub_array[] = null;
		$sub_array[] = "<a href='view/pengguna/".$row['id']."' class='link'>".$row['kode']."</a>";
		$sub_array[] = $row['nama'];
		$sub_array[] = $row['telepon'];
		$sub_array[] = $row['email'];
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
	$form['kode'] = getCodeIncrement($conn, 'm_pengguna', 'kode', "KLS/PN/", 5);
	$form['nama'] = mysqli_escape_string($conn, $form['nama']);
	$form['telepon'] = str_replace('-', '', $form['telepon']);
	$form['email'] = mysqli_escape_string($conn, $form['email']);

	if(issetEmpty($_FILES['avatar']['name'])) {
		if(checkFileSize($_FILES['avatar']['size'], 1048576) == false) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Ukuran file melebihi batas",
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}else if(checkFileType(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION), array('jpg', 'jpeg', 'png')) == false) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Tipe file tidak sesuai",
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}else {
			$form['avatar'] = getFileName($_FILES['avatar']);
		}
	}

	if(issetEmpty($form['password'])) {
		$password = mysqli_escape_string($conn, $form['password']);
		$form['password'] = password_hash($password, PASSWORD_DEFAULT);
	}else {
		unset($form['password']);
	}

	$telepon = $form['telepon'];
	$email = $form['email'];

	$countTeleponPengguna = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM m_pengguna WHERE telepon = '$telepon'")
	);
	if($countTeleponPengguna > 0) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "Telepon sudah digunakan oleh pengguna lain",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	$countEmailPengguna = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM m_pengguna WHERE email = '$email'")
	);
	if($countEmailPengguna > 0) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "E-mail sudah digunakan oleh pengguna lain",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	$countTeleponPelanggan = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE telepon = '$telepon'")
	);
	if($countTeleponPelanggan > 0) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "Telepon sudah digunakan oleh pelanggan",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	$countEmailPelanggan = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE email = '$email'")
	);
	if($countEmailPelanggan > 0) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "E-mail sudah digunakan oleh pelanggan",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	$countTeleponPetugas = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM m_petugas WHERE telepon = '$telepon'")
	);
	if($countTeleponPetugas > 0) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "Telepon sudah digunakan oleh petugas",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	$countEmailPetugas = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM m_petugas WHERE email = '$email'")
	);
	if($countEmailPetugas > 0) {
		echo json_encode([
			'status' => 0,
			'status_code' => 400,
			'message' => "E-mail sudah digunakan oleh petugas",
			"info_error" => null,
			'data' => null
		], JSON_PRETTY_PRINT);
		breakResponse();
	}

	try {
		$models = toInsertData($conn, 'm_pengguna', $form, true);
		if($models['status'] == true) {
			if(issetEmpty($_FILES['avatar']['name'])) {
				uploadFile($_FILES['avatar'], "../../assets/img/avatar", $form['avatar']);
			}

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
	$getData = toGetDataDetail($conn, 'm_pengguna', $_POST['id']);
	$form = $_POST;
	$form['nama'] = mysqli_escape_string($conn, $form['nama']);
	$form['telepon'] = str_replace('-', '', $form['telepon']);
	$form['email'] = mysqli_escape_string($conn, $form['email']);

	if(issetEmpty($_FILES['avatar']['name'])) {
		if(checkFileSize($_FILES['avatar']['size'], 1048576) == false) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Ukuran file melebihi batas",
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}else if(checkFileType(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION), array('jpg', 'jpeg', 'png')) == false) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Tipe file tidak sesuai",
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}else {
			$form['avatar'] = getFileName($_FILES['avatar']);
		}
	}

	if(issetEmpty($form['password'])) {
		$password = mysqli_escape_string($conn, $form['password']);
		$form['password'] = password_hash($password, PASSWORD_DEFAULT);
	}else {
		unset($form['password']);
	}

	$telepon = $form['telepon'];
	$email = $form['email'];

	if($telepon != $getData['telepon']) {
		$countTeleponPengguna = mysqli_num_rows(
			mysqli_query($conn, "SELECT * FROM m_pengguna WHERE telepon = '$telepon'")
		);
		if($countTeleponPengguna > 0) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Telepon sudah digunakan oleh pengguna lain",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}
	}
	
	if($email != $getData['email']) {
		$countEmailPengguna = mysqli_num_rows(
			mysqli_query($conn, "SELECT * FROM m_pengguna WHERE email = '$email'")
		);
		if($countEmailPengguna > 0) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "E-mail sudah digunakan oleh pengguna lain",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}
	}
	
	if($telepon != $getData['telepon']) {
		$countTeleponPelanggan = mysqli_num_rows(
			mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE telepon = '$telepon'")
		);
		if($countTeleponPelanggan > 0) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Telepon sudah digunakan oleh pelanggan",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}
	}
	
	if($email != $getData['email']) {
		$countEmailPelanggan = mysqli_num_rows(
			mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE email = '$email'")
		);
		if($countEmailPelanggan > 0) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "E-mail sudah digunakan oleh pelanggan",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}
	}
	
	if($telepon != $getData['telepon']) {
		$countTeleponPetugas = mysqli_num_rows(
			mysqli_query($conn, "SELECT * FROM m_petugas WHERE telepon = '$telepon'")
		);
		if($countTeleponPetugas > 0) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Telepon sudah digunakan oleh petugas",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}
	}
	
	if($email != $getData['email']) {
		$countEmailPetugas = mysqli_num_rows(
			mysqli_query($conn, "SELECT * FROM m_petugas WHERE email = '$email'")
		);
		if($countEmailPetugas > 0) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "E-mail sudah digunakan oleh petugas",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}
	}

	try {
		$models = toUpdateData($conn, 'm_pengguna', $form, $form['id'], null, true);
		if($models['status'] == true) {
			if(issetEmpty($_FILES['avatar']['name'])) {
				if(issetEmpty($getData['avatar']) && file_exists('../../assets/img/avatar/'.$getData['avatar']) && $getData['avatar'] != "default-avatar.png") {
					unlinkFile('../../assets/img/avatar/'.$getData['avatar']);
				}

				uploadFile($_FILES['avatar'], "../../assets/img/avatar", $form['avatar']);
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
		$query = mysqli_query($conn, "SELECT * FROM m_pengguna WHERE id = '$id'");
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
		$query = mysqli_query($conn, "SELECT * FROM m_pengguna WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toTrashData($conn, 'm_pengguna', $formPost['id'], null, true);

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
		$query = mysqli_query($conn, "SELECT * FROM m_pengguna WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$checkData = checkDataExist($conn, 'm_pengguna', 'm_pengguna_id', $formPost['id']);
		if(issetEmpty($checkData)) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Tidak dapat melakukan proses hapus data karena data sedang digunakan di menu " . $checkData['child_menu_name'],
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}

		$getData = toGetDataDetail($conn, 'm_pengguna', $formPost['id']);

		if(issetEmpty($getData['avatar']) && file_exists("../../assets/img/avatar/".$getData['avatar']) && $getData['avatar'] != "default-avatar.png") {
			unlinkFile("../../assets/img/avatar/".$getData['avatar']);
		}

		$models = toDeleteData($conn, 'm_pengguna', $formPost['id']);

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
		$query = mysqli_query($conn, "SELECT * FROM m_pengguna WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toRestoreData($conn, 'm_pengguna', $formPost['id'], null, true);

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
?>