<!DOCTYPE html>
<?php
	require $_SERVER['DOCUMENT_ROOT'].'/php/db_data_manager.php';
	require $_SERVER['DOCUMENT_ROOT'].'/php/global.php';
	$dbdm = new db_data_manager;
	
	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/view.css">
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
		<style type="text/css">
			#title {
				padding-left: 110px;
			}
		</style>
		<?php
			if(isset($_GET["artikel"])) {
				$article = $dbdm->getArticleByID($_GET['artikel']);
				if($article) {
					echo "<title>".htmlentities($article->getTitle())." Av ".htmlentities($article->getAuthor()->getName())."</title>";
				} else {
					echo "<title>404</title>";
				}
				unset($article);
			} else {
				echo "<title>Error. Invalid request</title>";
			}
		?>
	</head>
	<body>
		<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; echo $page_header ?>

		<div id="article">
			<?php
				if(isset($_GET["artikel"])) {
					$article = $dbdm->getArticleByID($_GET['artikel']);
					if($article) {
						$articleText = '<span id="edit_options_area">';

						// Delete and edit button (If user is logged in)
						if ($auth->isLogged()) {
							if(($auth->getCurrentUser()['email'] == $article->getAuthor()->getEmail() || $auth->getCurrentUser()['admin'] == true) && $auth->getCurrentUser()['blocked'] == false) {
								$articleText .= '<a href="/write.php?edit='.htmlentities($_GET["artikel"]).'" id="btn_edit" title="Redigera artikel" class="btn btn-light"><img class="edit_options" src="/img/edit.png"></a>';
								$articleText .= ' <button id="btn_delete" title="Radera artikel (Kan inte ångras)" class="btn btn-danger"><img class="edit_options" src="/img/delete.png"></button>';
							}
						}
						$articleText .= "</span>";

						// Title
						$articleText .= '<div id="title">'.htmlentities($article->getTitle());
						
						// Edited icon
						if($article->isEdited()) {
							$articleText .= ' <abbr title="Artikeln har redigerats"><img src="/img/edited.png" id="icon_edited"></abbr>';
						}
						$articleText .= "</div>";

						// Category
						$articleText .= '<div id="category">'.$article->getCategory()->getName().'</div>';
						// Author
						$articleText .= '<div id="author">Skriven av ';

						$uid = $auth->getUID($article->getAuthor()->getEmail());
						if($uid) {
							$user = $auth->getUser($uid);
							if($user['profile_picture'] != null) {
								$articleText.='<img width="50" height="50" class="rounded-circle" src="/userdata/img/'.$user['profile_picture'].'"> ';
							}
						}

						$articleText .= "<a href='mailto:".htmlentities($article->getAuthor()->getEmail())."'>".htmlentities($article->getAuthor()->getName())."</a>";

						// Date
						$articleText .= " ".date("Y-m-d",strtotime($article->getDate()))."</div>";
						
						// Image (if found)
						if($article->getImage()) {
							$articleText .= "<img id='image' src='/userdata/img/".$article->getImage()."'/>";
						}

						// Content
						$articleText .= "<br/><div id='content'>".closeTags(nl2br(addTags(htmlentities($article->getText()))))."</div>";

						echo $articleText;
					} else {
						echo "<h1>404</h1><br><h2>Kunde inte hitta artikeln med id ".htmlentities($_GET['artikel'])."</h2>";
					}
					unset($article);
				} else {
					echo "<h1>Invalid request</h1>";
				}
			?>
		</div>

		<div id="deleteAtricle">
			<div class="text-center alert alert-danger" role="alert">Vill du verkligen ta bort den här artikeln (Detta går inte att ångra)</div>
			<button id="btn_cancel" class="btn btn-secondary btn-lg">Avbryt</button>
			<button id="btn_delete_confirm" class="btn btn-danger btn-lg">Ta bort</button>
		</div>

		<script type="text/javascript">
			$("#btn_delete").click(function() {
				$("#deleteAtricle").show();
			});

			$("#btn_cancel").click(function() {
				$("#deleteAtricle").hide();
			});

			$("#btn_delete_confirm").click(function() {
				$("#deleteAtricle").hide();
				window.location.href = "/php/delete-article.php?id=" + <?php if(isset($_GET["artikel"])) { echo $_GET["artikel"]; } ?>;
			});
		</script>
	</body>
</html>