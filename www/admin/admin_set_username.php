<?php
require $_SERVER['DOCUMENT_ROOT'].'/php/global.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/db_data_manager.php';
$dbdm = new db_data_manager;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($auth->isLogged() && $auth->getCurrentUser()['admin'] == true && $auth->getCurrentUser()['blocked'] == false) {
		if(isset($_POST['user']) && isset($_POST['name'])) {
			$user = $_POST['user'];
			if($auth->getUser($user)) {
				if(strlen(str_replace(' ', '', $_POST['name'])) == 0 || strlen($_POST['name']) > 30) {
					die("0");
				} else {
					if($auth->updateUser($user, array("name" => $_POST['name']))) {
						$email = $auth->getUser($user)['email'];
						if($dbdm->getAuthorByEmail(strtolower($email))->getID() != null) {
							$dbdm->createOrUpdateAuthor(stripcslashes($_POST['name']), strtolower($email));
						}
						exit("1");
					}
				}
			}
		}
	}
	die("0");
} else {
	header("Location: /");
	die("0");
}
?>