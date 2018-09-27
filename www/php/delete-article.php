<!DOCTYPE html>
<?php
	require $_SERVER['DOCUMENT_ROOT'].'/php/db_data_manager.php';
	require $_SERVER['DOCUMENT_ROOT'].'/php/global.php';
	$dbdm = new db_data_manager;
	
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

	if (!$auth->isLogged()) {
		header("Location: /");
	}
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
	</head>
	<body>
		<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; echo $page_header ?>

		<h2>
			<?php 
				if(isset($_GET["id"])) {
					$article = $dbdm->getArticleByID($_GET['id']);
					if($article) {
						if($article->getAuthor()->getEmail() == $auth->getCurrentUser()['email'] || $auth->getCurrentUser()['admin'] == true) {
							if($dbdm->deleteArticleByID($_GET["id"])) {
								header("Location: /");
							} else {
								echo "Kunde inte radera artikeln. Okänt fel";
								die();
							}
						} else {
							echo "403. Du är inte författaren av den här artikeln";
							die();
						}
					} else {
						echo "Kunde inte hitta artikeln med id ".htmlentities($_GET['id']);
						die();
					}
					unset($article);
				} else {
					echo "<h1>Invalid request</h1>";
					die();
				}
			?>
		</h2>
	</body>
</html>