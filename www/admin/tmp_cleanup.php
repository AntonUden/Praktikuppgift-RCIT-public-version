<!DOCTYPE html>
<html>
	<head>
		<title>Admin tmp cleanup</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
	</head>
	<body>
		<?php
			require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';
			require $_SERVER['DOCUMENT_ROOT'].'/php/header.php';
			echo $page_header;
		?><br/><br/>
		<div class="ml-2">
			<?php
				if (!$auth->isLogged() || $auth->getCurrentUser()['admin'] != true) {
					die('<div class="alert alert-danger" role="alert">403. Access denied</div>');
				}

				if ($auth->getCurrentUser()['blocked'] == true) {
					die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
				}

				$path = $_SERVER['DOCUMENT_ROOT']."/userdata/tmp/";

				$files = scandir($path);

				echo "Startar rensning i ".$path."<br><br>";
				$deleted = 0;
				foreach ($files as $file) {
					if(strtolower(pathinfo($file, PATHINFO_EXTENSION)) != "html" && !is_dir($file)) {
						$deleted++;
						echo $deleted.": Raderade ".$path."<span class='badge badge-info'>".$file."</span><br>";
						unlink($path.$file);
					}
				}


				echo '<br>Klar. <span class="badge badge-danger">'.$deleted.' filer raderade</span><br>';
			?>
		</div>
	</body>
</html>