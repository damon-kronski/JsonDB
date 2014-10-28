<?php

/****************************************************/
/********************** JsonDB **********************/
/****************************************************/
/*                                                  */
/*  Name:        JsonDB                             */
/*  Author:      Damon-Kronski                      */
/*  Version:     1.0                                */
/*  Description:                                    */
/*    A lightweight database library for            */
/*    json files or cookies                         */
/*                                                  */
/****************************************************/



class JsonDB{

public $config = array();

function __construct($useCookies = true, $jsonDBPath = "db.json",$cookieName = "JsonDB",$defaultArray = array(array('id' => 1,'name' => 'Test','value' => 'Value'))){
	
}

    public static function withJson( $defaultArray = array(array('id' => 1,'name' => 'Test','value' => 'Value')), $jsonDBPath = "db.json" ) {
    	$instance = new self();
    	$instance->config['jsonDBPath'] = $jsonDBPath;
    	$instance->config['defaultArray'] = $defaultArray;
		$instance->config['useCookies'] = false;
		$instance->config['cookieName'] = '';
		
		if(!file_exists($instance->config['jsonDBPath']))
			$instance->writeDB($instance->config['defaultArray']);
		
		return $instance;
    }

    public static function withCookie( $defaultArray = array(array('id' => 1,'name' => 'Test','value' => 'Value')), $cookieName = "JsonDB" ) {
    	$instance = new self();
    	$instance->config['jsonDBPath'] = '';
    	$instance->config['defaultArray'] = $defaultArray;
		$instance->config['useCookies'] = true;
		$instance->config['cookieName'] = $cookieName;
		
		if(!isset($_COOKIE[$cookieName]))
			$instance->writeDB($instance->config['defaultArray']);
		
		return $instance;
    }

/***************************************************/
/********************* Script **********************/
/***************************************************/

function delItem($id){
	$db = $this->readDB();
	$dbnew = $this->rmArray($db,$id);
	$this->writeDB($dbnew);
}

function addItem($item){
	$db = $this->readDB();
	$item['id'] = $this->getNewId($db);
	$dbnew = $this->addArray($db,$item);
	$this->writeDB($dbnew);
	return $item['id'];
}

function setItem($id,$value){
	$db = $this->readDB();
	$dbnew = $this->setArray($db,$id,$value);
	$this->writeDB($dbnew);
}

function writeDB($array){
	if($this->config['useCookies'])
		setcookie($this->config['cookieName'],json_encode($array));
	else{
		$fh = fopen($this->config['jsonDBPath'], 'w+');
		fwrite($fh, json_encode($array));
		fclose($fh);
	}
}

function readDB(){
	if($this->config['useCookies'])
		return json_decode($_COOKIE[$this->config['cookieName']], true);
	else{
		$fh = fopen($this->config['jsonDBPath'], 'r');
		$json = fread($fh, filesize($this->config['jsonDBPath']));
		fclose($fh);
		return json_decode($json, true);
	}
	
}

function rmArray($array, $id){
	foreach ($array as $key => $value) {
		foreach ($value as $ckey => $cvalue) {
			if ($ckey == 'id' AND $cvalue == $id) {
				unset($array[$key]);
				continue 2;
			}
		}
	}
	
	return $array;
}

function addArray($array, $value){
	$array[] = $value;
	return $array;
}

function setArray($array, $id,$item){
	foreach ($array as $key => $value) {
		foreach ($value as $ckey => $cvalue) {
			if ($ckey == 'id' AND $cvalue == $id) {
				foreach($item as $ikey => $ivalue){
					$array[$key][$ikey] = $ivalue;
				}
			}
		}
	}
	
	return $array;
}

function getNewId($db){
	if(count($db) >= 1){
		return end($db)['id'] + 1;
	}else
	{
		return 1;
	}
}

}

?>
