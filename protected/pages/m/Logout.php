<?php

class Logout extends MainPageSystem {
		
	public function onLoad ($param) {		
		if (!$this->User->isGuest) {			
			$this->Application->getModule ('auth')->logout();			
			$url=$this->Service->constructUrl('m.Login');
			$this->Response->redirect($url);
		}else {
			$url=$this->Service->constructUrl('m.Login');
			$this->Response->redirect($url);
		}
	}	
}
?>