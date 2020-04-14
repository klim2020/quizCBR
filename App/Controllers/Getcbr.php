<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\CacheHandler;
use \App\Models\CbrHandler;
use function PHPUnit\Framework\isEmpty;


/**
 * Home controller
 *
 * PHP version 7.0
 */
class Getcbr extends \Core\Controller
{
    private CbrHandler $cbr;

    public function __construct($route_params)
    {
        //подключение библиотеки АПИ Центробанка, внедрения зависимости кэширования через интерфейс
        $this->cbr = new CbrHandler(new CacheHandler());
        parent::__construct($route_params);
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function index()
    {


        View::renderTemplate('Home/getcbr.html');
    }

	/*
	* Точка входа для api
	*@return string
	*
	*
	*/
	public function getdata(){

	    $setval = isset($_GET['answer']);
	    $cond22 = !empty($_GET['answer']);

	    $ff = (isset($_GET['answer']) && !empty($_GET['answer']) );

		if (isset($_GET['answer']) && !empty($_GET['answer']) ){
		    //получение значений катировок для заданной даты
	        $data = array("value"=>$this->cbr->GetCBR($_GET['answer']));}else{
	        $data = array("error"=>"no data provided");}

		header('Content-Type: application/json');
		echo json_encode($data);

	}
	public function test(){
        header('Content-Type: application/json');
	    echo json_encode($this->cbr->GetCBR());
    }
}
