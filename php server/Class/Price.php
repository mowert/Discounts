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

			foreach ($Data->table['price'] as $key) {
				if(!isset($this->$key)) continue;

				if(isset($this->id)){
					$data[] = "`{$key}` = ".$Data->Quote($this->$key);
				} else {
					$data_k[] = "`{$key}`";
					$data_v[] = $Data->Quote($this->$key);
				}
				
			}

			return $Data->Query((isset($this->id) ? "UPDATE `current_goods` SET ".implode(', ', $data)." WHERE `id` =".$Data->Quote($this->id):"INSERT INTO `current_goods` (".implode(', ', $data_k).") VALUES (".implode(', ', $data_v).")"));

		}

	}
?>