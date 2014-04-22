<?php
prado::using ('Application.pages.m.members.MainPageMembers');
prado::using ('Application.lib.NModalPanel.NModalPanel');
class BagiBonusDeposit extends MainPageMembers {    
	public function onLoad($param) {		
		parent::onLoad($param);								
        $this->showBagiBonusDeposit=true;        
        $this->createObjFinance();        
		if (!$this->IsPostBack&&!$this->IsCallBack) {            				            
            if (!isset($_SESSION['currentPageBagiBonusDeposit'])||$_SESSION['currentPageBagiBonusDeposit']['page_name']!='m.member.BagiBonusDeposit') {                
                $_SESSION['currentPageBagiBonusDeposit']=array('page_name'=>'m.member.BagiBonusDeposit','periodeawal'=>date('Y-m-01'),'datamember'=>array(),'search'=>false);												
            }
            $_SESSION['currentPageBagiBonusDeposit']['search']=false;
            $this->cmbPeriodAwal->Text=$this->TGL->tanggal('m-Y',$_SESSION['currentPageBagiBonusDeposit']['periodeawal']);            
            $this->populateData();
                        
		}
	}      
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageBagiBonusDeposit']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageBagiBonusDeposit']['search']);
	}
    public function filterRecord ($sender,$param) {
        $_SESSION['currentPageBagiBonusDeposit']['search']=true;
        $_SESSION['currentPageBagiBonusDeposit']['periodeawal']=date('Y-m-01',$this->cmbPeriodAwal->TimeStamp);
        $this->populateData($_SESSION['currentPageBagiBonusDeposit']['search']);
    }
    public function populateData($search=false) {	
        $bulan=$this->TGL->tanggal('Y-m',$_SESSION['currentPageBagiBonusDeposit']['periodeawal']);                    
        $str = "SELECT m.member_id,ibo,member_name,lft,rgt FROM members m WHERE m.member_id IN (SELECT member_id FROM omset WHERE DATE_FORMAT(date_modified,'%Y-%m')='$bulan' AND udah_dibagi=0)";
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
            $jumlah_baris=$this->DB->getCountRowsOfTable ("members m WHERE member_id IN (SELECT member_id FROM omset WHERE DATE_FORMAT(date_modified,'%Y-%m')='$bulan' AND udah_dibagi=0) $cluasa",'m.member_id');        
            $str = "$str $cluasa";
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("members m WHERE member_id IN (SELECT member_id FROM omset WHERE DATE_FORMAT(date_modified,'%Y-%m')='$bulan' AND udah_dibagi=0)",'m.member_id');        
        }
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageBagiBonusDeposit']['page_num'];				
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageBagiBonusDeposit']['page_num']=0;}
		$str = "$str ORDER BY member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','lft','rgt'));
		$r=$this->DB->getRecord($str,$offset+1);        
        $result=array(); 
        $lastday=date('Y-m-t',strtotime ($_SESSION['currentPageBagiBonusDeposit']['periodeawal']));
        $this->finance->setPeriode($_SESSION['currentPageBagiBonusDeposit']['periodeawal'],$lastday);
		foreach ($r as $k=>$v) {   
            $this->finance->dataMember=array('member_id'=>$v['member_id'],'lft'=>$v['lft'],'rgt'=>$v['rgt']);
            $totalomset=$this->finance->getTotalOmset();
            $v['totalomset']=$this->finance->toRupiah($totalomset);            
            $omsetbelanja=$this->finance->getTotalOmsetBelanja ();
            $v['omsetbelanja']=$this->finance->toRupiah($omsetbelanja);
            $groupright=$this->finance->getTotalOmset ('right');
            $v['totalomsetgroupkanan']=$this->finance->toRupiah($groupright);
            $groupleft=$this->finance->getTotalOmset ('left'); 
            $v['totalomsetgroupkiri']=$this->finance->toRupiah($groupleft);
            $bonuspribadi=$this->finance->calculateBonusPribadi($totalomset);
            $bonusgroup=$this->finance->calculateBonusGroup($groupright,$groupleft);  
            $totalbonus=$bonuspribadi+$bonusgroup;
            $totalbonusdeposit=$this->finance->calculateBonusDeposit($totalbonus);
            $v['bonusdeposit']=$totalbonusdeposit;
            $v['totalbonusdeposit']=$this->finance->toRupiah($totalbonusdeposit);            
            $result[$k]=$v;
        }
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();		
	} 
    public function bagiDeposit($sender,$param) {
        $this->idProcess='add';
        $id=$this->getDataKeyField($sender, $this->RepeaterS);
        $bonusdeposit=$sender->CommandParameter;
        $this->finance->setMemberID($id,true,1);
        $accountbalance=$this->finance->getAccountBalance();
        $this->dataMember=$this->finance->dataMember;
        $this->dataMember['accountbalance']=$accountbalance;
        $this->txtJumlahDeposit->Text=$this->finance->toRupiah($bonusdeposit);
        $_SESSION['currentPageBagiBonusDeposit']['datamember']=array('member_id'=>$id,'bonusdeposit'=>$bonusdeposit);
    }
    public function bagiMasal ($sender,$param) {
        $bool=false;
        foreach ($this->RepeaterS->Items as $komponen) {
            if ($komponen->chkChecked->checked) {
                $bool=true;
                $item=$komponen->chkChecked->getNamingContainer();
                $member_id=$this->RepeaterS->DataKeys[$item->getItemIndex()];
                $jumlah=$komponen->btnBagi->CommandParameter;
                $this->finance->setMemberID($member_id);                               
                $accountbalance=$this->finance->getAccountBalance('all');                                    
                $this->finance->doExpiringBonus();
                $saldo_deposit=$accountbalance['saldo_deposit']+$jumlah;    
                $str = "INSERT INTO deposit (member_id,jumlah_transfer,tanggal_transfer,jenis,sisa_bonus,saldo_deposit) VALUES ($member_id,$jumlah,NOW(),'bonus',$jumlah,$saldo_deposit)";                            
                $this->DB->query('BEGIN'); 
                if ($this->DB->insertRecord($str)) {                 
                    $bulan=$this->TGL->tanggal('Y-m',$_SESSION['currentPageBagiBonusDeposit']['periodeawal']); 
                    $deposit=$this->finance->toRupiah($jumlah);
                    $aktivitas="Bagi bonus deposit $deposit bulan ".$this->TGL->tanggal('F-Y',$_SESSION['currentPageBagiBonusDeposit']['periodeawal']); ;                
                    $strmutasi = "INSERT INTO mutasi (member_id,process,reference,aktivitas,kredit,accountbalance,date_activity) VALUES ($member_id,'addbonusdeposit',LAST_INSERT_ID(),'$aktivitas',$jumlah,$saldo_deposit,NOW())";                   
                    $this->DB->insertRecord($strmutasi);
                    $strudahdibagi = "UPDATE omset SET udah_dibagi=1 WHERE member_id=$member_id AND DATE_FORMAT(date_modified,'%Y-%m')='$bulan'";                    
                    $this->DB->updateRecord($strudahdibagi);
                    $this->DB->query('COMMIT');                                    
                }else {
                    $this->DB->query('ROLLBACK');
                } 
            }
        }
        if ($bool)
            $this->redirect('m.members.BagiBonusDeposit');
        else
            $this->modalPanel->Show();
        
    }
    public function saveData($sender,$param) {
        if ($this->IsValid){
            $jumlah=$this->session['currentPageBagiBonusDeposit']['datamember']['bonusdeposit'];
            $member_id=$this->session['currentPageBagiBonusDeposit']['datamember']['member_id'];
            $this->finance->setMemberID($member_id);
            $accountbalance=$this->finance->getAccountBalance('all');            
            $this->finance->doExpiringBonus();
            $saldo_deposit=$accountbalance['saldo_deposit']+$jumlah;    
            $str = "INSERT INTO deposit (member_id,jumlah_transfer,tanggal_transfer,jenis,sisa_bonus,saldo_deposit) VALUES ($member_id,$jumlah,NOW(),'bonus',$jumlah,$saldo_deposit)";            
            $this->DB->query('BEGIN'); 
            if ($this->DB->insertRecord($str)) {                 
                $bulan=$this->TGL->tanggal('Y-m',$_SESSION['currentPageBagiBonusDeposit']['periodeawal']); 
                $deposit=$this->finance->toRupiah($jumlah);
                $aktivitas="Bagi bonus deposit $deposit bulan ".$this->TGL->tanggal('F-Y',$_SESSION['currentPageBagiBonusDeposit']['periodeawal']); ;                
                $strmutasi = "INSERT INTO mutasi (member_id,process,reference,aktivitas,kredit,accountbalance,date_activity) VALUES ($member_id,'addbonusdeposit',LAST_INSERT_ID(),'$aktivitas',$jumlah,$saldo_deposit,NOW())";
                $this->DB->insertRecord($strmutasi);
                $strudahdibagi = "UPDATE omset SET udah_dibagi=1 WHERE member_id=$member_id AND DATE_FORMAT(date_modified,'%Y-%m')='$bulan'";
                $this->DB->updateRecord($strudahdibagi);
                $this->DB->query('COMMIT');                
                $this->redirect('m.members.BagiBonusDeposit');
            }else {
                $this->DB->query('ROLLBACK');
            }            
        }
    }
}
		
