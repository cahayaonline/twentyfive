<?php
prado::using ('Application.pages.m.members.MainPageMembers');
prado::using ('Application.lib.NModalPanel.NModalPanel');
class ExpiredBonus extends MainPageMembers {    
	public function onLoad($param) {		
		parent::onLoad($param);								
        $this->showBonusExpired=true;        
        $this->createObjFinance();        
		if (!$this->IsPostBack&&!$this->IsCallBack) {            				            
            if (!isset($_SESSION['currentPageBonusExpired'])||$_SESSION['currentPageBonusExpired']['page_name']!='m.member.ExpiredBonus') {                                                
                $_SESSION['currentPageBonusExpired']=array('page_name'=>'m.member.ExpiredBonus','datamember'=>array(),'search'=>false,'tanggal_bonus_expired'=>date('Y-m-d'),'bonus_transfer_tanggal'=>date('Y-m-d'),'bonus_transfer_period'=>date('Y-m'),'tanggal_bonus_expired'=>date('Y-m-d'),'bonus_expire_selected'=>true,'bonus_transfer_periode_selected'=>false,'bonus_transfer_tanggal_selected'=>false);												
            }            
            $this->labelHeader->Text='#Limit 31 Hari';   
            $this->cmbTanggalBonusExpire->Text=$this->TGL->tukarTanggal($_SESSION['currentPageBonusExpired']['tanggal_bonus_expired']);
            $this->cmbPeriodTransfer->Text=$this->TGL->tanggal('m-Y',$_SESSION['currentPageBonusExpired']['bonus_transfer_period'].'-01');
            $this->cmbTanggalTransfer->Text=$this->TGL->tukarTanggal($_SESSION['currentPageBonusExpired']['bonus_transfer_tanggal']);
            $this->rdPeriodTransfer->Checked=$_SESSION['currentPageBonusExpired']['bonus_transfer_tanggal_selected'];
            $this->rdTanggalTransfer->Checked=$_SESSION['currentPageBonusExpired']['bonus_transfer_periode_selected'];
            $this->rdTanggalExpire->Checked=$_SESSION['currentPageBonusExpired']['bonus_expire_selected'];
            $_SESSION['currentPageBonusExpired']['search']=false;            
            $this->populateData();                        
		}
	}      
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageBonusExpired']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageBonusExpired']['search']);
	}
    public function filterRecord ($sender,$param) {
        $_SESSION['currentPageBonusExpired']['search']=true;                
        $_SESSION['currentPageBonusExpired']['tanggal_bonus_expired']=date('Y-m-d',$this->cmbTanggalBonusExpire->TimeStamp);
        $_SESSION['currentPageBonusExpired']['bonus_transfer_period']=date('Y-m',$this->cmbPeriodTransfer->TimeStamp);
        $_SESSION['currentPageBonusExpired']['bonus_transfer_tanggal']=date('Y-m-d',$this->cmbTanggalTransfer->TimeStamp);
        $_SESSION['currentPageBonusExpired']['bonus_transfer_tanggal_selected']=$this->rdPeriodTransfer->Checked;
        $_SESSION['currentPageBonusExpired']['bonus_transfer_periode_selected']=$this->rdTanggalTransfer->Checked;
        $_SESSION['currentPageBonusExpired']['bonus_expire_selected']=$this->rdTanggalExpire->Checked;
        $this->populateData($_SESSION['currentPageBonusExpired']['search']);
    }
    public function populateData($search=false) {	        
        if ($this->rdTanggalExpire->Checked) {            
            $tanggalexpired=date('Y-m-d',$this->cmbTanggalBonusExpire->TimeStamp);
            $str = "SELECT d.iddeposit,m.member_id,m.ibo,m.member_name,d.jumlah_transfer,d.tanggal_transfer,d.tanggal_transfer + INTERVAL 31 DAY AS expire_date FROM members m, deposit d WHERE m.member_id=d.member_id AND d.jenis='bonus' AND d.expires=0 AND (TO_DAYS('$tanggalexpired')-TO_DAYS(d.tanggal_transfer))>31";               
            $str_baris = "members m, deposit d WHERE m.member_id=d.member_id AND d.jenis='bonus' AND d.expires=0 AND (TO_DAYS('$tanggalexpired')-TO_DAYS(d.tanggal_transfer))>31";                       
        }elseif ($this->rdPeriodTransfer->Checked) {
            $period=$_SESSION['currentPageBonusExpired']['bonus_transfer_period'];
            $tanggalexpired=date('Y-m-d');
            $str = "SELECT d.iddeposit,m.member_id,m.ibo,m.member_name,d.jumlah_transfer,d.tanggal_transfer,d.tanggal_transfer + INTERVAL 31 DAY AS expire_date FROM members m, deposit d WHERE m.member_id=d.member_id AND d.jenis='bonus' AND d.expires=0 AND (TO_DAYS('$tanggalexpired')-TO_DAYS(d.tanggal_transfer))>31 AND DATE_FORMAT(tanggal_transfer,'%Y-%m')='$period'";               
            $str_baris = "members m, deposit d WHERE m.member_id=d.member_id AND d.jenis='bonus' AND d.expires=0 AND (TO_DAYS('$tanggalexpired')-TO_DAYS(d.tanggal_transfer))>31 AND DATE_FORMAT(tanggal_transfer,'%Y-%m')='$period'";                       
        }elseif ($this->rdTanggalTransfer->Checked) {
            $tanggal_transfer=$_SESSION['currentPageBonusExpired']['bonus_transfer_tanggal'];
            $tanggalexpired=date('Y-m-d');
            $str = "SELECT d.iddeposit,m.member_id,m.ibo,m.member_name,d.jumlah_transfer,d.tanggal_transfer,d.tanggal_transfer + INTERVAL 31 DAY AS expire_date FROM members m, deposit d WHERE m.member_id=d.member_id AND d.jenis='bonus' AND d.expires=0 AND (TO_DAYS('$tanggalexpired')-TO_DAYS(d.tanggal_transfer))>31 AND DATE_FORMAT(tanggal_transfer,'%Y-%m-%d')='$tanggal_transfer'";               
            $str_baris = "members m, deposit d WHERE m.member_id=d.member_id AND d.jenis='bonus' AND d.expires=0 AND (TO_DAYS('$tanggalexpired')-TO_DAYS(d.tanggal_transfer))>31 AND DATE_FORMAT(tanggal_transfer,'%Y-%m-%d')='$tanggal_transfer'";                       
        }        
        if ($search){            
            $membername=  addslashes(trim($this->membername->Text));
            $ibo=addslashes(trim($this->noibo->Text));             
            if ($ibo == '' && $membername == '') {
                $cluasa='';
            }elseif($ibo == '' && $membername != ''){
                $cluasa="AND member_name LIKE '%$membername%'";
            }elseif($ibo != '' && $membername == ''){ 
                $cluasa="AND ibo='$ibo'";
            }else {
                $cluasa="AND ibo=='%$ibo%' AND member_name LIKE '%$membername%'";
            }
            $jumlah_baris=$this->DB->getCountRowsOfTable ("$str_baris $cluasa",'d.iddeposit');        
            $str = "$str $cluasa";
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ($str_baris,'d.iddeposit');        
        }
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageBonusExpired']['page_num'];				
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageBonusExpired']['page_num']=0;}
		$str = "$str ORDER BY member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','iddeposit','ibo','member_name','jumlah_transfer','tanggal_transfer','expire_date'));
		$r=$this->DB->getRecord($str,$offset+1);                
        $result=array();
        while (list($k,$v)=each($r)) {
            $this->finance->setMemberID($v['member_id']);
            $v['sisa_bonus']=$this->finance->getAccountBalance('sisa_bonus');
            $result[$k]=$v;
        }
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();		
	} 
    public function setExpired($sender,$param) {
        $iddeposit=$this->getDataKeyField($sender, $this->RepeaterS);        
        $member_id=$sender->CommandParameter;
        $this->finance->setMemberID($member_id);
        $accountbalance=$this->finance->getAccountBalance('all');        
        $this->DB->query('BEGIN');         
        if ($this->finance->doExpiringBonus()) {                                         
            $deposit_terakhir=$accountbalance['saldo_deposit'];
            $sisa_bonus=$accountbalance['sisa_bonus'];
            $saldo_deposit=$deposit_terakhir-$sisa_bonus; 
            $jumlah_bonus=$this->finance->toRupiah($this->finance->getJumlahBonus());                       
            $sisa_bonus2=$this->finance->toRupiah($sisa_bonus);                       
            $aktivitas="pembagian bonus deposit yang $jumlah_bonus di expired-kan. Sisa bonus terakhir ($sisa_bonus2)";            
            $strmutasi = "INSERT INTO mutasi (member_id,process,reference,aktivitas,debit,accountbalance,date_activity) VALUES ($member_id,'expiredbonusdeposit',$iddeposit,'$aktivitas',$sisa_bonus,$saldo_deposit,NOW())";                     
            $this->DB->insertRecord($strmutasi);            
            $this->DB->query('COMMIT');                                    
        }else {
            $this->DB->query('ROLLBACK');            
        }        
        $this->redirect('m.members.ExpiredBonus');
    }
    public function setExpiredMasal ($sender,$param) {
        $bool=false;
        foreach ($this->RepeaterS->Items as $komponen) {
            if ($komponen->chkChecked->checked) {
                $bool=true;
                $item=$komponen->chkChecked->getNamingContainer();
                $iddeposit=$this->RepeaterS->DataKeys[$item->getItemIndex()];
                $member_id=$komponen->btnExpired->CommandParameter;
                $this->finance->setMemberID($member_id);
                $accountbalance=$this->finance->getAccountBalance('all');        
                $this->DB->query('BEGIN');         
                if ($this->finance->doExpiringBonus()) {                                         
                    $deposit_terakhir=$accountbalance['saldo_deposit'];
                    $sisa_bonus=$accountbalance['sisa_bonus'];
                    $saldo_deposit=$deposit_terakhir-$sisa_bonus; 
                    $jumlah_bonus=$this->finance->toRupiah($this->finance->getJumlahBonus());                       
                    $sisa_bonus2=$this->finance->toRupiah($sisa_bonus);                       
                    $aktivitas="pembagian bonus deposit yang $jumlah_bonus di expired-kan. Sisa bonus terakhir ($sisa_bonus2)";            
                    $strmutasi = "INSERT INTO mutasi (member_id,process,reference,aktivitas,debit,accountbalance,date_activity) VALUES ($member_id,'expiredbonusdeposit',$iddeposit,'$aktivitas',$sisa_bonus,$saldo_deposit,NOW())";                     
                    $this->DB->insertRecord($strmutasi);            
                    $this->DB->query('COMMIT');                                    
                }else {
                    $this->DB->query('ROLLBACK');            
                }        
            }
        }
        if ($bool)
            $this->redirect('m.members.ExpiredBonus');
        else
            $this->modalPanel->Show();
    }
}
