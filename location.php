<!--
*
*	Страница схем корпусов
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
	<title><?php echo CONFIG["TITLE"]; ?> | Схема рабочих мест по корпусам</title>
	<link href="style/style.css" rel="stylesheet">
	<link href="style/fonts.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="modules/location/css/lightgallery.min.css" />
    <link type="text/css" rel="stylesheet" href="modules/location/css/lg-transitions.min.css" />
    <link type="text/css" rel="stylesheet" href="modules/location/css/custom.css" />
	<script src="js/jquery-1.12.0.min.js"></script>
    <script src="modules/location/js/lightgallery.min.js"></script>
    <script src="modules/location/js/lg-fullscreen.min.js"></script>
    <script src="modules/location/js/lg-thumbnail.min.js"></script>
    <script src="modules/location/js/lg-zoom.min.js"></script>
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
						<li class="menu-item"><a id="menu-2" class="nava" href="/help.php"><span class="glyphicon glyphicon-question-sign"></span>Справочник</a></li>
						<li class="menu-item"><a id="menu-3" class="nava" href="/move.php"><span class="glyphicon glyphicon-arrow-right"></span>Перемещение</a></li>
						<li class="menu-item current"><a id="menu-4" class="nava current" href="/location.php"><span class="glyphicon glyphicon-globe"></span>Рабочие места</a></li>
					</ul>
				</div>
				<div class="navbar-text navbar-right"><?php echo CONFIG["ORG"]; ?></div>
			</div>
			<!-- Конец: Главное меню -->
			<!-- Начало: основной контент страницы -->
			<div class="panel panel-default center" style="width: 98%">
				<div id="page-2">
					<div class="panel-heading bold">Нумерация кабинетов в корпусах и цехах</div>
					<div class="panel-body">
                        <div class="panel-wrapper">
							<!-- Начало: Галерея сосхемами -->
                            <div class="gallery">
                                <ul id="lightgallery" class="">
                                    <li class="col-xs-6" data-sub-html="<h4>Корпус МК-2 (A), Администрация</h4>" data-src="modules/location/img/a.png">
                                        <a class="th-link" href="">Корпус МК-2,<br>Администрация</a>
                                    </li>
                                    <li class="col-xs-6" data-sub-html="<h4>Корпус МК-2 (A), 1 этаж</h4>" data-src="modules/location/img/a-1.png">
                                        <a class="th-link" href="">Корпус МК-2,<br>1 этаж</a>
                                    </li>
                                    <li class="col-xs-6" data-sub-html="<h4>Корпус МК-2 (A), 2 этаж</h4>" data-src="modules/location/img/a-2.png">
                                        <a class="th-link" href="">Корпус МК-2,<br>2 этаж</a>
                                    </li>
                                    <li class="col-xs-6" data-sub-html="<h4>Корпус МК-2 (A), 3 этаж</h4>" data-src="modules/location/img/a-3.png">
                                        <a class="th-link" href="">Корпус МК-2,<br>3 этаж</a>
                                    </li>
                                    <li class="col-xs-6" data-sub-html="<h4>Корпус МК-2 (A), 4 этаж</h4>" data-src="modules/location/img/a-4.png">
                                        <a class="th-link" href="">Корпус МК-2,<br>4 этаж</a>
                                    </li>
									<li class="col-xs-6" data-sub-html="<h4>Корпус МК-1, МСП (B), 1 этаж</h4>" data-src="modules/location/img/b-1.png">
                                        <a class="th-link" href="">Корпус МК-1, МСП<br>1 этаж</a>
                                    </li>
									<li class="col-xs-6" data-sub-html="<h4>Корпус МК-1, МСП (B), 2 этаж</h4>" data-src="modules/location/img/b-2.png">
                                        <a class="th-link" href="">Корпус МК-1, МСП<br>2 этаж</a>
                                    </li>
									<li class="col-xs-6" data-sub-html="<h4>Корпус МК-1, МСП (B), 3 этаж</h4>" data-src="modules/location/img/b-3.png">
                                        <a class="th-link" href="">Корпус МК-1, МСП<br>3 этаж</a>
                                    </li>
									<li class="col-xs-6" data-sub-html="<h4>ЗУ и Цех 6 (C), 2 этаж</h4>" data-src="modules/location/img/c6-2.png">
                                        <a class="th-link" href="">ЗУ и Цех 6,<br>2 этаж</a>
                                    </li>
                                    <li class="col-xs-6" data-sub-html="<h4>ЗУ и Цех 6 (C), 3 этаж</h4>" data-src="modules/location/img/c6-3.png">
                                        <a class="th-link" href="">ЗУ и Цех 6,<br>3 этаж</a>
                                    </li>
									<li class="col-xs-6" data-sub-html="<h4>Производство метизов (C), 1 этаж</h4>" data-src="modules/location/img/pm-1.png">
                                        <a class="th-link" href="">Пр-во Метизов,<br>1 этаж</a>
                                    </li>
                                    <li class="col-xs-6" data-sub-html="<h4>Производство метизов (C), 3 этаж</h4>" data-src="modules/location/img/pm-3.png">
                                        <a class="th-link" href="">Пр-во Метизов,<br>3 этаж</a>
                                    </li>
                                    <li class="col-xs-6" data-sub-html="<h4>Экспериментальный цех (D)</h4>" data-src="modules/location/img/d.png">
                                        <a class="th-link" href=""> Экспериментальный цех</a>
                                    </li>
                                </ul>
                            </div>
							<!-- Конец: Галерея сосхемами -->
                        </div>
                    </div>
				</div>
			</div>
			<!-- Конец: основной контент страницы -->
		</div>
    </div>
<!-- Начало: Инициализация галереи со схемами -->
<script type="text/javascript">
    lightGallery(document.getElementById('lightgallery'), {
        thumbnail: false
    });
</script>
<!-- Конец: инициализация галереи со схемами -->
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