<?php
	require $_SERVER['DOCUMENT_ROOT'].'/php/global.php';
	ini_set('upload_max_filesize', '16M');

	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (!$auth->isLogged() || $auth->getCurrentUser()['blocked'] == true) {
			die("403");
		}

		if(isset($_POST['action'])) {
			$user = $auth->getCurrentUID();
			if(isset($_POST['user'])) {
				if ($auth->getCurrentUser()['admin'] != true) {
					die("0");
				}
				$user = $_POST['user'];

				if(!$auth->getUser($user)) {
					die("0");
				}
			}
			if(strtolower($_POST['action']) == "delete") {
				$auth->updateUser($user, array("profile_picture" => null));
				exit("1");
			}

			if(strtolower($_POST['action']) == "set") {
				if(isset($_POST['imgBase64'])) {
					$img = $_POST['imgBase64'];
					$img = str_replace('data:image/png;base64,', '', $img);
					$img = str_replace(' ', '+', $img);
					
					$save_target = $_SERVER['DOCUMENT_ROOT'].'/userdata/tmp/'.uniqid()."_new.png";
					$image = imagecreatefromstring(base64_decode($img));

					if($image == false) {
						die("Invalid data");
					}
					
					$imgX = imagesx($image);
					$imgY = imagesy($image);
					
					if($imgX > 300 || $imgY > 300) {
						$newImgSize = getImageSizeKeepAspectRatio($image, 300, 300);
						$image = imagescale($image, $newImgSize["width"], $newImgSize["height"]);
					}
					imagepng($image, $save_target);

					$new_name = md5_file($save_target).".png";

					$target = $_SERVER['DOCUMENT_ROOT'].'/userdata/img/'.basename($new_name);
					if(!file_exists($target)) {
						rename($save_target, $target);
					} else {
						unlink($save_target);
					}

					$auth->updateUser($user, array("profile_picture" => $new_name));
					exit(1);
				}
			}
		}
		exit("0");
	}

	if (!$auth->isLogged()) {
		header("Location: /");
		exit();
	}

	$profilePicture = "/img/user_icon.png";
	if ($auth->getCurrentUser()['blocked'] == true) {
		die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/Jcrop.css">
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
		<script src="/js/Jcrop.min.js"></script>
		<script src="/js/set-profilepicture.php.js"></script>
		<style type="text/css">
			.center {
				display: block
				margin: 0 auto;
				text-align: center;
			}
			.jcrop-holder {
				opacity: 0.5;
			}
			#cropImage {
				background-color: white;
			}
		</style>
		<title>Byt profilbild</title>
	</head>
	<body>
		<?php 
			require $_SERVER['DOCUMENT_ROOT'].'/php/header.php';
			echo $page_header;
			if ($auth->getCurrentUser()['blocked'] == true) {
				die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
			}
		?>
		<h2 class="text-center">Byt profilbild</h2>
		<center>
			<img id="cropImage" src="<?php echo $profilePicture; ?>"/>
		</center>
		<br>
		<div class="center">
			<div>
				<button id="btn_delete" class="btn btn-danger">Ta bort bild</button>
				<button id="btn_set" class="btn btn-success">Skicka</button>
			</div>
			<br>
			<input type="file" name="profile_picture" id="profile_picture" accept="image/*">
		</div>

		<canvas id="profile_picture_canvas" class="d-none"></canvas>
	</body>
</html>