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

class Friend extends Controller {
	public function __construct() {
		parent::__construct();
	}

	public function request() {
	
		if (!Session::isAuthed()) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend"); 
			$this->view->bind("error->message", "You can't perform this action when you're not logged in!");
			$this->view->publish();
			return;
		} 
		
		if (!isset($_GET['username'])) {
			$_GET['username'] == "!@#$";
		}
		
		if ($_GET['username'] == "") {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend"); 
			$this->view->bind("error->message", "Your friend request cannot be blank!");
			$this->view->publish();
			return;
		}
		
		if ($_GET['username'] == Session::auth()->username) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend"); 
			$this->view->bind("error->message", "Your friend request cannot be yourself!");
			$this->view->publish();
			return;
		}
		
		if (!UserDao::exists("username", $_GET['username'])) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend"); 
			$this->view->bind("error->message", "Sorry, that name doesn't exist in the user database!");
			$this->view->publish();
			return;
		}
		
		if (FriendDao::requestExists($_GET['username'], Session::auth()->username)) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend"); 
			$this->view->bind("error->message", "You have already sent them a friend request!");
			$this->view->publish();
			return;
		}
		
		
		$friend = R::dispense('users_friendrequest');
        $friend->to = $_GET['username'];
        $friend->from = Session::auth()->username;
        $id = R::store($friend);
		
		$this->load_view("error");
	    $this->view->bind("error->title", "Friend"); 
	    $this->view->bind("error->message", "You have sent them a friend request, please wait until they accept it!");
		$this->view->publish();
	}
	
	public function requests() {
	
		if (!Session::isAuthed()) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend Requests"); 
			$this->view->bind("error->message", "You can't perform this action when you're not logged in!");
			$this->view->publish();
			return;
		} 
		
		$this->load_view("user/requests");
		$this->view->publish();
	} 
	
	public function accept() {
	
		if (!Session::isAuthed()) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend Accept"); 
			$this->view->bind("error->message", "You can't perform this action when you're not logged in!");
			$this->view->publish();
			return;
		} 
	
		if (!isset($_GET['id'])) {
			$_GET['id'] = 0;
		}
		
		if (!FriendDao::requestExistsID($_GET['id'])) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend Accept"); 
			$this->view->bind("error->message", "You have tried to accept a friend request which never existed!");
			$this->view->publish();
			return;
		}
		
		$friendRequest = FriendDao::getFriendRequest($_GET['id']);
		
		if ($friendRequest->to != Session::auth()->username) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend Accept"); 
			$this->view->bind("error->message", "You have tried to accept a friend request which never existed!");
			$this->view->publish();
			return;
		}
		
		R::exec("DELETE FROM users_friendrequest WHERE id = ?", array($_GET['id']));
		
		$friend = R::dispense('users_friends');
		$friend->user = Session::auth()->username;
        $friend->friend = $friendRequest->from;
        R::store($friend);
		
		$friend = R::dispense('users_friends');
        $friend->user = $friendRequest->from;
        $friend->friend = Session::auth()->username;
        R::store($friend);
		
		$this->load_view("error");
		$this->view->bind("error->title", "Friend Accept"); 
		$this->view->bind("error->message", "You have accepted that friend request!");
		$this->view->publish();
	} 
	
	public function deny() {
	
		if (!Session::isAuthed()) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend Request Deny"); 
			$this->view->bind("error->message", "You can't perform this action when you're not logged in!");
			$this->view->publish();
			return;
		} 
	
		if (!isset($_GET['id'])) {
			$_GET['id'] = 0;
		}
		
		if (!FriendDao::requestExistsID($_GET['id'])) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend Request Deny"); 
			$this->view->bind("error->message", "You have tried to deny a friend request which never existed!");
			$this->view->publish();
		}
		
		$friendRequest = FriendDao::getFriendRequest($_GET['id']);
		
		if ($friendRequest->to != Session::auth()->username) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend Request Deny"); 
			$this->view->bind("error->message", "You have tried to deny a friend request which never existed!");
			$this->view->publish();
		}
		
		R::exec("DELETE FROM users_friendrequest WHERE id = ?", array($_GET['id']));
		
		$this->load_view("error");
		$this->view->bind("error->title", "Friend Accept"); 
		$this->view->bind("error->message", "You have denied that friend request!");
		$this->view->publish();
	}
	
	public function remove() {
	
		if (!Session::isAuthed()) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend Remove"); 
			$this->view->bind("error->message", "You can't perform this action when you're not logged in!");
			$this->view->publish();
			return;
		} 
	
		if (!isset($_GET['username'])) {
			$_GET['username'] = "";
		}
		
		if (!UserDao::isFriend($_GET['username'], Session::auth()->username)) {
			$this->load_view("error");
			$this->view->bind("error->title", "Friend Accept"); 
			$this->view->bind("error->message", "You have tried to accept a friend request which never existed!");
			$this->view->publish();
			return;
		}
	
		R::exec("DELETE FROM users_friends WHERE user = ? AND friend = ?", array(Session::auth()->username, $_GET['username']));	
		R::exec("DELETE FROM users_friends WHERE user = ? AND friend = ?", array($_GET['username'], Session::auth()->username));
		
		$this->load_view("error");
		$this->view->bind("error->title", "Friend Remove"); 
		$this->view->bind("error->message", "You have removed " . $_GET['username'] . " as a friend!");
		$this->view->publish();
	}
}