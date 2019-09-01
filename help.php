<!--
*
*	Страница справочных материалов
*
 -->
<!DOCTYPE html>
<?php
	require_once("modules/data.config.php");
	require_once("modules/sql.php");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo CONFIG["TITLE"]; ?> | Справка</title>
	<link href="style/style.css" rel="stylesheet">
	<link href="style/fonts.css" rel="stylesheet">
	<script src="js/jquery-1.12.0.min.js"></script>
	<!-- Начало: Скрипт проверки, является ли брацзер IE -->
	<script type="text/javascript">
		function getInternetExplorerVersion() {
			var rv = -1;
			if (navigator.appName == 'Microsoft Internet Explorer') {
				var ua = navigator.userAgent;
				var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
				if (re.exec(ua) != null)
					rv = parseFloat( RegExp.$1 );
			} else if (navigator.appName == 'Netscape') {
				var ua = navigator.userAgent;
				var re  = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
				if (re.exec(ua) != null)
					rv = parseFloat( RegExp.$1 );
			}
			return rv;
		}
	</script>
	<!-- Конец: Скрипт проверки, является ли брацзер IE -->
</head>
<body>
	<div id="page-new">
		<div id="wrapper">
			<!-- Начало: Главное меню -->
			<div class="navbar">
				<div class="logo"><a href="/"><img id="logotype" src="images/logo_150.png" title="ВгТЗ" /></a></div>
				<div class="topmenu">
					<ul class="menu-list">
						<li class="menu-item"><a id="menu-1" class="nava" href="/"><span class="glyphicon glyphicon-edit"></span>Заявка</a></li>
						<li class="menu-item current"><a id="menu-2" class="nava current" href="/help.php"><span class="glyphicon glyphicon-question-sign"></span>Справочник</a></li>
                        <li class="menu-item"><a id="menu-3" class="nava" href="/move.php"><span class="glyphicon glyphicon-arrow-right"></span>Перемещение</a></li>
                        <li class="menu-item"><a id="menu-4" class="nava" href="/location.php"><span class="glyphicon glyphicon-globe"></span>Рабочие места</a></li>
					</ul>
				</div>
				<div class="navbar-text navbar-right"><?php echo CONFIG["ORG"]; ?></div>
			</div>
			<!-- Конец: Главное меню -->
			<!-- Начало: основной контент страницы -->
			<div class="panel panel-default center" style="width: 98%">
				<div id="page-2">
					<div class="panel-heading bold">Справочные материалы</div>
					<div class="panel-body"><div class="panel-wrapper">
						<!-- Начало: вывод таблицы справочных материалов -->
						<?php
						$sql = new sql;
						$h = $sql->dataSQL("getHelpDocs");
						if ($h)
							if (sizeOf($h)>0) {
								$tr = "<table class=\"table\"><thead></th><th>Документ</th><th>Файл</th></tr></thead><tbody>";
								$te = "";
								for ( $i=0; $i<sizeOf($h); $i++ ) {
									$fl = $_SERVER['DOCUMENT_ROOT'] . PATH["HELP"] . $h[$i]["file"];
									if ( file_exists($fl) ) {
										$descr = ( strlen($h[$i]["description"]) > 0 ) ? "<p class=\"tbl-doc-desc\">".$h[$i]["description"]."</p>" : "";
										$link = "<a href=\"".CONFIG["HOST"].PATH["HELP"].$h[$i]["file"]."\" role=\"button\" target=\"_blank\" download>".$h[$i]["file"]."</a>";
										$te .= "<tr><td><p class=\"tbl-doc-title\">".$h[$i]["title"]."</p>".$descr."</td><td>".$link."</td></tr>";
									}
								}
								$tr .= $te . "</body></table>";
								echo $tr;
							} else {
								echo '<div class="alert alert-warning center" role="alert">В Справочник пока еще не добавлено ни одного документа.</div>';
							}
						?>
						<!-- Конец: вывод таблицы справочных материалов -->
					</div></div>
				</div>
			</div>
			<!-- Конец: основной контент страницы -->
		</div>
	</div>
<!-- Начало: Вывод ошибки, если брацзер - IE -->
<script>
$(document).ready(function() {
	if(getInternetExplorerVersion()!==-1){
		$('#wrapper').html('<div class="alert alert-danger center" role="alert">Internet Explorer не предназначен для работы в системе Единой заявки. Рекомендуемые браузеры: Mozilla Firefox, Google Chrome, Opera.</div>');
	}
});
</script>
<!-- Конец: Вывод ошибки, если брацзер - IE -->
</body>
</html>