<?php
// формирование строк с SQL-запросами для скрипта sql.php
require_once("data.config.php");
class db {
	const	host = SQL["HOST"];
	const	base = SQL["BASE"];
	const	user = SQL["USER"];
	const	password = SQL["PASSWORD"];

	// список Бизнес-единиц
	public static function getCompanyList($data, $v1) {
		return "SELECT id, company FROM company ORDER BY company";
	}
	const	errgetCompanyList	=	"ERROR: getCompanyList";
		
	// список Подразделений
	public static function getDivisionList($data, $v1) {
		return "SELECT id, division FROM division WHERE company='$data' ORDER BY division";
	}
	const	errgetDivisionList	=	"ERROR: getDivisionList";
	
	// список Должностей
	public static function getFunctionList($data, $v1) {
		return "SELECT id, func FROM function WHERE ( FIND_IN_SET('$data', division)>0 ) ORDER BY func";
	}
	const	errgetFunctionList	=	"ERROR: getFunctionList";
			
	// "владелец ресурса" по его id
	public static function getOwnerNameById($data, $v1) {
		return "SELECT u.name, f.func FROM user AS u LEFT JOIN function AS f ON f.id=u.function WHERE (u.id='$data')";
	}
	const errgetOwnerNameById	=	"ERROR: getOwnerNameById";
	
	// "владелец ресурса"
	public static function getServiceOwner($data, $v1) {
		return "SELECT u.id, u.name, s.form AS form_org FROM user AS u, service AS s WHERE (u.id=(SELECT id_own FROM service WHERE id='$data') AND (s.id='$data'))";
	}
	const errgetServiceOwner	=	"ERROR: getServiceOwner";
	
	// "владелец информации"
	public static function getServiceVizier($data, $v1) {
		return "SELECT u.id, u.name FROM user AS u, service AS s WHERE (u.id=(SELECT id_vise FROM service WHERE id='$data') AND (s.id='$data'))";
	}
	const errgetServiceVizier	=	"ERROR: getServiceVizier";

	// ОУОП-каталог по id
	public static function getExt1ById($data, $v1) {
		return "SELECT e.name, r.name AS rw FROM ext1 AS e LEFT JOIN role AS r ON e.id_role=r.id WHERE e.id='$data'";
	}
	const errgetExt1ById	=	"ERROR: getExt1ById";
	
	// ИТ-роль по id
	public static function getRoleById($data, $v1) {
		return "SELECT name, id_ext1 FROM role WHERE id='$data'";
	}
	const errgetRoleById	=	"ERROR: getRoleById";
	
	// данные пользователя по ФИО
	public static function getUserName($data, $v1) {
		return "SELECT u.id, u.name, c.company, f.func, d.division FROM user AS u LEFT JOIN company AS c ON c.id=u.company LEFT JOIN function AS f ON f.id=u.function LEFT JOIN division AS d ON d.id=u.division WHERE ((u.name LIKE '$data%') AND (u.enabled='1')) ORDER BY name";
	}
	const errgetUserName	=	"ERROR: getUserName";
	
	// ИТ-ресурс по id
	public static function getResourceById($data, $v1) {
		return "SELECT name FROM resource WHERE id='$data'";
	}
	const errgetResourceById	=	"ERROR: getResourceById";
	
	// ИТ-услуга по id
	public static function getServiceById($data, $v1) {
		return "SELECT name FROM service WHERE id='$data'";
	}
	const errgetServiceById	=	"ERROR: getServiceById";
	
	// ИТ-услуги в зависимости от ИТ-ресурса
	public static function getServiceFromResource($data, $v1) {
		$v = json_decode($v1, true, 3);
		$v1 = $v["v1"];
		$v2 = $v["v2"];
		if ( $v2 === "-1" ) {
			$w = "";
		} else {
			$w = "AND (form='$v2')";
		}
		return "SELECT id, name, role_list_type FROM service WHERE ( (id_res='$data') AND (enabled='1') $w ) ORDER BY name";
	}
	const errgetServiceFromResource	=	"ERROR: getServiceFromResource";
	
	// ИТ-роль в зависимости от ИТ-услуги
	public static function getRoleFromService($data, $v1) {
		if ( $data ==="a" ) {
			return "SELECT id, func AS name FROM function WHERE (visible='1') ORDER BY func";
		} else if ( $data === "b" ) {
			return "SELECT id, division AS name FROM division_osp WHERE (visible='1') ORDER BY division";
		} else
			return "SELECT id, name FROM role WHERE ((FIND_IN_SET('$data',id_srv)>0) AND (visible='1')) ORDER BY name";
	}
	const errgetRoleFromService	=	"ERROR: getRoleFromService";

