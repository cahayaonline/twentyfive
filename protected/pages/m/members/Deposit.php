<?php
prado::using ('Application.pages.m.members.MainPageMembers');
class Deposit extends MainPageMembers {
	public function onLoad($param) {		
		parent::onLoad($param);				
        $this->showDeposit=true;
        $this->createObjFinance();
        if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (isset($this->session['currentPageDeposit']['datamember']['member_id'])){
                $this->dataMember=$this->session['currentPageDeposit']['datamember'];
                $this->processDetails();
            }else{
                if (!isset($_SESSION['currentPageDeposit'])||$_SESSION['currentPageDeposit']['page_name']!='m.sales.Deposit') {
                    $_SESSION['currentPageDeposit']=array('page_name'=>'m.sales.Deposit','page_num'=>0,'datamember'=>array(),'filter_date'=>date('Y-m'));												
                }			
                $this->populateData();
            }        
        }
	}   
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageDeposit']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageDeposit']['search']);
	}
    public function populateData($search=false) {	        
        $str = 'SELECT d.member_id,m.ibo,m.member_name,d.sisa_bonus,d.saldo_deposit FROM deposit d LEFT JOIN members m ON (m.member_id=d.member_id) WHERE iddeposit=(SELECT MAX(iddeposit) FROM deposit d2 WHERE d2.member_id=m.member_id)';
        if ($search){            
            $membername=$this->membername->Text;
            $cluasa=" AND member_name LIKE '%$membername%'";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("deposit d LEFT JOIN members m ON (m.member_id=d.member_id) WHERE iddeposit=(SELECT MAX(iddeposit) FROM deposit d2 WHERE d2.member_id=m.member_id) $cluasa",'member_id');
            $str = "$str $cluasa";
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('deposit d LEFT JOIN members m ON (m.member_id=d.member_id) WHERE iddeposit=(SELECT MAX(iddeposit) FROM deposit d2 WHERE d2.member_id=m.member_id)','d.member_id');
        }
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageDeposit']['page_num'];				
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageDeposit']['page_num']=0;}
		$str = "$str ORDER BY d.tanggal_transfer DESC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('iddeposit','member_id','ibo','member_name','sisa_bonus','saldo_deposit'));
		$r=$this->DB->getRecord($str,$offset+1);      
		
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();		
		
	}
    public function suggestIBO($sender,$param) {
        $this->errorMessage->Text='';
        $this->hiddenmemberid->Value='';
        $id=$param->getToken();
        $str = "SELECT member_id,ibo,member_name FROM members WHERE ibo LIKE '$id%'";
        $this->DB->setFieldTable(array('member_id','ibo','member_name'));
        $r=$this->DB->getRecord($str);            
        $sender->DataSource=$r;
        $sender->dataBind(); 
    }
    public function iboSuggetionSelected($sender,$param) {
        $this->setData($this->txtIBO,$sender,$param);
    }
    public function suggestMember($sender,$param) {
        $this->errorMessage->Text='';
        $id=$param->getToken();
        $str = "SELECT member_id,ibo,member_name FROM members WHERE member_name LIKE '$id%'";
        $this->DB->setFieldTable(array('member_id','ibo','member_name'));
        $r=$this->DB->getRecord($str);            
        $sender->DataSource=$r;
        $sender->dataBind(); 
    }
    public function memberSuggetionSelected($sender,$param) {
        $this->setData($this->txtMemberName,$sender,$param);
    }
    private function setData($inputbox,$sender,$param) {
        $id=$sender->Suggestions->DataKeys[$param->selectedIndex];
        $this->hiddenmemberid->Value=$id;
        $text=explode('-',$inputbox->Text);
        $this->txtIBO->Text=$text[0];
        $this->txtMemberName->Text=$text[1];
        $this->btnAddDeposit->Enabled=true;                
        $this->btnAddDeposit->CssClass='button'; 
    }
    public function addDeposit($sender,$param) {
        if ($this->IsValid) {
            if ($this->hiddenmemberid->Value=='') {
                $this->errorMessage->Text="<p id='error' class='info'><span class='info_inner'>Unknown Member</span></p>";
            }else {
                $member_id=$this->hiddenmemberid->Value;               
                $_SESSION['currentPageDeposit']['datamember']=array('member_id'=>$member_id,'member_name'=>$this->txtMemberName->Text,'ibo'=>$this->txtIBO->Text);            
                $this->redirect('m.members.Deposit');
            }
        }
    }
    public function viewDepositMember($sender,$param) {
        if ($this->IsValid) {                
            $member_id=$this->getDataKeyField($sender,$this->RepeaterS);
            $this->finance->setMemberID($member_id,true,1);
            $accountbalance=$this->finance->getAccountBalance();
            $this->finance->dataMember['accountbalance']=$accountbalance;
            $_SESSION['currentPageDeposit']['datamember']=$this->finance->dataMember;            
            $this->redirect('m.members.Deposit');            
        }
    }
    public function closeDeposit($sender,$param) {        
        unset($_SESSION['currentPageDeposit']);
        $this->redirect('m.members.Deposit');
    }
    private function processDetails() {
        $this->idProcess='add';
        $this->dataMember=$this->session['currentPageDeposit']['datamember'];       
        $this->finance->setMemberID($this->dataMember['member_id'],true,1);
        $this->cmbFilterMonth->Text=$_SESSION['currentPageDeposit']['filter_date'];
        $deposit_akhir=$this->finance->getAccountBalance('all');
        $this->literalDepositBonus->Text=$this->finance->toRupiah($deposit_akhir['sisa_bonus']);
        $this->literalSaldoDeposit->Text=$this->finance->toRupiah($deposit_akhir['saldo_deposit']);
        $this->populateMutasi();
    }
    public function changeFilterDate($sender,$param) {        
        $this->idProcess='add';
        $_SESSION['currentPageDeposit']['filter_date']=$this->cmbFilterMonth->Text;
        $this->populateMutasi();
    }
    private function populateMutasi() {
        $tanggal=$_SESSION['currentPageDeposit']['filter_date'];
        $member_id=$this->session['currentPageDeposit']['datamember']['member_id'];
        $str="SELECT idmutasi,aktivitas,kredit,debit,accountbalance,date_activity FROM mutasi WHERE member_id=$member_id AND date_activity BETWEEN '$tanggal-01' AND '$tanggal-31'  ORDER BY date_activity ASC";
        $this->DB->setFieldTable(array('idmutasi','aktivitas','kredit','debit','accountbalance','date_activity'));
        $r=$this->DB->getRecord($str); 
        $this->RepeaterMutasi->DataSource=$r;
        $this->RepeaterMutasi->dataBind();
    }
    public function saveData($sender,$param) {
        if ($this->IsValid){
            $member_id=$this->session['currentPageDeposit']['datamember']['member_id'];
            $this->finance->setMemberID($member_id);
            $account_balance=$this->finance->getAccountBalance('all');
            $deposit_terakhir=$account_balance['saldo_deposit'];
            $sisa_bonus=$account_balance['sisa_bonus'];
            $jumlah_deposit=$this->finance->toInteger($this->txtJumlahDeposit->Text);            
            $accountbalance=$jumlah_deposit+$deposit_terakhir;            
            $str = "INSERT INTO deposit (member_id,jumlah_transfer,sisa_bonus,saldo_deposit,tanggal_transfer) VALUES ($member_id,$jumlah_deposit,$sisa_bonus,$accountbalance,NOW())";            
            $this->DB->query('BEGIN'); 
            if ($this->DB->insertRecord($str)) {                              
                $deposit=$this->finance->toRupiah($jumlah_deposit);
                $aktivitas="Menambah deposit tunai $deposit";                
                $strmutasi = "INSERT INTO mutasi (member_id,process,reference,aktivitas,kredit,accountbalance,date_activity) VALUES ($member_id,'adddeposittunai',LAST_INSERT_ID(),'$aktivitas',$jumlah_deposit,$accountbalance,NOW())";
                $this->DB->insertRecord($strmutasi);
                $this->DB->query('COMMIT');                
                $this->redirect('m.members.Deposit');
            }else {
                $this->DB->query('ROLLBACK');
            }            
        }
    }
}
