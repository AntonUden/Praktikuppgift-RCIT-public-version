<!DOCTYPE html>
<?php 
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

	if ($auth->isLogged()) {
		header("Location: /");
	}
?>
<html>
	<head>
		<title>Logga in</title>
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
		<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; echo $page_header ?><br/>

		<div id="inputArea" class="rounded">
			<h2>Logga in</h2>
			<div id="inputForm" class="">
				<form action="/login.php" method="post">
					<div class="form-group">
						<input type="text" name="email" placeholder="Email" class="form-control" id="inputEmail" required>
					</div>

					<div class="form-group">
						<input type="password" name="password" placeholder="Lösenord" class="form-control" required>
					</div>

					<div class="form-group">
						<div class="row">
							<div class="col">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" name="rememberme" id="inputRememberme" value="true">
									<label class="form-check-label" for="gridCheck">
										Förbli inloggad
									</label>
							  	</div>
							</div>
						
							<div class="col">
								<a href="/forgot-password.php">Glömt lösenordet?</a>
							</div>
						</div>
					</div>

					<div class="form-group">
						<input type="submit" class="form-control btn btn-primary">
					</div>
				</form>
				<?php 
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						if(!isset($_POST["email"]) || !isset($_POST["password"])) {
							echo '<div id="error" class="alert alert-danger" role="alert">Bad request</div>';
							die();
						}

						$rememberme = false;
						if(isset($_POST['rememberme']) && $_POST['rememberme'] == 'true') {
							$rememberme = true;
						}

						$result = $auth->login(strtolower($_POST["email"]), $_POST["password"], $rememberme, null);
						if($result['error']) {
							echo '<div id="error" class="alert alert-danger" role="alert">'.$result['message'].'</div>';
							echo '<script>$("#inputEmail").val('.json_encode($_POST["email"]).'); $("#inputRememberme").prop("checked", '.json_encode($rememberme).');</script>';
						} else {
							header("Location: /");
						}
					}	
				?>
			</div>
			
			<div id="LoginRegister_link">
				Har du inget konto? <a href="/register.php">Registrera dig</a>
			</div>
		</div>
	</body>
</html>