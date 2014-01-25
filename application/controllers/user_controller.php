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

class User extends Controller {
	public function __construct() {
		parent::__construct();
	}

	public function profile() {

		try
		{
			$profileName = explode('/', $_GET["do"]);
			
			$this->load_view("profile");
			$this->view->data->username = $profileName[1];
			
			if ($this->view->data->username == "")  {
				$this->load_view("error");
				$this->view->bind("error->title", "No such user exists"); 
				$this->view->bind("error->message", "I'm sorry. The user does not exist. ##1");
				$this->view->publish();
				return;
			}
			
			if (!UserDao::exists("username", $this->view->data->username)) {
				$this->load_view("error");
				$this->view->bind("error->title", "No such user exists"); 
				$this->view->bind("error->message", "I'm sorry. The user does not exist. ##2");
				$this->view->publish();
				return;
			}
		
			$row = UserDao::getUser($this->view->data->username);

			$this->view->bind("user->name", $row->username);
			$this->view->bind("user->last_online",  humanTiming($row->last_online));
			$this->view->data->user = $row;
			
			$k = 0;
			foreach (UserDao::getStatsNoLimit($this->view->data->user->id, "kill") as $kills) {
				$k++;
			}
			
			R::exec("UPDATE users SET kills = '" . $k . "' WHERE username = '" . $this->view->data->username . "'");
			$this->view->bind("user->kills", $row->kills);
			
			// ----------- END OF KILLS ----- //
			
			$d = 0;
			foreach (UserDao::getStatsNoLimit($this->view->data->user->id, "death") as $deaths) {
				$d++;
			}
			R::exec("UPDATE users SET deaths = '" . $d . "' WHERE username = '" . $this->view->data->username . "'");
			$this->view->bind("user->deaths", $row->deaths);
			
			// ----------- END OF DEATHS ----- //
			
			$monument = 0;
			foreach (UserDao::getStatsNoLimit($this->view->data->user->id, "monument") as $monuments) {
				$monument++;
			}
			
			R::exec("UPDATE users SET broke_monument = '" . $monument . "' WHERE username = '" . $this->view->data->username . "'");
			$this->view->bind("user->monuments", $monument);
			
			// ----------- END OF BROKE MONUMENT ----- //
			
			$core = 0;
			foreach (UserDao::getStatsNoLimit($this->view->data->user->id, "core") as $monuments) {
				$core++;
			}
			
			R::exec("UPDATE users SET leaked_core = '" . $core . "' WHERE username = '" . $this->view->data->username . "'");
			$this->view->bind("user->cores", $core);
			
			// ----------- END OF BROKE CORE ----- //
			
            $k = $row->kills;
            $d = $row->deaths;
            
			if ($k == 0 || $d == 0) {
				$kdRatio = 0;
				$kdPercent = 0;
			} else {
			
				$kdRatio = round($k / $d, 3);
				$kdPercent = round($k / ($k + $d), 2) * 100;
			
			}
			$this->view->bind("user->kdratio", $kdRatio);
			$this->view->bind("user->kdpercent", $kdPercent);
			
			// ----------- //
			
			$i = 0;
			foreach (UserDao::getFriendsNoLimit($this->view->data->username) as $friend) {
				$i++;
			}
			
			
			$this->view->bind("user->friends", $i);
			$this->view->publish();
		}
		catch (Exception $e)
		{
			echo $e;
		}
	}
    	
	public function register() {
	
		$this->load_view("user/register");
		$this->view->data->tab = 'register';
		$this->view->publish();
	
	}
	
	public function recent() {
	
		$this->load_view("user/recent");
		$this->view->data->tab = 'recent';
		$this->view->publish();
	
	}
	
	public function login() {
	
		$form = new Form("post", array('username', 'password'));
	
		if($form->check()) {
			$form->produce();

			$username = $form->field->username;
			$password = $form->field->password;

			if(!UserDao::exists('username', $username)) {
				$this->load_view("error");
				$this->view->bind("error->title", "Login"); 
				$this->view->bind("error->message", "That user is not registered!");
				$this->view->publish();
				return;
			}

			$user = UserDao::getByKey('username', $username); 

			if($user[0]['password'] == $password) {
			
				Session::set($user[0]['id']);
			
				Router::sendTo("index");
				
				
			} else {
				$this->load_view("error");
				$this->view->bind("error->title", "Login"); 
				$this->view->bind("error->message", "You have entered the wrong password!");
				$this->view->publish();
			}
		} else {
			$this->load_view("user/login");
			$this->view->data->tab = 'login';
			$this->view->publish();
		}

	}
	
	public function redeem() {
	
		if (!isset($_POST['pin']))
		{
			$_POST['pin'] = "";
		}
	
		if ($_POST['pin'] == "")
		{
			$this->load_view("error");
			$this->view->bind("error->title", "Invalid PIN"); 
			$this->view->bind("error->message", "You cannot enter a blank PIN.");
			$this->view->publish();
			return;
		}
		
		if (!SiteDao::pinExists($_POST['pin']))
		{
			$this->load_view("error");
			$this->view->bind("error->title", "Invalid PIN"); 
			$this->view->bind("error->message", "You have entered an invalid PIN.");
			$this->view->publish();
			return;
		}
	
		
		$this->load_view("user/redeem");
		$this->view->data->pincode = UserDao::getPinData($_POST['pin']);
		$this->view->data->password = $this->view->data->pincode->password;//randString(8);
		
		R::exec("UPDATE `users` SET `password` = '" . $this->view->data->password . "' WHERE `username` = '" . $this->view->data->pincode->name . "'");
		R::exec("UPDATE `users` SET `email` = '" . $this->view->data->pincode->email . "' WHERE `username` = '" . $this->view->data->pincode->name . "'");
		R::exec("DELETE FROM `pincodes` WHERE `id` = ?", array($_POST['pin']));
		
		$this->view->bind("redeem->username", $this->view->data->pincode->name); 
		$this->view->bind("redeem->password", $this->view->data->password);
		$this->view->bind("buffer", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
		$this->view->publish();
		
	}
}