	// директор СЭБ по id
	public static function getUserNameById($data, $v1) {
		return "SELECT name AS chief FROM user WHERE (id='$data')";
	}
	const errgetUserNameById	=	"ERROR: getUserNameById";
	
	// справочная документация
	public static function getHelpDocs($data, $v1) {
		return "SELECT * FROM helpdocs ORDER BY title";
	}
	const errgetHelpDocs	=	"ERROR: getHelpDocs";

	// бланки на перемещение ИТ-активов
	public static function getMoveDocs($data, $v1) {
		return "SELECT * FROM movedocs ORDER BY title";
	}
	const errgetMoveDocs	=	"ERROR: getMoveDocs";
	
	// данные пользователя по id
	public static function getUserInfo($data, $v1) {
		return "SELECT name, company, division, function AS func, email, phone, tnumber, location FROM user WHERE id='$data' ORDER BY name";
	}
	const errgetUserInfo	=	"ERROR: getUserInfo";
	
	// добавление заявки
	public static function newTicket($data, $v1) {
		$tickets = json_decode($data, true, 2, JSON_BIGINT_AS_STRING);
		$req = "INSERT INTO ticket (u_name, u_company, u_division, u_func, u_phone, u_tnumber, u_location, resource, service, role, switch0, start, vise_ess, vise_own, vise_vise, form_org, comment) VALUES ";
		$t = "('".self::mysql_safe($tickets['name'])."', '".self::mysql_safe($tickets['company'])."', '".self::mysql_safe($tickets['division'])."', '".self::mysql_safe($tickets['func'])."', '".self::mysql_safe($tickets['phone'])."', '".self::mysql_safe($tickets['tnumber'])."', '".self::mysql_safe($tickets['location'])."', '".self::mysql_safe($tickets['resource'])."', '".self::mysql_safe($tickets['service'])."', '".self::mysql_safe($tickets['role'])."', '".self::mysql_safe($tickets['switch0'])."', '".date('Y-m-d')."', '".self::mysql_safe($tickets['ess_name'])."', '".self::mysql_safe($tickets['own_name'])."', '".self::mysql_safe($tickets['vise_name'])."', '".self::mysql_safe($tickets['form_org'])."', '".self::mysql_safe(htmlspecialchars($tickets['comment']))."')";
		$req = $req . $t;
		return $req;
	}
	const errnewTicket	=	"ERROR: errNewTicket";

    // добавление нового пользователя
    public static function addUser($data, $v1) {
        $new = json_decode($data, true, 2, JSON_BIGINT_AS_STRING);
        return "INSERT INTO user (name, company, division, function, email, phone, location, tnumber) VALUES ('".self::mysql_safe($new['name'])."', '".$new['company']."', '".$new['division']."', '".$new['func']."', '".self::mysql_safe($new['email'])."', '".self::mysql_safe($new['phone'])."', '".self::mysql_safe($new['location'])."', '".self::mysql_safe($new['tnumber'])."')";
    }
    const erraddUser	=	"ERROR: addUser";
	
	// информация о заявке
	public static function getTicket($data, $v1) {
		return "SELECT t.id, t.u_name, c.company AS u_company, d.division AS u_division, f.func AS u_func, t.u_phone, t.resource, t.service, t.role, t.switch0, t.start, t.vise_ess, t.vise_own, t.vise_vise, t.form_org, t.comment FROM ticket AS t LEFT JOIN company AS c ON c.id=t.u_company LEFT JOIN division AS d ON d.id=t.u_division LEFT JOIN function AS f ON f.id=t.u_func WHERE t.id='$data'";
	}
	const errgetTicket	=	"ERROR: getTicket";
	
	// список Ресурсов
	public static function getResourceList($data, $v1) {
		if ( $data === "-1" ) {
			$w = "";
		} else {
			$w = "WHERE ( FIND_IN_SET('$data',form)>0 )";
		}
		return "SELECT * FROM resource $w ORDER BY name";
	}
	const	errgetResourceList = "ERROR: getResourceList";

	// удаление запрещенных символов из строк
	private static function mysql_safe($data) {
		return mb_ereg_replace('[\x00\x0A\x0D\x1A\x22\x27\x5C]', '\\\0', $data );
	}
}
?>