<!DOCTYPE html>
<?php
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

	if ($auth->isLogged()) {
		header("Location: /");
	}
?>
<html>
	<head>
		<title>Glömt ditt lösenord</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/account.css">
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
	</head>
	<body>
		<?php
			require $_SERVER['DOCUMENT_ROOT'].'/php/header.php';
			echo $page_header;
		?><br/>

		<div id="inputArea" class="rounded">
			<h2>Glömt ditt lösenord</h2>
			<div id="inputForm" class="">
				<form action="/forgot-password.php" method="post">
					<div class="form-group">
						<input type="text" name="email" placeholder="Din email" class="form-control" required>
					</div>

					<div class="form-group">
						<input type="submit" class="form-control btn btn-primary">
					</div>
				</form>
				<?php 
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						if(!isset($_POST["email"])) {
							echo '<div id="error" class="alert alert-danger" role="alert">Bad request</div>';
							die();
						}
						require $_SERVER['DOCUMENT_ROOT'].'/php/db_connection.php';
						$connection = new db_connection;

						$connection->selectDB("phpauth");
						$queryResult = $connection->exec_statement("SELECT id FROM phpauth_users WHERE email=?", "s", strtolower($_POST["email"]));
						
						if($queryResult['success']) {
							$d=mysqli_fetch_assoc($queryResult['result']);
							$user = $auth->getUser($d['id']);
							if($user) {
								if($user['blocked']) {
									echo '<div id="error" class="alert alert-danger" role="alert">Kontot är blockerat</div>';
								} else {
									$result = $auth->requestReset($_POST["email"]);

									if($result['error']) {
										echo '<div id="error" class="alert alert-danger" role="alert">'.$result['message'].'</div>';
									} else {
										echo '<div id="info" class="alert alert-success" role="alert">'.$result['message'].'</div>';
									}
								}
							} else {
								echo '<div id="error" class="alert alert-danger" role="alert">Inget konto med den email adressen hittades</div>';
							}
						} else {
							echo '<div id="error" class="alert alert-danger" role="alert">mysql server fel</div>';
						}
					}	
				?>
			</div>
		</div>
	</body>
</html>