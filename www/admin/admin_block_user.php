<?php
require $_SERVER['DOCUMENT_ROOT'].'/php/global.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($auth->isLogged() && $auth->getCurrentUser()['admin'] == true && $auth->getCurrentUser()['blocked'] == false) {
		if(isset($_POST['user']) && isset($_POST['blocked'])) {
			$user = $_POST['user'];
			if($auth->getUser($user)) {
				$setBlocked = 0;

				if($_POST['blocked'] == "true") {
					$setBlocked = 1;
				}
				$auth->updateUser($user, array("blocked" => $setBlocked));
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