<?php
	/**
	* Конфигурация
	*/ 
	class Conf {

		private $conf;

		/**
		* Инициализация параметров конфигурации
		*/
		function __construct(){
			global $root;
			
			header('Content-Type: text/html; charset=utf-8');
			$this->conf = @include($root.'/conf.dat');
		}

		/**
		* @brief
		* @param $key - ключ массива значений конфигурации
		*/
		public function Get($key){
			return $this->conf[$key];
		}

	}
?>