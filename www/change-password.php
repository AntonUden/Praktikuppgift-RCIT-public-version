<!DOCTYPE html>
<?php 
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

	if (!$auth->isLogged()) {
		header("Location: /");
	}
?>
<html>
	<head>
		<title>Byt lösenord</title>
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
			if ($auth->getCurrentUser()['blocked'] == true) {
				die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
			}
		?><br/>

		<div id="inputArea" class="rounded">
			<h2>Byt lösenord</h2>
			<div id="inputForm" class="">
				<form action="/change-password.php" method="post">
					<div class="form-group">
						<input type="password" name="oldpassword" placeholder="Gammalt lösenord" class="form-control" required>
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
						if(!isset($_POST["oldpassword"]) || !isset($_POST["newpassword"]) || !isset($_POST["repeatnewpassword"])) {
							echo '<div id="error" class="alert alert-danger" role="alert">Bad request</div>';
							die();
						}

						$result = $auth->changePassword($auth->getUID($auth->getCurrentUser()['email']), $_POST["oldpassword"], $_POST["newpassword"], $_POST["repeatnewpassword"]);

						if($result['error']) {
							echo '<div id="error" class="alert alert-danger" role="alert">'.$result['message']."</div>";
						} else {
							echo '<div id="info" class="alert alert-success" role="alert">'.$result['message'].'</div>';
						}
					}
				?>
			</div>
		</div>
	</body>
</html>