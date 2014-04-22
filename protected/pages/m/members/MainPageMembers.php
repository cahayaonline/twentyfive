<?php

class MainPageMembers extends MainPageSystem {
	/**
	* member id
	*/
	public $member_id;    
	public function onLoad ($param) {		
		parent::onLoad($param);			
		$this->showMembers=true;
        $this->member_id=addslashes(trim($this->request['id']));
		$this->createObjMember();		               
        if (!$this->IsPostBack&&!$this->IsCallBack) {
            
        }
	}
}
?>