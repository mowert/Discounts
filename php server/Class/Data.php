<?php
	/**
	* Работа с БД
	*/ 
	class Data {

		private $data;
		private $register;
		var $table = Array(
			"price" => Array('id', 'ids', 'title', 'parent', 'type', 'start', 'end', 'image', 'priceold', 'price'),
		);


		/**
		* Инициализация подключения к бд
		*/
		function __construct() {
			global $Conf;
			
			$this->data = new mysqli($Conf->Get('db_host'), $Conf->Get('db_user'), $Conf->Get('db_password'), $Conf->Get('db_table'));
			if ($this->data->connect_errno) {
			    echo "Не удалось подключиться к MySQL: (" . $this->data->connect_errno . ") " . $this->data->connect_error;
			   exit();
			}
		}
		
		/**
		* @brief Получение всех строк результатов запроса
		* @param $q Запрос
		*/
		function GetRows($q) {
			$r = array();
			if ($result = $this->Query($q)) {
			    while ($row = $result->fetch_assoc())
			        $r[] = $row;
			    $result->free();
			}
			return $r;
		}
		
		/**
		* @brief Экранирование специальных символов в строке и добавление кавычек
		* @param $text Строка
		*/
		function Quote($text) {
			return "'" . $this->data->real_escape_string($text) . "'";
		}
		
		/**
		* @brief Выполнение запроса
		* @param $q Запрос
		*/
		function Query($q) {
			return $this->data->query($q);
		}
		
		/**
		* @brief Получение следующей строки результатов запроса
		* @param $result Результаты запроса
		*/
		function Fetch($result) {
			$a = $this->data->mysql_fetch_assoc($result);
			if (isset($this->charset) && is_array($a)) foreach ($a as $k => $v) if (is_string($v)) $a[$k] = iconv($this->charset, "UTF-8", $v);
			return $a;
		}
		
	}
?>