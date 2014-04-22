<?php

class Logout extends MainPageSystem {
		
	public function onLoad ($param) {		
		if (!$this->User->isGuest) {			
			$this->Application->getModule ('auth')->logout();			
			$url=$this->Service->constructUrl('Home');
			$this->Response->redirect($url);
		}else {
			$url=$this->Service->constructUrl('a.Login');
			$this->Response->redirect($url);
		}
	}	
}
?>