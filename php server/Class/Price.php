<?php
	/*
	* Объект позиции каталога 
	*/

	class Price {

		/*
		* Сохранение параметров в бд
		*/

		public function Save(){
			global $Data;

			$isset = count($Data->GetRows("SELECT `id` FROM `current_goods` WHERE `ids` = ".$Data->Quote($this->ids)." AND `parent` = ".$Data->Quote($this->parent)));

			foreach ($Data->table['price'] as $key) {
				if(!isset($this->$key)) continue;

				if($isset){
					$data[] = "`{$key}` = ".$Data->Quote($this->$key);
				} else {
					$data_k[] = "`{$key}`";
					$data_v[] = $Data->Quote($this->$key);
				}
				
			}

			if($isset){
				$Data->Query("UPDATE `current_goods` SET ".implode(', ', $data)." WHERE `ids` = ".$Data->Quote($this->ids)." AND `parent` = ".$Data->Quote($this->parent));
			} else $Data->Query("INSERT INTO `current_goods` (".implode(', ', $data_k).") VALUES (".implode(', ', $data_v).")");

		}

	}
?>