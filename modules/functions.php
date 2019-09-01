<?php
require_once("data.config.php");

// функция создания бланка pdf
function createPDF($data, $v1) {
	require_once("sql.php");
	date_default_timezone_set("Europe/Moscow");
	$sql = new sql;
	$hostroot = $_SERVER['DOCUMENT_ROOT'];
	$pdfcreator = PATH["PDF"];
	$descriptorspec = Array(
		0 => Array('pipe', 'r'),
		1 => Array('pipe', 'w'),
		2 => Array('pipe', 'w')
	);
	
	// получение данных о заявке
	$tck = json_decode($data, true, 2, JSON_BIGINT_AS_STRING);
	$v1 = json_decode($v1, true, 2, JSON_BIGINT_AS_STRING);
	$doc = $sql->dataSQL("getTicket", $v1["id"]);
			
	// определение Бизнес-единицы
	if ( mb_strpos(mb_strtolower($doc[0]["u_company"]), "вгтз") !== false ) {
		$org = "vmk";
		$orgname = "ООО «ВМК «ВгТЗ»";
		$h = "35mm";
	} else if ( mb_strpos(mb_strtolower($doc[0]["u_company"]), "осп") !== false ) {
		$org = "osp";
		$orgname = "ОСП «ВМК КМЗ» в г. Волгограде";
		$h = "45mm";
	} else {
		$org = "kmz";
		$orgname = "ОАО «КМЗ»";
		$h = "30mm";
	}
	
	// определение путей хранения и записи файлов
	$month = date("Y-m");
	$folder = PATH["TICKET"] . $month;
	$pdf = "ticket_" . $v1["id"] . ".pdf";
	if (!file_exists($hostroot.$folder))
		@mkdir($hostroot.$folder, 0777, true);
	$flink = $folder . "/" . $pdf;
	$pdffullname = $hostroot . $folder . "/" . $pdf;
	if ( file_exists($pdffullname) ) {
		$pdffullname = $hostroot . $folder . "/ticket_" . $v1["id"] . "_" . date("YmdHis") . ".pdf";
		$flink = $folder . "/ticket_" . $v1["id"] . "_" . date("YmdHis") . ".pdf";
	}
	
	// список "владельцев ресурсов"
	$owner_list_ = array_map(function($elem){
		$sql = new sql;
		$olt = Array( "name"=>"", "func"=>"" );
		return ( strlen($elem)>0 ) ? $sql->dataSQL("getOwnerNameById", $elem)[0] : $olt;
	},explode("|",$doc[0]["vise_own"]));
	
	// список "сладельцев информации"
	$vizier_list_ = array_map(function($elem){
		$sql = new sql;
		$vlt = Array( "name"=>"", "func"=>"" );
		return ( strlen($elem)>0 ) ? $sql->dataSQL("getOwnerNameById", $elem)[0] : $vlt;
	},explode("|",$doc[0]["vise_vise"]));

	// директор СЭБ
	$vise_ess_ = ( strlen($doc[0]["vise_ess"])>0 ) ? $sql->dataSQL("getUserNameById", $doc[0]["vise_ess"])[0]["chief"] : "";
	
	// список Ресурсов
	$res = array_map(function($elem){
		$sql = new sql;
		return ( strlen($elem)>0 ) ? $sql->dataSQL("getResourceById", $elem)[0]["name"] : "";
	},explode("|",$doc[0]["resource"]));
		
	// список Услуг
	$srv = array_map(function($elem){
		if ( strlen($elem)>0 )
			return implode(", ",array_map(function($elem){
				$sql = new sql;
				if  ( strlen($elem)>0 )
					return ( strlen($elem)>0 ) ? $sql->dataSQL("getServiceById", $elem)[0]["name"] : "";
				else return "";
			},explode(",",$elem)));
		else return "";
	},explode("|",$doc[0]["service"]));
				
	// список Ролей
	$role = array_map(function($elem){
		if ( strlen($elem)>0 )
			return implode(", ",array_map(function($elem){
				$sql = new sql;
				if  ( strlen($elem)>0 )
					return ( strlen($elem)>0 ) ? $sql->dataSQL("getRoleById", $elem)[0]["name"] : "";
				else return "";
			},explode(",",$elem)));
		else return "";
	},explode("|",$doc[0]["role"]));

	// список ОУОП-каталогов
	$ouop = array_map(function($elem){
        if ( strlen($elem)>0 ) {
            $sql = new sql;
            $id_ext1 = $sql->dataSQL("getRoleById", $elem)[0]["id_ext1"];
            if ( $id_ext1 !== "0" ) {
                return array_map(function($elem){
                    $sql = new sql;
                    return ( strlen($elem)>0 ) ? $sql->dataSQL("getExt1ById", $elem) : "";
                },explode(",",$id_ext1));
            } else return "";
        } else return "";
    },explode("|",$doc[0]["role"]));

	// список состояний "отключить-подключить"
	$sw = array_map(function($elem){
		return ( $elem === "1" ) ? "Подключить" : "Отключить";
	},explode("|",$doc[0]["switch0"]));
	
	// данные пользователя
	$func = $doc[0]["u_func"];
	$div = $doc[0]["u_division"];
	$form = explode("|",$doc[0]["form_org"]);
	$vise_chief = Array();
    $location = ( strlen($tck["location"]) > 2 ) ? "Месторасположение: " . $tck["location"] . ". " : "";
	$comment = ( strlen($doc[0]["comment"]) > 0 ) ? $location . $doc[0]["comment"] : $location;
	$name1 = preg_replace('/([А-Яа-яёЁ]{2,})+\s+([А-Яа-яёЁ]{2,})+\s+([А-Яа-яёЁ]{2,})/u', '${1}', $tck["name"]);
	$name2 = preg_replace('/([А-Яа-яёЁ]{2,})+\s+([А-Яа-яёЁ]{2,})+\s+([А-Яа-яёЁ]{2,})/u', '${2}', $tck["name"]);
	$name3 = preg_replace('/([А-Яа-яёЁ]{2,})+\s+([А-Яа-яёЁ]{2,})+\s+([А-Яа-яёЁ]{2,})/u', '${3}', $tck["name"]);
	$phone = $tck["phone"];
	$tnumber = $tck["tnumber"];
	$vise_chief["name"] = "";
	$vise_chief["func"] = "Директор по направлению";
	$vise_chief["division"] = "_____________________________________";
	$br = "<br />";
	$chief_name = "<tr><td><br />".$br.$vise_chief["func"].$br.$vise_chief["division"]."</td><td><br />"." ____________ / ___________________</td></tr>";
	
	$item = 0;
	$owner_list = "";
	$vise_ess = "";
	if ( strlen($vise_ess_) > 0 ) {
		$vise_ess = convertInitials($vise_ess_);
	}
	$htable = Array(
		"0" => "",
		"1" => Array()
	);

	$isExtField = false;

	// формирование html-таблицы со списком Ресурсов, Услуг, Ролей, ОУОП-каталогов
	for ( $i=0; $i<sizeOf($res); $i++ ) {
		if ( $form[$i] === "1" ) {
			// заявка для курганской промплощадки, инициализация
			$sz = sizeOf($htable["1"]);
			$htable["1"][$sz]["u"] = "";
			$htable["1"][$sz]["r"] = "";
		}
		if ( $form[$i] === "0" ) {
			// заявка для волгоградской промплощадки
			$item = $item + 1;
			if ( strlen($owner_list_[$i]["name"]) > 7 )
				$owner_list .= "<tr><td><br />[п.".$item."] Владелец ресурса: ".$owner_list_[$i]["func"]."</td><td><br />"." ____________ ".convertInitials($owner_list_[$i]["name"])."</td></tr>";
			if ( strlen($vizier_list_[$i]["name"]) > 7 )
				$owner_list .= "<tr><td><br />[п.".$item."] Ответственный за ресурс: ".$vizier_list_[$i]["func"]."</td><td><br />"." ____________ ".convertInitials($vizier_list_[$i]["name"])."</td></tr>";
			
			// Ресурсы, Услуги, Роли
			$htable["0"] .= "<tr><td>" . $item . "</td>" .
			"<td>" . $res[$i] . "</td>" .
			"<td>" . $srv[$i] . "</td>" .
			"<td>" . $role[$i] . "</td>" .
			"<td>" . $sw[$i] . "</td>".
			"</tr>";

			// ОУОП-каталоги
			if ( is_array($ouop[$i]) )
			    if ( sizeOf($ouop[$i]) !== "0" ) {
                    $isExtField = true;
                    for ( $k=0; $k<sizeOf($ouop[$i]); $k++ ) {
                        $htable["0"] .= "<tr><td>" . "*" . $item . "." . ($k+1) . "</td>" .
                            "<td>" . $res[$i] . "</td>" .
                            "<td>" . $ouop[$i][$k][0]["name"] . "</td>" .
                            "<td>" . $ouop[$i][$k][0]["rw"] . "</td>" .
                            "<td>" . $sw[$i] . "</td>".
                            "</tr>";
                    }
                }
		} else {
			// заявка для курганской промплощадки
			// данные пользователя
			$htable["1"][$sz]["u"] .= "<tr><td>1</td>" .
			"<td>" . $name1 . "</td>" .
			"<td>" . $name2 . "</td>" .
			"<td>" . $name3 . "</td>" .
			"<td>" . $func . "</td>" .
			"<td>" . $div . "</td>" .
			"<td>" . $orgname . "</td>" .
			"<td>" . $tnumber . "</td>" .
			"<td>" . $phone . "</td></tr>";
			
			// Ресурсы, Услуги, Роли
			$comment_ = ( strlen($doc[0]["comment"]) > 0 ) ? " (".$doc[0]["comment"].")" : "";
			$htable["1"][$sz]["r"] .= "<tr><td>" . $res[$i] . " : " . $srv[$i] . "</td>" .
			"<td>" . $role[$i] . $comment_ . "</td>" .
			"<td>" . $sw[$i] . "</td></tr>";
		}
	}
	
	if ( strlen($htable["0"]) > 0 ) $htable["0"] .= "</tbody></table>";
	if ( sizeOf($htable["1"]) > 0 )
		for ( $j=0; $j<sizeOf($htable["1"]); $j++ ) {
			$htable["1"][$j]["u"] .= "</tbody></table>";
			$htable["1"][$j]["r"] .= "</tbody></table>";
		}

	if ( $isExtField ) {
        $htable["0"] .= "<br />*Примечание: данные ресурсы подкючаются автоматически при выборе ИТ-ресурса АС ОУОП";
    }
	
	// имя pdf-файла
	$tpl_pdf_head = $hostroot."/docs/templates/tck.".$org.".head.html";
	$sec = file_get_contents($hostroot.PATH["TPL"]."sec.html");
    $br = "<div style=\"page-break-after: always;\"></div>";
	$tpl_cont = "";

	// сборка заявки по шаблону из templates
	if ( strlen($htable["0"]) > 0 ) {
		$tpl_pdf_footer = "--footer-html ".$hostroot."/docs/templates/tck.footer.html";
		$tpl = file_get_contents($hostroot.PATH["TPL"]."tck.html");
		$num = $v1["id"] . " / " . $doc[0]["start"];
		$vise_block = $chief_name . $owner_list . "<tr><td><br />Директор по экономической безопасности</td><td><br />"." ____________ ".$vise_ess . "</td></tr>";
		$nume = "<br/>к заявке № $num";
		$tpl_cont .= sprintf($tpl, $num, $htable["0"], $comment, $func, $div, $tck["name"], $vise_block, $phone, $br) . sprintf($sec, $nume, $orgname, $orgname, $orgname);
	} else if ( sizeOf($htable["1"]) > 0 ) {
		$tpl = file_get_contents($hostroot.PATH["TPL"]."tck.1.html");
		$tpl_pdf_footer = "";
		for ( $j=0; $j<sizeOf($htable["1"]); $j++ ) {
			$fin = ( $j > 0 ) ? $br : "";
			$tpl_cont .= sprintf($tpl, $htable["1"][$j]["u"], $htable["1"][$j]["r"]) . $br . sprintf($sec, "", $orgname, $orgname, $orgname) . $fin;
		}
		
	}
	
	// запись заявки из памяти в файл
	$process = proc_open($pdfcreator." -L 10mm -R 10mm -B 10mm -T $h $tpl_pdf_footer --header-html $tpl_pdf_head - -", $descriptorspec, $pipes, null, null, ['bypass_shell' => true]);
	if (@is_resource($process)) {
		fwrite($pipes[0], $tpl_cont);
		fclose($pipes[0]);
		$stdOut = stream_get_contents($pipes[1]);
		$stdErr = stream_get_contents($pipes[2]);
		fclose($pipes[1]);
		fclose($pipes[2]);
		file_put_contents($pdffullname, $stdOut);
		$exitCode = proc_close($process);
	}
	
	// отправка оповещения пользователю
	/*if ( isEmailValid($tck["email"]) ) {
		sendEmail($tck["email"], $tck["name"], $month, $v1["id"]);
	} else {
		sendEmail("", $tck["name"], $month, $v1["id"]);
	}*/
	
	// обработка ошибок php
	if ( error_get_last() ) {
		$ret = Array(
			"error"	=> "PHP-ERROR",
			"msg"	=>	error_get_last()["message"] . " (" . error_get_last()["file"] . " :" . error_get_last()["line"] . ")"
		);
	// возврат ссылки на файл заявки в вызывающий скрипт
	} else {
		$ret = Array(
			"flink"	=>	$flink
		);
	};
	
	return $ret;
}

