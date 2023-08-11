<?php
function getUserIP() {
	if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
		$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	$client  = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : null;
	$forward = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;
	$remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
	if(isset($client) && filter_var($client, FILTER_VALIDATE_IP)) {
		$ip = $client;
	}elseif(isset($forward) && filter_var($forward, FILTER_VALIDATE_IP)){
		$ip = $forward;
	}else {
		$ip = $remote;
	}
	return $ip;
}
function getClientBrowser() {
	$browser = '';
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape'))
		$browser = 'Netscape';
	else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox'))
		$browser = 'Firefox';
	else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome'))
		$browser = 'Chrome';
	else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera'))
		$browser = 'Opera';
	else if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
		$browser = 'Internet Explorer';
	else
		$browser = 'Other';
	return $browser;
}
function formatDateIndonesia($str) {
	$tr = trim($str);
	$str = str_replace(array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'), array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'), $tr);
	return $str;
}
function formatMonthIndonesia($data) {
	$array_bln = array(1 => "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	$bln = $array_bln[$data];
	return $bln;
}
function limitText($text, $limit) {
	if (str_word_count($text, 0) > $limit) {
		$words = str_word_count($text, 2);
		$pos   = array_keys($words);
		$text  = substr($text, 0, $pos[$limit]) . '...';
	}
	return $text;
}
function dateCompare($element1, $element2) {
	$datetime1 = strtotime($element1['created']);
	$datetime2 = strtotime($element2['created']);
	return $datetime1 - $datetime2;
} 
function getInitialString($string = null) {
	return array_reduce(
		explode(' ', $string),
		function ($initials, $word) {
			return sprintf('%s%s', $initials, substr($word, 0, 1));
		},
		''
	);
}
function splitText($value, $batas) {
	$value = strip_tags($value);
	if (strlen($value) > $batas) {
		return substr($value, 0, $batas) . "...";
	} else {
		return $value;
	}
}
function formatRupiah($price = 0, $prefix = true, $decimal = 0) {
	if($price === '-' || empty($price)) {
		return '';
	}else {
		if($prefix === "-") {
			return $price;
		}else {
			$rp = ($prefix) ? 'Rp. ' : '';

			if($price < 0) {
				$price  = (float) $price * -1;
				$result = '(' . $rp . number_format($price, $decimal, ",", ".") . ')';
			}else {
				$price  = (float) $price;
				$result = $rp . number_format($price, $decimal, ",", ".");
			}
			return $result;
		}
	}
}
function timeSince($original, $format_text = 'true') {
	$chunks = array(array(60 * 60 * 24 * 365, 'tahun'), array(60 * 60 * 24 * 30, 'bulan'), array(60 * 60 * 24 * 7, 'minggu'), array(60 * 60 * 24, 'hari'), array(60 * 60, 'jam'), array(60, 'menit'));

	$today = time();
	$since = $today - $original;
	for($i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		if(($count = floor($since / $seconds)) != 0) {
			break;
		}
	}
	$print = ($count == 1) ? '1 '.$name : "$count {$name}";

	if($format_text == 'true') {
		return $print.' yang lalu';
	}else {
		return $print;
	}
}
function denominatorNumbers($nilai) {
	$nilai = abs($nilai);
	$huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
	$temp = "";
	if ($nilai < 12) {
		$temp = " ". $huruf[$nilai];
	} else if ($nilai < 20) {
		$temp = denominatorNumbers($nilai - 10). " Belas";
	} else if ($nilai < 100) {
		$temp = denominatorNumbers($nilai / 10)." Puluh". denominatorNumbers($nilai % 10);
	} else if ($nilai < 200) {
		$temp = " Seratus" . denominatorNumbers($nilai - 100);
	} else if ($nilai < 1000) {
		$temp = denominatorNumbers($nilai / 100) . " Ratus" . denominatorNumbers($nilai % 100);
	} else if ($nilai < 2000) {
		$temp = " Seribu" . denominatorNumbers($nilai - 1000);
	} else if ($nilai < 1000000) {
		$temp = denominatorNumbers($nilai / 1000) . " Ribu" . denominatorNumbers($nilai % 1000);
	} else if ($nilai < 1000000000) {
		$temp = denominatorNumbers($nilai / 1000000) . " Juta" . denominatorNumbers($nilai % 1000000);
	} else if ($nilai < 1000000000000) {
		$temp = denominatorNumbers($nilai / 1000000000) . " Milyar" . denominatorNumbers(fmod($nilai, 1000000000));
	} else if ($nilai < 1000000000000000) {
		$temp = denominatorNumbers($nilai / 1000000000000) . " Trilyun" . denominatorNumbers(fmod($nilai, 1000000000000));
	}     
	return $temp;
}
function spelledNumbers($nilai) {
	if($nilai < 0) {
		$hasil = "Minus ". trim(denominatorNumbers($nilai));
	} else {
		$hasil = trim(denominatorNumbers($nilai));
	}           
	return $hasil . " Rupiah";
}
function sendNotification($title,$message,$topic,$from,$idBerita) {
	$a = $message;
	$fields = array(
		'to' => '/topics/'.$topic,
		"notification" => array(
			"title"         => $title,
			"body"          => "$a",
			"click_action"  => "FLUTTER_NOTIFICATION_CLICK",
			"sound"         => "default"
		),
		"data" => [
			"body" => array(
				"message"     => $message,
				"from"        => $from,
				"id_berita"   => $idBerita
			),
			"click_action" => "FLUTTER_NOTIFICATION_CLICK"
		]
	);

	$url = 'https://fcm.googleapis.com/fcm/send';
	$headers = array(
		'Authorization: key=AAAAwilvHs0:APA91bH5qq2rVu3ryMgqaenPlJrr6PcmNoUb70ajT9Pv7CIcQHQYgHVrziDAFAXStWGa-zwH244f64G3dTpIg4Qron1OUIFWyxNYGNcOuLWcuM2m1ZqbdOOOOW4-5nqONrk0MsGVCMS6',
		'Content-Type: application/json'
	);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

	$result = curl_exec($ch);
	if ($result === FALSE) {
		die('Curl failed: ' . curl_error($ch));
	}

	curl_close($ch);
	return $result;
}
function convertJson($array, $namekey) {
	$model = (object)[
		'value' => json_decode(json_decode($array[$namekey])->json),
		'string' => implode(",", json_decode(json_decode($array[$namekey])->json)),
		'length' => json_decode($array[$namekey])->length
	];
	return $model;
}
function implodeParse($data, $key) {
	$models = array();
	foreach ($data as $value) {
		$models[] = $value->{$key};
	}
	$models = implode(",", $models);
	$models = empty($models) ? 0 : $models;
	return $models;
}
function getCodeIncrement($conn, $table_name, $field_name, $string_code, $length, $with_string_code = false) {
	$lengthKode = strlen($string_code);
	if($with_string_code == true) {
		$getKode = mysqli_query($conn, "SELECT MAX({$field_name}) as maxKode FROM {$table_name} WHERE {$field_name} LIKE '%{$string_code}%'");
	}else {
		$getKode = mysqli_query($conn, "SELECT MAX({$field_name}) as maxKode FROM {$table_name}");
	}
	$rowKode = mysqli_fetch_array($getKode, MYSQLI_ASSOC);
	$showKode = $rowKode['maxKode'];
	$noUrutKode = (int) substr($showKode, $lengthKode, $length);
	$noUrutKode++;
	$charKode = $string_code;
	$myKode = $charKode . sprintf("%0{$length}s", $noUrutKode);
	return $myKode;
}
function formatRomawi($num) {
	$n = intval($num);
	$res = '';

	$romanNumber_Array = [
		'M' => 1000,
		'CM' => 900,
		'D' => 500,
		'CD' => 400,
		'C' => 100,
		'XC' => 90,
		'L' => 50,
		'XL' => 40,
		'X' => 10,
		'IX' => 9,
		'V' => 5,
		'IV' => 4,
		'I' => 1,
	];

	foreach ($romanNumber_Array as $roman => $number) {
		$matches = intval($n / $number);

		$res .= str_repeat($roman, $matches);

		$n = $n % $number;
	}
	return $res;
}
function sendMessageTelegram($messaggio) {
	global $conn;

	$query = mysqli_query($conn, "SELECT * FROM p_konfigurasi_umum ORDER BY id ASC LIMIT 1");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);

	if(issetEmpty($row['telegram_token']) && issetEmpty($row['telegram_chat_id'])) {
		$token      = $row['telegram_token'];
		$chat_id    = $row['telegram_chat_id'];
		$thread_id  = $row['telegram_thread_id'];
		$result     = '';
		
		if (!empty($chat_id)) {
			$url        = "https://api.telegram.org/bot".$token."/sendMessage?chat_id=".$chat_id."&message_thread_id=".$thread_id."&text=".urlencode($messaggio)."&parse_mode=HTML";
			$ch         = curl_init();
			$optArray   = [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true];
			
			curl_setopt_array($ch, $optArray);
			
			$result = curl_exec($ch);
			
			curl_close($ch);
		}
		
		return $result;
	}else {
		return [];
	}
}
function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context) {
	$error = "lvl: " . $error_level . " | msg:" . $error_message . " | file:" . $error_file . " | ln:" . $error_line;
	switch ($error_level) {
		case E_ERROR:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_PARSE:
		mylog($error, "fatal");
		break;
		case E_USER_ERROR:
		case E_RECOVERABLE_ERROR:
		mylog($error, "error");
		break;
		case E_WARNING:
		case E_CORE_WARNING:
		case E_COMPILE_WARNING:
		case E_USER_WARNING:
		mylog($error, "warn");
		break;
		case E_NOTICE:
		case E_USER_NOTICE:
		mylog($error, "info");
		break;
		case E_STRICT:
		mylog($error, "debug");
		break;
		default:
		mylog($error, "warn");
	}
}
function shutdownHandler() {
	$lasterror = error_get_last();
	if(isset($lasterror) && !empty($lasterror)) {
		switch ($lasterror['type']) {
			case E_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_PARSE:
			$error = "[SHUTDOWN] lvl:" . $lasterror['type'] . " \nMessage:" . $lasterror['message'] . " \nFile:" . $lasterror['file'] . " line:" . $lasterror['line'];
			mylog($error, "fatal");
		}
	}
}
function mylog($error, $errlvl) {
	$username = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Not found';
	$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];
	$text = "<pre>";
	$text .= date("d-m-Y H:i:s"). " \n \n";
	$text .= "Url : ". $link. "\n \n";
	$text .= "IP : ".getUserIP()." | User : ".$username." \n \n";
	$text .= $error." </pre>";

	sendMessageTelegram($text);
}
function checkImageSize($value, $max_value) {
	if(($value >= $max_value) || ($value == 0)) {
		return false;
	}else {
		return true;
	}
}
function checkImageType($value, $list_type) {
	if((!in_array($value, $list_type)) && (!empty($value))) {
		return false;
	}else {
		return true;
	}
}
function autoVersionFile($file) {
	if(strpos($file, '/') !== 0 || !file_exists($_SERVER['DOCUMENT_ROOT'] . $file))
		return $file;

	$mtime = filemtime($_SERVER['DOCUMENT_ROOT'] . $file);
	return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
}
function clearCacheFile($file) {
	return $file."?".substr(round(microtime(true) * 1000), -3);
}
function pathToFile($file) {
	$lastPathURI = array_slice(explode('/', rtrim($_SERVER['REQUEST_URI'], '/')), -2)[0];
	if($lastPathURI == "app" || $lastPathURI == "kelola-sampah-v2") {
		return "../".$file;
	}else if($lastPathURI == "add" || $lastPathURI == "edit" || $lastPathURI == "view") {
		return "../../".$file;
	}else {
		return "../../../".$file;
	}
}
function makeSlug($conn, $table_name, $field_name, $title, $id, $type) {
	$slug = "";
	if($type == "create") {
		$slug = preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($title)));

		$query = mysqli_query($conn, "SELECT {$field_name} FROM {$table_name} WHERE {$field_name} LIKE '$slug%'");
		if(mysqli_num_rows($query) > 0) {
			while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
				$data[] = $row[$field_name];
			}
			if(in_array($slug, $data)) {
				$count = 0;
				while(in_array(($slug."-".++$count), $data));
				$slug = $slug."-".$count;
			}
		}
	}else {

	}
	return $slug;
}
function makeSlugV2($conn, $table_name, $field_name, $title, $type) {
	$slug = "";
	$time1 = round(microtime(true) * 1000);
	$time2 = round(microtime(true) * 2000);

	$rand = substr($time2, -3) . substr($time1, -4);
	$slug = preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($title)));
	return $slug."-".$rand;
}
function includeWithParameters($phpinclude) {
	$pos_incl = strpos($phpinclude, '?');
	if ($pos_incl !== FALSE) {
		$qry_string = substr($phpinclude, $pos_incl+1);
		$phpinclude = substr($phpinclude, 0, $pos_incl);
		$arr_qstr = explode('&',$qry_string);
		foreach ($arr_qstr as $param_value) {
			list($qstr_name, $qstr_value) = explode('=', $param_value);
			$$qstr_name = $qstr_value;
		}
	}
	include($phpinclude);
}
function toInsertData($conn, $table_name, $data, $with_created = true) {
	unset($data['function']);

	$idName = toGetPrimaryKeyName($conn, $table_name);

	$field_name = [];
	$field_value = [];

	foreach ($data as $key => $value) {
		$field_name[] = $key;
	}

	foreach ($data as $key => $value) {
		if($value === 0) {
			$value = "0";
		}
		$field_value[] = $value != null ? "'".$value."'" : "null";
	}

	$field_name = implode(', ', $field_name);
	$field_value = implode(', ', $field_value);
	$created_by = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
	$created_by_type = isset($_SESSION['tipe']) ? ($_SESSION['tipe'] == "Pengguna" ? "m_pengguna_id" : ($_SESSION['tipe'] == "Pelanggan" ? "m_pelanggan_id" : ($_SESSION['tipe'] == "Petugas" ? "m_petugas_id" : "m_pengguna_id"))) : "m_pengguna_id";
	$modified_by = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
	$modified_by_type = isset($_SESSION['tipe']) ? ($_SESSION['tipe'] == "Pengguna" ? "m_pengguna_id" : ($_SESSION['tipe'] == "Pelanggan" ? "m_pelanggan_id" : ($_SESSION['tipe'] == "Petugas" ? "m_petugas_id" : "m_pengguna_id"))) : "m_pengguna_id";

	if($with_created == true) {
		$query = mysqli_query($conn, "INSERT INTO {$table_name}($field_name, created_at, created_by, created_by_type, modified_at, modified_by, modified_by_type) VALUES($field_value, NOW(), {$created_by}, '{$created_by_type}', NOW(), {$modified_by}, '{$modified_by_type}')");
	}else {
		$query = mysqli_query($conn, "INSERT INTO {$table_name}($field_name) VALUES($field_value)");
	}

	if($query) {
		return array('status' => true, $idName => mysqli_insert_id($conn));
	}else {
		return array('status' => false, 'message' => mysqli_error($conn));
	}
}
function toUpdateData($conn, $table_name, $data, $key_value, $by = null, $with_created = true) {
	unset($data['function']);

	$idName = toGetPrimaryKeyName($conn, $table_name);

	$set_field = "";
	$x = 1;

	foreach ($data as $key => $value) {
		if($value === 0) {
			$value = "0";
		}
		$value = $value != null ? "\"{$value}\"" : "null";
		$set_field .= "{$key} = {$value}";
		if($x < count($data)) {
			$set_field .= ",";
		}
		$x++;
	}

	$created_by = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
	$created_by_type = isset($_SESSION['tipe']) ? ($_SESSION['tipe'] == "Pengguna" ? "m_pengguna_id" : ($_SESSION['tipe'] == "Pelanggan" ? "m_pelanggan_id" : ($_SESSION['tipe'] == "Petugas" ? "m_petugas_id" : "m_pengguna_id"))) : "m_pengguna_id";
	$modified_by = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
	$modified_by_type = isset($_SESSION['tipe']) ? ($_SESSION['tipe'] == "Pengguna" ? "m_pengguna_id" : ($_SESSION['tipe'] == "Pelanggan" ? "m_pelanggan_id" : ($_SESSION['tipe'] == "Petugas" ? "m_petugas_id" : "m_pengguna_id"))) : "m_pengguna_id";

	if(issetEmpty($by)) {
		if($with_created == true) {
			$query = mysqli_query($conn, "UPDATE {$table_name} SET {$set_field}, modified_at = NOW(), modified_by = {$modified_by}, modified_by_type = '{$modified_by_type}' WHERE $by = '{$key_value}'");
		}else {
			$query = mysqli_query($conn, "UPDATE {$table_name} SET {$set_field} WHERE $by = '{$key_value}'");
		}
	}else {
		if($with_created == true) {
			$query = mysqli_query($conn, "UPDATE {$table_name} SET {$set_field}, modified_at = NOW(), modified_by = {$modified_by}, modified_by_type = '{$modified_by_type}' WHERE $idName = {$key_value}");
		}else {
			$query = mysqli_query($conn, "UPDATE {$table_name} SET {$set_field} WHERE $idName = {$key_value}");
		}
	}

	if($query) {
		return array('status' => true, $idName => $key_value);
	}else {
		return array('status' => false, 'message' => mysqli_error($conn));
	}
}
function toGetData($conn, $table_name) {
	$idName = toGetPrimaryKeyName($conn, $table_name);

	$query = mysqli_query($conn, "SELECT * FROM {$table_name} ORDER BY $idName DESC LIMIT 1");
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);

	return $row;
}
function toGetDataDetail($conn, $table_name, $key_value, $by = null) {
	$idName = toGetPrimaryKeyName($conn, $table_name);

	if(issetEmpty($by)) {
		$query = mysqli_query($conn, "SELECT * FROM {$table_name} WHERE $by = '{$key_value}'");
	}else {
		$query = mysqli_query($conn, "SELECT * FROM {$table_name} WHERE $idName = {$key_value}");
	}
	$row = mysqli_fetch_array($query, MYSQLI_ASSOC);

	return $row;
}
function toGetPrimaryKeyName($conn, $table_name) {
	$getNameOfID = mysqli_fetch_array(
		mysqli_query($conn, "SHOW KEYS FROM {$table_name} WHERE Key_name = 'PRIMARY'"), MYSQLI_ASSOC
	);

	return $getNameOfID['Column_name'];
}
function toTrashData($conn, $table_name, $key_value, $by = null, $with_created = true) {
	$idName = toGetPrimaryKeyName($conn, $table_name);

	$created_by = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
	$created_by_type = isset($_SESSION['tipe']) ? ($_SESSION['tipe'] == "Pengguna" ? "m_pengguna_id" : ($_SESSION['tipe'] == "Pelanggan" ? "m_pelanggan_id" : ($_SESSION['tipe'] == "Petugas" ? "m_petugas_id" : "m_pengguna_id"))) : "m_pengguna_id";
	$modified_by = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
	$modified_by_type = isset($_SESSION['tipe']) ? ($_SESSION['tipe'] == "Pengguna" ? "m_pengguna_id" : ($_SESSION['tipe'] == "Pelanggan" ? "m_pelanggan_id" : ($_SESSION['tipe'] == "Petugas" ? "m_petugas_id" : "m_pengguna_id"))) : "m_pengguna_id";

	if(issetEmpty($by)) {
		if($with_created == true) {
			mysqli_query($conn, "UPDATE {$table_name} SET is_deleted = 1, modified_at = NOW(), modified_by = {$modified_by}, modified_by_type = '{$modified_by_type}' WHERE $by = '{$key_value}'");
		}else {
			mysqli_query($conn, "UPDATE {$table_name} SET is_deleted = 1 WHERE $by = '{$key_value}'");
		}
	}else {
		if($with_created == true) {
			$query = mysqli_query($conn, "UPDATE {$table_name} SET is_deleted = 1, modified_at = NOW(), modified_by = {$modified_by}, modified_by_type = '{$modified_by_type}' WHERE $idName = {$key_value}");
		}else {
			mysqli_query($conn, "UPDATE {$table_name} SET is_deleted = 1 WHERE $idName = {$key_value}");
		}
	}

	return $key_value;
}
function toDeleteData($conn, $table_name, $key_value, $by = null) {
	$idName = toGetPrimaryKeyName($conn, $table_name);

	if(issetEmpty($by)) {
		mysqli_query($conn, "DELETE FROM {$table_name} WHERE $by = '{$key_value}'");
	}else {
		mysqli_query($conn, "DELETE FROM {$table_name} WHERE $idName = {$key_value}");
	}

	return $key_value;
}
function toRestoreData($conn, $table_name, $key_value, $by = null, $with_created = true) {
	$idName = toGetPrimaryKeyName($conn, $table_name);

	$created_by = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
	$created_by_type = isset($_SESSION['tipe']) ? ($_SESSION['tipe'] == "Pengguna" ? "m_pengguna_id" : ($_SESSION['tipe'] == "Pelanggan" ? "m_pelanggan_id" : ($_SESSION['tipe'] == "Petugas" ? "m_petugas_id" : "m_pengguna_id"))) : "m_pengguna_id";
	$modified_by = isset($_SESSION['id']) ? $_SESSION['id'] : 1;
	$modified_by_type = isset($_SESSION['tipe']) ? ($_SESSION['tipe'] == "Pengguna" ? "m_pengguna_id" : ($_SESSION['tipe'] == "Pelanggan" ? "m_pelanggan_id" : ($_SESSION['tipe'] == "Petugas" ? "m_petugas_id" : "m_pengguna_id"))) : "m_pengguna_id";

	if(issetEmpty($by)) {
		if($with_created == true) {
			mysqli_query($conn, "UPDATE {$table_name} SET is_deleted = 0, modified_at = NOW(), modified_by = {$modified_by}, modified_by_type = '{$modified_by_type}' WHERE $by = '{$key_value}'");
		}else {
			mysqli_query($conn, "UPDATE {$table_name} SET is_deleted = 0 WHERE $by = '{$key_value}'");
		}
	}else {
		if($with_created == true) {
			mysqli_query($conn, "UPDATE {$table_name} SET is_deleted = 0, modified_at = NOW(), modified_by = {$modified_by}, modified_by_type = '{$modified_by_type}' WHERE $idName = {$key_value}");
		}else {
			mysqli_query($conn, "UPDATE {$table_name} SET is_deleted = 0 WHERE $idName = {$key_value}");
		}
	}

	return $key_value;
}
function sendSmsOtp() {

}
function sendSmsNotification() {

}
function sendSmsBroadcast() {

}
function sendEmail() {

}
function sendVericallOtp() {

}
function trackPackages() {

}
function postageCheck() {

}
function checkRecaptchaGoogle() {

}
function numberToString() {

}
function fileNameExtension() {

}
function generateRandomString($length) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
function curlRequest($method, $url, $data) {
	$data = json_encode($data);
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	$output = curl_exec($ch);
	curl_close($ch);

	return $output;
}
function curlRequestPost($url, $data){
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($curl);

	return $response;
}
function toInsertActivityLogin($conn, $data) {
	unset($data['function']);

	$postData = [];

	$ipAddress = getUserIP();
	$browser = getClientBrowser();
	$perambanWeb = $_SERVER['HTTP_USER_AGENT'];
	$sistemOperasi = php_uname();
	$kode = getCodeIncrement($conn, 't_aktivitas_log', 'kode', "KLS/AL/", 5);
	$keterangan = $data['nama']." telah masuk ke dalam aplikasi dengan alamat ip yaitu ".$ipAddress." menggunakan browser ".$browser." dan sistem operasi yang digunakan adalah ".$sistemOperasi."";

	$postData['ip'] = $ipAddress;
	$postData['browser'] = $browser;
	$postData['peramban_web'] = $perambanWeb;
	$postData['sistem_operasi'] = $sistemOperasi;
	$postData['keterangan'] = $keterangan;
	$postData['kode'] = $kode;

	$models = toInsertData($conn, 't_aktivitas_log', $postData, true);

	return $models;
}
function checkDuplicateData($conn, $table_name, $data) {

}
function printDie($data) {
	print_r($data);
	die;
}
function checkFileSize($value, $max_value) {
	if(($value >= $max_value) || ($value == 0)) {
		return false;
	}else {
		return true;
	}
}
function checkFileType($value, $list_type) {
	if((!in_array($value, $list_type)) && (!empty($value))) {
		return false;
	}else {
		return true;
	}
}
function uploadFile($file, $url_folder, $name_file) {
	if(!empty($file)) {
		$url_to_folder = $url_folder;

		$file_post_name = $file['name'];
		$extension = pathinfo($file_post_name, PATHINFO_EXTENSION);
		$file_name = sha1(round(microtime(true) * 1000)).".".$extension;

		$name_files = issetEmpty($name_file) ? $name_file : $file_name;

		$file_tmp = $file['tmp_name'];
		$file_directory = $url_to_folder.'/'.$name_files;
		move_uploaded_file($file_tmp, $file_directory);

		return true;
	}
}
function unlinkFile($url) {
	unlink($url);
}
function menuActive($array, $akses, $status = 'active') {
	$lastPathURI = array_slice(explode('/', rtrim($_SERVER['REQUEST_URI'], '/')), -1)[0];
	$lastPathURIView = array_slice(explode('/', rtrim($_SERVER['REQUEST_URI'], '/')), -2)[0];

	$desc = '';

	if(in_array(strtok($lastPathURI, '?'), $array) || in_array(strtok($lastPathURIView, '?'), $array)) {
		if($status == "active") {
			$desc .= " active";
		}else {
			$desc .= " menu-open";
		}
	}

	$akses = json_decode($akses['akses'], true);
	$arrAkses = [];

	foreach ($akses as $key => $value) {
		$arrAkses[] = $key;
	}

	if(count($array) == 1) {
		if(!in_array($array[0], $arrAkses)) {
			$desc .= " no-access";
		}
	}

	if(count($array) > 1) {
		if(count($array) == count(array_diff($array, $arrAkses))) {
			$desc .= " no-access";
		}
	}
	return $desc;
}
function getBaseURL() {
	return "http://localhost/kelola-sampah-v2/";
}
function checkValidHexCode($value) {
	if(preg_match('/^#[a-f0-9]{6}$/i', $value)) {
		return $value;
	}else {
		return "#".$value;
	}
}
function ucwordsWithBracket($value) {
	$new_value = ucwords(strtolower(str_replace('(', '( ', $value)));
	$last_value = str_replace('( ', '(', $new_value);

	return $last_value;
}
function ucwordsWithBracketV2($value) {
	$words = explode(' ', str_replace('(', '( ', str_replace(')', ' )', str_replace('/', ' / ', strtolower($value)))));
	$newWords = array();

	foreach ($words as $value) {
		if(!preg_match("/^m{0,4}(cm|cd|d?c{0,3})(xc|xl|l?x{0,3})(ix|iv|v?i{0,3})$/", $value)) {
			$joinWords = ucfirst($value);
		}else {
			$joinWords = strtoupper($value);
		}
		array_push($newWords, $joinWords);
	}

	$newString = join(' ', $newWords);
	$lastString = str_replace("( ", "(", str_replace(" )", ")", str_replace(" / ", "/",  $newString)));

	return $lastString;
}
function passwordStrength($value) {
	$upperCase = preg_match('@[A-Z]@', $value);
	$lowerCase = preg_match('@[a-z]@', $value);
	$numberInp = preg_match('@[0-9]@', $value);
	$specialCh = preg_match('@[^\w]@', $value);

	if(!$upperCase || !$lowerCase || !$numberInp || !$specialCh || strlen($value) < 8) {
		return false;
	}else {
		return true;
	}
}
function checkAccessUser($value, $akses) {
	if(!isset($akses[$value])) {
		header("Location: ".getBaseURL() . "app/403");
		exit();
	}
}
function checkMaintenance($rowAkses, $rowUmum) {
	if(isset($rowAkses)) {
		if( (isset($rowUmum['is_maintenance']) && $rowUmum['is_maintenance'] == 1) && (isset($rowAkses['status_akses']) && !empty($rowAkses['status_akses']) && $rowAkses['status_akses'] == "Tidak Dapat Akses") ) {
			header("Location: ".getBaseURL() . "app/503");
			exit();
		}
	}
}
function breakResponse() {
	die;
}
function jsedie($data) {
	echo json_encode($data);
	breakResponse();
}
function checkAccess($value, $akses) {
	if(isset($akses[$value]) && $akses[$value] == true) {
		return "checked=''";
	}
}
function issetEmpty($value) {
	if(isset($value) && !empty($value)) {
		return true;
	}else {
		return false;
	}
}
function getFileName($file) {
	if(!empty($file)) {
		$file_post_name = $file['name'];
		$extension = pathinfo($file_post_name, PATHINFO_EXTENSION);
		$file_name = sha1(round(microtime(true) * 1000)).".".$extension;

		return $file_name;
	}
}
function isSelected($value, $key_value) {
	if($value == $key_value) {
		return "selected=''";
	}else {
		return "";
	}
}
function validateHTML($string, $encode = 'UTF-8', $flags = ENT_QUOTES) {
	return htmlspecialchars($string, $flags, $encode);
}
function allRowValidateHTML($row = []) {
	if(issetEmpty($row)) {
		$arr = [];
		foreach ($row as $keys => $values) {
			if($keys != "akses") {
				$row[$keys] = validateHTML($row[$keys]);
			}
			$arr[$keys] = $row[$keys];
		}
		return $arr;
	}
}
function getDashboardData($conn) {

	$dateFull = date('Y-m-d');
	$dateMonth = date('Y-m');
	$arr = [];

	$dataBank = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM m_bank WHERE is_deleted = '0'")
	);

	$dataPelanggan = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0'")
	);

	$dataPetugas = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM m_petugas WHERE is_deleted = '0'")
	);

	$dataPenarikanSampah = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM t_penarikan_sampah WHERE is_deleted = '0'")
	);

	$dataAktivitasLog = mysqli_num_rows(
		mysqli_query($conn, "SELECT * FROM t_aktivitas_log WHERE is_deleted = '0'")
	);

	$queryAllPengguna = mysqli_query($conn, "SELECT * FROM m_pengguna WHERE is_deleted = '0' ORDER BY created_at DESC LIMIT 10");
	while($rowAllPengguna = mysqli_fetch_array($queryAllPengguna, MYSQLI_ASSOC)) {
		$arr[] = [
			'kode'          =>  $rowAllPengguna['kode'],
			'nama'          =>  $rowAllPengguna['nama'],
			'telepon'       =>  $rowAllPengguna['telepon'],
			'email'         =>  $rowAllPengguna['email'],
			'avatar'        =>  $rowAllPengguna['avatar'],
			'created_at'    =>  $rowAllPengguna['created_at'],
			'tipe'          =>  "Pengguna"
		];
	}

	$queryAllPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE is_deleted = '0' ORDER BY created_at DESC LIMIT 10");
	while($rowAllPelanggan = mysqli_fetch_array($queryAllPelanggan, MYSQLI_ASSOC)) {
		$arr[] = [
			'kode'          =>  $rowAllPelanggan['kode'],
			'nama'          =>  $rowAllPelanggan['nama'],
			'telepon'       =>  $rowAllPelanggan['telepon'],
			'email'         =>  $rowAllPelanggan['email'],
			'avatar'        =>  $rowAllPelanggan['avatar'],
			'created_at'    =>  $rowAllPelanggan['created_at'],
			'tipe'          =>  "Pelanggan"
		];
	}

	$queryAllPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE is_deleted = '0' ORDER BY created_at DESC LIMIT 10");
	while($rowAllPetugas = mysqli_fetch_array($queryAllPetugas, MYSQLI_ASSOC)) {
		$arr[] = [
			'kode'          =>  $rowAllPetugas['kode'],
			'nama'          =>  $rowAllPetugas['nama'],
			'telepon'       =>  $rowAllPetugas['telepon'],
			'email'         =>  $rowAllPetugas['email'],
			'avatar'        =>  $rowAllPetugas['avatar'],
			'created_at'    =>  $rowAllPetugas['created_at'],
			'tipe'          =>  "Petugas"
		];
	}

	usort($arr, 'compareDatetimeDesc');

	return [
		'totalBank'             =>  $dataBank,
		'totalPelanggan'        =>  $dataPelanggan,
		'totalPetugas'          =>  $dataPetugas,
		'totalPenarikanSampah'  =>  $dataPenarikanSampah,
		'totalAktivitasLog'     =>  $dataAktivitasLog,
		'dataPenggunaBaru'      =>  array_splice($arr, 0, 8)
	];
}
function checkDataExist($conn, $parent_table, $foreign_key_name, $id) {
	if(issetEmpty($parent_table) && issetEmpty($id)) {
		$queryChild = mysqli_query($conn, "SELECT * FROM tables_used WHERE parent_table = '{$parent_table}'");
		$totalChild = mysqli_num_rows($queryChild);
		if($totalChild > 0) {
			$arr = [];
			while($rowChild = mysqli_fetch_array($queryChild, MYSQLI_ASSOC)) {
				if($rowChild['child_table'] != "m_produk_gambar") {
					$arr[$rowChild['child_table']]['child_table'] = $rowChild['child_table'];
					$arr[$rowChild['child_table']]['child_menu_name'] = $rowChild['child_menu_name'];
				}
			}
			$arr = array_values($arr);

			if(issetEmpty($arr)) {
				$loop = true;
				$arrExist = [];
				foreach ($arr as $keys => $values) {
					if($loop == true) {
						$queryData = mysqli_query($conn, "SELECT EXISTS(SELECT 1 FROM {$values['child_table']} WHERE {$foreign_key_name} = {$id} LIMIT 1) AS total");
						$rowData = mysqli_fetch_array($queryData, MYSQLI_ASSOC);

						if($rowData['total'] > 0) {
							$rowData['child_table'] = $values['child_table'];
							$rowData['child_menu_name'] = $values['child_menu_name'];
							$arrExist[] = $rowData;
							$loop = false;
						}
					}
				}
				return isset($arrExist[0]) && !empty($arrExist[0]) ? $arrExist[0] : [];die;
			}else {
				return [];
			}
		}else {
			return [];
		}
	}else {
		return [];
	}
}
function fileReplaceContent($path, $oldContent, $newContent) {
	$str = file_get_contents($path);
	$str = str_replace($oldContent, $newContent, $str);
	file_put_contents($path, $str);
}
function getDetailSEO($row = []) {
	$lastPathURIParent = array_slice(explode('/', rtrim($_SERVER['REQUEST_URI'], '/')), -2)[0];
	if($lastPathURIParent == "kelola-sampah-v2") {
		$row['title'] = issetEmpty($row['title']) ? $row['title'] : (issetEmpty($row['nama_website']) ? $row['nama_website'] : 'Kelola Sampah V2');
	}else if($lastPathURIParent == "app" || $lastPathURIParent == "add") {
		$lastPathURIChildren = array_slice(explode('/', rtrim($_SERVER['REQUEST_URI'], '/')), -1)[0];
		if($lastPathURIChildren == "index") {
			$row['title'] = issetEmpty($row['title']) ? $row['title'] : (issetEmpty($row['nama_website']) ? $row['nama_website'] : 'Kelola Sampah V2');
		}else if($lastPathURIChildren == "404") {
			$row['title'] = "Halaman Tidak Ditemukan - " . (issetEmpty($row['nama_website']) ? $row['nama_website'] : 'Kelola Sampah V2');
		}else if($lastPathURIChildren == "403") {
			$row['title'] = "Tidak Mempunyai Akses Halaman - " . (issetEmpty($row['nama_website']) ? $row['nama_website'] : 'Kelola Sampah V2');
		}else if($lastPathURIChildren == "500") {
			$row['title'] = "Kesalahan Server Dalam - " . (issetEmpty($row['nama_website']) ? $row['nama_website'] : 'Kelola Sampah V2');
		}else if($lastPathURIChildren == "503") {
			$row['title'] = "Sedang Dilakukan Perbaikan - " . (issetEmpty($row['nama_website']) ? $row['nama_website'] : 'Kelola Sampah V2');
		}else {
			$row['title'] = ucwords(str_replace("-", " ", ucwords($lastPathURIChildren))) . " - " . (issetEmpty($row['nama_website']) ? $row['nama_website'] : 'Kelola Sampah V2');
		}
	}else {
		$row['title'] = ucwords(str_replace("-", " ", ucwords($lastPathURIParent))) . " - " . (issetEmpty($row['nama_website']) ? $row['nama_website'] : 'Kelola Sampah');
	}
	if(issetEmpty($row)) {
		return [
			'author' => issetEmpty($row['author']) ? $row['author'] : '-',
			'description' => issetEmpty($row['description']) ? $row['description'] : '-',
			'keyword' => issetEmpty($row['keyword']) ? $row['keyword'] : '-',
			'title' => issetEmpty($row['title']) ? $row['title'] : '-',
			'copyright' => issetEmpty($row['copyright']) ? $row['copyright'] : '-',
			'publisher' => issetEmpty($row['publisher']) ? $row['publisher'] : '-',
			'robots' => issetEmpty($row['robots']) ? $row['robots'] : '-',
			'logo' => issetEmpty($row['logo']) ? $row['logo'] : 'default-logo.png',
			'url' => issetEmpty($row['url']) ? $row['url'] : getBaseURL() . "app/"
		];
	}else {
		return [
			'author' => '-',
			'description' => '-',
			'keyword' => '-',
			'title' => '-',
			'copyright' => '-',
			'publisher' => '-',
			'robots' => '-',
			'logo' => 'default-logo.png',
			'url' => getBaseURL() . "app/"
		];
	}
} 
function getLastWeekDates() {
	$lastWeek = array();

	$prevMon = abs(strtotime("previous monday"));
	$currentDate = abs(strtotime("today"));
	$seconds = 86400;

	$dayDiff = ceil(($currentDate - $prevMon) / $seconds); 

	if($dayDiff < 7) {
		$dayDiff += 1;
		$prevMon = strtotime("previous monday", strtotime("-$dayDiff day"));
	}

	$prevMon = date("Y-m-d", $prevMon);

	for($i = 0; $i < 7; $i++) {
		$d = date("Y-m-d", strtotime($prevMon." + $i day"));
		$lastWeek[] = $d;
	}

	return $lastWeek;
}
function weekOfMonth($date) {
	$firstOfMonth = strtotime(date("Y-m-01", $date));
	return weekOfYear($date) - weekOfYear($firstOfMonth) + 1;
}

