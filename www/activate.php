<!DOCTYPE html>
<?php
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

	if ($auth->isLogged()) {
		header("Location: /");
	}
?>
<html>
	<head>
		<title>Aktivera ditt konto</title>
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
			<h2>Aktivera ditt konto</h2>
			<div id="inputForm" class="">
				<form action="/activate.php" method="post">
					<div class="form-group">
						<input type="text" name="key" placeholder="Aktiveringsnyckel" class="form-control" required>
					</div>

					<div class="form-group">
						<input type="submit" class="form-control btn btn-primary">
					</div>
				</form>
				<?php 
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						if(!isset($_POST["key"])) {
							echo '<div id="error" class="alert alert-danger" role="alert">Bad request</div>';
							die();
						}

						$result = $auth->activate($_POST["key"]);

						if($result['error']) {
							echo '<div id="error" class="alert alert-danger" role="alert">'.$result['message'].'</div>';
						} else {
							echo '<div id="info" class="alert alert-success" role="alert">'.$result['message'].'</div>';
						}
					}	
				?>
			</div>
		</div>
	</body>
</html>