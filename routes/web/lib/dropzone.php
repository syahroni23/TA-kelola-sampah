<?php
require "../../../config/autoload.php";
$uploadDir = '../../../assets/img/'.$_POST['toFolder'];

if (issetEmpty($_FILES)) {
	$form = [];
	$form[$_POST['fromTable']] = $_POST['fromId'];
	$form['gambar'] = getFileName($_FILES['file']);

	toInsertData($conn, $_POST['toTable'], $form);

	if(issetEmpty($_FILES['file']['name'])) {
		uploadFile($_FILES['file'], $uploadDir, $form['gambar']);
	}

	echo json_encode(['message' => 'success']);
}
?>