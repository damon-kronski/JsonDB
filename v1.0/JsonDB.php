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

function __construct($useCookies = true, $jsonDBPath = "db.json",$cookieName = "JsonDB",$defaultArray = array(array('id' => 1,'name' => 'Test','value' => 'Value'))){
	GLOBAL $config;
	$config['defaultArray'] = $defaultArray;
	$config['useCookies'] = $useCookies;
	$config['cookieName'] = $cookieName;
	$config['jsonDBPath'] = $jsonDBPath;
	
	if($config['useCookies']){
		if(!isset($_COOKIE[$cookieName]))
			$this->writeDB($config['defaultArray']);
	}
	else
		if(!file_exists($config['jsonDBPath']))
			$this->writeDB($config['defaultArray']);
	
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
	global $config;
	if($config['useCookies'])
		setcookie($config['cookieName'],json_encode($array));
	else{
		$fh = fopen($config['jsonDBPath'], 'w+');
		fwrite($fh, json_encode($array));
		fclose($fh);
	}
}

function readDB(){
	global $config;
	if($config['useCookies'])
		return json_decode($_COOKIE[$config['cookieName']], true);
	else{
		$fh = fopen($config['jsonDBPath'], 'r');
		$json = fread($fh, filesize($config['jsonDBPath']));
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
