<?php
	// получение данных запроса (от скрипта script.js)	
	$func = ( isset($_REQUEST["func"]) ) ? $_REQUEST["func"] : "";
	$data = ( isset($_REQUEST["data"]) ) ? $_REQUEST["data"] : "";
	$v1 = ( isset($_REQUEST["v1"]) ) ? $_REQUEST["v1"] : "";
	if (isset($_REQUEST["term"])) $data = $_REQUEST["term"];
	
	// обработка данных ...
	if ( $func !== "" )  dataRequest($func, $data, $v1);

	function dataRequest($func, $data="", $v1="") {
		require_once("sql.php");
		$sql = new sql;
		// ... и выдача ответа
		echo json_encode($sql->dataSQL($func, $data, $v1));
	}
?>