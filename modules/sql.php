<?php
// класс для работы с БД MySQL
require_once("data.db.php");
require_once("functions.php");

class sql {
	private $error = Array();
	
	// сообщение об ошибке
	private function getLastError($err, $msg) {
		return Array(
			"error"	=> $err,
			"msg"	=> $msg
		);
	}
	
	// SQL-запрос
	public function dataSQL($func, $data="", $v1="") {
		if ( $mysqli = $this->db_connect() ) {
            if ( $v1 === "user_add" ) {
			// lj,fdktybt gjkmpjdfntkz
                if ( $result = $mysqli->query(db::addUser($data, $v1)) ) {
                    if ( $mysqli->insert_id > 0 ) {
                        $id = Array( "id"	=>	$mysqli->insert_id );
                        notifyEmail($v1, $data, $id);
                    }
                }
            }
			if ( $result = $mysqli->query(db::$func($data, $v1)) ) {
				if ( $mysqli->insert_id > 0 ) {
					// добавление заявки, создание бланка pdf
					$id = Array( "id"	=>	$mysqli->insert_id );
					$data = createPDF($data, json_encode($id));
				} else {
					// ответ на запрос
					if ( is_object($result) )
						for ( $data=Array(); $row=$result->fetch_assoc(); $data[]=$row );
					else $data = $result;
				}
				$mysqli->close();
				$this->error = false;
				return $data;
			} else {
				// ошибка
				$errParam = "err" . $func;
				$this->error = $this->getLastError(constant("db::".$errParam), $mysqli->error);
				$mysqli->close();
				return $this->error;
			}
		} else {
			$this->error = $this->getLastError("MySQL Connecting Error", $mysqli->error);
			$mysqli->close();
			return $this->error;
		}
	}
	
	// подключение к БД
	private function db_connect() {
		$mysqli_ = new mysqli(db::host, db::user, db::password, db::base);
		
		if ( $mysqli_->connect_error ) {
			$this->error = $mysqli_->connect_errno . " : " . $mysqli_->connect_error;
			return false;
		}
		$mysqli_->set_charset("utf8");
		return $mysqli_;
	}
}
?>