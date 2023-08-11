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
	$sql = "SELECT * FROM t_aktivitas_log";

	$queryAll = mysqli_query($conn, $sql);
	$total_all_rows = mysqli_num_rows($queryAll);
	$total_filter_rows = $total_all_rows;

	$columns = array(
		0	=>	't_aktivitas_log.id',
		1	=>	't_aktivitas_log.kode',
		2	=>	't_aktivitas_log.ip',
		3	=>	't_aktivitas_log.browser',
		4	=>	't_aktivitas_log.sistem_operasi',
		5	=>	't_aktivitas_log.modified_at'
	);

	if(isset($_POST['isDeleted'])) {
		$sql .= " WHERE t_aktivitas_log.is_deleted = ".$_POST['isDeleted'];
	}

	if(isset($_POST['search']['value'])) {
		$search_value = $_POST['search']['value'];
		$sql .= " AND (t_aktivitas_log.kode LIKE '%".$search_value."%'";
		$sql .= " OR t_aktivitas_log.ip LIKE '%".$search_value."%'";
		$sql .= " OR t_aktivitas_log.browser LIKE '%".$search_value."%'";
		$sql .= " OR t_aktivitas_log.sistem_operasi LIKE '%".$search_value."%')";
	}

	if(isset($_POST['order'])) {
		$order_name = $_POST['order'][0]['column'];
		$order_value = $_POST['order'][0]['dir'];
		$sql .= " ORDER BY ".$columns[$order_name]." ".$order_value."";
	}else {
		$sql .= " ORDER BY t_aktivitas_log.kode ASC";
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
			$button = "<div title='Lihat' class='d-inline-block'>
			<a class='btn btn-info btn-sm btn-action mr-1 cursor-pointer' data-toggle='modal' data-target='#viewData' data-id='".$row['id']."'><i class='fas fa-eye fa-sm'></i></a>
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
		$sub_array[] = "<a href='view/aktivitas-log/".$row['id']."' class='link'>".$row['kode']."</a>";
		$sub_array[] = $row['ip'];
		$sub_array[] = $row['browser'];
		$sub_array[] = $row['sistem_operasi'];
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

function viewData($conn) {
	$form = $_POST;
	$id = $form['id'];

	try {
		$query = mysqli_query($conn, "SELECT * FROM t_aktivitas_log WHERE id = '$id'");
		$row = mysqli_fetch_array($query, MYSQLI_ASSOC);

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
		$query = mysqli_query($conn, "SELECT * FROM t_aktivitas_log WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toTrashData($conn, 't_aktivitas_log', $formPost['id'], null, true);

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
		$query = mysqli_query($conn, "SELECT * FROM t_aktivitas_log WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$getData = toGetDataDetail($conn, 't_aktivitas_log', $formPost['id']);

		$models = toDeleteData($conn, 't_aktivitas_log', $formPost['id']);

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
		$query = mysqli_query($conn, "SELECT * FROM t_aktivitas_log WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toRestoreData($conn, 't_aktivitas_log', $formPost['id'], null, true);

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