function weekOfYear($date) {
	$weekOfYear = intval(strftime("%U", $date));
	if (date('n', $date) == "1" && $weekOfYear > 51) {
		return 0;
	}else if (date('n', $date) == "12" && $weekOfYear == 1) {
		return 53;
	}else {
		return $weekOfYear;
	}
}
function getDateBetween($month, $year) {
	$startDate = date("Y-m-d", strtotime($year."-".$month."-01"));
	$endDate   = date("Y-m-d", strtotime($year."-".$month."-".cal_days_in_month(CAL_GREGORIAN, $month, $year)));

	$selesai = new DateTime($endDate);
	$period  = new DatePeriod(
		new DateTime($startDate),
		new DateInterval('P1D'),
		$selesai
	);

	return $period;
}
function getDateBetweenWeek($week, $month, $year) {
	$startDate = date("Y-m-d", strtotime($year."-".$month."-01"));
	$endDate   = date("Y-m-d", strtotime($year."-".$month."-".cal_days_in_month(CAL_GREGORIAN, $month, $year)));

	$selesai = new DateTime($endDate);
	$period  = new DatePeriod(
		new DateTime($startDate),
		new DateInterval('P1D'),
		$selesai
	);

	$arrPeriodWeek = [];

	foreach ($period as $k => $f) {
		if(weekOfMonth(strtotime($f->format('Y-m-d'))) == $week) {
			$arrPeriodWeek[] = $f;
		}
	}

	return $arrPeriodWeek;
}
function getRangeDayWeeks() {
	$arrDay = ["Senin", "Selasa", "Rabu", "Kamis", "Jum'at"];

	$weeks = weekOfMonth(strtotime(date('Y-m-d')));
	$months = date('m');
	$years = date('Y');

	$arrListDate = [];
	$arrListDateFormat = [];

	foreach (getDateBetweenWeek($weeks, $months, $years) as $k => $f) {
		$arrListDate[] = $f->format('Y-m-d');
		$arrListDateFormat[] = $f->format('d');
	}

	$arrListDate = array_values($arrListDate);
	$arrListDateFormat = array_values($arrListDateFormat);

	return [
		'listOfDate' => $arrListDate,
		'listOfDateFormat' => $arrListDateFormat
	];
}
function getWeeksInMonth($year, $month, $lastDayOfWeek) {
	$aWeeksOfMonth = [];
	$date = new DateTime("{$year}-{$month}-01");
	$iDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$aOneWeek = [$date->format('Y-m-d')];
	$weekNumber = 1;
	for ($i = 1; $i <= $iDaysInMonth; $i++) {
		if ($lastDayOfWeek == $date->format('N') || $i == $iDaysInMonth) {
			$aOneWeek[] = $date->format('Y-m-d');
			$aWeeksOfMonth[$weekNumber++] = $aOneWeek;
			$date->add(new DateInterval('P1D'));
			$aOneWeek = [$date->format('Y-m-d')];
			$i++;

		}
		$date->add(new DateInterval('P1D'));
	}
	return $aWeeksOfMonth;
}
function dateCompareDashboard($element1, $element2) {
	$datetime1 = strtotime($element1['tanggal']);
	$datetime2 = strtotime($element2['tanggal']);
	return $datetime1 - $datetime2;
}
function compareDatetimeDesc($a, $b) {
	$datetimeA = strtotime($a['created_at']);
	$datetimeB = strtotime($b['created_at']);
	
	if ($datetimeA == $datetimeB) {
		return 0;
	}
	
	return ($datetimeA > $datetimeB) ? -1 : 1;
}
function getFirstWords($str, $wordCount = 10) {
	$pregSplit = preg_split('/([\s,\.;\?\!]+)/', trim($str), $wordCount * 2 + 1, PREG_SPLIT_DELIM_CAPTURE);
	$wordSlice = array_slice($pregSplit, 0, $wordCount * 2 - 1);

	return implode('', $wordSlice);
}
function removeTemplateHtmlTags($value = "") {
	return strip_tags(str_replace(array('<b>', '</b>', '<ol>', '</ol>', '<br>', '<i>', '</i>', '<p>', '</p>'), array('*', '*', '', '', '', '_', '_', '', "\n"), $value));
}
function formatPhone($nomor_hp = null, $kode_negara = "+62") {
	if(issetEmpty($nomor_hp) && $nomor_hp != "-") {
		$kode_negara = issetEmpty($kode_negara) ? $kode_negara : "+62";
		$kode_negara = str_replace("+", "", $kode_negara);

		$splitKodeNegara = str_split($kode_negara);
		$splitTelepon = str_split($nomor_hp);

		if($splitTelepon[0] == "+" && $splitTelepon[1] == $splitKodeNegara[0] && $splitTelepon[2] == $splitKodeNegara[1]) {
			unset($splitTelepon[0]);
			unset($splitTelepon[1]);
			unset($splitTelepon[2]);

			$telepon = $kode_negara.implode($splitTelepon);
		}else if($splitTelepon[0] == $splitKodeNegara[0] && $splitTelepon[1] == $splitKodeNegara[1]) {
			unset($splitTelepon[0]);
			unset($splitTelepon[1]);

			$telepon = $kode_negara.implode($splitTelepon);
		}else if($splitTelepon[0] == "0") {
			unset($splitTelepon[0]);

			$telepon = $kode_negara.implode($splitTelepon);
		}else {
			$telepon = $kode_negara.implode($splitTelepon);
		}

		return str_replace("-", "", $telepon);
	}else {
		return null;
	}
}
function sendNotificationWablas($data, $type = "send-message", $api_key = null, $domain_url = null) {

	if(issetEmpty($api_key) && issetEmpty($domain_url)) {
		$host = $domain_url;
		$apiKeys = $api_key;
		
		if($type == "send-message") {
			$urlTujuan = $host."/api/v2/send-message";
		}else if($type == "schedule") {
			$urlTujuan = $host."/api/v2/schedule";
		}else {
			$urlTujuan = $host."/api/v2/send-message";
		}
		
		if(issetEmpty($data)) {
			$token = $apiKeys;
			$payload = [
				"data" => $data
			];
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_HTTPHEADER,
				array(
					"Authorization: $token",
					"Content-Type: application/json"
				)
			);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload) );
			curl_setopt($curl, CURLOPT_URL,  $urlTujuan);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_FAILONERROR, true);
			
			$result = curl_exec($curl);
			if ($result === FALSE) {
				return [
					'status_code'   =>  400,
					'error'         =>  'Curl Failed: ' . curl_error($curl),
					'message'       =>  'error',
					'data'          =>  []
				];
				breakResponse();
			}
			
			curl_close($curl);
			return [
				'status_code'   =>  200,
				'error'         =>  null,
				'message'       =>  'success',
				'data'          =>  json_decode($result, true)
			];
			breakResponse();
		}else {
			return [
				'status_code'   =>  400,
				'error'         =>  'Error unknown',
				'message'       =>  'error',
				'data'          =>  []
			];
			breakResponse();
		}
	}else {
		return [
			'status_code'   =>  400,
			'error'         =>  'Error unknown',
			'message'       =>  'error'
		];
		breakResponse();
	}
}
function encryptedHash($value, $key = "Gonata Indonesia 2021", $method = "aes-256-cbc") {
	$iv = openssl_random_pseudo_bytes(16);
	$encrypted = openssl_encrypt($value, $method, $key, OPENSSL_RAW_DATA, $iv);
	$encryptedData = $iv . $encrypted;

	return base64_encode($encryptedData);
}
function decryptedHash($value, $key = "Gonata Indonesia 2021", $method = "aes-256-cbc") {
	$value = base64_decode($value);
	$iv = substr($value, 0, 16);
	$encrypted = substr($value, 16);
	$decryptedData = openssl_decrypt($encrypted, $method, $key, OPENSSL_RAW_DATA, $iv);

	return $decryptedData;
}
function isDisabledHref($value) {
	if($value == true) {
		return " is-disabled-button";
	}
}
function calculateDistance($longitudeDestination, $latitudeDestination, $longitudeStart, $latitudeStart) {
    $earthRadius = 6371000;

    $latDestRad = deg2rad($latitudeDestination);
    $lonDestRad = deg2rad($longitudeDestination);
    $latStartRad = deg2rad($latitudeStart);
    $lonStartRad = deg2rad($longitudeStart);

    $latDiff = $latDestRad - $latStartRad;
    $lonDiff = $lonDestRad - $lonStartRad;

    $a = sin($latDiff / 2) * sin($latDiff / 2) + cos($latStartRad) * cos($latDestRad) * sin($lonDiff / 2) * sin($lonDiff / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;

    $roundedDistance = round($distance, 2);

    return $roundedDistance;
}
?>