<?php
require $_SERVER['DOCUMENT_ROOT'].'/php/global.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($auth->isLogged() && $auth->getCurrentUser()['admin'] == true && $auth->getCurrentUser()['blocked'] == false) {
		if(isset($_POST['user'])) {
			$user = $_POST['user'];

			if($auth->getUser($user)) {
				$result = $auth->deleteUser($user, "", null, true);
				if($result['error']) {
					die($result['message']);
				}
				exit("1");
			}
		}
	}
	die("0");
} else {
	header("Location: /");
	die("0");
}
?>