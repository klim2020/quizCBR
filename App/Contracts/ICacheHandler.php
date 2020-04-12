<?php
namespace App\Contracts;


interface ICacheHandler
{
	//Sets Cache Var
    public function setVariable($name, $var);
	//Get Cache Var
    public function getVariable($name);
	//Get TTL for a cache  var
	public function getTime($name);
}