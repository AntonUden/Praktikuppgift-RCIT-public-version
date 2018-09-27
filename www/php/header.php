<?php
$loginUserEmail = "null";
$loginUserName = "null";
$profilePicture = "/img/user-icon.png";
$admin = false;
$blocked = false;
if($auth->isLogged()) {
	if($auth->getCurrentUser()['admin'] == true) {
		$admin = true;
	}
	if($auth->getCurrentUser()['blocked'] == true) {
		$blocked = true;
	}
}

$page_header = '
<nav class="navbar navbar-dark bg-dark">
	<form class="form-inline">
		<a class="btn btn-primary btn-lg active" href="/">Start</a>
		<a class="ml-1 btn btn-secondary btn-lg active" href="/write.php">Skicka in artikel</a>';
if ($admin) {
	$page_header.= '<a class="ml-1 btn btn-secondary btn-lg active" href="/admin">Admin</a>';
}
$page_header.= '</form>';

if ($auth->isLogged()) {
	$page_header.='<button class="btn btn-secondary btn-lg dropdown-toggle active" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Konto</button>';
	$loginUserEmail = htmlentities($auth->getCurrentUser()['email']);
	$loginUserName = htmlentities($auth->getCurrentUser()['name']);
	if($auth->getCurrentUser()['profile_picture'] != null) {
		$profilePicture = "/userdata/img/".$auth->getCurrentUser()['profile_picture'];
	}
} else {
	$page_header.='<a href="/login.php" class="btn btn-secondary btn-lg active float-right">Logga in</a>';
}
$page_header.='
	<div class="dropdown-menu dropdown-menu-right">
		<div class="dropdown-header"><div><img class="rounded-circle d-inline-block mr-2" width="75" height="75" src="'.$profilePicture.'"><span class="d-inline-block">Inloggad som '.$loginUserName.'<br/>'.$loginUserEmail.'</span></div>';
if($admin) {
	$page_header.='<span class="badge badge-success">Admin account</span>';
}
if($blocked) {
	$page_header.='<abbr title="Ditt konto har blivit blockerat. Du kan nu bara läsa andras artiklar"><span class="badge badge-danger">Blockerad</span></abbr>';
}
$page_header.='</div>
		<div class="dropdown-divider"></div>
		<a class="dropdown-item" href="/set-profilepicture.php">Byt profilbild</a>
		<a class="dropdown-item" href="/change-name.php">Byt namn</a>
		<a class="dropdown-item" href="/change-password.php">Byt lösenord</a>
		<div class="dropdown-divider"></div>
		<a class="dropdown-item" href="/logout.php">Logga ut</a>
	</div>
</nav><br/>
';

unset($loginUserEmail);
unset($loginUserName);
?>