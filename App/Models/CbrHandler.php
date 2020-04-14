<?php

namespace App\Models;

 use AXP\CBRRU\CBR;
 use \App\Contracts\ICacheHandler;


/**
 * Класс для работы с АПИ Центробанка
 *
 * PHP version 7.0
 */
class CbrHandler extends \Core\Model
{
	private ICacheHandler $ic;
    //конструктор,   внедрение зависимости кэширования через интерфейс
	public function __construct(ICacheHandler $ic){
		$this->ic = $ic;
	}


    /**
     * @param $url
     * @param array $params
     * @return array|mixed
     */
    private function query($url, $params = [] ) {
        $data = [];

        try {
            $xml = simplexml_load_file( $url . '?' . http_build_query( $params ) );
            $json = json_encode( $xml );
            $data = json_decode( $json , true );
        } catch (Exception $e) {
            $data = ['error' => $e->getMessage()];
        }

        return $data;
    }

    /**
     * Get all the users as an associative array
     *
     * @return array
     */
    public function GetCBR($date2 = '20/03/2020',$types = array("USD","EUR"))
    {

        $index = str_replace('/','',$date2);
        //пробуем прочитать из кеша
        $out = $this->ic->getVariable($index);

		if ($out){
			/*в кеше найдена запись*/

            return $out;

		}else{
		    //Запись не найдена, обращаемся к АПИ Центробанка
		    $out = array();
            $date = explode('/',$date2);
            //
            $from_unix_time = mktime(0, 0, 0,  (int)$date[1],  (int)$date[0],  (int)$date[2]);
            $day_before = strtotime("yesterday", $from_unix_time);
            $formatted = date('d/m/Y', $day_before);
            //
            $dynamic1 = $this->query( CBR::DAILY, [
                'date_req' => $date2,
            ]);

            $dynamic2 = $this->query( CBR::DAILY, [
                'date_req' => $formatted,
            ]);
            foreach ($types as $key){


                $key1 = array_search($key, array_column($dynamic1['Valute'], 'CharCode'));

                $val1 = $dynamic1['Valute'][$key1]['Value'];

                $key2 = array_search($key, array_column($dynamic2['Valute'], 'CharCode'));
                $val2 = $dynamic2['Valute'][$key2]['Value'];

                $outup =  array("val1"=>$val1,
                            "nominal"=>$dynamic1['Valute'][$key1]['Nominal'],
                            "name"=>$key,
                            "full"=>$dynamic1['Valute'][$key1]['Name'],
                            "date"=>$date2
                            );

                if ($val1>$val2){
                    $outup["impulse"]="up";
                }elseif($val1<$val2){
                    $outup["impulse"]="down";
                }else{$outup["impulse"]="equal";}
                //$out += array($outup);
                array_push($out,$outup);
            }
            //упаковываем JSON
            $this->ic->setVariable($index,$out);
        }



		return $out;






		//var_dump($dynamic);
    }
}
