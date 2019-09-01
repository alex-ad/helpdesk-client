<!--
*
*	Страница подачи заявки
*
 -->
<!DOCTYPE html>
<?php require_once("modules/data.config.php"); ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo CONFIG["TITLE"]; ?></title>
	<link href="select2/select2.css" rel="stylesheet">
	<link href="style/jquery-ui.min.css" rel="stylesheet">
	<link href="style/jquery-ui.structure.min.css" rel="stylesheet">
	<link href="style/jquery-ui.theme.min.css" rel="stylesheet">
	<link href="style/style.css?ver=1" rel="stylesheet">
	<link href="style/fonts.css" rel="stylesheet">
	<script src="js/jquery-1.12.0.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="select2/select2.full.js"></script>
	<script type="text/javascript" src="select2/i18n/ru.js"></script>
	<script type="text/javascript" src="select2/select2.multi-checkboxes.js"></script>
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
						<li class="menu-item current"><a id="menu-1" class="nava current" href="/"><span class="glyphicon glyphicon-edit"></span>Заявка</a></li>
						<li class="menu-item"><a id="menu-2" class="nava" href="/help.php"><span class="glyphicon glyphicon-question-sign"></span>Справочник</a></li>
                        <li class="menu-item"><a id="menu-3" class="nava" href="/move.php"><span class="glyphicon glyphicon-arrow-right"></span>Перемещение</a></li>
                        <li class="menu-item"><a id="menu-4" class="nava" href="/location.php"><span class="glyphicon glyphicon-globe"></span>Рабочие места</a></li>
					</ul>
				</div>
				<div class="navbar-text navbar-right"><?php echo CONFIG["ORG"]; ?></div>
			</div>
			<!-- Конец: Главное меню -->
			<!-- Начало: основной контент страницы -->
			<div class="panel panel-default center" style="width: 98%">
				<div id="page-1">
					<div class="panel-heading bold">Добавление заявки на подключение ИТ-ресурсов</div><!-- Оглавление панели -->
					<div class="panel-body"><div class="panel-wrapper">
						<div class="alert alert-warning center" role="alert"><label><input type="checkbox" name="term_agree" id="term_agree" style="margin-right:7px;"/>С <a href="<?php echo CONFIG['HOST'].PATH['HELP']; ?>Приказ 209.pdf" role="button" target="_blank" download>положением о коммерческой тайне</a> ознакомлен</label></div>
						<!-- Начало: форма ввода данных -->
						<div id="page-print" class="hidden"><form action="" method="post" id="form-new" name="form-new" onsubmit="submitNewTicket();return false;">
							<h3><span style="color: green"><span style="font-weight:normal">Шаг 1: </span>Укажите свои данные</span></h3>
							<!-- Начало: ввод данных пользователя -->
							<ul id="user-data" class="list-group">
								<li class="list-group-item small"><div class="form-item"><div class="label">Ф.И.О. Пользователя </div><input autofocus required id="new_fullname" class="form-input" style="width:350px" type="text" name="new_fullname" placeholder="(обязательно для заполнения)"/><input type="hidden" id="new_fullname-id" value=""/></div><div class="clr"></div></li>
								<li class="list-group-item small">
									<div class="form-item"><div class="label">Организация </div>
										<select class="new_company" size="1" name="new_company" required>
											<option></option>
											<?php
											require_once('modules/sql.php');
											require_once('modules/functions.php');
											$sql = new sql;
											$c = $sql->dataSQL("getCompanyList");
											for ( $i=0; $i<sizeOf($c); $i++ ) {
												echo '<option value="' . $c[$i]['id'] . '">' . $c[$i]['company'] . '</option>';
											}
											?>
										</select>
									</div>
									<div class="clr"></div>
								</li>
								</li>
								<li class="list-group-item small">
									<div class="form-item"><div class="label">Подразделение (*)</div>
										<select class="new_division" size="1" name="new_division" required>
											<option></option>
										</select>
									</div>
									<div class="clr"></div>
								</li>
								<li class="list-group-item small">
									<div class="form-item"><div class="label">Должность (*)</div>
										<select class="new_function" size="1" name="new_function" required>
											<option></option>
										</select>
									</div>
									<div class="clr"></div>
								</li>
								<li class="list-group-item small"><div class="form-item"><div class="label">Телефон</div><input required id="new_phone" class="form-input" style="width:350px" type="text" name="new_phone" placeholder="(обязательно для заполнения)"/></div><div class="clr"></div></li>
								<li class="list-group-item small"><div class="form-item"><div class="label">E-mail</div><input id="new_email" class="form-input" style="width:350px" type="text" name="new_email" placeholder="(если есть)"/></div><div class="clr"></div></li>
								<li class="list-group-item small"><div class="form-item"><div class="label">Табельный номер</div><input id="new_tnumber" class="form-input" style="width:350px" type="text" name="new_tnumber" placeholder="(если есть)"/></div><div class="clr"></div></li>
								<span id="idlocation" class="hidden"><li class="list-group-item small"><div class="form-item"><div class="label">Рабочее место (корпус/кабинет)</div><input id="new_location" class="form-input" style="width:350px" type="text" name="new_location" placeholder="(обязательно для заполнения)"/></div><div class="clr"></div></li></span>
							</ul>
							<!-- Конец: ввод данных пользователя -->
							<div id="aster-ootiz">* Список должностей сформирован на основании данных ООТиЗ</div>
							<!-- Начало: выбор ИТ-ресурсов, услуг, ролей -->
							<div id="bl-2" class="hidden">
								<br />
								<h3><span style="color: green"><span style="font-weight:normal">Шаг 2: </span>Выберите услуги, которые нужно включить или отключить</span></h3>
								<div id="alert_new_user"><div class="alert alert-info center" role="alert" ><label><input type="checkbox" id="new_newuser" name="new_newuser" style="margin-right:7px;"><strong>Новый пользователь</strong> (Отметьте, ЕСЛИ заявка подается от имени нового сотрудника, которому нужно настроить и предоставить рабочее место)</label></div><br /></div>
								<div><table class="table">
									<thead>
										<tr>
											<th>Вид ИТ-ресурса</th>
											<th>ИТ-услуга</th>
											<th>Роли доступа</th>
											<th>Подключить/Отключить</th>
											<th></th>
										</tr>
									</thead>
									<tbody id="new_tickets">
										<tr class="tck_1">
											<td>
												<select size="1" name="new_resource" class="new_resource" onchange="changeResource(1)">
												</select>
											</td>
											<td>
												<select size="1" name="new_service" class="new_service" onchange="changeService(1)">
												</select>
											</td>
											<td>
												<select size="1" name="new_role" class="new_role" onchange="changeRole(1)">
												</select>
											</td>
											<td>
												<input type="radio" class="switcher" name="new_act_1" value="1" checked/>Подключить<br />
												<input type="radio" class="switcher" name="new_act_1" value="0" />Отключить
												<input type="hidden" name="new_own_name" class="new_own_name" value=""/>
												<input type="hidden" name="new_vise_name" class="new_vise_name" value=""/>
												<input type="hidden" name="new_res_id" class="new_res_id" value=""/>
												<input type="hidden" name="new_srv_id" class="new_srv_id" value=""/>
												<input type="hidden" name="new_role_id" class="new_role_id" value=""/>
												<input type="hidden" name="new_own_id" class="new_own_id" value=""/>
												<input type="hidden" name="new_vise_id" class="new_vise_id" value=""/>
												<input type="hidden" name="new_form" class="new_form" value=""/>
											</td>
											<td>
												<div class="hidden"><input type="button" class="btn btn-warning btn-narrow btn-xs" id="btnNewDelete" name="btnNewDelete" value="Удалить" onclick="clickDeleteService(1);return false;" /></div>
											</td>
										</tr>
									</tbody>
								</table></div>
								<input type="button" class="btn btn-success btn-xs" id="btnNewAdd" name="btnNewAdd" value="Добавить услугу" />
								<br /><br />
								<p><strong>Ваши комментарии и замечания к любой услуге:</strong></p>
								<p><textarea id="new_comment" class="form-control form-control-textarea" rows="5" name="new_comment" placeholder="Максимум 4000 символов"></textarea></p>
								<div id="tck-id" style="display:none">1</div>
								<div id="vise-div" class="hidden"><div id="vise-div-name"></div><div id="vise-div-id" style="display:none"></div></div>
								<div id="vise-sec" class="hidden">
									<div id="vise-sec-0-name"><?php $ess = $sql->dataSQL("getUserNameById","102"); echo ((isset($ess[0]["chief"])) ? $ess[0]["chief"] : "___________________"); ?></div>
									<div id="vise-sec-0-id" style="display:none">102</div>
								</div>
								<br />
								<div id="bl-3" class="">
									<h3><span style="color: green"><span style="font-weight:normal">Шаг 3: </span>Сформируйте бланк заявки и сохраните его себе на компьютер</span></h3>
									<div id="ticket-vise-button"><input type="submit" class="btn btn-primary" name="btnNewVise" id="btnNewVise" style= "width:232px" value="Сформировать бланк заявки"/></div>
								</div>
							</div>
							<!-- Конец: выбор ИТ-ресурсов, услуг, ролей -->
						</form></div>
						<!-- Конец: форма ввода данных -->
					</div></div>
				</div>
				
				<div id="page-2" class="hidden">
					<div class="panel-heading bold">Заявк на предоставление ИТ-услуг сформирована</div>
					<div class="panel-body"></div>
				</div>
			</div>
			<!-- Конец: основной контент страницы -->
		</div>
	</div>

