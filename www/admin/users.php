<!DOCTYPE html>
<html>
	<head>
		<title>Admin - Användare</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/table.css">
		<link rel="stylesheet" type="text/css" href="/css/admin-users.css">
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

			require $_SERVER['DOCUMENT_ROOT'].'/php/db_connection.php';
			$connection = new db_connection;
		?><br/><br/>
		
		<table>
			<?php
				$connection->selectDB("phpauth");
				$queryResult = $connection->exec_statement("SELECT id FROM phpauth_users");
				
				if($queryResult['success']) {
					$newHtml = "<tr><th>ID</th><th>Email</th><th>Namn</th><th>Is active</th><th>Profilbild</th><th>Admin</th><th>Blockerad</th><th></th></tr>";
					while ($d=mysqli_fetch_assoc($queryResult['result'])) {
						$user = $auth->getUser($d['id']);
						$newHtml.= '<tr>';

						$newHtml.= '<td>'.$user['id'].'</td>';
						$newHtml.= '<td>'.htmlentities($user['email']).'</td>';
						$newHtml.= '<td>'.htmlentities($user['name']).'</td>';
						$newHtml.= '<td>'.($user['isactive'] ? 'true' : 'false').'</td>';
						$newHtml.='<td>';
						if($user['profile_picture'] != null) {
							$newHtml.='<img class="profile_picture float-left rounded-circle" src="/userdata/img/'.$user['profile_picture'].'">';
						}
						$newHtml.= json_encode($user['profile_picture']).'</td>';
						$newHtml.= '<td>'.($user['admin'] ? 'true' : 'false').'</td>';
						$newHtml.= '<td>'.($user['blocked'] ? 'true' : 'false').'</td>';
						$newHtml.= '<td><a href="/admin/user.php?id='.$user['id'].'"><img src="/img/options.png" class="settings_icon"></a></td>';
						$newHtml.= "</tr>";
					}
					echo $newHtml;
					unset($newHtml);
				}
			?>
		</table>
	</body>
</html>