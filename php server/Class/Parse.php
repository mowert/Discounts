<?php

	class Parse {

		var $stores = array(
			
			'1' => Array(
				'url' => 'https://5ka.ru/api/special_offers/?format=json&page=1&records_per_page=10000',
				'cookie' => Array(
					'location_id=1858'
				),
			),
			'2' => Array(
				'url' => Array(
					'http://www.lenta.com/api-data/products/catalog/nnvgrd/2',
					'http://www.lenta.com/api-data/products/catalog/nnvgrd/950',
					'http://www.lenta.com/api-data/products/catalog/nnvgrd/951',
				),
				'suburl' => 'http://www.lenta.com/api-data/products/catalogidinfo/nnvgrd/{suburl_id}',
			),
			
		);
		
		public function GetData ($store_id, $catalog_id = null, $suburl_id = null) {

			if(intval($store_id) === false || !$store = $this->stores[intval($store_id)] ) return "Ошибка. Неверный ID магазина";

			if(is_array($store)){

			$ch = curl_init();
			$cp = false;

			/*
			* Формирование suburl
			*/
			if ($suburl_id != null){
				$url = str_replace('{suburl_id}', $suburl_id, $store['suburl']);
			} else 
			/*
			* Получение данных для одного из каталога данных
			*/
			if(is_array($store['url']) && intval($catalog_id) !== false){
				$url = $store['url'][intval($catalog_id)];
				/*
				* Флаг необходимости парсинга подкаталога
				*/
				if(isset($store['suburl'])) $cp = true;
			} else if(!is_array($store['url'])){
				$url = $store['url'];
			} else {
				return false;
			}

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

			if(isset($store['cookie']) && is_array($store['cookie'])){	
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
			
			/*
			* Обработчик данных
			*/
			if($cp)
				$this->GetSubcatalogs($data, $store_id);
			else 
				$this->GetPriceData($data, $store_id);
			
			return true;
			}

		}

		/*
		* Получение конечного url для парсинга позиций каталога
		*/
		function GetSubcatalogs(&$data, $id){
			
			switch($id){
				case '2':
					/*
					* Обработка каталогов Лента
					*/
					$data = json_decode($data);
					if($c = count($data->dataResult->child)){
						for($i = 0; $i <= ($c-1); $i++){
							if($data->dataResult->child[$i]->count > 0)
								$this->GetData($id, null, $data->dataResult->child[$i]->category->id);
							/*
							* Обработка подкаталогов
							*/
							if($sc = count($data->dataResult->child[$i]->child)){
								for($j = 0; $j <= ($sc-1); $j++){
									if($data->dataResult->child[$i]->child[$j]->count > 0)
										$this->GetData($id, null, $data->dataResult->child[$i]->child[$j]->category->id);
								}
							}
						}
					} else return false;
				break;
				default: 
					return false;
				break;
			}
		}

		/*
		* Заполнение объекта данными
		*/
		function SetPriceData ($data){
			global $Main;

			$Price = $Main->CreateObject('price');
			foreach ($data as $key => $value) {
				$Price->$key = $value;
			}
			
			print var_dump($Price)."<br><br><br>";

			$Price->Save();
		}

		/*
		* Парсинг данных позиций каталога
		*/
		function GetPriceData (&$data, $id) {

			switch($id){
				case '1':
					/*
					* Обработка позиций Пятерочка
					*/
					$data = json_decode($data);
					if($c = count($data->results)){
						for($i = 0; $i <= ($c-1); $i++){

							$resultRow = &$data->results[$i];

							$this->SetPriceData(Array('ids' => $resultRow->shop_items[0]->id, 'title' => $resultRow->name, 'parent' => $id, 'type' => $resultRow->type, 'start' => date('Y-m-d H:i:s', $resultRow->params->date_start), 'end' => date('Y-m-d H:i:s', $resultRow->params->date_end), 'image' => ($resultRow->image_small ? 'https://5ka.ru/'.$resultRow->image_small:''),'priceold' => floatval($resultRow->params->regular_price),'price' => floatval($resultRow->params->special_price)));

						}
					} else return false;
				break; 
				case '2':
					/*
					* Обработка позиций Лента
					*/
					$data = json_decode($data);

					if($c = count($data->dataResult->items)){
						for($i = 0; $i <= ($c-1); $i++){

							$resultRow = &$data->dataResult->items[$i];

							$this->SetPriceData(Array('ids' =>  $resultRow->id, 'title' => $resultRow->name, 'parent' => $id, 'type' => $resultRow->type, 'start' => date('Y-m-d H:i:s', $resultRow->dateStart), 'end' => date('Y-m-d H:i:s', $resultRow->dateEnd), 'image' => $resultRow->img->file,'priceold' => floatval($resultRow->oldPrice),'price' => floatval($resultRow->newPrice)));

						}
					} else return false;
				break;
				default: 
					return false;
				break;
			}
		}
	}
?>