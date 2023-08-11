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
	$sql = "SELECT t_pengambilan_saldo.*, m_pelanggan.nama AS nama_pelanggan, m_petugas.nama AS nama_petugas FROM t_pengambilan_saldo LEFT JOIN m_pelanggan ON t_pengambilan_saldo.m_pelanggan_id = m_pelanggan.id LEFT JOIN m_petugas ON t_pengambilan_saldo.m_petugas_id = m_petugas.id";

	$queryAll = mysqli_query($conn, $sql);
	$total_all_rows = mysqli_num_rows($queryAll);
	$total_filter_rows = $total_all_rows;

	$columns = array(
		0	=>	't_pengambilan_saldo.id',
		1	=>	't_pengambilan_saldo.kode',
		2	=>	function($data) {
			return isset($data['m_pelanggan']['nama']) && !empty($data['m_pelanggan']['nama']) ? $data['m_pelanggan']['nama'] : $data['m_petugas']['nama'];
		},
		3	=>	't_pengambilan_saldo.nominal',
		4	=>	't_pengambilan_saldo.status',
		5	=>	't_pengambilan_saldo.modified_at'
	);

	if(isset($_POST['isDeleted'])) {
		$sql .= " WHERE t_pengambilan_saldo.is_deleted = ".$_POST['isDeleted'];
	}
	
	if(isset($_SESSION['tipe']) && !empty($_SESSION['tipe']) && ($_SESSION['tipe'] == "Pelanggan" || $_SESSION['tipe'] == "Petugas")) {
		$idSession = $_SESSION['id'];
		if($_SESSION['tipe'] == "Pelanggan") {
			$sql .= " AND t_pengambilan_saldo.m_pelanggan_id = '$idSession'";
		}else {
			$sql .= " AND t_pengambilan_saldo.m_petugas_id = '$idSession'";
		}
	}

	if(isset($_POST['isStatusTransaksi']) && $_POST['isStatusTransaksi'] != "Semua") {
		$sql .= " AND t_pengambilan_saldo.status = '".$_POST['isStatusTransaksi']."'";
	}

	if(isset($_POST['search']['value'])) {
		$search_value = $_POST['search']['value'];
		$sql .= " AND (t_pengambilan_saldo.kode LIKE '%".$search_value."%'";
		$sql .= " OR m_pelanggan.nama LIKE '%".$search_value."%'";
		$sql .= " OR m_petugas.nama LIKE '%".$search_value."%'";
		$sql .= " OR t_pengambilan_saldo.nominal LIKE '%".$search_value."%'";
		$sql .= " OR t_pengambilan_saldo.status LIKE '%".$search_value."%')";
	}

	if(isset($_POST['order'])) {
		$order_name = $_POST['order'][0]['column'];
		$order_value = $_POST['order'][0]['dir'];
		$sql .= " ORDER BY ".$columns[$order_name]." ".$order_value."";
	}else {
		$sql .= " ORDER BY t_pengambilan_saldo.id DESC";
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
		if($row['status'] == "Disetujui") {
			$isDisabledEdit = true;
			$isDisabledDelete = true;
		}else if($row['status'] == "Ditolak") {
			$isDisabledEdit = true;
		}
		if($row['is_deleted'] == 0) {
			$button = "<div title='Ubah' class='d-inline-block'>
			<a href='edit/pengambilan-saldo/".$row['id']."' class='btn btn-primary btn-sm btn-action mr-1 cursor-pointer".isDisabledHref($isDisabledEdit)."'><i class='fas fa-edit fa-sm'></i></a>
			</div>
			<div title='Hapus' class='d-inline-block'>
			<a class='btn btn-danger btn-sm btn-action mr-1 cursor-pointer trashed".isDisabledHref($isDisabledDelete)."' data-id='".$row['id']."'><i class='fas fa-trash fa-sm'></i></a>
			</div>";
		}else {
			$button = "<div title='Pulihkan' class='d-inline-block'>
			<a class='btn btn-warning btn-sm btn-action mr-1 cursor-pointer restored' data-id='".$row['id']."'><i class='fas fa-recycle fa-sm'></i></a>
			</div>
			<div title='Hapus' class='d-inline-block'>
			<a class='btn btn-danger btn-sm btn-action cursor-pointer deleted' data-id='".$row['id']."'><i class='fas fa-minus-circle fa-sm'></i></a>
			</div>";
		}
		if($row['status'] == "Pengajuan") {
			$badgeStatus = "<span class='badge badge-secondary'>".$row['status']."</span>";
		}else if($row['status'] == "Disetujui") {
			$badgeStatus = "<span class='badge badge-success'>".$row['status']."</span>";
		}else if($row['status'] == "Ditolak") {
			$badgeStatus = "<span class='badge badge-danger'>".$row['status']."</span>";
		}else {
			$badgeStatus = "<span class='badge badge-primary'>".$row['status']."</span>";
		}
		if(isset($row['m_pelanggan_id']) && !empty($row['m_pelanggan_id'])) {
			$badgeTipe = "<span class='badge badge-success'>Pelanggan</span>";
		}else {
			$badgeTipe = "<span class='badge badge-danger'>Petugas</span>";
		}
		$sub_array = array();
		$sub_array[] = null;
		$sub_array[] = "<a href='view/pengambilan-saldo/".$row['id']."' class='link'>".$row['kode']."</a>";
		$sub_array[] = isset($row['nama_pelanggan']) && !empty($row['nama_pelanggan']) ? $row['nama_pelanggan'] . "<br><small>" . $badgeTipe . "</small>" : $row['nama_petugas'] . "<br><small>" . $badgeTipe . "</small>";
		$sub_array[] = "Rp. " . number_format($row['nominal']);
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
	$kategori = "Pelanggan";

	if($form['tipe'] == "Pengguna") {
		$formPengguna = [];
		$formPengguna['kode'] = getCodeIncrement($conn, 't_pengambilan_saldo', 'kode', "KLS/PL/", 5);
		$formPengguna['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
		$formPengguna['nominal'] = mysqli_escape_string($conn, str_replace(',', '', $form['nominal']));
		$formPengguna['m_bank_id'] = $form['m_bank_id'];
		$formPengguna['atas_nama'] = mysqli_escape_string($conn, $form['atas_nama']);
		$formPengguna['nomor_rekening'] = mysqli_escape_string($conn, $form['nomor_rekening']);
		$formPengguna['status'] = "Disetujui";

		$kodePilihan = $form['pelanggan_petugas'];
		$queryGetPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE kode = '$kodePilihan' AND is_deleted = 0");
		$rowGetPelanggan = mysqli_fetch_array($queryGetPelanggan, MYSQLI_ASSOC);

		$queryGetPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE kode = '$kodePilihan' AND is_deleted = 0");
		$rowGetPetugas = mysqli_fetch_array($queryGetPetugas, MYSQLI_ASSOC);

		$formPengguna['m_pelanggan_id'] = isset($rowGetPelanggan['id']) && !empty($rowGetPelanggan['id']) ? $rowGetPelanggan['id'] : null;
		$formPengguna['m_petugas_id'] = isset($rowGetPetugas['id']) && !empty($rowGetPetugas['id']) ? $rowGetPetugas['id'] : null;

		$kategori = isset($formPengguna['m_pelanggan_id']) && !empty($formPengguna['m_pelanggan_id']) ? "Pelanggan" : "Petugas";

		$saldoTersimpan = isset($rowGetPelanggan['saldo']) && !empty($rowGetPelanggan['saldo']) ? $rowGetPelanggan['saldo'] : (isset($rowGetPetugas['saldo']) && !empty($rowGetPetugas['saldo']) ? $rowGetPetugas['saldo'] : 0);

		if($formPengguna['nominal'] > $saldoTersimpan) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Nominal terlalu besar, saldo tidak mencukupi",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}
	}else if($form['tipe'] == "Pelanggan") {
		$formPelanggan = [];
		$formPelanggan['kode'] = getCodeIncrement($conn, 't_pengambilan_saldo', 'kode', "KLS/PL/", 5);
		$formPelanggan['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
		$formPelanggan['nominal'] = mysqli_escape_string($conn, str_replace(',', '', $form['nominal']));
		$formPelanggan['m_bank_id'] = $form['m_bank_id'];
		$formPelanggan['atas_nama'] = mysqli_escape_string($conn, $form['atas_nama']);
		$formPelanggan['nomor_rekening'] = mysqli_escape_string($conn, $form['nomor_rekening']);
		$formPelanggan['status'] = "Pengajuan";
		$formPelanggan['m_pelanggan_id'] = isset($form['m_pelanggan_id']) && !empty($form['m_pelanggan_id']) ? $form['m_pelanggan_id'] : null;

		$idPelanggan = $formPelanggan['m_pelanggan_id'];
		$queryGetPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE id = '$idPelanggan'");
		$rowGetPelanggan = mysqli_fetch_array($queryGetPelanggan, MYSQLI_ASSOC);

		$kategori = "Pelanggan";

		$saldoTersimpan = isset($rowGetPelanggan['saldo']) && !empty($rowGetPelanggan['saldo']) ? $rowGetPelanggan['saldo'] : 0;

		if($formPelanggan['nominal'] > $saldoTersimpan) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Nominal terlalu besar, saldo tidak mencukupi",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}
	}else if($form['tipe'] == "Petugas") {
		$formPetugas = [];
		$formPetugas['kode'] = getCodeIncrement($conn, 't_pengambilan_saldo', 'kode', "KLS/PL/", 5);
		$formPetugas['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
		$formPetugas['nominal'] = mysqli_escape_string($conn, str_replace(',', '', $form['nominal']));
		$formPetugas['m_bank_id'] = $form['m_bank_id'];
		$formPetugas['atas_nama'] = mysqli_escape_string($conn, $form['atas_nama']);
		$formPetugas['nomor_rekening'] = mysqli_escape_string($conn, $form['nomor_rekening']);
		$formPetugas['status'] = "Pengajuan";
		$formPetugas['m_petugas_id'] = isset($form['m_petugas_id']) && !empty($form['m_petugas_id']) ? $form['m_petugas_id'] : null;

		$idPetugas = $formPetugas['m_petugas_id'];
		$queryGetPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE id = '$idPetugas'");
		$rowGetPetugas = mysqli_fetch_array($queryGetPetugas, MYSQLI_ASSOC);

		$kategori = "Petugas";

		$saldoTersimpan = isset($rowGetPetugas['saldo']) && !empty($rowGetPetugas['saldo']) ? $rowGetPetugas['saldo'] : 0;

		if($formPetugas['nominal'] > $saldoTersimpan) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Nominal terlalu besar, saldo tidak mencukupi",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}
	}

	try {

		if($form['tipe'] == "Pengguna") {
			$models = toInsertData($conn, 't_pengambilan_saldo', $formPengguna, true);
		}else if($form['tipe'] == "Pelanggan") {
			$models = toInsertData($conn, 't_pengambilan_saldo', $formPelanggan, true);
		}else if($form['tipe'] == "Petugas") {
			$models = toInsertData($conn, 't_pengambilan_saldo', $formPetugas, true);
		}

		if($models['status'] == true) {
			if($form['tipe'] == "Pengguna") {

				if($formPengguna['status'] == "Disetujui") {
					$dataJurnal = [];
					$dataJurnal['kode'] = getCodeIncrement($conn, 'trans_detail', 'kode', "KLS/TD/", 5);
					$dataJurnal['nomor_jurnal'] = getCodeIncrement($conn, 'trans_detail', 'nomor_jurnal', "KLS/".date('Y')."/".date('m')."/PGL/", 5, true);
					$dataJurnal['tanggal'] = date('Y-m-d');
					$dataJurnal['tipe'] = 'Pengeluaran';
					$dataJurnal['debit'] = 0;
					$dataJurnal['kredit'] = $formPengguna['nominal'];

					if($kategori == "Pelanggan") {
						$idPelanggan = $formPengguna['m_pelanggan_id'];
						$queryDataPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE id = '$idPelanggan'");
						$rowDataPelanggan = mysqli_fetch_array($queryDataPelanggan, MYSQLI_ASSOC);
						
						if(isset($rowDataPelanggan) && !empty($rowDataPelanggan)) {
							$saldoPelanggan = $rowDataPelanggan['saldo'];
							$saldoPelangganBaru = $saldoPelanggan - $formPengguna['nominal'];
							
							$formUpdatePelanggan = [];
							$formUpdatePelanggan['saldo'] = $saldoPelangganBaru;
							
							toUpdateData($conn, 'm_pelanggan', $formUpdatePelanggan, $rowDataPelanggan['id'], null, true);

							$dataJurnal['keterangan'] = "Pengambilan saldo sebesar Rp. " . number_format($formPengguna['nominal']) .  " oleh pelanggan " . $rowDataPelanggan['nama'];
						}
					}else if($kategori == "Petugas") {
						$idPetugas = $formPengguna['m_petugas_id'];
						$queryDataPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE id = '$idPetugas'");
						$rowDataPetugas = mysqli_fetch_array($queryDataPetugas, MYSQLI_ASSOC);
						
						if(isset($rowDataPetugas) && !empty($rowDataPetugas)) {
							$saldoPetugas = $rowDataPetugas['saldo'];
							$saldoPetugasBaru = $saldoPetugas - $formPengguna['nominal'];
							
							$formUpdatePetugas = [];
							$formUpdatePetugas['saldo'] = $saldoPetugasBaru;
							
							toUpdateData($conn, 'm_petugas', $formUpdatePetugas, $rowDataPetugas['id'], null, true);

							$dataJurnal['keterangan'] = "Pengambilan saldo sebesar Rp. " . number_format($formPengguna['nominal']) .  " oleh petugas " . $rowDataPetugas['nama'];
						}
					}
					
					$modelsJurnal = toInsertData($conn, 'trans_detail', $dataJurnal, true);
				}
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
	$getData = toGetDataDetail($conn, 't_pengambilan_saldo', $_POST['id']);
	$form = $_POST;

	$kategori = isset($getData['m_pelanggan_id']) && !empty($getData['m_pelanggan_id']) ? "Pelanggan" : "Petugas";

	if($_SESSION['tipe'] == "Pengguna") {
		$formProses = [];
		$formProses['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
		$formProses['nominal'] = mysqli_escape_string($conn, str_replace(',', '', $form['nominal']));
		$formProses['status'] = $form['status'];
		$formProses['m_pelanggan_id'] = isset($getData['m_pelanggan_id']) && !empty($getData['m_pelanggan_id']) ? $getData['m_pelanggan_id'] : null;
		$formProses['m_petugas_id'] = isset($getData['m_petugas_id']) && !empty($getData['m_petugas_id']) ? $getData['m_petugas_id'] : null;
		$formProses['m_bank_id'] = $form['m_bank_id'];
		$formProses['atas_nama'] = mysqli_escape_string($conn, $form['atas_nama']);
		$formProses['nomor_rekening'] = mysqli_escape_string($conn, $form['nomor_rekening']);
	}else {
		$formPengajuan = [];
		$formPengajuan['tanggal'] = date('Y-m-d', strtotime($form['tanggal']));
		$formPengajuan['nominal'] = mysqli_escape_string($conn, str_replace(',', '', $form['nominal']));
		$formPengajuan['m_bank_id'] = $form['m_bank_id'];
		$formPengajuan['atas_nama'] = mysqli_escape_string($conn, $form['atas_nama']);
		$formPengajuan['nomor_rekening'] = mysqli_escape_string($conn, $form['nomor_rekening']);
	}

	$pelangganID = isset($getData['m_pelanggan_id']) && !empty($getData['m_pelanggan_id']) ? $getData['m_pelanggan_id'] : null;
	$petugasID = isset($getData['m_petugas_id']) && !empty($getData['m_petugas_id']) ? $getData['m_petugas_id'] : null;

	$queryGetPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE id = '$pelangganID' AND is_deleted = 0");
	$rowGetPelanggan = mysqli_fetch_array($queryGetPelanggan, MYSQLI_ASSOC);

	$queryGetPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE id = '$petugasID' AND is_deleted = 0");
	$rowGetPetugas = mysqli_fetch_array($queryGetPetugas, MYSQLI_ASSOC);

	$nominalInput = (int) str_replace(',', '', $form['nominal']);
	$saldoTersimpan = isset($rowGetPelanggan['saldo']) && !empty($rowGetPelanggan['saldo']) ? $rowGetPelanggan['saldo'] : (isset($rowGetPetugas['saldo']) && !empty($rowGetPetugas['saldo']) ? $rowGetPetugas['saldo'] : 0);

	if($nominalInput > $saldoTersimpan) {
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
		if($_SESSION['tipe'] == "Pengguna") {
			$models = toUpdateData($conn, 't_pengambilan_saldo', $formProses, $form['id'], null, true);
		}else {
			$models = toUpdateData($conn, 't_pengambilan_saldo', $formPengajuan, $form['id'], null, true);
		}

		if($models['status'] == true) {
			if($_SESSION['tipe'] == "Pengguna") {

				if($formProses['status'] == "Disetujui") {
					$dataJurnal = [];
					$dataJurnal['kode'] = getCodeIncrement($conn, 'trans_detail', 'kode', "KLS/TD/", 5);
					$dataJurnal['nomor_jurnal'] = getCodeIncrement($conn, 'trans_detail', 'nomor_jurnal', "KLS/".date('Y')."/".date('m')."/PGL/", 5, true);
					$dataJurnal['tanggal'] = date('Y-m-d');
					$dataJurnal['tipe'] = 'Pengeluaran';
					$dataJurnal['debit'] = 0;
					$dataJurnal['kredit'] = $formProses['nominal'];

					if($kategori == "Pelanggan") {
						$idPelanggan = $formProses['m_pelanggan_id'];
						$queryDataPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE id = '$idPelanggan'");
						$rowDataPelanggan = mysqli_fetch_array($queryDataPelanggan, MYSQLI_ASSOC);
						
						if(isset($rowDataPelanggan) && !empty($rowDataPelanggan)) {
							$saldoPelanggan = $rowDataPelanggan['saldo'];
							$saldoPelangganBaru = $saldoPelanggan - $formProses['nominal'];
							
							$formUpdatePelanggan = [];
							$formUpdatePelanggan['saldo'] = $saldoPelangganBaru;
							
							toUpdateData($conn, 'm_pelanggan', $formUpdatePelanggan, $rowDataPelanggan['id'], null, true);

							$dataJurnal['keterangan'] = "Pengambilan saldo sebesar Rp. " . number_format($formProses['nominal']) .  " oleh pelanggan " . $rowDataPelanggan['nama'];
						}
					}else if($kategori == "Petugas") {
						$idPetugas = $formProses['m_petugas_id'];
						$queryDataPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE id = '$idPetugas'");
						$rowDataPetugas = mysqli_fetch_array($queryDataPetugas, MYSQLI_ASSOC);
						
						if(isset($rowDataPetugas) && !empty($rowDataPetugas)) {
							$saldoPetugas = $rowDataPetugas['saldo'];
							$saldoPetugasBaru = $saldoPetugas - $formProses['nominal'];
							
							$formUpdatePetugas = [];
							$formUpdatePetugas['saldo'] = $saldoPetugasBaru;
							
							toUpdateData($conn, 'm_petugas', $formUpdatePetugas, $rowDataPetugas['id'], null, true);

							$dataJurnal['keterangan'] = "Pengambilan saldo sebesar Rp. " . number_format($formProses['nominal']) .  " oleh petugas " . $rowDataPetugas['nama'];
						}
					}

					$modelsJurnal = toInsertData($conn, 'trans_detail', $dataJurnal, true);
				}
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
		$query = mysqli_query($conn, "SELECT * FROM t_pengambilan_saldo WHERE id = '$id'");
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
		$query = mysqli_query($conn, "SELECT * FROM t_pengambilan_saldo WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toTrashData($conn, 't_pengambilan_saldo', $formPost['id'], null, true);

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
		$query = mysqli_query($conn, "SELECT * FROM t_pengambilan_saldo WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$checkData = checkDataExist($conn, 't_pengambilan_saldo', 't_pengambilan_saldo_id', $formPost['id']);
		if(issetEmpty($checkData)) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Tidak dapat melakukan proses hapus data karena data sedang digunakan di menu " . $checkData['child_menu_name'],
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}

		$getData = toGetDataDetail($conn, 't_pengambilan_saldo', $formPost['id']);

		$models = toDeleteData($conn, 't_pengambilan_saldo', $formPost['id']);

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
		$query = mysqli_query($conn, "SELECT * FROM t_pengambilan_saldo WHERE id = '$id'");
		$count = mysqli_num_rows($query);

		$formPost = [];
		if($count > 0) {
			$row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC));
			$formPost['id'] = $row['id'];
		}

		$models = toRestoreData($conn, 't_pengambilan_saldo', $formPost['id'], null, true);

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