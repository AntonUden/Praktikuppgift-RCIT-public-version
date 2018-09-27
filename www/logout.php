<?php 
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';
	if ($auth->isLogged()) {
		$auth->logout($auth->getSessionHash());
	}
	header("Location: /");
	exit();
?>