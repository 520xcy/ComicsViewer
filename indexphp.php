<?php
require_once './config.inc.php';
session_start();
$_token = md5(crypt(date('Y-m-d H:m:s'). session_id()));
$_SESSION['_token'] = $_token;
?>
<!DOCTYPE html>

<head>
	<!--==============手机端适应============-->
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
	<!--===================================-->
	<meta name="description" content="">
	<meta name="author" content="">
	<!--==============强制双核浏览器使用谷歌内核============-->
	<meta name="renderer" content="webkit" />
	<meta name="force-rendering" content="webkit" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="format-detection" content="telephone=no">
	<meta name="referrer" content="never">
	<title>ComicViewer v1.0.0-SNAPSHOT</title>
	<link rel="icon" href="h/img/favicon.ico">
	<link href="h/css/black-t.css" rel="stylesheet">
	<script type="text/javascript" src="h/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="h/js/jquery.lazyload.min.js"></script>
	<style>
		a {
			color: #fff;
			text-decoration: none;
			outline: 0
		}

		.index-nav {
			display: none;
			position: fixed;
			width: 60px;
			z-index: 10010;
			text-align: center;
			background: #111111;
			-webkit-touch-callout: none;
			-webkit-user-select: none;
			-khtml-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;

		}

		.name {
			color: #fff;
		}


		.index-nav .pointer-block {
			position: absolute;
			width: 60px;
			height: 30px;
			background: rgba(255, 255, 255, 0.1);
			z-index: -1;
			transition: .1s all linear;
			display: none;
		}


		.index-nav.customizing .tip {
			top: 0;
			opacity: 1;
		}

		.index-nav .nav-list {
			position: relative;
			z-index: 233;
			width: 60px;
		}

		.index-nav .n-i {
			border-bottom: 1px solid #414141;
			cursor: pointer;
		}



		.index-nav .n-i .name {
			line-height: 30px;
			width: 60px;
			font-size: 12px;
			text-align: center;
			height: 30px;
			transition: .1s background linear;
		}

		.file_list li .date {
			position: relative;
			padding: 1px 0 0;
			color: #fff;
			font-size: 12px;
			text-align: right
		}
	</style>
</head>

<body class="page page-id-15 page-template page-template-list_tag-php">

	<div id="container">
		<div id="contents">
			<section class="leftbox l1">

				<ul class="file_list">
					<?php
					$m = new Model();
					$result = $m->fetchAll('files', '*', '', 'title');
					foreach ($result as $row) {
					?>
						<li><a href="<?php echo $row['path'] ?>/detail.html" target="_blank" title="1">
								<h2><?php echo $row['title'] ?></h2>
								<div class="image"><img class="lazy" src="h/img/loading.gif" data-original="<?php echo $row['path'] . '/' . $row['pic'] ?>">
									<table class="data">
										<tr>
											<th scope="row">枚数</th>
											<td><?php echo $row['count'] ?>枚</td>
										</tr>
										<tr>
											<td class="tag" colspan="2"><span>1</span></td>
										</tr>
									</table>
								</div>
							</a>
							<p class="date"><?php echo $row['created_at'] ?>&nbsp;<a href="javascript:if(confirm('确实要删除吗?'))del('<?php echo $row['id'] ?>')">删</a></p>

						</li>
					<?php } ?>
				</ul>
			</section>
		</div>
	</div>
	<footer>
		<div class="box">
			<p id="copyright">
				<span class="copyright">
					<a href="https://github.com/masazumi-github/ComicsViewer" target="_blank">See at Github</a>
				</span>
		</div>
	</footer>
</body>
<script type="text/javascript" charset="utf-8">
	$(function() {
		$("img").lazyload({
			placeholder: "h/img/loading.gif",
			effect: "fadeIn"
		});
	});
</script>
<script>
	function del(path) {

		var dlform = document.createElement('form');
		dlform.style = "display:none;";
		dlform.method = 'post';
		dlform.action = 'action.php';
		dlform.target = '_blank';
		var action = document.createElement('input');
		action.type = 'hidden';
		action.name = 'action';
		action.value = 'remove';
		dlform.appendChild(action);
		var del_id = document.createElement('input');
		del_id.type = 'hidden';
		del_id.name = 'id';
		del_id.value = path;
		dlform.appendChild(del_id);
		var token = document.createElement('input');
		token.type = 'hidden';
		token.name = '_token';
		token.value = '<?php echo $_token ?>';
		dlform.appendChild(token);
		document.body.appendChild(dlform);
		dlform.submit();
		document.body.removeChild(dlform);
	}
</script>

</html>