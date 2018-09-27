<!DOCTYPE html>
<html>
	<head>
		<title>Admin cleanup</title>
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

				require $_SERVER['DOCUMENT_ROOT'].'/php/db_connection.php';

				$conn = (new db_connection())->getConnection();

				if (!$conn) {
					die('<div class="alert alert-danger" role="alert">MYSql connection failed</div>');
				}

				$usedImages = array();

				$conn->select_db("artikelsida");
				$result = $conn->query("SELECT bild FROM artikel");

				while ($d = mysqli_fetch_assoc($result)) {
					if($d['bild'] != null) {
						array_push($usedImages, $d['bild']);
					}
				}

				$conn->select_db("phpauth");
				$result = $conn->query("SELECT profile_picture FROM phpauth_users");

				while ($d = mysqli_fetch_assoc($result)) {
					if($d['profile_picture'] != null) {
						array_push($usedImages, $d['profile_picture']);
					}
				}

				$path = $_SERVER['DOCUMENT_ROOT']."/userdata/img/";

				$files = scandir($path);

				$deleted = 0;
				$ignored = 0;
				$counter = 0;

				echo "Startar rensning i ".$path."<br><br>";

				foreach ($files as $file) {
					if(strtolower(pathinfo($file, PATHINFO_EXTENSION)) == "png") {
						$counter++;
						if(!array_search($file, $usedImages)) {
							$deleted++;
							echo $counter.': <span class="badge badge-danger">Raderade</span> '.$path.'<span class="badge badge-info">'.$file.'</span><br>';
							unlink($path.$file);
						} else {
							echo $counter.': <span class="badge badge-success">Ignorerade</span> '.$path.'<span class="badge badge-info">'.$file.'</span><br>';
							$ignored++;
						}
					}
				}


				echo '<br>Klar. <span class="badge badge-danger">'.$deleted.' filer raderade</span>. <span class="badge badge-success">'.$ignored.' filer ignorerades</span><br>';
			?>
		</div>
	</body>
</html>