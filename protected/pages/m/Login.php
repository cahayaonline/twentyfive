<?php

class Login extends MainPageSystem { 
    public function OnPreInit ($param) {	
		parent::onPreInit ($param);	
		$this->MasterClass="Application.layouts.LoginTemplates";		
		$this->Theme="defaultsystem";
	}
	public function onLoad($param) {		
		parent::onLoad($param);				
		if (!$this->IsPostBack&&!$this->IsCallBack) {
		}
	}
	private function checkUsernameAndPassword($username) {		
		$auth = $this->Application->getModule ('auth');			
		$password = addslashes($this->txtPassword->Text);
		if ($auth->login ("$username/m",$password)){			
			return true;			
		}else {
			throw new Exception ('<br />Login Details Incorrect. Please try again.');						
		}
	}
	public function doLogin ($sender,$param) {
		if ($this->IsValid) {
			try {									
				$this->checkUsernameAndPassword(addslashes($this->txtUsername->Text));						
				$pengguna=$this->getLogic('Users');	
				$pengguna->redirect('m.Home');
			}catch (Exception $e) {				
				$message='<div class="alert alert-error">
                    <strong>Error!</strong>
                    '.$e->getMessage().'</div>';
				$this->errormessage->Text=$message;					
				$param->IsValid=false;		
			}	
		}
	}
}
?>