<?php
prado::using('Application.MainPage');
class MainPageAccount extends MainPage {	
    /**
	* show dasboard page
	*/
	public $showDashboard = false;	    
	/**
	* show member page 
	*/
	public $showMembers = false;
    /**
	* tab members home [members]
	*/
	public $showMembersHome=false;
	/**
	* tab add new member [members]
	*/
	public $showAddNewMember=false;
    /**
	* tab edit member [members]
	*/
	public $showEditMember=false;    
    /**
	* show deposito tunai [members]
	*/
	public $showDeposit=false;
    /**
	* tab konfirmasi deposito [members]
	*/
	public $showKonfirmasiDeposit=false;
    /**
	* show sales page 
	*/
	public $showSales = false;
    /**
     * menampilkan halaman home sales [sales]
     * @var boolean
     */
    public $showSalesHome=false;
    /**
     * menampilkan halaman new order  [sales]
     * @var boolean
     */
    public $showNewOrder=false;    
    /**
	* show omset member  [sales]
	*/
	public $showOmset=false;
    /**
     * menampilkan halaman pengembalian product  [sales]
     * @var boolean
     */
    public $showReturnProduct=false;   
	public function OnPreInit ($param) {	
		parent::onPreInit ($param);
		//instantiasi database		
		$this->DB = $this->Application->getModule ('db')->getLink();
		$this->MasterClass="Application.layouts.MainAccount";		
		$this->Theme="defaultsystem";
	}
	public function onLoad ($param) {		
		parent::onLoad($param);			
        $this->createObjMember();
        $this->createObjFinance();
        $this->dataMember=$this->Pengguna->getDataUser();         
        $this->member->setMemberID($this->dataMember['member_id'],false,2);
        $this->member->dataMember=$this->dataMember;   
        $this->finance->dataMember=$this->dataMember;
	}	
    
}
?>