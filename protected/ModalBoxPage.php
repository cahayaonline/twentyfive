<?php

class ModalBoxPage extends MainPage {
	/**
	* Object Variable "Database"	
	*/
	public $DB;	
	
	/**
	* untuk variabel session
	*/
	public $session;		
	/**
	* Object Variable "Tanggal"	
	*/
	public $TGL;
	
	public function OnPreInit ($param) {	
		parent::onPreInit ($param);
		//instantiasi database		
		$this->DB = $this->Application->getModule ('db')->getLink();
		$this->MasterClass="Application.layouts.ModalBox";		
		$this->Theme="defaultsystem";		
	}
	public function onLoad ($param) {		
		parent::onLoad($param);		
		$this->session = new THttpSession;
		$this->session->open();
		//instantiasi fungsi tanggal
		$this->TGL = $this->getLogic ('Penanggalan');	
	}
	/**
	* mendapatkan lo object
	* @return obj	
	*/
	public function getLogic ($_class=null) {
		if ($_class === null)
			return $this->Application->getModule ('logic');
		else 
			return $this->Application->getModule ('logic')->getInstanceOfClass($_class);	
	}		
	/**
	* digunakan untuk mendapatkan sebuah data key dari repeater
	* @return data key
	*/
	protected function getDataKeyField($sender,$repeater) {
		$item=$sender->getNamingContainer();
		return $repeater->DataKeys[$item->getItemIndex()];
	}	
}
?>