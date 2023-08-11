<?php
set_error_handler("errorHandler");
register_shutdown_function("shutdownHandler");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

if(isset($_SESSION['id'])) {
	$idUser = $_SESSION['id'];
	if(isset($_SESSION['tipe']) && !empty($_SESSION['tipe'])) {
		if($_SESSION['tipe'] == "Pengguna") {
			$queryAkses = mysqli_query($conn, "SELECT m_hak_akses.akses, m_hak_akses.status_akses FROM m_pengguna LEFT JOIN m_hak_akses ON m_pengguna.m_hak_akses_id = m_hak_akses.id WHERE m_pengguna.id = '$idUser'");
		}else if($_SESSION['tipe'] == "Pelanggan") {
			$queryAkses = mysqli_query($conn, "SELECT m_hak_akses.akses, m_hak_akses.status_akses FROM m_pelanggan LEFT JOIN m_hak_akses ON m_pelanggan.m_hak_akses_id = m_hak_akses.id WHERE m_pelanggan.id = '$idUser'");
		}else if($_SESSION['tipe'] == "Petugas") {
			$queryAkses = mysqli_query($conn, "SELECT m_hak_akses.akses, m_hak_akses.status_akses FROM m_petugas LEFT JOIN m_hak_akses ON m_petugas.m_hak_akses_id = m_hak_akses.id WHERE m_petugas.id = '$idUser'");
		}else {
			$queryAkses = mysqli_query($conn, "SELECT m_hak_akses.akses, m_hak_akses.status_akses FROM m_pengguna LEFT JOIN m_hak_akses ON m_pengguna.m_hak_akses_id = m_hak_akses.id WHERE m_pengguna.id = '$idUser'");            
		}
	}else {
		$queryAkses = mysqli_query($conn, "SELECT m_hak_akses.akses, m_hak_akses.status_akses FROM m_pengguna LEFT JOIN m_hak_akses ON m_pengguna.m_hak_akses_id = m_hak_akses.id WHERE m_pengguna.id = '$idUser'");
	}
	$rowAkses = mysqli_fetch_array($queryAkses, MYSQLI_ASSOC);
}

$querySEO = mysqli_query($conn, "SELECT * FROM p_konfigurasi_seo ORDER BY id ASC LIMIT 1");
if(issetEmpty($querySEO)) {
	$rowSeo = mysqli_fetch_array($querySEO, MYSQLI_ASSOC);
}

$queryUmum = mysqli_query($conn, "SELECT * FROM p_konfigurasi_umum ORDER BY id ASC LIMIT 1");
if(issetEmpty($queryUmum)) {
	$rowUmum = mysqli_fetch_array($queryUmum, MYSQLI_ASSOC);
	$rowSeo['nama_website'] = $rowUmum['nama_website'];
}

$dataSEO = getDetailSEO($rowSeo);

$datesNow = date('Y-m-d');
$daysNow = formatDateIndonesia(date('D', strtotime($datesNow)));

$waktuSekarang_ = date('Y-m-d H:i:s');
mysqli_query($conn, "UPDATE t_pengisian_saldo SET status = 'Ditolak' WHERE status = 'Pengajuan' AND batas_waktu <= '$waktuSekarang_'");
?>