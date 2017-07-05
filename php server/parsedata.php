<?php
	
	include('init.php');

	if($_GET['key'] == $Conf->Get('key')){
		if(isset($_GET['store_id'])){

			$id = intval($_GET['store_id']);
			if($id === false) return false;
			
			global $Parse;
			require_once('Class/Parse.php');
			if(!is_object($Parse)) $Parse = new Parse;

			$Parse->GetData($id);
		} else {
			echo "No store number";
		}
	} else {
		echo "No auth key";
	}
	return false;
?>