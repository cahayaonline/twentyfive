<?php
class Deposit extends MainPageAccount {
	public function onLoad($param) {		
		parent::onLoad($param);	
        $this->showMembers = true;
        $this->showDeposit=true;
        $this->createObjFinance();
        if (!$this->IsPostBack&&!$this->IsCallBack) {                        
            if (!isset($_SESSION['currentPageDeposit'])||$_SESSION['currentPageDeposit']['page_name']!='a.members.Deposit') {
                $_SESSION['currentPageDeposit']=array('page_name'=>'a.members.Deposit','page_num'=>0,'datamember'=>$this->Pengguna->getDataUser(),'filter_date'=>date('Y-m'));												                              
            }       
            $this->processDetails();
                    
        }
	}   
    private function processDetails() {        
        $this->dataMember=$this->session['currentPageDeposit']['datamember'];      
        $this->finance->dataMember=$this->dataMember;
        $this->finance->setMemberID($this->dataMember['member_id']);
        $accountbalance=$this->finance->getAccountBalance();
        $_SESSION['currentPageDeposit']['datamember']['accountbalance']=$accountbalance;        
        $this->cmbFilterMonth->Text=$_SESSION['currentPageDeposit']['filter_date'];
        $deposit_akhir=$this->finance->getAccountBalance('all');
        $this->literalDepositBonus->Text=$this->finance->toRupiah($deposit_akhir['sisa_bonus']);
        $this->literalSaldoDeposit->Text=$this->finance->toRupiah($deposit_akhir['saldo_deposit']);
        $this->populateMutasi();
    }
    public function changeFilterDate($sender,$param) {                
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
}
