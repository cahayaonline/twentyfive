<?php

class Home extends MainPageAccount {
	public function onLoad($param) {		
		parent::onLoad($param);		
		$this->showDashboard=true;              
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageDashboard'])||$_SESSION['currentPageDashboard']['page_name']!='a.Home') {
                $_SESSION['currentPageDashboard']=array('page_name'=>'a.Home','page_num_right'=>0,'page_num_left'=>0,'withdate'=>false,'periodeawal'=>date('Y-m-01'),'periodeakhir'=>date('Y-m-t'),'search'=>false,'datamember'=>array());												
            }            
            $_SESSION['currentPageMember']['search']=false;            
            $this->populateData();
		}
	}    
    public function populateData () {        
		$downline=$this->member->getCountDownline('split');                 
        $this->dataMember['left']=$downline['left'];
        $this->dataMember['right']=$downline['right'];                    
        $this->finance->setPeriode($_SESSION['currentPageDashboard']['periodeawal'],$_SESSION['currentPageDashboard']['periodeakhir']);        
        $this->finance->setMemberID($this->dataMember['member_id']);                
        $totalomset=$this->finance->getTotalOmset ();
        $omsetgroup=round(($totalomset/2),2);
        $this->dataMember['totalomset']=$totalomset;                
        $this->dataMember['omsetpribadigroupleft']=$omsetgroup;
        $this->dataMember['omsetpribadigroupright']=$omsetgroup;

        $groupright=$this->finance->getTotalOmset ('right');               
        $this->dataMember['totalomsetgroupright']=$groupright;

        $groupleft=$this->finance->getTotalOmset ('left');               
        $this->dataMember['totalomsetgroupleft']=$groupleft;

        $bonuspribadi=$this->finance->calculateBonusPribadi($totalomset);
        $this->dataMember['bonuspribadi']=$bonuspribadi;
        $bonusgroup=$this->finance->calculateBonusGroup($groupright,$groupleft);        
        $this->dataMember['bonusgroup']=$bonusgroup;

        $totalbonus=$bonuspribadi+$bonusgroup;
        $this->dataMember['totalbonus']=$totalbonus;
        $totalbonusdeposit=$this->finance->calculateBonusDeposit($totalbonus);        
        $this->dataMember['totalbonusdeposit']=$totalbonusdeposit;
        $totalbonuscash=$totalbonus < 2500000?0:0.75*$totalbonus;        

        $this->dataMember['totalbonuscash']=$totalbonuscash;
        
        $deposit_akhir=$this->finance->getAccountBalance('all');
        $this->literalDepositBonus->Text=$this->finance->toRupiah($deposit_akhir['sisa_bonus']);
        $this->literalSaldoDeposit->Text=$this->finance->toRupiah($deposit_akhir['saldo_deposit']);
        
        $latestmember=$this->member->getLastMember();
        $this->dataMember['latest_left']=$latestmember['left'];
        $this->dataMember['latest_right']=$latestmember['right'];                
        $_SESSION['currentPageDashboard']['datamember']=$this->dataMember;
        $this->populateDataRight();
        $this->populateDataLeft();
	}
    public function searchClient ($sender,$param) {
        $_SESSION['currentPageDashboard']['search']=true;
        if ($this->cmbPosisiFilter->Text==1) {//kiri
            $this->populateDataLeft($_SESSION['currentPageDashboard']['search']);
        }else {
            $this->populateDataRight($_SESSION['currentPageDashboard']['search']);
        }       
	}
    public function renderCallbackRight ($sender,$param) {
		$this->RepeaterRight->render($param->NewWriter);	
	}	
	public function Page_ChangedRight ($sender,$param) {
		$_SESSION['currentPageDashboard']['page_num_right']=$param->NewPageIndex;
		$this->populateDataRight();
	}
	protected function populateDataRight($search=false) {		   
        $member_id=$this->dataMember['member_id'];        
        $right=$this->dataMember['rgt'];
        $str = "SELECT member_id,ibo,member_name,lft,rgt,enabled,date_reg FROM members WHERE rgt=$right AND member_id > $member_id";
        if ($search) {
            $status=$this->cmbStatusFilter->Text;
            $cluasa="AND enabled=$status";            
            $ibo=addslashes($this->noiboFilter->Text);
            if ($ibo != '')$cluasa .= " AND ibo='$ibo'"; 
            $membername=addslashes($this->membernameFilter->Text);
            if ($membername != '')$cluasa .= " AND member_name LIKE '%$membername%'";
            $email=addslashes($this->emailFilter->Text);
            if ($email != '')$cluasa .= " AND email LIKE '%$email%'";            
            $str = "$str $cluasa";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("members WHERE rgt=$right AND member_id > $member_id $cluasa",'member_id');
        }else{
            $jumlah_baris=$this->DB->getCountRowsOfTable ("members WHERE rgt=$right AND member_id > $member_id",'member_id');            
        }
        $this->RepeaterRight->CurrentPageIndex=$_SESSION['currentPageDashboard']['page_num_right'];        		
		$this->RepeaterRight->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterRight->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterRight->PageSize;		
		$itemcount=$this->RepeaterRight->VirtualItemCount;
		$limit=$this->RepeaterRight->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=5;$_SESSION['currentPageDashboard']['page_num_right']=0;}
		$str = "$str ORDER BY date_reg DESC,member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','lft','rgt','enabled','date_reg'));
		$r=$this->DB->getRecord($str,$offset+1);
        $result=array();
        $this->finance->setPeriode($_SESSION['currentPageDashboard']['periodeawal'],$_SESSION['currentPageDashboard']['periodeakhir']);        
		foreach ($r as $k=>$v) {
            $this->finance->setMemberID($v['member_id']);
            $this->finance->dataMember=array('member_id'=>$v['member_id'],'lft'=>$v['lft'],'rgt'=>$v['rgt']);            
            $v['downline']=$this->finance->getCountDownline();
            $v['totalomset']=$this->finance->toRupiah($this->finance->getTotalOmset());
            $v['enabled']=$this->member->getLabelMemberStatus($v['enabled']);
            $result[$k]=$v;
        }
		$this->RepeaterRight->DataSource=$result;
		$this->RepeaterRight->dataBind();	
	}
    public function renderCallbackLeft ($sender,$param) {
		$this->RepeaterLeft->render($param->NewWriter);	
	}	
	public function Page_ChangedLeft ($sender,$param) {
		$_SESSION['currentPageDashboard']['page_num_left']=$param->NewPageIndex;
		$this->populateDataLeft();
	}
	protected function populateDataLeft($search=false) {		
        $member_id=$this->dataMember['member_id'];
        $left=$this->dataMember['lft'];    
        $str = "SELECT member_id,ibo,member_name,lft,rgt,enabled,date_reg FROM members WHERE lft=$left AND member_id > $member_id";
        if ($search) {
            $status=$this->cmbStatusFilter->Text;
            $cluasa="AND enabled=$status";            
            $ibo=addslashes($this->noiboFilter->Text);
            if ($ibo != '')$cluasa .= " AND ibo='$ibo'"; 
            $membername=addslashes($this->membernameFilter->Text);
            if ($membername != '')$cluasa .= " AND member_name LIKE '%$membername%'";
            $email=addslashes($this->emailFilter->Text);
            if ($email != '')$cluasa .= " AND email LIKE '%$email%'";            
            $str = "$str $cluasa";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("members WHERE lft=$left AND member_id > $member_id $cluasa",'member_id');
        }else{
            $jumlah_baris=$this->DB->getCountRowsOfTable ("members WHERE lft=$left AND member_id > $member_id",'member_id');            
        }
        $this->RepeaterLeft->CurrentPageIndex=$_SESSION['currentPageDashboard']['page_num_left'];        
		$this->RepeaterLeft->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterLeft->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterLeft->PageSize;		
		$itemcount=$this->RepeaterLeft->VirtualItemCount;
		$limit=$this->RepeaterLeft->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=5;$_SESSION['currentPageDashboard']['page_num_left']=0;}
		$str = "$str ORDER BY date_reg DESC,member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','lft','rgt','enabled','date_reg'));
		$r=$this->DB->getRecord($str,$offset+1);
        $result=array();
        $this->finance->setPeriode($_SESSION['currentPageDashboard']['periodeawal'],$_SESSION['currentPageDashboard']['periodeakhir']);        
		foreach ($r as $k=>$v) {
            $this->finance->setMemberID($v['member_id']);
            $this->finance->dataMember=array('member_id'=>$v['member_id'],'lft'=>$v['lft'],'rgt'=>$v['rgt']);            
            $v['downline']=$this->finance->getCountDownline();
            $v['totalomset']=$this->finance->toRupiah($this->finance->getTotalOmset());
            $v['enabled']=$this->member->getLabelMemberStatus($v['enabled']);
            $result[$k]=$v;
        }
		$this->RepeaterLeft->DataSource=$result;
		$this->RepeaterLeft->dataBind();	
	}
    public function refresh ($sender,$param) {
        $_SESSION['currentPageDashboard']['page_num_right']=0;
        $_SESSION['currentPageDashboard']['page_num_left']=0;
        $this->member->redirect('a.Home');
    }
}
		