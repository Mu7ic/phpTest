<?php

class Messages
{
	// ключ сообщества
	const KEY_GROUP='98dc54197c9003c79fe93db07737f67c1c4e6101123b0b7cbed9ce6dafa5fad11291bfab2aba1cfc9f200';

	/*
	 * date
	 */
	public function getMessages($date){
		$time=strtotime($_POST['date']);
		return $this->outMainMessage($this->getRequest(),$time);
	}

	//Запрос на список сообщений
	public function outMainMessage($main_message,$time){
		$result="";
		foreach ($main_message->response->items as $items) {
			//вывод сообщений выбраной даты
			if(date('d-n-Y',$items->message->date)==date('d-n-Y',$time)){
			$result.='Последний текст: '.$items->message->body.';';
			$result.=$this->outMessages($this->getFullMessages($items->message->user_id));
			$result.=$this->checkAnswerTime($this->getFullMessages($items->message->user_id));			
			}else echo 'На данной дате пока нету сообщений!!';
		}
		return $result;
	}

	public function getRequest(){
		//отправка запроса
		$url = 'https://api.vk.com/method/messages.getDialogs';

		//параметры запроса
		$params = array(
		    'v' => '5.80', // параметр v=5.80
		    'access_token' => self::KEY_GROUP, // параметр access_token
		    'count' => 10, // количество новый диалогов
		    'offset' => 0 // отрицательное значение
		);


		$myCurl = curl_init();
		curl_setopt($myCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.6 (KHTML, like Gecko) Chrome/16.0.897.0 Safari/535.6');
			curl_setopt_array($myCurl, array(
			    CURLOPT_URL => $url,
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_POST => true,
			    CURLOPT_POSTFIELDS => http_build_query($params)
			));
			$response = curl_exec($myCurl);
			curl_error($myCurl);
			curl_close($myCurl);

		return json_decode($response);
	}

	public function getFullMessages($idPeer){
		//https://api.vk.com/method/messages.getHistory?v=5.80&access_token=98dc54197c9003c79fe93db07737f67c1c4e6101123b0b7cbed9ce6dafa5fad11291bfab2aba1cfc9f200&peer_id=234225865&offset=0&count=10
		$url = 'https://api.vk.com/method/messages.getHistory';

		
		$params = array(
		    'v' => '5.41', 
		    'access_token' => self::KEY_GROUP,
		    'count' => 10,
		    'peer_id'=>$idPeer,
		    'offset' => 0 
		);


		$myCurl = curl_init();
		curl_setopt($myCurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.6 (KHTML, like Gecko) Chrome/16.0.897.0 Safari/535.6');
			curl_setopt_array($myCurl, array(
			    CURLOPT_URL => $url,
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_POST => true,
			    CURLOPT_POSTFIELDS => http_build_query($params)
			));
			$response = curl_exec($myCurl);
			curl_error($myCurl);
			curl_close($myCurl);

		return json_decode($response);
	}

	//Перебор все сообщения
	public function outMessages($msg){
		$result="";
		$result.='<div class="bl">';
		$summ=[];
		$array=[];
		for($i=0;$i<=count($msg->response->items);$i++) {
			if(!empty($msg->response->items[$i]->date) and  !empty($msg->response->items[$i+1]->date))
			$array[]=$this->dateDiff($msg->response->items[$i]->date,$msg->response->items[$i+1]->date);
		}
		$result.=$this->fromArraytoString($this->getMinMaxAverageTime($array));
		$result.='</div>';

		return $result;
	}

	// Проверка время ответа администратора
	public function checkAnswerTime($msgs){
		asort($msgs->response->items);
		$new_array = array_slice($msgs->response->items, -1);

		foreach ($new_array as $key) {
			if($key->out==0){
				$time=$this->dateDiff($key->date,time());
			
			return $time>=900 ? "<a href='https://vk.com/gim155500719?sel=".$key->user_id."&tab=unrespond' target='_blank'>ссылка</a>" : $this->timeSet($time). ' до ответа' ;
			}
			else{
				return 'Отвечено';
			}
		}
	}

	//обработка максимум, минимум и среднее времени
	public function getMinMaxAverageTime(array $array){
		return ['max'=>max($array),'min'=>min($array),'average'=>array_sum($array)/count($array)];
	}

	// преобразовываем из массива в строку
	public function fromArraytoString(array $array){
		return 'Минимальное время: '.$this->timeSet($array['min']).'; Максимальное время: '.$this->timeSet($array['max']).'; Среднее время: '.$this->timeSet(round($array['average']));
	}

	// Преобразовании секунды на минуты, часы, дни
	public function timeSet($time){
		switch ($time) {
			case ($time<60): return '1 минута';
				break;
			case ($time>60 and $time<3600): return round($time/60).' минут';
				break;
			case ($time>3600 and $time<86400): return round($time/(60*60)).' час';
				break;
			case ($time>86400): return round($time/60*60*24).' день';
				break;
		}
	}

	// расчёт времени
	public function dateDiff($date1,$date2){
		$diff = abs($date2 - $date1); 

		$years   = floor($diff / (365*60*60*24)); 
		$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
		$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

		$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 

		$minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 

		$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60)); 

		if ($years > 0) {
            return $years.' год';
        } elseif ($months > 0) {
            return $months.' месяц';
        } elseif ($days > 0) {
            return $days.' день';
        } elseif ($hours*60*60*24 > 0) {
       		return $hours*60*60;
        }elseif ($minuts > 0) {
            return $minuts*60;
        }  elseif ($seconds > 0) {
            return $seconds;
        } 

	}
}

?>