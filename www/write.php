<!DOCTYPE html>
<?php
	require $_SERVER['DOCUMENT_ROOT'].'/php/db_data_manager.php';
	require $_SERVER['DOCUMENT_ROOT'].'/php/global.php';
	ini_set('upload_max_filesize', '16M');
	$dbdm = new db_data_manager;

	require $_SERVER['DOCUMENT_ROOT'].'/php/get_phpauth.php';

	$authorUserName = "null";
?>
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="/css/main.css">
		<link rel="stylesheet" type="text/css" href="/css/write.css">
		<link rel="stylesheet" type="text/css" href="/css/Jcrop.css">
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
		<script src="/js/Jcrop.min.js"></script>
		<script src="/js/write.php.js"></script>
		<title>Skriv artikel</title>
	</head>
	<body>
		<div id="dimScreen"></div>

		<?php 
			$editCategory = "";
			$editTitle = "";
			$editContent = "";

			function clearGet() {
				echo '<script type="text/javascript">let uri = window.location.toString();if (uri.indexOf("?") > 0) {let clean_uri = uri.substring(0, uri.indexOf("?"));window.history.replaceState({}, document.title, clean_uri);}location.reload();</script>';
			}
		?>

		<div id="mainDiv">
			<?php
				require $_SERVER['DOCUMENT_ROOT'].'/php/header.php';
				echo $page_header;
				if (!$auth->isLogged()) {
					echo '<div id="msg_noLogin">Du måste <a href="/login.php">logga in</a> för att skriva artiklar</div>';
				}

				if ($auth->getCurrentUser()['blocked'] == true) {
					die('<div class="alert alert-danger" role="alert">Ditt konto är blockerat</div>');
				}
				$authorUserName = $auth->getCurrentUser()['name'];
				if(isset($_GET["edit"])) {
					$article = $dbdm->getArticleByID($_GET['edit']);
					if($article) {
						if($auth->getCurrentUser()['email'] == $article->getAuthor()->getEmail() || $auth->getCurrentUser()["admin"] == true) {
							$editCategory = $article->getCategory()->getID();
							$editTitle = $article->getTitle();
							$editContent = $article->getText();
							$authorUserName = $article->getAuthor()->getName();
						} else {
							clearGet();
						}
					} else {
						clearGet();
					}
				}
			?><br/>
			<div id="write_main">
				<div id="userInput">
					<form action="/php/post_article.php" method="post" id="articleForm" enctype="multipart/form-data">
						<div class="form-group">
							<div class="row">
								<div class="col">
									<input type="text" name="title" class="form-control" placeholder="Titel" maxlength="50" required value="<?php echo htmlentities($editTitle); ?>">
								</div>

								<div class="col">
									<select name="category" class="form-control" required>
										<option value="">Välj kategori</option>
										<?php
											$categoryList = $dbdm->getCategoryList();
											foreach ($categoryList as $category) {
												$extra = "";
												if($category->getID() == $editCategory) {
													$extra .= "selected";
												}
												echo '<option value="'.$category->getID().'" '.$extra.'>'.$category->getName().'</option>';
											}
											unset($categoryList);
										?>
									</select>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row">
								<div class="col">
									<button type="button" id="previewButton" class="form-control btn btn-info">Förhandsvisning</button>
								</div>
								
								<div class="col">
									<button type="button" id="btn_submit" class="form-control btn btn-success">Skicka</button><br/>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="form-row">
								<label for="exampleFormControlFile1">Välj en bild</label>
								<div class="col-auto">
									<input type="file" name="articleImage" class="form-control-file" id="articleImage" accept="image/*">
								</div>

								<div class="col-auto">
									<button type="button" id="btn_crop" class="btn btn-primary" disabled>Beskär bild</button>
								</div>

								<div class="col-auto">
									<span class="scaledownInfo">Bilden kommer bli nedskalad!</span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<textarea rows="40" class="form-control" id="articleContent" name="articleContent" form="articleForm" maxlength="5000"><?php echo htmlentities(htmlToTags($editContent)); ?></textarea>
						</div>

						<?php if(isset($_GET["edit"])) { echo '<input type="hidden" id="edit" name="edit" value="'.intval(htmlentities($_GET["edit"])).'" />'; } ?>
						<input type="hidden" id="cropX" name="cropX" />
						<input type="hidden" id="cropY" name="cropY" />
						<input type="hidden" id="cropW" name="cropW" />
						<input type="hidden" id="cropH" name="cropH" />
					</form>
				</div>
				<div id="helpText">
					Tips: du han använda html taggar genom att skriva t.ex [h2]Hej[/h2].<br/>
					Lista på taggar:
					<code>
						<?php
							foreach ($validTags as &$value) {
								echo "[".$value."] ";
							}
						?>
					</code>
					<br/>
					Beskrivning av vad taggarna gör kan du hitta här: <a href="https://www.w3schools.com/tags/default.asp">w3schools.com/tags/default.asp</a><br/>
					Glöm inte att avsluta alla taggar.
				</div>

				<div id="confirmBox">
					Är du säker på att du vill skicka in artikeln<br/>
					<div>
						<button id="btn_cancel" class="btn btn-danger">Avbryt</button>
						<button id="btn_preview" class="btn btn-info">Förhandsvisning</button>
						<button id="btn_send" class="btn btn-success">Ja</button>
					</div>
					<div id="postError"></div>
				</div>
			</div>
		</div>
		<div id="cropDiv">
			<center>
				<img id="cropImage" class="image" src="#"/>
			</center>
			<div class="scaledownInfo">Bilden kommer bli nedskalad!</div>
			<button id="closeCropButton" class="btn btn-success">Klar</button>
		</div>

		<div id="preview">
			<nav class="navbar navbar-dark bg-dark">
				<form class="form-inline">
					<a class="btn btn-primary btn-lg active">Start</a>
					<a class="ml-1 btn btn-secondary btn-lg active">Skicka in artikel</a>
				</form>
				<button id="closePreviewButton" class="btn btn-success btn-lg">Stäng förhandsvisning</button>
			</nav>

			<div id="article">
				<div id='title'></div>

				<div id='category'></div>

				<div id='author'><?php echo 'Skriven av <a href="#">'.htmlentities($authorUserName).'</a>'; ?></div><br/>

				<canvas id="image"></canvas>

				<div id='content'></div>
			</div>
		</div>
		<?php
			$script="<script>function addTags(str) {";
			foreach ($GLOBALS['validTags'] as &$value) {
				$script.="str = str.replace('[".$value."]', '<".$value.">').replace('[/".$value."]', '</".$value.">');";
			}
			$script.="return str;}</script>";

			echo $script;
			unset($script);
		?>
		<script type="text/javascript">
			if ($('#msg_noLogin').length > 0){
				$('#write_main').hide();
			}
		</script>
	</body>
</html>