<?php
require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/db_data_manager.php';

$dbdm = new db_data_manager;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if ($auth->isLogged() && $auth->getCurrentUser()['admin'] == true) {
		if(isset($_POST['action'])) {
			if(strtolower($_POST['action']) == "delete" && isset($_POST['id'])) {
				if($dbdm->deleteCategory($_POST['id'])) {
					exit("1");
				}
			} else if(strtolower($_POST['action']) == "create" && isset($_POST['name'])) {
				if(strlen($_POST['name']) > 1 && $_POST['name'] <= 50) {
					if($dbdm->createCategory($_POST['name'])) {
						exit("1");
					}
				}
			}
		}
	}
	die("0");
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Admin - Kategorier</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/table.css">
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
	</head>
	<body>
		<?php
			require $_SERVER['DOCUMENT_ROOT'].'/php/header.php';

			echo $page_header;

			if (!$auth->isLogged() || $auth->getCurrentUser()['admin'] != true) {
				die('<div class="alert alert-danger" role="alert">403. Access denied</div>');
			}

			if ($auth->getCurrentUser()['blocked'] == true) {
				die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
			}
		?><br/><br/>

		<div class="alert alert-danger text-center mx-auto" style="width: 80%;" role="alert">
			Alternativen körs utan bekräftelse!
		</div>
		<table>
			<?php
				$newHtml = "<tr><th>ID</th><th>Kategorinamn</th><th></th></tr>";
				$categoryList = $dbdm->getCategoryList();
				foreach ($categoryList as $category) {
					$newHtml.= '<tr>';

					$newHtml.= '<td>'.$category->getID().'</td>';
					$newHtml.= '<td>'.$category->getName().'</td>';
					$newHtml.= '<td width="30"><button id="btn_delete" class="btn btn-danger" onClick="deleteCategory('.$category->getID().');"><img src="/img/delete.png" width="20" height="20"></button></td>';
					$newHtml.= '</tr>';
				}
				echo $newHtml;
				unset($newHtml);
				unset($categoryList);
			?>
		</table>

		<div id="createCategory" style="width: 80%; background-color: #FFFFFF;" class="mx-auto mt-2 border rounded">
			<h5>Skapa kategori</h5>
			<form>
				<div class="form-row">
					<div class="col">
						<input type="text" maxlength="50" id="categoryName" placeholder="Nytt Kategorinamn" class="form-control">
					</div>

					<div class="col-md-2">
						<button type="button" id="btnCreateCategory" class="form-control btn btn-success">Skapa kategori</button>
					</div>
				</div>
			</form>
		</div>
		
		<script type="text/javascript">
			function deleteCategory(id) {
				$.ajax({ type: "POST", url:"/admin/categories.php", data:{action:"delete", id:id}}).done(function(response) {
					console.log(response);
					location.reload();
				});
			}

			$("#btnCreateCategory").click(function() {
				$("#btnCreateCategory").prop("disabled", true);
				let name = $("#categoryName").val();
				$.ajax({ type: "POST", url:"/admin/categories.php", data:{action:"create", name:name}}).done(function(response) {
					console.log(response);
					location.reload();
				});
			});
		</script>
	</body>
</html>