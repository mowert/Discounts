<?php

	class Parse {

		var $stores = array(
			
			Array(
				'id' => '1',
				'type' => 'json',
				'url' => 'https://5ka.ru/api/special_offers/?format=json&page=1&records_per_page=10000',
				'cookie' => Array(
					'location_id=1858'
				),
			),
			
		);
		
		public function GetData ($store_id) {

			if(intval($store_id) === false || !$store = $this->stores[intval($store_id)] ) return "Ошибка. Неверный ID магазина";

			if(is_array($store)){

			$ch = curl_init(); 

			curl_setopt($ch, CURLOPT_URL, $store['url']);
			curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

			if(is_array($store['cookie'])){	
				/*
				* COOKIE FILE
				*/
				$cookie_file = "/home/u613091763/public_html/discounts/classes/cookie.txt";

				curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie_file);
				curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie_file);
				/*
				* COOKIE PARAMETRS
				*/
				curl_setopt ($ch, CURLOPT_COOKIE, implode($store['cookie'], ';')); 
			}

			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLINFO_HEADER_OUT, 0);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 30);

			$data = curl_exec($ch); 
			curl_close($ch);

			$this->SaveDataToDB($data, $store['id']);
			return true;
			}

		}

		function SaveDataToDB (&$data, $id) {
			global $Main;

			switch($id){
				case '1':
					/*
					* Сохранение позиций пятерочки
					*/ 
					$data = json_decode($data);
					if($c = count($data->results)){
						for($i = 0; $i <= ($c-1); $i++){

							$resultRow = &$data->results[$i];

							$Price = $Main->CreateObject('price');
							$Price->title = $resultRow->name;
							$Price->parent = $id;
							$Price->type = $resultRow->type;
							$Price->start = date('Y-m-d H:i:s', $resultRow->params->date_start);
							$Price->end = date('Y-m-d H:i:s', $resultRow->params->date_end);

							$Price->image = $resultRow->image_small;
							$Price->priceold = floatval($resultRow->params->regular_price);
							$Price->price = floatval($resultRow->params->special_price);

							if($Price->Save())
								unset($Price);
						}
					} else return 0;
				break; 
				default: 
					return false;
				break;
			}
		}
	}
?>