<?php
require "../../config/autoloads.php";
require 'lib/phpmailer/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

call_user_func($_POST['function'], $conn);

function login($conn) {
	$form = $_POST;

	$email = mysqli_escape_string($conn, $form['email']);
	$password = mysqli_escape_string($conn, $form['password']);

	try {
		$queryPengguna = mysqli_query($conn, "SELECT * FROM m_pengguna WHERE email = '$email' AND is_deleted = 0");
		$countPengguna = mysqli_num_rows($queryPengguna);

		$attempt = [];
		$attempt['kode'] = getCodeIncrement($conn, 't_attempt_log', 'kode', "KLS/AL/", 5);
		$attempt['ip'] = getUserIP();
		$attempt['email'] = $email;
		$attempt['password'] = $password;
		$attempt['waktu'] = date('Y-m-d H:i:s');

		$log = date('Y-m-d H:i:s');

		if($countPengguna > 0) {
			$rowPengguna = mysqli_fetch_array($queryPengguna);

			$dbNama = $rowPengguna['nama'];
			$dbKode = $rowPengguna['kode'];
			$idPengguna = $rowPengguna['id'];
			if(password_verify($password, $rowPengguna['password'])) {
				toInsertActivityLogin($conn, $rowPengguna);

				mysqli_query($conn, "UPDATE m_pengguna SET log = '$log' WHERE id = '$idPengguna'");

				$_SESSION['nama'] = $rowPengguna['nama'];
				$_SESSION['kode'] = $rowPengguna['kode'];
				$_SESSION['id'] = $rowPengguna['id'];
				$_SESSION['time'] = date('Y-m-d H:i:s');
				$_SESSION['login'] = true;
				$_SESSION['tipe'] = "Pengguna";

				if(isset($_POST['remember'])) {
					setcookie('_ga_NP5R9ZCR7Z2', md5('login'), time() + 3600, '/');
					setcookie('_ga_NZ0K2CYT673', base64_encode($rowPengguna['kode']), time() + 3600, '/');
				}

				echo json_encode([
					'status' => 1,
					'status_code' => 200,
					'message' => "Selamat datang ".$dbNama,
					"info_error" => null,
					'data' => $form
				], JSON_PRETTY_PRINT);

				if(isset($_SESSION['attempt_code'])) {
					$attempt_code = $_SESSION['attempt_code'];
					$attempt_ip = getUserIP();
					mysqli_query($conn, "DELETE FROM t_attempt_log WHERE attempt_code = '$attempt_code' OR ip = '$attempt_ip'");
					unset($_SESSION['attempt_code']);
				}
			}else {
				echo json_encode([
					'status' => 0,
					'status_code' => 400,
					'message' => "Kata sandi tidak cocok",
					"info_error" => null,
					'data' => null
				], JSON_PRETTY_PRINT);

				if(isset($_SESSION['attempt_code'])) {
					$attempt_code = $_SESSION['attempt_code'];
					$attempt['attempt_code'] = $attempt_code;
				}else {
					$attempt_code = generateRandomString(6);
					$_SESSION['attempt_code'] = $attempt_code;
					$attempt['attempt_code'] = $attempt_code;
				}

				toInsertData($conn, 't_attempt_log', $attempt);
				breakResponse();
			}
		}else {
			$queryPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE email = '$email' AND is_deleted = 0");
			$countPelanggan = mysqli_num_rows($queryPelanggan);

			if($countPelanggan > 0) {
				$rowPelanggan = mysqli_fetch_array($queryPelanggan);
				$dbNama = $rowPelanggan['nama'];
				$dbKode = $rowPelanggan['kode'];
				$idPelanggan = $rowPelanggan['id'];
				if(password_verify($password, $rowPelanggan['password'])) {
					toInsertActivityLogin($conn, $rowPelanggan);

					mysqli_query($conn, "UPDATE m_pelanggan SET log = '$log' WHERE id = '$idPelanggan'");

					$_SESSION['nama'] = $rowPelanggan['nama'];
					$_SESSION['kode'] = $rowPelanggan['kode'];
					$_SESSION['id'] = $rowPelanggan['id'];
					$_SESSION['time'] = date('Y-m-d H:i:s');
					$_SESSION['login'] = true;
					$_SESSION['tipe'] = "Pelanggan";

					if(isset($_POST['remember'])) {
						setcookie('_ga_NP5R9ZCR7Z2', md5('login'), time() + 3600, '/');
						setcookie('_ga_NZ0K2CYT673', base64_encode($rowPelanggan['kode']), time() + 3600, '/');
					}

					echo json_encode([
						'status' => 1,
						'status_code' => 200,
						'message' => "Selamat datang ".$dbNama,
						"info_error" => null,
						'data' => $form
					], JSON_PRETTY_PRINT);

					if(isset($_SESSION['attempt_code'])) {
						$attempt_code = $_SESSION['attempt_code'];
						$attempt_ip = getUserIP();
						mysqli_query($conn, "DELETE FROM t_attempt_log WHERE attempt_code = '$attempt_code' OR ip = '$attempt_ip'");
						unset($_SESSION['attempt_code']);
					}
				}else {
					echo json_encode([
						'status' => 0,
						'status_code' => 400,
						'message' => "Kata sandi tidak cocok",
						"info_error" => null,
						'data' => null
					], JSON_PRETTY_PRINT);

					if(isset($_SESSION['attempt_code'])) {
						$attempt_code = $_SESSION['attempt_code'];
						$attempt['attempt_code'] = $attempt_code;
					}else {
						$attempt_code = generateRandomString(6);
						$_SESSION['attempt_code'] = $attempt_code;
						$attempt['attempt_code'] = $attempt_code;
					}

					toInsertData($conn, 't_attempt_log', $attempt);
					breakResponse();
				}
			}else {
				$queryPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE email = '$email' AND is_deleted = 0");
				$countPetugas = mysqli_num_rows($queryPetugas);

				if($countPetugas > 0) {
					$rowPetugas = mysqli_fetch_array($queryPetugas);
					$dbNama = $rowPetugas['nama'];
					$dbKode = $rowPetugas['kode'];
					$idPetugas = $rowPetugas['id'];
					if(password_verify($password, $rowPetugas['password'])) {
						toInsertActivityLogin($conn, $rowPetugas);
						
						mysqli_query($conn, "UPDATE m_petugas SET log = '$log' WHERE id = '$idPetugas'");
						
						$_SESSION['nama'] = $rowPetugas['nama'];
						$_SESSION['kode'] = $rowPetugas['kode'];
						$_SESSION['id'] = $rowPetugas['id'];
						$_SESSION['time'] = date('Y-m-d H:i:s');
						$_SESSION['login'] = true;
						$_SESSION['tipe'] = "Petugas";
						
						if(isset($_POST['remember'])) {
							setcookie('_ga_NP5R9ZCR7Z2', md5('login'), time() + 3600, '/');
							setcookie('_ga_NZ0K2CYT673', base64_encode($rowPetugas['kode']), time() + 3600, '/');
						}
						
						echo json_encode([
							'status' => 1,
							'status_code' => 200,
							'message' => "Selamat datang ".$dbNama,
							"info_error" => null,
							'data' => $form
						], JSON_PRETTY_PRINT);
						
						if(isset($_SESSION['attempt_code'])) {
							$attempt_code = $_SESSION['attempt_code'];
							$attempt_ip = getUserIP();
							mysqli_query($conn, "DELETE FROM t_attempt_log WHERE attempt_code = '$attempt_code' OR ip = '$attempt_ip'");
							unset($_SESSION['attempt_code']);
						}
					}else {
						echo json_encode([
							'status' => 0,
							'status_code' => 400,
							'message' => "Kata sandi tidak cocok",
							"info_error" => null,
							'data' => null
						], JSON_PRETTY_PRINT);
						
						if(isset($_SESSION['attempt_code'])) {
							$attempt_code = $_SESSION['attempt_code'];
							$attempt['attempt_code'] = $attempt_code;
						}else {
							$attempt_code = generateRandomString(6);
							$_SESSION['attempt_code'] = $attempt_code;
							$attempt['attempt_code'] = $attempt_code;
						}
						
						toInsertData($conn, 't_attempt_log', $attempt);
						breakResponse();
					}
				}else {
					echo json_encode([
						'status' => 0,
						'status_code' => 400,
						'message' => "Akun tidak ditemukan",
						"info_error" => null,
						'data' => null
					], JSON_PRETTY_PRINT);
					
					if(isset($_SESSION['attempt_code'])) {
						$attempt_code = $_SESSION['attempt_code'];
						$attempt['attempt_code'] = $attempt_code;
					}else {
						$attempt_code = generateRandomString(6);
						$_SESSION['attempt_code'] = $attempt_code;
						$attempt['attempt_code'] = $attempt_code;
					}
					
					toInsertData($conn, 't_attempt_log', $attempt);
					breakResponse();
				}
			}
		}
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Akun tidak ditemukan",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function logout($conn) {
	unset($_SESSION);
	session_destroy();

	if(isset($_COOKIE['_ga_NP5R9ZCR7Z2']) && isset($_COOKIE['_ga_NZ0K2CYT673'])) {
		setcookie('_ga_NP5R9ZCR7Z2', '', time() - 3600, '/');
		setcookie('_ga_NZ0K2CYT673', '', time() - 3600, '/');
	}

	echo json_encode([
		'status' => 1,
		'status_code' => 200,
		'message' => "Berhasil keluar dari aplikasi",
		"info_error" => null,
		'data' => null
	], JSON_PRETTY_PRINT);
}

function getAttemptLog($conn) {
	try {
		if(isset($_SESSION['attempt_code'])) {
			$attemptCode = $_SESSION['attempt_code'];
			$attemptIP = getUserIP();
			$count = mysqli_fetch_array(
				mysqli_query($conn, "SELECT COUNT(*) AS total FROM t_attempt_log WHERE attempt_code = '$attemptCode' AND ip = '$attemptIP'")
			);
		}
		echo json_encode([
			'status' => 1,
			'status_code' => 200,
			'message' => "Berhasil mendapatkan attempt log",
			"info_error" => null,
			'data' => null,
			'attempt' => isset($count) ? (int) $count['total'] : 0
		], JSON_PRETTY_PRINT);
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Gagal mendapatkan attempt log",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function resetMyPassword($conn) {
	$form = $_POST;	

	try {
		if(empty($form['token'])) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Token tidak ditemukan",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}else {
			$token = $form['token'];
			$queryHistory = mysqli_query($conn, "SELECT * FROM t_history_reset_password WHERE token = '$token'");
			$countHistory = mysqli_num_rows($queryHistory);
			
			if($countHistory > 0) {
				$rowHistory = mysqli_fetch_array($queryHistory, MYSQLI_ASSOC);

				if($rowHistory['is_used'] == 1) {
					echo json_encode([
						'status' => 0,
						'status_code' => 400,
						'message' => "Token sudah digunakan",
						"info_error" => null,
						'data' => null
					], JSON_PRETTY_PRINT);
					breakResponse();
				}else if(date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($rowHistory['batas_waktu']))) {
					echo json_encode([
						'status' => 0,
						'status_code' => 400,
						'message' => "Link atau token sudah kadaluarsa",
						"info_error" => null,
						'data' => null
					], JSON_PRETTY_PRINT);
					breakResponse();
				}else {
					$password = mysqli_escape_string($conn, $form['password']);
					$confirmPassword = mysqli_escape_string($conn, $form['confirmPassword']);

					if($password != $confirmPassword) {
						echo json_encode([
							'status' => 0,
							'status_code' => 400,
							'message' => "Konfirmasi Kata Sandi tidak cocok",
							"info_error" => null,
							'data' => null
						], JSON_PRETTY_PRINT);
						breakResponse();
					}else {
						$hashPassword = password_hash($confirmPassword, PASSWORD_DEFAULT);
						$idHistory = $rowHistory['id'];
						$emailPengguna = $rowHistory['email'];

						if($rowHistory['jenis_pengguna'] == "Pengguna") {
							mysqli_query($conn, "UPDATE m_pengguna SET password = '$hashPassword' WHERE email = '$emailPengguna'");
						}else if($rowHistory['jenis_pengguna'] == "Pelanggan") {
							mysqli_query($conn, "UPDATE m_pelanggan SET password = '$hashPassword' WHERE email = '$emailPengguna'");
						}else if($rowHistory['jenis_pengguna'] == "Petugas") {
							mysqli_query($conn, "UPDATE m_petugas SET password = '$hashPassword' WHERE email = '$emailPengguna'");
						}else {
							mysqli_query($conn, "UPDATE m_pengguna SET password = '$hashPassword' WHERE email = '$emailPengguna'");   
						}

						mysqli_query($conn, "UPDATE t_history_reset_password SET is_used = 1 WHERE id = '$idHistory'");
						echo json_encode([
							'status' => 1,
							'status_code' => 200,
							'message' => "Berhasil mengubah kata sandi",
							"info_error" => null,
							'data' => null
						], JSON_PRETTY_PRINT);
					}
				}
			}else {
				echo json_encode([
					'status' => 0,
					'status_code' => 400,
					'message' => "Token tidak ditemukan",
					"info_error" => null,
					'data' => null
				], JSON_PRETTY_PRINT);
				breakResponse();
			}
		}
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Ada kesalahan ketika mengubah kata sandi Anda",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function sendLinkToEmail($conn) {
	$form = $_POST;
	$mail = new PHPMailer(true);

	$email = mysqli_escape_string($conn, $form['email']);

	try {
		$dataPengguna = [];
		$jenisPengguna = "Pengguna";

		$queryPengguna = mysqli_query($conn, "SELECT * FROM m_pengguna WHERE email = '$email'");
		$countPengguna = mysqli_num_rows($queryPengguna);

		if($countPengguna > 0) {
			$rowPengguna = mysqli_fetch_array($queryPengguna, MYSQLI_ASSOC);

			$dataPengguna = $rowPengguna;
			$jenisPengguna = "Pengguna";
		}else {
			$queryPelanggan = mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE email = '$email'");
			$countPelanggan = mysqli_num_rows($queryPelanggan);
			
			if($countPelanggan > 0) {
				$rowPelanggan = mysqli_fetch_array($queryPelanggan, MYSQLI_ASSOC);
				
				$dataPengguna = $rowPelanggan;
				$jenisPengguna = "Pelanggan";
			}else {
				$queryPetugas = mysqli_query($conn, "SELECT * FROM m_petugas WHERE email = '$email'");
				$countPetugas = mysqli_num_rows($queryPetugas);
				
				if($countPetugas > 0) {
					$rowPetugas = mysqli_fetch_array($queryPetugas, MYSQLI_ASSOC);
					
					$dataPengguna = $rowPetugas;
					$jenisPengguna = "Petugas";
				}
			}
		}

		if(isset($dataPengguna) && !empty($dataPengguna)) {
			$queryKonfigurasiUmum = mysqli_query($conn, "SELECT * FROM p_konfigurasi_umum ORDER BY id ASC LIMIT 1");
			$rowKonfigurasiUmum = mysqli_fetch_array($queryKonfigurasiUmum, MYSQLI_ASSOC);

			if($rowKonfigurasiUmum['metode_reset_password'] == "Whatsapp") {
				$waktuSekarang = date('Y-m-d H:i:s');
				$token = bin2hex(openssl_random_pseudo_bytes(16));
				$url = getBaseURL() . "app/atur-ulang-kata-sandi?email=" . $dataPengguna['email'] . "&token=" . $token;
				$masaBerlaku = date('Y-m-d H:i:s', strtotime($waktuSekarang . " +5 minutes"));

				$templatePesan = "";
				$templatePesan .= "*" . $rowKonfigurasiUmum['nama_website'] . " - Lupa Kata Sandi*\n\n";
				$templatePesan .= "Hai, " . $dataPengguna['nama'] . "!\n";
				$templatePesan .= "Apabila Anda lupa dengan kata sandi akun " . $rowKonfigurasiUmum['nama_website'] . ". Anda dapat menekan atau copy link dibawah ini untuk dapat mengatur ulang kata sandi Anda.\n\n";
				$templatePesan .= $url . "\n\n";
				$templatePesan .= "Link diatas hanya berlaku sampai *" . formatDateIndonesia(date('d F Y, H:i:s', strtotime($masaBerlaku))) . " WIB*\n\n";
				$templatePesan .= "Anda dapat menghubungi Kami jika link tersebut tidak dapat diakses atau mengalami kendala.\n";
				$templatePesan .= "Terimakasih atas perhatiannya.";

				$arr = [];
				$arr[] = [
					'phone' 	=> formatPhone($dataPengguna['telepon'], "+62"),
					'message'	=> $templatePesan,
				];

				$notifWhatsapp = sendNotificationWablas($arr, "send-message", $rowKonfigurasiUmum['wablas_api_key'], $rowKonfigurasiUmum['wablas_domain_url']);

				if(issetEmpty($notifWhatsapp['data']['data'])) {
					$preHistory = [];
					$preHistory['kode'] = getCodeIncrement($conn, 't_history_reset_password', 'kode', "KLS/RP/", 5);
					$preHistory['attempt_code'] = generateRandomString(6);
					$preHistory['token'] = $token;
					$preHistory['ip'] = getUserIP();
					$preHistory['email'] = $dataPengguna['email'];
					$preHistory['telepon'] = $dataPengguna['telepon'];
					$preHistory['kategori'] = "Whatsapp";
					$preHistory['jenis_pengguna'] = $jenisPengguna;
					$preHistory['batas_waktu'] = $masaBerlaku;

					toInsertData($conn, 't_history_reset_password', $preHistory, true);
				}

				echo json_encode([
					'status' => 1,
					'status_code' => 200,
					'message' => "Berhasil mengirim pesan ke Whatsapp",
					"info_error" => null,
					'data' => []
				], JSON_PRETTY_PRINT);
				breakResponse();
			}else if($rowKonfigurasiUmum['metode_reset_password'] == "E-mail") {
				$queryKonfigurasiMailer = mysqli_query($conn, "SELECT * FROM p_konfigurasi_mailer ORDER BY id ASC LIMIT 1");
				$rowKonfigurasiMailer = mysqli_fetch_array($queryKonfigurasiMailer, MYSQLI_ASSOC);

				$waktuSekarang = date('Y-m-d H:i:s');
				$token = bin2hex(openssl_random_pseudo_bytes(16));
				$url = getBaseURL() . "app/atur-ulang-kata-sandi?email=" . $dataPengguna['email'] . "&token=" . $token;
				$masaBerlaku = date('Y-m-d H:i:s', strtotime($waktuSekarang . " +5 minutes"));

				$templateHtml = file_get_contents('template/reset_password.html');

				$templateHtml = str_replace('{nama_pengguna}', $dataPengguna['nama'], $templateHtml);
				$templateHtml = str_replace('{nama_aplikasi}', $rowKonfigurasiUmum['nama_website'], $templateHtml);
				$templateHtml = str_replace('{url_reset_password}', $url, $templateHtml);
				$templateHtml = str_replace('{batas_waktu}', formatDateIndonesia(date('d F Y, H:i:s', strtotime($masaBerlaku))) . " WIB", $templateHtml);
				$templateHtml = str_replace('{alamat_aplikasi}', $rowKonfigurasiUmum['alamat'], $templateHtml);
				$templateHtml = str_replace('{tahun_aplikasi}', date('Y'), $templateHtml);

				$saltEnkripsi = "Gonata Indonesia 2021";
				$passwordDekripsi = openssl_decrypt(base64_decode($rowKonfigurasiMailer['password']), "AES-256-CBC", $saltEnkripsi, OPENSSL_RAW_DATA, base64_decode($rowKonfigurasiMailer['iv']));

				$mail->SMTPDebug = 0;
				$mail->isSMTP();
				$mail->Host       = $rowKonfigurasiMailer['host'];
				$mail->SMTPAuth   = true;
				$mail->SMTPSecure = $rowKonfigurasiMailer['secure'];
				$mail->Username   = $rowKonfigurasiMailer['email'];
				$mail->Password   = $passwordDekripsi;
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->Port       = $rowKonfigurasiMailer['port'];
				$mail->setFrom($rowKonfigurasiMailer['email'], $rowKonfigurasiMailer['name']);
				$mail->addAddress($dataPengguna['email'], $dataPengguna['nama']);
				$mail->isHTML(true);
				$mail->Subject = $rowKonfigurasiMailer['name'] . " - Lupa Kata Sandi";
				$mail->Body    = $templateHtml;
				$mail->AltBody = "Gunakan link yang tertera pada pesan ini untuk dapat mengatur ulang kata sandi Anda.";

				if (!$mail->send()) {
					throw new Exception($mail->ErrorInfo, 1);
				} else {
					$preHistory = [];
					$preHistory['kode'] = getCodeIncrement($conn, 't_history_reset_password', 'kode', "KLS/RP/", 5);
					$preHistory['attempt_code'] = generateRandomString(6);
					$preHistory['token'] = $token;
					$preHistory['ip'] = getUserIP();
					$preHistory['email'] = $dataPengguna['email'];
					$preHistory['telepon'] = $dataPengguna['telepon'];
					$preHistory['kategori'] = "E-mail";
					$preHistory['jenis_pengguna'] = $jenisPengguna;
					$preHistory['batas_waktu'] = $masaBerlaku;
					
					toInsertData($conn, 't_history_reset_password', $preHistory, true);
					
					echo json_encode([
						'status' => 1,
						'status_code' => 200,
						'message' => "Berhasil mengirim pesan ke E-mail",
						"info_error" => null,
						'data' => []
					], JSON_PRETTY_PRINT);
					breakResponse();
				}
			}else {
				echo json_encode([
					'status' => 0,
					'status_code' => 400,
					'message' => "Metode Reset Kata Sandi belum disetting",
					"info_error" => null,
					'data' => null
				], JSON_PRETTY_PRINT);
				breakResponse();
			}
		}else {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Akun tidak ditemukan",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
		}
	} catch (Exception $e) {
		echo json_encode([
			'status' => 0,
			'status_code' => 422,
			'message' => "Ada kesalahan ketika mengirim url",
			"info_error" => $e->getMessage(),
			"mail_error" => isset($mail->ErrorInfo) && !empty($mail->ErrorInfo) ? $mail->ErrorInfo : "",
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function signUpCustomer($conn) {
	$form = $_POST;	

	$form['kode'] = getCodeIncrement($conn, 'm_pelanggan', 'kode', "KLS/PL/", 5);
	$form['nama'] = mysqli_escape_string($conn, $form['nama']);
	$form['telepon'] = str_replace('-', '', $form['telepon']);
	$form['email'] = mysqli_escape_string($conn, $form['email']);
	$form['m_hak_akses_id'] = 2;

	try {
		if(issetEmpty($form['password'])) {
			$password = mysqli_escape_string($conn, $form['password']);
			$form['password'] = password_hash($password, PASSWORD_DEFAULT);
		}else {
			unset($form['password']);
		}
		
		$telepon = $form['telepon'];
		$email = $form['email'];
		
		$countTeleponPelanggan = mysqli_num_rows(
			mysqli_query($conn, "SELECT * FROM m_pelanggan WHERE telepon = '$telepon'")
		);
		if($countTeleponPelanggan > 0) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Telepon sudah digunakan oleh pelanggan lain",
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
				'message' => "E-mail sudah digunakan oleh pelanggan lain",
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

		$countTeleponPengguna = mysqli_num_rows(
			mysqli_query($conn, "SELECT * FROM m_pengguna WHERE telepon = '$telepon'")
		);
		if($countTeleponPengguna > 0) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Telepon sudah digunakan oleh pengguna",
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
				'message' => "E-mail sudah digunakan oleh pengguna",
				"info_error" => null,
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}

		$models = toInsertData($conn, 'm_pelanggan', $form, true);
		if($models['status'] == true) {
			echo json_encode([
				'status' => 1,
				'status_code' => 200,
				'message' => "Berhasil melakukan pendaftaran",
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
			'message' => "Gagal melakukan pendaftaran",
			"info_error" => $e->getMessage(),
			'data' => null
		], JSON_PRETTY_PRINT);
	}
}

function updateKonfigurasiMailer($conn) {
	$getData = toGetDataDetail($conn, 'p_konfigurasi_mailer', $_POST['id']);
	$form = $_POST;
	$form['host'] = mysqli_escape_string($conn, $form['host']);
	$form['secure'] = mysqli_escape_string($conn, $form['secure']);
	$form['port'] = mysqli_escape_string($conn, $form['port']);
	$form['name'] = mysqli_escape_string($conn, $form['name']);
	$form['email'] = mysqli_escape_string($conn, $form['email']);

	if(issetEmpty($form['password'])) {
		$password = mysqli_escape_string($conn, $form['password']);

		$dataEnkripsi = $password;
		$saltEnkripsi = "Gonata Indonesia 2021";
		$iv = openssl_random_pseudo_bytes(16);
		$passwordEnkripsi = openssl_encrypt($dataEnkripsi, "AES-256-CBC", $saltEnkripsi, OPENSSL_RAW_DATA, $iv);

		$form['password'] = base64_encode($passwordEnkripsi);
		$form['iv'] = base64_encode($iv);
	}else {
		unset($form['password']);
	}

	try {
		$models = toUpdateData($conn, 'p_konfigurasi_mailer', $form, $form['id'], null, true);
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

function updateKonfigurasiSEO($conn) {
	$getData = toGetDataDetail($conn, 'p_konfigurasi_seo', $_POST['id']);
	$form = $_POST;
	$form['author'] = mysqli_escape_string($conn, $form['author']);
	$form['description'] = mysqli_escape_string($conn, $form['description']);
	$form['keyword'] = mysqli_escape_string($conn, $form['keyword']);
	$form['title'] = mysqli_escape_string($conn, $form['title']);
	$form['copyright'] = mysqli_escape_string($conn, $form['copyright']);
	$form['publisher'] = mysqli_escape_string($conn, $form['publisher']);
	$form['robots'] = mysqli_escape_string($conn, $form['robots']);
	$form['url'] = mysqli_escape_string($conn, $form['url']);

	if(issetEmpty($_FILES['logo']['name'])) {
		if(checkFileSize($_FILES['logo']['size'], 1048576) == false) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Ukuran file melebihi batas",
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}else if(checkFileType(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION), array('jpg', 'jpeg', 'png')) == false) {
			echo json_encode([
				'status' => 0,
				'status_code' => 400,
				'message' => "Tipe file tidak sesuai",
				'data' => null
			], JSON_PRETTY_PRINT);
			breakResponse();
		}else {
			$form['logo'] = getFileName($_FILES['logo']);
		}
	}

	try {
		$models = toUpdateData($conn, 'p_konfigurasi_seo', $form, $form['id'], null, true);
		if($models['status'] == true) {
			if(issetEmpty($_FILES['logo']['name'])) {
				if(issetEmpty($getData['logo']) && file_exists('../../assets/img/logo/'.$getData['logo']) && $getData['logo'] != "default-logo.jpg") {
					unlinkFile('../../assets/img/logo/'.$getData['logo']);
				}

				uploadFile($_FILES['logo'], "../../assets/img/logo", $form['logo']);
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

function updateKonfigurasiUmum($conn) {
	$getData = toGetDataDetail($conn, 'p_konfigurasi_umum', $_POST['id']);
	$form = $_POST;
	$form['nama_website'] = mysqli_escape_string($conn, $form['nama_website']);
	$form['telepon'] = str_replace('-', '', $form['telepon']);
	$form['email'] = mysqli_escape_string($conn, $form['email']);
	$form['nama_alamat'] = mysqli_escape_string($conn, $form['nama_alamat']);
	$form['alamat'] = mysqli_escape_string($conn, $form['alamat']);
	$form['biaya_pengambilan'] = mysqli_escape_string($conn, str_replace(',', '', $form['biaya_pengambilan']));
	$form['wablas_api_key'] = mysqli_escape_string($conn, $form['wablas_api_key']);
	$form['wablas_domain_url'] = mysqli_escape_string($conn, $form['wablas_domain_url']);
	$form['telegram_token'] = mysqli_escape_string($conn, $form['telegram_token']);
	$form['telegram_chat_id'] = mysqli_escape_string($conn, $form['telegram_chat_id']);
	$form['telegram_thread_id'] = mysqli_escape_string($conn, $form['telegram_thread_id']);

	try {
		$models = toUpdateData($conn, 'p_konfigurasi_umum', $form, $form['id'], null, true);
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

function getChartDashboard($conn) {
	$arrDates = $arrSpesifik = [];

	$dayOfWeeks = getWeeksInMonth(date('Y'), date('m'), 6);
	$dayOfWeeks = array_values($dayOfWeeks);

	foreach ($dayOfWeeks as $keys => $values) {
		if( (isset($values[0]) && !empty($values[0])) && (isset($values[1]) && !empty($values[1])) ) {
			$arrSpesifik[$values[0]."-".$values[1]]['nama'] = "Minggu " . ($keys + 1);
		}
	}

	foreach ($dayOfWeeks as $keys => $values) {
		if( (isset($values[0]) && !empty($values[0])) && (isset($values[1]) && !empty($values[1])) ) {
			$sumPemasukan = mysqli_fetch_array(
				mysqli_query($conn, "SELECT SUM(debit) AS total FROM trans_detail WHERE is_deleted = '0' AND tipe = 'Pemasukan' AND tanggal BETWEEN '$values[0]' AND '$values[1]'"), MYSQLI_ASSOC
			);

			$sumPengeluaran = mysqli_fetch_array(
				mysqli_query($conn, "SELECT SUM(kredit) AS total FROM trans_detail WHERE is_deleted = '0' AND tipe = 'Pengeluaran' AND tanggal BETWEEN '$values[0]' AND '$values[1]'"), MYSQLI_ASSOC
			);

			if(isset($arrSpesifik[$values[0]."-".$values[1]])) {
				if(isset($sumPemasukan['total']) && !empty($sumPemasukan['total'])) {
					$totalPemasukan = (int) $sumPemasukan['total'];
				}else {
					$totalPemasukan = 0;
				}

				if(isset($sumPengeluaran['total']) && !empty($sumPengeluaran['total'])) {
					$totalPengeluaran = (int) $sumPengeluaran['total'];
				}else {
					$totalPengeluaran = 0;
				}

				$arrSpesifik[$values[0]."-".$values[1]]['jumlah_pemasukan'] = $totalPemasukan;
				$arrSpesifik[$values[0]."-".$values[1]]['jumlah_pengeluaran'] = $totalPengeluaran;
			}
		}
	}

	$arrName = $arrPemasukan = $arrPengeluaran = [];
	foreach ($arrSpesifik as $keys => $values) {
		$arrName[] = $values['nama'];
		$arrPemasukan[] = $values['jumlah_pemasukan'];
		$arrPengeluaran[] = $values['jumlah_pengeluaran'];
	}

	$arr = [
		'listName' 				=> 	$arrName,
		'listCountPemasukan' 	=> 	$arrPemasukan,
		'listCountPengeluaran' 	=> 	$arrPengeluaran
	];

	echo json_encode([
		'status' => 1,
		'status_code' => 200,
		'message' => "Berhasil mendapatkan data",
		"info_error" => null,
		'data' => $arr
	], JSON_PRETTY_PRINT);
}
?>