<?php
require "../../../config/autoload.php";

$targetDir = '../../../assets/img/'.$_POST['folder'].'/';
$id = $_POST['id'];
$table = $_POST['to_table'];
$from_table = $_POST['from_table'];

$fileList = array();

$queryGet = mysqli_query($conn, "SELECT * FROM $table WHERE $from_table = '$id'");
while($rowGet = mysqli_fetch_array($queryGet, MYSQLI_ASSOC)) {
	$filePath = $targetDir.$rowGet['gambar'];
	$size = filesize($filePath);
	$fileList[] = array('name' => $rowGet['gambar'], 'size' => $size, 'path' => $filePath);
}
echo json_encode($fileList);
exit;
?>