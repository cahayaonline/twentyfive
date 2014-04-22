<?php
prado::using('Application.MainPage');
class MainPageSystem extends MainPage {	
    /**
	* show dasboard page
	*/
	public $showDashboard = false;
    /**
	* show member page
	*/
	public $showProducts = false;    
    /**
     * menampilkan halaman home product [products]
     * @var boolean
     */
    public $showProductHome=false;
    /**
     * menampilkan halaman summary product  [products]
     * @var boolean
     */
    public $showProductDetails=false;
    /**
     * menampilkan halaman tambah product [products]
     * @var boolean
     */
    public $showAddNewProduct=false; 
    /**
     * menampilkan halaman ubah product [products]
     * @var boolean
     */
    public $showEditNewProduct=false;   
    /**
     * menampilkan halaman kategori product [products]
     * @var boolean
     */
    public $showCategoryProduct=false;
	/**
	* show member page 
	*/
	public $showMembers = false;
    /**
	* show members home [members]
	*/
	public $showMembersHome=false;
	/**
	* show add new member [members]
	*/
	public $showAddNewMember=false;
    /**
	* show edit member [members]
	*/
	public $showEditMember=false;
    /**
	* show konfirmasi deposito [members]
	*/
	public $showKonfirmasiDeposit=false;    
    /**
	* show deposito tunai [members]
	*/
	public $showDeposit=false;    
	/**
	* show summary member [members]
	*/
	public $showSummaryMember=false;
    /**
	* show bagi bonus tunai [members]
	*/
	public $showBagiBonusTunai=false;
    /**
	* show bagi bonus deposit [members]
	*/
	public $showBagiBonusDeposit=false;
    /**
	* show bonus expired [members]
	*/
	public $showBonusExpired=false;
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
    /**
	* show reports page
	*/
	public $showReports = false;
    /**
	* show summary reports [report]
	*/
	public $showSummaryReport = false;    
    /**
	* show report bonus deposit [report]
	*/
	public $showReportBonusDeposit = false;            
    /**
	* show setting page
	*/
	public $showSetting = false;	    
    /**
	* show Global setting page
	*/
	public $showSettingGeneral = false;	    
    /**
	* show setup page
	*/
	public $showUsers = false;		
    /**
	* show setting bank
	*/
	public $showBank = false;
    /**
	* show clear cache page
	*/
	public $showCache = false;		
	public function OnPreInit ($param) {	
		parent::onPreInit ($param);
		//instantiasi database		
		$this->DB = $this->Application->getModule ('db')->getLink();
		$this->MasterClass="Application.layouts.MainSystem";		
		$this->Theme="defaultsystem";
	}
	public function onLoad ($param) {		
		parent::onLoad($param);			
	}	
    
}
?>