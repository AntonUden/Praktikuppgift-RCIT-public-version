<!DOCTYPE html>
<?php 
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

	if ($auth->isLogged()) {
		header("Location: /");
	}
?>
<html>
	<head>
		<title>Skapa konto</title>
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
			<h2>Skapa konto</h2>
			<div id="inputForm" class="">
				<form action="/register.php" method="post">
					<div class="form-group">
						<input type="text" name="email" placeholder="Email" class="form-control" id="inputEmail" maxlength="300" required>
					</div>

					<div class="form-group">
						<input type="text" name="name" placeholder="Ditt namn" class="form-control" id="inputName" maxlength="30" required>
					</div>

					<div class="form-group">
						<input type="password" name="password" placeholder="Lösenord" class="form-control" required>
					</div>

					<div class="form-group">
						<input type="password" name="repeatpassword" placeholder="Repetera lösenord" class="form-control" required>
					</div>

					<div class="form-group">
						<input type="submit" class="form-control btn btn-primary">
					</div>
				</form>
				<?php 
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						if(!isset($_POST["email"]) || !isset($_POST["password"]) || !isset($_POST["repeatpassword"]) || !isset($_POST["name"])) {
							echo '<div id="error" class="alert alert-danger" role="alert">Bad request</div>';
							die();
						}

						if(strlen($_POST["email"]) > 300 || strlen($_POST["name"]) > 30) {
							echo '<div id="error" class="alert alert-danger" role="alert">Invalid data</div>';
							die();
						}

						if(strlen(str_replace(' ', '', $_POST['name'])) == 0) {
							echo '<div id="error" class="alert alert-danger" role="alert">Ogiltigt namn</div>';
							die();
						}

						$userData = array("name" => $_POST["name"]);
						$result = $auth->register(strtolower($_POST["email"]), $_POST["password"], $_POST["repeatpassword"], $userData, null, false);

						if($result['error']) {
							echo '<div id="error" class="alert alert-danger" role="alert">'.$result['message']."</div>";
							echo '<script>$("#inputEmail").val('.json_encode($_POST["email"]).'); $("#inputName").val('.json_encode($_POST["name"]).');</script>';
						} else {
							echo '<div id="info" class="alert alert-success" role="alert">'.$result['message'].'</div>';
						}
					}
				?>
			</div>
			<div id="LoginRegister_link">
				Har du redan ett konto? <a href="/login.php">Logga in</a>
			</div>
		</div>
	</body>
</html>