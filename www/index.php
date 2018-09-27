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
		<link rel="stylesheet" type="text/css" href="/css/index.css">
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="sv" />
		<title>Artikel sida</title>
		<script src="/js/jquery.min.js"></script>
		<script src="/js/popper.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
	</head>
	<body>
		<?php require $_SERVER['DOCUMENT_ROOT'].'/php/header.php'; echo $page_header ?>

		<div id="content">
			<div id="content_header">
				<span id="searchTitle"></span>
				<span id="searchOptions">
					Visa författare <select name="author" id="author" class="rounded">
						<option value="">Alla författare</option>
						<?php
							$authorList = $dbdm->getAuthorList();
							foreach ($authorList as $author) {
								$extra = "";
								if(isset($_GET['forfattare'])) {
									if($_GET['forfattare'] == $author->getID()) {
										$extra.="selected";
									}
								}
								echo "<option ".$extra." value='".$author->getID()."'>".htmlentities($author->getName())."</option>";
							}
							unset($authorList);
						?>
					</select>
					Visa kategori <select name="category" id="category" class="rounded">
						<option value="">Alla kategorier</option>
						<?php
							$categoryList = $dbdm->getCategoryList();
							foreach ($categoryList as $category) {
								$extra = "";
								if(isset($_GET['kategori'])) {
									if($_GET['kategori'] == $category->getID()) {
										$extra.="selected";
									}
								}
								echo "<option ".$extra." value='".$category->getID()."'>".htmlentities($category->getName())."</option>";
							}
							unset($categoryList);
						?>
					</select>
				</span>
			</div>
			<table id="articles" border="1">
				<tr>
					<th>Titel</th>
					<th>Sammanfattning</th> 
					<th>Författare</th>
					<th>Kategori</th>
				</tr>
				<?php
					$offset = 0;
					if(isset($_GET['sida'])) {
						$offset = intval($_GET['sida']) - 1;
						if($offset == -1) {
							$offset = 0;
						}
						$offset*=10;
					}

					$category = null;
					if(isset($_GET['kategori'])) {
						$category = $_GET['kategori'];
					}

					$author = null;
					if(isset($_GET['forfattare'])) {
						$author = $_GET['forfattare'];
					}

					$result = $dbdm->getArticles(10, $offset, $category, $author);
					foreach ($result as $article) {
						$articleText = "<tr>";

						$articleText.= "<td><a href='/view.php?artikel=".$article->getID()."'>".htmlentities($article->getTitle())."</a></td>";

						$articleText.= "<td>";
						if($article->getImage()) {
							$articleText.= '<abbr title="innehåller en bild"><img src="/img/image.png" class="imageIcon"></abbr>';
						}
						$articleText.= substr(removeTags(htmlentities($article->getText())), 0, 30);
						if(strlen(removeTags(htmlentities($article->getText()))) > 30) {
							$articleText.= "...";
						}
						$articleText.= "</td>";

						$articleText.= "<td>";

						$uid = $auth->getUID($article->getAuthor()->getEmail());
						if($uid) {
							$user = $auth->getUser($uid);
							if($user['profile_picture'] != null) {
								$articleText.='<img width="28" height="28" class="float-left rounded-circle" src="/userdata/img/'.$user['profile_picture'].'">';
							}
						}

						$articleText.= "<a href='#' onclick='javascript:setAuthor(".$article->getAuthor()->getID().")'>".htmlentities($article->getAuthor()->getName())."</a></td>";
						$articleText.= "<td><a href='#' onclick='javascript:setCategory(".$article->getCategory()->getID().")'>".htmlentities($article->getCategory()->getName())."</a></td>";

						$articleText.="</tr>";
						echo $articleText;
					}
					unset($result);
				?>
			</table>
			<div id="navigation">
				<button id="nav_newer" class="btn btn-success">Nyare</button>
				<?php
					$category = null;
					if(isset($_GET['kategori'])) {
						$category = $_GET['kategori'];
					}

					$author = null;
					if(isset($_GET['forfattare'])) {
						$author = $_GET['forfattare'];
					}

					$count = $dbdm->countArticles($category, $author);
					$page = 1;

					if(isset($_GET['sida'])) {
						$page = intval($_GET['sida']);
					}

					$extra = "";
					if($page >= ceil($count / 10)) {
						$extra = "disabled";
					}
					echo "<button id='nav_older' class='btn btn-success' ".$extra.">Äldre</button>";
					$lastPage = ceil($count / 10);
					if($lastPage == 0) {
						$lastPage = 1;
					}
					echo "<div id='pageCount'>Sida ".$page."/".$lastPage."</div>";
				?>
			</div>
		</div>
		<script>
			var url = new URL(window.location.href);
			console.log(window.location.href);
			let page = url.searchParams.get("sida");

			if(page) {
				if(parseInt(page) != null && !isNaN(page)) {
					if(parseInt(page) < 1) {
						setSearchParam("sida", 1);
						setUrlParams();
					}

					if(parseInt(page) == 1) {
						$("#nav_newer").prop("disabled",true);
					}
				} else {
					delSearchParam("sida");
					setUrlParams();
				}
			} else {
				$("#nav_newer").prop("disabled",true);
			}

			let kategori = url.searchParams.get("kategori");
			if(kategori) {
				if(parseInt(kategori) == null || isNaN(kategori)) {
					delSearchParam("kategori");
					setUrlParams();
				}
			}

			let forfattare = url.searchParams.get("forfattare");
			if(forfattare) {
				if(parseInt(forfattare) == null || isNaN(forfattare)) {
					delSearchParam("forfattare");
					setUrlParams();
				}
			}

			$("#nav_older").click(function(){
				let page = url.searchParams.get("sida");
				if(page) {
					if(parseInt(page) != null) {
						setSearchParam("sida", parseInt(page)+1);
						setUrlParams();
					} else {
						setSearchParam("sida", 2);
						setUrlParams();
					}
				} else {
					setSearchParam("sida", 2);
					setUrlParams();
				}
			});

			$("#nav_newer").click(function(){
				let page = url.searchParams.get("sida");
				if(page) {
					if(parseInt(page) != null) {
						if(parseInt(page) > 1) {
							setSearchParam("sida", parseInt(page)-1);
							setUrlParams();
						}
					} else {
						setSearchParam("sida", 1);
						setUrlParams();
					}
				}
			});

			$("#category").change(function(){
				let newCategory = $("#category").val();
				if(newCategory) {
					console.log("Selected " + newCategory);
					setSearchParam("kategori", newCategory);
				} else {
					delSearchParam("kategori");
				}

				let page = url.searchParams.get("sida");
				if(page) {
					setSearchParam("sida", 1);
				}
				setUrlParams();
			});

			$("#author").change(function(){
				let newAuthor = $("#author").val();
				if(newAuthor) {
					console.log("Selected " + newAuthor);
					setSearchParam("forfattare", newAuthor);
				} else {
					delSearchParam("forfattare");
				}
				let page = url.searchParams.get("sida");
				if(page) {
					setSearchParam("sida", 1);
				}
				setUrlParams();
			});

			function updateTitle() {
				let title = "Visar alla artiklar";
				if($("#author").val()) {
					title += " skrivna av " + $("#author").children("option").filter(":selected").text();
				}

				if($("#category").val()) {
					title += " i kategorin " + $("#category").children("option").filter(":selected").text();
				}

				title+= " "+$('#pageCount').text().toLowerCase();
				//console.log(title);
				
				document.title = title;
				$("#searchTitle").html(title);
			}

			// I might need to change it later to support IE.
			function setSearchParam(name, value) {
				url.searchParams.set(name, value);
			}

			function delSearchParam(name) {
				url.searchParams.delete(name);
			}

			function setCategory(category) {
				$("#category").val(category);
				$("#category").change();
			}

			function setAuthor(id) {
				setSearchParam("forfattare", id);
				setUrlParams();
			}

			// Apply url params and reload
			function setUrlParams() {
				window.history.replaceState({}, '', location.pathname + '?' + url.searchParams);
				location.reload();
			}

			updateTitle();
		</script>
	</body>
</html>