// конвертирование полного ФИО к имени и инициалам
function convertInitials($name) {
	if ( strlen($name)<5 ) return $name;
	mb_internal_encoding("UTF-8");
	$tmp = explode(" ", $name);
	if ( sizeOf($tmp) > 1 ) {
		$tmp[1] = ( isset($tmp[1]) ) ? ( " " . mb_substr($tmp[1], 0, 1) . "." ) : "";
		$tmp[2] = ( isset($tmp[2]) ) ? ( mb_substr($tmp[2], 0, 1) . "." ) : "";
		return $tmp[0] . $tmp[1] . $tmp[2];
	} else return $name;
	
}

// проверка валидности адреса электронной почты
function isEmailValid($email) {
	return ( ( strlen($email) > 3 ) and ( strpos($email,"@") !== false ) );
}

// отправка почты
function sendEmail( $email_to, $email_name, $email_path, $email_ticket ) {
	$subject = "Заявка ($email_name)";
	$message = "<html><head><title>Оповещение о заявке</title></head><body><p>Здравствуйте, $email_name</p><p>Ваша заявка № $email_ticket на подключение/отключение ИТ-ресурсов успешно создана.</p><p>Бланк заявки во вложении.</p><br /><p>---------</p><p>Это письмо сгенерировано автоматически роботом, не нужно на него отвечать</p><p>---------</p><p>ДИТ ОАО «КМЗ»</p></body></html>";
	
	$filename = "ticket_" . $email_ticket . ".pdf";
	$filepath = "/var/www/html" . PATH["TICKET"] . "$email_path/ticket_$email_ticket.pdf";
	
	$boundary = "--".md5(uniqid(time())); 

	$mailheaders = "MIME-Version: 1.0;\r\n"; 
	$mailheaders .="Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n"; 

	$mailheaders .= "From: servicedesk@vgtz.com\r\n"; 
	$mailheaders .= "Reply-To: servicedesk@vgtz.com\r\n"; 

	$multipart = "--$boundary\r\n"; 
	$multipart .= "Content-Type: text/html; charset=utf-8\r\n";
	$multipart .= "Content-Transfer-Encoding: base64\r\n";    
	$multipart .= "\r\n";
	$multipart .= chunk_split(base64_encode($message));


	$fp = fopen($filepath, "r"); 
	if (!$fp) { 
		echo "ERROR Reading File"; 
		return false;
	} 
	$file = fread($fp, filesize($filepath)); 
	fclose($fp); 

	$message_part = "\r\n--$boundary\r\n"; 
	$message_part .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";  
	$message_part .= "Content-Transfer-Encoding: base64\r\n"; 
	$message_part .= "Content-Disposition: attachment; filename=\"$filename\"\r\n"; 
	$message_part .= "\r\n";
	$message_part .= chunk_split(base64_encode($file));
	$message_part .= "\r\n--$boundary--\r\n";

	$multipart .= $message_part;

	$email_to .= ",servicedesk@vgtz.com";
	$email_to = trim($email_to, ",");
	$ret = mail($email_to, $subject, $multipart, $mailheaders);
	if ( $ret === TRUE ) {
		return true;
	} else {
		return false;
	}
}

// оповещение о создании нового пользователя
function notifyEmail( $ntype, $data, $id ) {
    $data = json_decode($data, true, 2, JSON_BIGINT_AS_STRING);
    $subject = $ntype . " (" . $data['name'] . ")";
    $message = "<html><head><title>Новый пользователь</title></head><body><p>Автосоздание пользователя: " . $data['name'] . "</p><br /><p>Дата: " . date("Y-m-d") . "</p><br /><p>Время: " . date("H:i") . "</p></body></html>";

	$mailheaders = "MIME-Version: 1.0;\r\n"; 
	$mailheaders .= "Content-type: text/html; charset=utf-8;\r\n";
	$mailheaders .= "From: servicedesk@vgtz.com\r\n"; 

    $email_to = "servicedesk@vgtz.com";
    $ret = mail($email_to, $subject, $message, $mailheaders);
    if ( $ret === TRUE ) {
        return true;
    } else {
        return false;
    }
}

?>