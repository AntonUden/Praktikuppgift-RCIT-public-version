<!DOCTYPE html>
<?php
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';
	require $_SERVER['DOCUMENT_ROOT'].'/php/db_data_manager.php';

	if (!$auth->isLogged()) {
		header("Location: /");
	}

	$dbdm = new db_data_manager;
?>
<html>
	<head>
		<title>Byt namn</title>
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
			<h2>Byt namn</h2>
			<div id="inputForm" class="">
				<form action="/change-name.php" method="post">
					<div class="form-group">
						<input type="text" name="name" placeholder="Nytt namn" class="form-control" maxlength="30" required>
					</div>

					<div class="form-group">
						<input type="submit" class="form-control btn btn-primary">
					</div>
				</form>
				<?php
					if ($_SERVER['REQUEST_METHOD'] === 'POST') {
						if(!isset($_POST["name"])) {
							echo '<div id="error" class="alert alert-danger" role="alert">Bad request</div>';
							die();
						}

						if(strlen($_POST['name']) > 30) {
							echo '<div id="error" class="alert alert-danger" role="alert">Invalid value</div>';
							die();
						}

						$user = $auth->getCurrentUID();

						if(isset($_POST["user"])) {
							if($auth->getCurrentUser()['admin'] == true && $auth->getCurrentUser()['blocked'] == false) {
								$user = $_POST["user"];
								if(!$auth->getUser($user)) {
									die("user not found");
								}
							} else {
								echo '<div id="error" class="alert alert-danger" role="alert">403. only admins can change user name of other user</div>';
								die();
							}
						}

						if(strlen(str_replace(' ', '', $_POST['name'])) == 0) {
							echo '<div id="error" class="alert alert-danger" role="alert">Ogiltigt namn</div>';
							die();
						}

						$result = $auth->updateUser($user, array("name" => $_POST['name']));

						if($result['error']) {
							echo '<div id="error" class="alert alert-danger" role="alert">'.$result['message'].'</div>';
						} else {
							$email = $auth->getUser($user)['email'];
							if($dbdm->getAuthorByEmail(strtolower($email))->getID() != null) {
								$dbdm->createOrUpdateAuthor(stripcslashes($_POST['name']), strtolower($email));
							}
							if($_POST["user"]) {
								echo '<div id="info" class="alert alert-success" role="alert">Användarnamnet har ändrats till '.stripcslashes($_POST['name']).'</div>';
							} else {
								echo '<div id="info" class="alert alert-success" role="alert">Ditt användarnamn har ändrats till '.stripcslashes($_POST['name']).'</div>';
							}
						}
					}
				?>
			</div>
		</div>
	</body>
</html>