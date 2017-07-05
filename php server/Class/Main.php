<?php
	class Main {

		function Load($id, $type){
			global $Data;
				if($result = $Data->GetRows("SELECT * FROM `current_goods` WHERE `id` = ".$Data->Quote($id))){
					
					$Obj = $this->CreateObject($type);
					foreach ($result as $key => $value)
						$Obj->$key = $value;
				} 
			return $Obj;
		}

		function CreateObject($type){

			$class = ucfirst(strtolower($type));
			
			global $$class;
			if(!is_object($$class)){
				global $root;
				require_once($root.'/Class/'.$class.'.php');
				$$class = new $class;
			} 

			$Obj = new $class;
			return $Obj;
		}

	}
?>