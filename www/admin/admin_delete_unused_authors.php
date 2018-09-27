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

				$conn = new db_connection();

				$result = $conn->exec_statement("SELECT * FROM forfattare");

				$ignored = 0;
				$deleted = 0;

				echo "Startar rensning<br><br>";

				while ($d = mysqli_fetch_assoc($result['result'])) {
					$result2 = $conn->exec_statement("SELECT id FROM artikel WHERE forfattarid = ?", "i", $d['id']);
					if($result2['success']) {
						if($result2['result']->num_rows > 0) {
							echo htmlentities($d['email']).': <span class="badge badge-success">Används</span><br>';
							$ignored++;
						} else {
							$conn->exec_statement("DELETE FROM forfattare WHERE id = ?", "i", $d['id']);
							echo htmlentities($d['email']).': <span class="badge badge-danger">Raderad</span><br>';
							$deleted++;
						}
					} else {
						echo htmlentities($d['email']).': <span class="badge badge-warning">MYSql query failed</span><br>';
					}
				}
				echo '<br>Klar. <span class="badge badge-danger">'.$deleted.' Författare raderade</span>. <span class="badge badge-success">'.$ignored.' Författare ignorerades</span>';
			?>
		</div>
	</body>
</html>