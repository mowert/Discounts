<?php
	
	// root каталог
	global $root;
	$root = __DIR__;
	
	// класс конфигурации
	global $Conf;
	require_once('Class/Conf.php');
	$Conf = new Conf;

	// класс работы с бд
	global $Data;
	require_once('Class/Data.php');
	$Data = new Data;

	// main класс
	global $Main;
	require_once('Class/Main.php');
	$Main = new Main;


?>