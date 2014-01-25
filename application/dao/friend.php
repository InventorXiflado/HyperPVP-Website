<?php
/*
	 /'\_/`\                                    
	/\      \     __      ___      __     ___   
	\ \ \__\ \  /'__`\  /' _ `\  /'_ `\  / __`\ 
	 \ \ \_/\ \/\ \L\.\_/\ \/\ \/\ \L\ \/\ \L\ \
	  \ \_\\ \_\ \__/.\_\ \_\ \_\ \____ \ \____/
	   \/_/ \/_/\/__/\/_/\/_/\/_/\/___L\ \/___/ 
	                               /\____/      
	                               \_/__/       
	@author Leon Hartley
*/

class FriendDao {
	
	public static function loadStdClass($query)
	{		
		$array = array();
		foreach($query as $a) {
			$obj = new StdClass();
			foreach($a as $key => $val) {
			
				echo $key;
			
				$obj->$key = $val;
			}
			$array[] = $obj;
		}
		return $array;
	}
	
	public static function requestExists($to, $from) {
	
		$count  = count(R::getAll("SELECT id FROM `users_friendrequest` WHERE `to` = ? AND `from` = ?", array($to, $from)));

		if($count != 1) {
			return false;
		}

		return true;
	}
	
	public static function requestExistsID($id) {
	
		$count  = count(R::getAll("SELECT id FROM `users_friendrequest` WHERE `id` = ?", array($id)));

		if($count != 1) {
			return false;
		}

		return true;
	}
	
	public static function friendRequests($to) {
	
		return R::getAll("SELECT id FROM `users_friendrequest` WHERE `to` = ?", array($to));
	}
	
	public static function getFriendRequests($to) {
	
		$query = R::getAll("SELECT id FROM `users_friendrequest` WHERE `to` = ?", array($to));
		return $query;//self::loadStdClass($query);
	}
	
	public static function getFriendRequest($id) {
	
		$query = R::load('users_friendrequest', $id); 
		return $query;//self::loadStdClass($query);
	}
}