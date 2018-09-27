<!DOCTYPE html>
<html>
	<head>
		<title>Admin</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
	</head>
	<body>
		<?php
			require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';
			require $_SERVER['DOCUMENT_ROOT'].'/php/header.php';
			echo $page_header;

			if (!$auth->isLogged() || $auth->getCurrentUser()['admin'] != true) {
				die('<div class="alert alert-danger" role="alert">403. Access denied</div>');
			}

			if ($auth->getCurrentUser()['blocked'] == true) {
				die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
			}
		?><br/><br/>
		<form class="w-50 mx-auto border border-secondary rounded">
			<div class="form-group mt-2 mx-1">
				<button type="button" onClick="window.location.href = '/admin/users.php';" class="form-control btn btn-primary">Användare</button>
			</div>

			<div class="form-group mx-1">
				<button type="button" onClick="window.location.href = '/admin/categories.php';" class="form-control btn btn-primary">Kategorier</button>
			</div>

			<div class="form-group mx-1">
				<button type="button" onClick="window.location.href = '/admin/admin_show_broken_articles.php';" class="form-control btn btn-primary">Hitta trasiga artiklar</button>
			</div>

			<div class="form-group mx-1">
				<button type="button" onClick="window.location.href = '/admin/userdata_cleanup.php';" class="form-control btn btn-warning">Rensa oanvänd data</button>
			</div>

			<div class="form-group mx-1">
				<button type="button" onClick="window.location.href = '/admin/tmp_cleanup.php';" class="form-control btn btn-warning">Redera temporär data</button>
			</div>

			<div class="form-group mx-1">
				<button type="button" onClick="window.location.href = '/admin/admin_delete_unused_authors.php';" class="form-control btn btn-warning">Ta bort oanvända författare</button>
			</div>
		</form>
	</body>
</html>