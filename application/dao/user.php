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

class UserDao {

	public static function loadStdClass($query)
	{		
		$array = array();
		foreach($query as $a) {
			$obj = new StdClass();
			foreach($a as $key => $val) {
				$obj->$key = $val;
			}
			$array[] = $obj;
		}
		return $array;
	}

	public static function get() {
		$user = R::load('users', Session::getAuth()); 
		return $user;
	}

	public static function getId($id) {
		$user = R::load('users', $id); 
		return $user;
	}

	public static function exists($key, $value) {
		$count  = count(R::getAll("SELECT id FROM `users` WHERE `" . $key . "` = ?", array($value)));

		if($count < 1) {
			return false;
		}

		return true;
	}
	
	public static function getByKey($key, $value) {
		$user = R::getAll("SELECT * FROM `users` WHERE `" . $key . "` = :value", array(":value" => $value));
		return $user;
	}
	
	public static function getUser($username) {
	
		$query = R::getAll('SELECT * FROM `users` WHERE `username` = :name ', array(":name" => $username));
		
		$a = self::loadStdClass($query);
		
		return $a[0];
	}
	
	public static function getPinData($pin) {
		$pin = R::getAll("SELECT * FROM `pincodes` WHERE `code` = :value", array(":value" => $pin));
		
		$a = self::loadStdClass($pin);
		
		return $a[0];
	}
	
	public static function getStats($username, $type) {
	
		$query = R::getAll('SELECT * FROM `users_statistics` WHERE `from_id` = :name AND `type` = :type ORDER BY id DESC LIMIT 25', array(":name" => $username, ":type" => $type));
		return self::loadStdClass($query);
	}
	
	public static function getStatsNoLimit($username, $type) {
	
		$query = R::getAll('SELECT * FROM `users_statistics` WHERE `from_id` = :name AND `type` = :type', array(":name" => $username, ":type" => $type));
		return self::loadStdClass($query);
	}
	
	public static function getFriends($username) {
	
		$query = R::getAll('SELECT * FROM `users_friends` WHERE `user` = :name ORDER BY id DESC LIMIT 25', array(":name" => $username));
		return self::loadStdClass($query);
	}
	
	public static function getFriendsNoLimit($username) {
	
		$query = R::getAll('SELECT * FROM `users_friends` WHERE `user` = :name', array(":name" => $username));
		return self::loadStdClass($query);
	}
	
	public static function isFriend($username, $name) {
	
		$query = R::getAll('SELECT * FROM `users_friends` WHERE `user` = :name', array(":name" => $username));
		
		foreach (self::loadStdClass($query) as $friend) {
		
			if ($friend->friend == $name) {
				return TRUE;
			}
		}
		
		return FALSE;
	}

}