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
		<div class="ml-2">
			<?php
				require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';
				require $_SERVER['DOCUMENT_ROOT'].'/php/header.php';
				echo $page_header;
			?><br/><br/>
			<?php
				if (!$auth->isLogged() || $auth->getCurrentUser()['admin'] != true) {
					die('<div class="alert alert-danger" role="alert">403. Access denied</div>');
				}

				if ($auth->getCurrentUser()['blocked'] == true) {
					die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
				}

				require $_SERVER['DOCUMENT_ROOT'].'/php/db_connection.php';

				$conn = new db_connection();

				$articles = $conn->exec_statement("SELECT * FROM artikel");

				$count = 0;

				while ($d = mysqli_fetch_assoc($articles['result'])) {
					$result1 = $conn->exec_statement("SELECT id FROM kategori WHERE id = ?", "s", $d['kategoriid']);
					$result2 = $conn->exec_statement("SELECT id FROM forfattare WHERE id = ?", "s", $d['forfattarid']);

					if($result1['success'] && $result2['success']) {
						if($result1['result']->num_rows == 0 || $result1['result']->num_rows == 0 || strlen($d['titel']) == 0 || strlen($d['text']) == 0) {
							$error="";
							if($result1['result']->num_rows == 0) {
								$error.='<span class="badge badge-danger">Kategorin existerar inte</span> ';
							}

							if($result2['result']->num_rows == 0) {
								$error.='<span class="badge badge-danger">Författaren existerar inte</span> ';
							}

							if(strlen($d['titel']) == 0) {
								$error.='<span class="badge badge-danger">Ingen titel </span> ';
							}

							if(strlen($d['text']) == 0) {
								$error.='<span class="badge badge-danger">Ingen text </span> ';
							}
							$count++;
							echo '<a href="http://localhost/view.php?artikel='.$d['id'].'">'.$d['id'].' : '.htmlentities($d['titel']).'</a> '.$error.'<br>';
						}
					} else {
						echo '<a href="http://localhost/view.php?artikel='.$d['id'].'">'.$d['id'].' : '.htmlentities($d['titel']).'</a> <span class="badge badge-warning">MYSql query failed</span><br>';
					}
				}

				echo '<br>'.$count.' resultat';
			?>
		</div>
	</body>
</html>