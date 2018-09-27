<!DOCTYPE html>
<html>
	<head>
		<?php if(isset($_GET['id'])) {
			echo "<title>Admin - Användare ".$_GET['id']."</title>";
		} else {
			echo "<title>Invalid request</title>";
		}?>
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
			
			if ($auth->getCurrentUser()['blocked'] == true) {
				die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
			}

			if(!isset($_GET['id'])) {
				die('<div class="alert alert-danger" role="alert">Invalid request</div>');
			}

			$user = $auth->getUser($_GET['id']);
			if(!$user) {
				die('<div class="alert alert-danger" role="alert">User not found</div>');
			}
		?><br/><br/>
		<table>
			<tr>
				<th>ID</th>
				<th>Email</th>
				<th>Namn</th>
				<th>Is active</th>
				<th>Profilbild</th>
				<th>Admin</th>
				<th>Blockerad</th>
			</tr>
			<tr>
				<?php
					$newHtml = '<tr>';

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
					echo $newHtml;
					unset($newHtml);
				?>
			</tr>
		</table>
		<div id="account_option" class="mx-auto">
			<div class="alert alert-danger text-center mx-1 my-1" role="alert">
				Alternativen körs utan bekräftelse och vissa ändringar är permanenta!
			</div>
			<form>
				<div class="form-group mx-1">
					<div class="row">
						<div class="col">
							<button type="button" class="form-control btn btn-info" id="btn_openChangeName">Byt namn</button>
						</div>

						<div class="col">
							<button type="button" class="form-control btn btn-warning" id="btn_delProfileficture">Radera profilbild</button>
						</div>

						<div class="col">
							<?php
								if($user['admin'] == true) {
									echo '<button type="button" class="form-control btn btn-danger" id="btn_removeAdmin">Ta bort admin</button>';
								} else {
									echo '<button type="button" class="form-control btn btn-danger" id="btn_setAdmin">Gör till admin</button>';
								}
							?>
							
						</div>

						<div class="col">
							<?php
								if($user['blocked'] == true) {
									echo '<button type="button" class="form-control btn btn-danger" id="btn_unblock">Avblockera</button>';
								} else {
									echo '<button type="button" class="form-control btn btn-danger" id="btn_block">Blockera</button>';
								}
							?>
							
						</div>

						<div class="col">
							<button type="button" class="form-control btn btn-danger" id="btn_delUser">Radera användare</button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div id="changeUsernameBox">
			<form class="my-2 mx-auto">
				<div class="row">
					<div class="col">
						<input type="text" maxlength="30" class="form-control" id="newUsername" placeholder="Nytt namn">
					</div>
					<div id="col">
						<button type="button" title="Avbryt" class="btn btn-danger" onclick="$('#changeUsernameBox').hide();">
							<img src="/img/close.png" class="close_icon">
						</button>
					</div>
				</div>
				<div class="row my-2">
					<button type="button" class="form-control btn btn-info" id="btn_setUsername">Byt namn</button>
				</div>
			</form>
		</span>

		<script>
			var userID = <?php echo $user['id']; ?>;
			
			$('form').submit(false);

			$("#btn_delProfileficture").click(function() {
				$.ajax({ type: "POST", url:"/set-profilepicture.php", data:{action:"delete", user:userID}}).done(function(response) {
					location.reload();
				});
			});

			$("#btn_openChangeName").click(function() {
				$("#changeUsernameBox").show();
			});

			$("#btn_delUser").click(function() {
				$.ajax({ type: "POST", url:"/admin/admin_delete_user.php", data:{user:userID}}).done(function(response) {
					console.log("response: " + response);
					if(response.localeCompare("1")) {
						window.location.href = "/admin/users.php";
					}
				});
			});

			$("#btn_setUsername").click(function() {
				$.ajax({ type: "POST", url:"/admin/admin_set_username.php", data:{user:userID, name:$("#newUsername").val()}}).done(function(response) {
					console.log("response: " + response);
					location.reload();
				});
			});

			$("#btn_setAdmin").click(function() {
				$.ajax({ type: "POST", url:"/admin/admin_set_admin_user.php", data:{user:userID, admin:"true"}}).done(function(response) {
					console.log("response: " + response);
					location.reload();
				});
			});

			$("#btn_removeAdmin").click(function() {
				$.ajax({ type: "POST", url:"/admin/admin_set_admin_user.php", data:{user:userID, admin:"false"}}).done(function(response) {
					console.log("response: " + response);
					location.reload();
				});
			});

			$("#btn_block").click(function() {
				$.ajax({ type: "POST", url:"/admin/admin_block_user.php", data:{user:userID, blocked:"true"}}).done(function(response) {
					console.log("BLOCK: response: " + response);
					$.ajax({ type: "POST", url:"/set-profilepicture.php", data:{action:"delete", user:userID}}).done(function(response) {
						console.log("DELETE PROFILE PICTURE: response: " + response);
						location.reload();
					});
				});
			});

			$("#btn_unblock").click(function() {
				$.ajax({ type: "POST", url:"/admin/admin_block_user.php", data:{user:userID, blocked:"false"}}).done(function(response) {
					console.log("response: " + response);
					location.reload();
				});
			});
		</script>
	</body>
</html>