<?php
require "../../config/autoloads.php";
require "../../vendor/tcpdf/tcpdf.php";
require '../../vendor/phpoffice/vendor/autoload.php';
require "../../vendor/excelreader/excel_reader2.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

call_user_func($_POST['function'], $conn);

function exportData($conn) {
	$form = $_POST;

	if($form['jenis'] == "excel") {
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();

		$spreadsheet = $reader->load("../format_excel/export/laporan-pelanggan.xls");

		$sheet = $spreadsheet->getActiveSheet();

		$style_col = [
			'font' => ['bold' => true],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
			]
		];

		$style_row = [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
			],
			'borders' => [
				'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
				'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
			]
		];

		$index = 6;

		if($form['kategori_filter'] == "Pilihan Otomatis") {

			if($form['pilihan'] == "Hari Ini") {
				$tanggal = date('Y-m-d');
				$periode_laporan = "Periode : " . formatDateIndonesia(date('d F Y', strtotime($tanggal)));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Kemarin") {
				$tanggal = date('Y-m-d', strtotime(date('Y-m-d') . "-1 day"));
				$periode_laporan = "Periode : " . formatDateIndonesia(date('d F Y', strtotime($tanggal)));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "7 Hari Terakhir") {
				$tanggal_akhir = date('Y-m-d');
				$tanggal_awal = date('Y-m-d', strtotime($tanggal_akhir . "-7 day"));
				$periode_laporan = "Periode : " . formatDateIndonesia(date('d F Y', strtotime($tanggal_awal))) . " - " . formatDateIndonesia(date('d F Y', strtotime($tanggal_akhir)));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Bulan Ini") {
				$tanggal = date('Y-m');
				$periode_laporan = "Periode : " . formatDateIndonesia(date('F Y', strtotime(date('Y-m-d'))));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Bulan Kemarin") {
				$tanggal = date('Y-m', strtotime(date('Y-m-d') . "-1 month"));
				$periode_laporan = "Periode : " . formatDateIndonesia(date('F Y', strtotime(date('Y-m-d') . "-1 month")));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Tahun Ini") {
				$tanggal = date('Y');
				$periode_laporan = "Periode : " . formatDateIndonesia(date('Y'));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Tahun Kemarin") {
				$tanggal = date('Y', strtotime(date('Y-m-d') . "-1 year"));
				$periode_laporan = "Periode : " . formatDateIndonesia(date('Y', strtotime(date('Y-m-d') . "-1 year")));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Semua") {
				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' ORDER BY kode ASC");
			}else {
				$tanggal = date('Y');
				$periode_laporan = "Periode : " . formatDateIndonesia(date('Y'));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}

		}else if($form['kategori_filter'] == "Rentang Tanggal") {
			$split_periode = explode(" - ", $form['periode']);
			$tanggal_awal = date('Y-m-d', strtotime($split_periode[0]));
			$tanggal_akhir = date('Y-m-d', strtotime($split_periode[1]));

			$periode_laporan = "Periode : " . formatDateIndonesia(date('d F Y', strtotime($split_periode[0]))) . " - " . formatDateIndonesia(date('d F Y', strtotime($split_periode[1])));

			$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY kode ASC");
		}else {
			$tanggal = date('Y');
			$periode_laporan = "Periode : " . formatDateIndonesia(date('Y'));

			$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
		}

		$sheet->setCellValue('A2', "Periode : " . $periode_laporan);

		while($row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC))) {
			if($row['is_deleted'] == 0) {
				$status = "Tidak Terhapus";
			}else {
				$status = "Terhapus";
			}
			$sheet->setCellValue('A'.$index, $row['kode']);
			$sheet->setCellValue('B'.$index, $row['nama']);
			$sheet->setCellValue('C'.$index, $row['jenis_kelamin']);
            $sheet->setCellValue('D'.$index, $row['tempat_lahir']);
            $sheet->setCellValue('E'.$index, $row['tanggal_lahir']);
            $sheet->setCellValue('F'.$index, $row['telepon']);
            $sheet->setCellValue('G'.$index, $row['email']);
            $sheet->setCellValue('H'.$index, $row['alamat']);
            $sheet->setCellValue('I'.$index, $row['saldo']);
			$sheet->setCellValue('J'.$index, $status);
			$sheet->setCellValue('K'.$index, formatDateIndonesia(date('d F Y, H:i', strtotime($row['created_at']))));

            $index++;
		}

        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

		$spreadsheet->getProperties()
		->setCreator("Kelola Sampah V2")
		->setTitle("Laporan Pelanggan")
		->setCompany("Kelola Sampah V2")
		->setSubject("Data Laporan");

		$spreadsheet->getProperties()->setDescription('Data Laporan Pelanggan Periode ' . $periode_laporan);

		$spreadsheet->getProperties()->setKeywords("excel, laporan pelanggan")
		->setCategory("Master");

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="laporan_pelanggan_'.date('Ymd').'.xls"');
		header('Cache-Control: max-age=0');

		$writer = new Xls($spreadsheet);
		$writer->save('php://output');
	}else {

		if($form['kategori_filter'] == "Pilihan Otomatis") {

			if($form['pilihan'] == "Hari Ini") {
				$tanggal = date('Y-m-d');
				$periode_laporan = "Periode : " . formatDateIndonesia(date('d F Y', strtotime($tanggal)));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Kemarin") {
				$tanggal = date('Y-m-d', strtotime(date('Y-m-d') . "-1 day"));
				$periode_laporan = "Periode : " . formatDateIndonesia(date('d F Y', strtotime($tanggal)));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "7 Hari Terakhir") {
				$tanggal_akhir = date('Y-m-d');
				$tanggal_awal = date('Y-m-d', strtotime($tanggal_akhir . "-7 day"));
				$periode_laporan = "Periode : " . formatDateIndonesia(date('d F Y', strtotime($tanggal_awal))) . " - " . formatDateIndonesia(date('d F Y', strtotime($tanggal_akhir)));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Bulan Ini") {
				$tanggal = date('Y-m');
				$periode_laporan = "Periode : " . formatDateIndonesia(date('F Y', strtotime(date('Y-m-d'))));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Bulan Kemarin") {
				$tanggal = date('Y-m', strtotime(date('Y-m-d') . "-1 month"));
				$periode_laporan = "Periode : " . formatDateIndonesia(date('F Y', strtotime(date('Y-m-d') . "-1 month")));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Tahun Ini") {
				$tanggal = date('Y');
				$periode_laporan = "Periode : " . formatDateIndonesia(date('Y'));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Tahun Kemarin") {
				$tanggal = date('Y', strtotime(date('Y-m-d') . "-1 year"));
				$periode_laporan = "Periode : " . formatDateIndonesia(date('Y', strtotime(date('Y-m-d') . "-1 year")));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}else if($form['pilihan'] == "Semua") {
				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' ORDER BY kode ASC");
			}else {
				$tanggal = date('Y');
				$periode_laporan = "Periode : " . formatDateIndonesia(date('Y'));

				$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
			}

		}else if($form['kategori_filter'] == "Rentang Tanggal") {
			$split_periode = explode(" - ", $form['periode']);
			$tanggal_awal = date('Y-m-d', strtotime($split_periode[0]));
			$tanggal_akhir = date('Y-m-d', strtotime($split_periode[1]));

			$periode_laporan = "Periode : " . formatDateIndonesia(date('d F Y', strtotime($split_periode[0]))) . " - " . formatDateIndonesia(date('d F Y', strtotime($split_periode[1])));

			$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at BETWEEN '$tanggal_awal' AND '$tanggal_akhir' ORDER BY kode ASC");
		}else {
			$tanggal = date('Y');
			$periode_laporan = "Periode : " . formatDateIndonesia(date('Y'));

			$query = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' AND created_at LIKE '$tanggal%' ORDER BY kode ASC");
		}

		class MYPDF extends TCPDF {
			public function Header() {
				$this->Image('@'.file_get_contents('../../assets/img/default/kop.png'), 17.5, 2, 175);
				$this->SetFont('times', 'B', 20);
				$style = ['width' => 0.35, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
				$this->SetLineStyle($style);
				$this->SetY(38);
				$this->Line(PDF_MARGIN_LEFT, $this->getY(), $this->getPageWidth() - PDF_MARGIN_LEFT, $this->getY());
				$this->Ln();
				$this->SetTopMargin(40);
			}

			public function Footer() {
				$this->SetFont('times', '', 11);
				$this->WriteHTML('© Kelola Sampah V2', true, false, false, false, 'L');
			}
		}

		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Kelola Sampah V2');
		$pdf->SetTitle('Laporan Pelanggan');
		$pdf->SetSubject('Data Laporan Pelanggan');
		$pdf->SetKeywords('pdf, laporan pelanggan, pelanggan');
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, 19.9);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		$pdf->SetFont('times', 'B', 20);
		$pdf->AddPage();

		$text = <<<EOD
		LAPORAN PELANGGAN
		EOD;

		$pdf->Write(0, $text, '', 0, 'C', true, 0, false, false, 0);
		$pdf->SetFont('times', '', 11);
		$pdf->setY(51.3);

		$content1 = '';
		$content1 .= '<br><br><b>'.$periode_laporan.'</b><br><table style="width: 100%;" border="1" cellpading="5" cellspacing="0">
		<thead>
		<tr style="font-size: 12px;">
		<td style="width: 5%;" align="center">No</td>
		<td style="width: 13%;" align="left">Kode</td>
		<td style="width: 36%;" align="left">Nama</td>
		<td style="width: 16%;" align="left">Telepon</td>
        <td style="width: 15%;" align="center">Jenis Kelamin</td>
		<td style="width: 15%;" align="right">Saldo</td>
		</tr>
		</thead>
		<tbody>';

		$no = 1;
		while($row = allRowValidateHTML(mysqli_fetch_array($query, MYSQLI_ASSOC))) {
			$content1 .= '<tr style="font-size:12px;" nobr="true">
			<td style="width: 5%;" align="center">'.$no++.'</td>
			<td style="width: 13%;" align="left">'.$row['kode'].'</td>
			<td style="width: 36%;" align="left">'.$row['nama'].'</td>
            <td style="width: 16%;" align="left">'.$row['telepon'].'</td>
            <td style="width: 15%;" align="center">'.$row['jenis_kelamin'].'</td>
			<td style="width: 15%;" align="right">Rp. '.number_format($row['saldo']).'</td>
			</tr>';
		}

		$content1 .= '</tbody>
		</table>';

		$pdf->writeHTML($content1, true, false, true, false, '');

		$content2 = '
		<br><br><br><br>
		<table style="width: 100%;" border="0" cellpadding="2" cellspacing="0" nobr="true">
		<tr style="text-align: center;">
		<td style="width: 50%;"></td>
		<td style="width: 50%; text-align:center;">
		<table border="0">
		<tr>
		<td>Handil Bakti, '.formatDateIndonesia(date('d F Y')).'</td>
		</tr>
		<tr>
		<td>Kelola Sampah V2</td>
		</tr>
		<tr>
		<td></td>
		</tr>
		<tr>
		<td></td>
		</tr>
		<tr>
		<td></td>
		</tr>
		<tr>
		<td></td>
		</tr>
		<tr>
		<td></td>
		</tr>
		<tr>
		<td>( . . . . . . . . . . . . . . . . . . .)</td>
		</tr>
		<tr>
		<td>Administrator</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>';

		$pdf->writeHTML($content2, true, false, false, false, '');

		$pdf->lastPage();

		$pdf->Output('laporan_pelanggan_'.date('Ymd').'.pdf', 'D');
	}
}
?>