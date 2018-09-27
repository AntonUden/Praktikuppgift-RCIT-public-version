<!DOCTYPE html>
<?php
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

	if ($auth->isLogged()) {
		header("Location: /");
	}
?>
<html>
	<head>
		<title>Återställ ditt lösenord</title>
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
			<h2>Återställ ditt lösenord</h2>
			<div id="inputForm" class="">
				<form action="/reset-password.php" method="post">
					<div class="form-group">
						<input type="text" name="key" placeholder="Återställningsnyckel" class="form-control" id="inputKey" required>
					</div>

					<div class="form-group">
						<input type="password" name="newpassword" placeholder="Nytt lösenord" class="form-control" required>
					</div>

					<div class="form-group">
						<input type="password" name="repeatnewpassword" placeholder="Repetera nytt lösenord" class="form-control" required>
					</div>

					<div class="form-group">
						<input type="submit" class="form-control btn btn-primary">
					</div>
				</form>
				<?php 
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						if(!isset($_POST["key"]) || !isset($_POST["newpassword"]) || !isset($_POST["repeatnewpassword"])) {
							echo '<div id="error" class="alert alert-danger" role="alert">Bad request</div>';
							die();
						}
						
						$result = $auth->resetPass($_POST["key"], $_POST["newpassword"], $_POST["repeatnewpassword"]);

						if($result['error']) {
							echo '<div id="error" class="alert alert-danger" role="alert">'.$result['message'].'</div>';
							echo '<script>$("#inputKey").val("'.json_encode($_POST["key"]).'");</script>';
						} else {
							echo '<div id="info" class="alert alert-success" role="alert">Ditt lösenord har ändrats</div>';
						}
					}	
				?>
			</div>
		</div>
	</body>
</html>