<script>
$(document).ready(function() {
	<!-- Начало: Вывод ошибки, если брацзер - IE -->
	if(getInternetExplorerVersion()!==-1){
		$('#wrapper').html('<div class="alert alert-danger center" role="alert">Internet Explorer не предназначен для работы в системе Единой заявки. Рекомендуемые браузеры: Mozilla Firefox, Google Chrome, Opera.</div>');
	}
	<!-- Конец: Вывод ошибки, если брацзер - IE -->
	<!-- Начало: инициализация выпадающих списков -->
    $('.new_company').select2({
		language: "ru",
		placeholder: 'Выберите организацию'
	});
	$('.new_division').select2({
		language: "ru",
		placeholder: 'Выберите подразделение'
	});
	$('.new_function').select2({
		language: "ru",
		placeholder: 'Выберите должность'
	});
	$('.new_resource').select2({
		language: "ru",
		placeholder: 'Выберите ресурс'
	});
	$('.new_service').select2({
		language: "ru",
		placeholder: 'Выберите услугу'
	});
	$('.new_role').select2({
		language: "ru",
		placeholder: 'Выберите роль',
		width: '350px'
	});
	<!-- Конец: инициализация выпадающих списков -->
});
</script>
<script type="text/javascript" src="/js/script.js?ver=1.1"></script>
</body>
</html>