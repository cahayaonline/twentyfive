<?php

class MainPage extends TPage {
	/**
	* id process
	*/
	public $idProcess;	
	/**
	* Object Variable "Database"
	*
	*/
	public $DB;
	/**
	* Object Variable "DMaster"
	*
	*/
	public $DMaster;
	/**
	* Object Variable "Setup"
	*
	*/
	public $setup;		
	/**
	* Object Variable "Tanggal"
	*
	*/
	public $TGL;
	/**
	* Object Variable "Member"
	*
	*/
	public $member;
        /**
	* Object Variable "Product"
	*
	*/
	public $product;
    /**
	* Object Variable "Finance"
	*
	*/
	public $finance;
	/**
	* Object Variable "User"
	*
	*/
	public $Pengguna;		
	/**
	* untuk variabel session
	*/
	public $session;	
	/**
	* data member
	*/
	public $dataMember = array();	        
    /**
	* data toko
	*/
	public $dataToko = array();	        
    /**
	* data product
	*/
	public $dataProduct = array();    
	public function OnPreInit ($param) {	
		parent::onPreInit ($param);
		//instantiasi database		
		$this->DB = $this->Application->getModule ('db')->getLink();
		$this->MasterClass="Application.layouts.default.Main";		
		$this->Theme="default";
	}
	public function onLoad ($param) {		
		parent::onLoad($param);		
		$this->session = new THttpSession;
		$this->session->open();			
		
		//instantiasi user
		$this->Pengguna = $this->getLogic('Users');
		$this->Pengguna->checkPageUser($this->Page->getPagePath());					
// 		$this->Pengguna->setThemesObject($this->Themes);		
		//instantiasi fungsi tanggal
		$this->TGL = $this->getLogic ('Penanggalan');
        //instantiasi fungsi setup
        $this->setup = $this->getLogic('Setup');        
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
	* id proses tambah, delete, update,show
	*/
	protected function setIdProcess ($sender,$param) {		
		$this->idProcess=$sender->getId();
	}
	
	/**
	* add panel
	* @return boolean
	*/
	protected function getAddProcess ($disabletoolbars=true) {
		if ($this->idProcess == 'add') {			
			if ($disabletoolbars)$this->disableToolbars();
			return true;
		}else {
			return false;
		}
	}
	
	/**
	* edit panel
	* @return boolean
	*/
	protected function getEditProcess ($disabletoolbars=true) {
		if ($this->idProcess == 'edit') {			
			if ($disabletoolbars)$this->disableToolbars();
			return true;
		}else {
			return false;
		}

	}
	
	/**
	* view panel
	* @return boolean
	*/
	protected function getViewProcess ($disabletoolbars=true) {
		if ($this->idProcess == 'view') {
			if ($disabletoolbars)$this->disableToolbars();			
			return true;
		}else {
			return false;
		}

	}
	
	/**
	* default panel
	* @return boolean
	*/
	protected function getDefaultProcess () {
		if ($this->idProcess == 'add' || $this->idProcess == 'edit'|| $this->idProcess == 'view') {
			return false;
		}else {
			return true;
		}
	}	
	/**
	* digunakan untuk mendapatkan sebuah data key dari repeater
	* @return data key
	*/
	protected function getDataKeyField($sender,$repeater) {
		$item=$sender->getNamingContainer();
		return $repeater->DataKeys[$item->getItemIndex()];
	}
    /**
	* digunakan untuk mendapatkan index
	*/
    public function getIndexFromList($data,$field,$id) {
        $indexdata=-1;        
        if (is_array($data)) {
            foreach($data as $index=>$row){                            
                if($row[$field]==$id) {                    
                    $indexdata=$index;                   
                }
            }
        }        
        return $indexdata;
    }
    /**
	* Redirect
	*/
	protected function redirect ($page,$param=array()) {
		$this->Response->Redirect($this->Service->ConstructUrl($page,$param));	
	}
	public function createObjDMaster () {
		$this->DMaster = $this->getLogic('DMaster');
	}
	public function createObjMember () {
		$this->member = $this->getLogic('Member');
	}
    public function createObjProduct () {
		$this->product = $this->getLogic('Product');
	}
    public function createObjFinance () {
		$this->finance = $this->getLogic('Finance');
	}
}
?>