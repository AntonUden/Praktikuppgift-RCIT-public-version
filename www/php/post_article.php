<!DOCTYPE html>
<?php
	require $_SERVER['DOCUMENT_ROOT'].'/php/global.php';
	require $_SERVER['DOCUMENT_ROOT'].'/php/db_data_manager.php';
	ini_set('upload_max_filesize', '16M'); 

	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';
				
	$dbdm = new db_data_manager;
	$userEmail = "null";
	$userName = "null";

	$edit = false;
	$editID = 0;
?>
<html>
	<head>
		<title>ladda upp artikel</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
	</head>
	<body>
		<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; echo $page_header; ?><br/><br/>

		<h2 class="ml-2">
		<?php
			function fixInput($input) {
				$input = stripcslashes($input);
				return $input;
			}

			if (!$auth->isLogged()) {
				header('HTTP/1.0 403 Forbidden');
				echo '<div class="alert alert-danger" role="alert">403. Du är inte inloggad</div>';

				exit();
			}

			if ($auth->getCurrentUser()['blocked'] == true) {
				die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
			}

			$userEmail = $auth->getCurrentUser()['email'];
			$userName = $auth->getCurrentUser()['name'];

			if(isset($_POST["edit"])) {
				$editArticle = $dbdm->getArticleByID($_POST["edit"]);
				if($editArticle) {
					if($editArticle->getAuthor()->getEmail() == $userEmail) {
						$edit = true;
						$editID = $editArticle->getID();
					} else {
						// Admin edit
						if($auth->getCurrentUser()["admin"] == true) {
							$userEmail = $editArticle->getAuthor()->getEmail();
							$userName = $editArticle->getAuthor()->getName();
							$edit = true;
							$editID = $editArticle->getID();
						} else {
							// 403
							header('HTTP/1.0 403 Forbidden');
							die('<div class="alert alert-danger" role="alert">Edit error. 403 Access denied</div>');
						}
					}
				} else {
					// 404
					die('<div class="alert alert-danger" role="alert">Edit error. 404</div>');
				}
			}
			
			if(isset($_POST["titel"]) && isset($_POST["articleContent"]) && isset($_POST["category"])) {
				die('<div class="alert alert-danger" role="alert">Invalid request</div>');
			}

			if (empty(fixInput($_POST["title"])) || strlen(fixInput($_POST["title"])) > 50) {
				die('<div class="alert alert-danger" role="alert">Ingen titel</div>');
			}

			if (empty($_POST["articleContent"])) {
				die('<div class="alert alert-danger" role="alert">Inget artikel innehåll</div>');
			}
			
			if (empty(removeTags($_POST["articleContent"])) || strlen(fixInput($_POST["articleContent"])) > 5000) {
				die('<div class="alert alert-danger" role="alert">Ogiltigt giltigt artikel innehåll</div>');
			}

			if(empty($_POST["category"])) {
				die('<div class="alert alert-danger" role="alert">Ingen kategori</div>');
			}

			if(!$dbdm->getCategoryByID($_POST["category"])) {
				die('<div class="alert alert-danger" role="alert">Ogiltig kategori</div>');
			}

			if($dbdm->articleExists(fixInput($_POST["title"]), fixInput($_POST["articleContent"])) && !$edit) {
				die('<div class="alert alert-danger" role="alert">En artikel med exakt samma titel och innehåll existerar redan</div>');
			}

			$dbdm->createOrUpdateAuthor(fixInput($userName), $userEmail);

			$articleImage = null;

			if($edit) {
				$articleImage = $editArticle->getImage();
			}

			if($_FILES['articleImage']['name']) {
				//if no errors...
				if(!$_FILES['articleImage']['error']) {
					if(file_exists($_FILES['articleImage']['tmp_name'])) {
						$ext = end((explode(".", $_FILES['articleImage']['name'])));

						if(!($ext == "png" || $ext == "jpg" || $ext == "jpeg" || $ext == "git")) {
							die('<div class="alert alert-danger" role="alert">Kunde inte ladda upp bilden. Ogiltigt filformat.<br> Gå tillbaka och försök igen.</div>');
						}

						$img_r = imagecreatefromstring(file_get_contents($_FILES['articleImage']['tmp_name']));
						$save_target = $_SERVER['DOCUMENT_ROOT'].'/userdata/tmp/'.basename($_FILES['articleImage']['tmp_name'])."_new.png";
						
						if(isset($_POST["cropX"]) && isset($_POST["cropY"]) && isset($_POST["cropW"]) && isset($_POST["cropH"])) {
							if(ctype_digit($_POST["cropX"]) && ctype_digit($_POST["cropY"]) && ctype_digit($_POST["cropW"]) && ctype_digit($_POST["cropH"])) {
								$dst_r = ImageCreateTrueColor( $_POST["cropW"], $_POST["cropH"]);
								imagecopyresampled($dst_r,$img_r,0,0,$_POST['cropX'],$_POST['cropY'], $_POST["cropW"], $_POST["cropH"],$_POST["cropW"],$_POST["cropH"]);
								imagedestroy($img_r);
							} else {
								die('<div class="alert alert-danger" role="alert">beskärning misslyckades. Gå tillbaka och välj bilden på nytt</div>');
							}
						} else {
							$dst_r = $img_r;
						}

						$imgX = imagesx($dst_r);
						$imgY = imagesy($dst_r);
						
						if($imgX > 1920 || $imgY > 1080) {
							$newImgSize = getImageSizeKeepAspectRatio($dst_r, 1920, 1080);
							$dst_r = imagescale($dst_r, $newImgSize["width"], $newImgSize["height"]);
						}				
		 				imagepng($dst_r, $save_target);

						$new_name = md5_file($save_target).".png";
						echo "<br>".$new_name."<br>";

						$target = $_SERVER['DOCUMENT_ROOT'].'/userdata/img/'.basename($new_name);
						if(!file_exists($target)) {
							rename($save_target, $target);
						} else {
							unlink($save_target);
						}
						$articleImage = $new_name;
					}
				} else {
					die('<div class="alert alert-danger" role="alert">Kunde inte ladda upp bilden. Gå tillbaka och försök igen.'.$_FILES['articleImage']['error'].'</div>');
				}
			}

			$forfattarid = fixInput($dbdm->getAuthorByEmail($userEmail)->getID());
			$kategoriid = fixInput($_POST["category"]);
			$titel = fixInput($_POST["title"]);
			$text = fixInput($_POST["articleContent"]);
			if($edit) {
				$queryResult = $dbdm->getConnection()->exec_statement("UPDATE artikel SET titel = ?, text = ?, kategoriid = ?, bild = ?, redigerad = ? WHERE id = ?", "ssssis", $titel, $text, $kategoriid, $articleImage, 1, $editID);
				if($queryResult['success']) {
					echo "Updated article with id ".$editID;
					header("Location: /view.php?artikel=".$editID);
					exit();
				} else {
					echo '<div class="alert alert-danger" role="alert">Kunde inte ändra artikeln</div><br>';
				}
			} else {
				$queryResult = $dbdm->getConnection()->exec_statement("INSERT INTO `artikel` (`id`, `titel`, `text`, `kategoriid`, `forfattarid`, `bild`) VALUES (NULL, ?, ?, ?, ?, ?)", "sssss", $titel, $text, $kategoriid, $forfattarid, $articleImage);

				if($queryResult['success']) {
					echo "Published with id ".$queryResult['insert_id'];
					header("Location: /view.php?artikel=".$queryResult['insert_id']);
					exit();
				} else {
					echo '<div class="alert alert-danger" role="alert">Kunde inte ladda upp artikeln</div><br>';
				}
			}
		?>
		</h2>
	</body>
</html>