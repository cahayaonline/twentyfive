<?php
prado::using ('Application.pages.m.members.MainPageMembers');
class SummaryMember extends MainPageMembers {    
	public function onLoad($param) {		
		parent::onLoad($param);								
        $this->showSummaryMember=true;
        $this->member->setMemberID($this->member_id,true,2);
        $this->dataMember=$this->member->dataMember; 
        $this->createObjFinance();
        $this->finance->dataMember=$this->member->dataMember;         
		if (!$this->IsPostBack&&!$this->IsCallBack) {            				
            if (isset($this->member->dataMember['member_id'])) {
                if (!isset($_SESSION['currentPageSummaryMember'])||$_SESSION['currentPageSummaryMember']['page_name']!='m.member.SummaryMember') {
                    $_SESSION['currentPageSummaryMember']=array('page_name'=>'m.member.SummaryMember','page_num_right'=>0,'page_num_left'=>0,'withdate'=>false,'periodeawal'=>date('Y-m-d'),'periodeakhir'=>date('Y-m-d'),'search'=>false,'datamember'=>array());												
                }        
                $_SESSION['currentPageMember']['search']=false;
                $this->idProcess='view';
                $this->chkWithDate->Checked=$_SESSION['currentPageSummaryMember']['withdate'];
                $this->cmbPeriodAkhir->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageSummaryMember']['periodeakhir']);
                $this->cmbPeriodAwal->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageSummaryMember']['periodeawal']);
                $this->populateData();
            }            
		}
	}
    public function calculate ($sender,$param) {
        $this->idProcess='view';
        if ($this->cmbPeriodAwal->TimeStamp <= $this->cmbPeriodAkhir->TimeStamp) {            
            $_SESSION['currentPageSummaryMember']['withdate']=$this->chkWithDate->Checked; 
            $_SESSION['currentPageSummaryMember']['periodeawal']=date('Y-m-d',$this->cmbPeriodAwal->TimeStamp);
            $_SESSION['currentPageSummaryMember']['periodeakhir']=date('Y-m-d',$this->cmbPeriodAkhir->TimeStamp);
            $this->populateData();
        }
    }    
    public function populateData () {        
		$downline=$this->member->getCountDownline('split');                 
        $this->dataMember['left']=$downline['left'];
        $this->dataMember['right']=$downline['right'];
        if ($_SESSION['currentPageSummaryMember']['withdate']) {            
            $this->finance->setPeriode($_SESSION['currentPageSummaryMember']['periodeawal'],$_SESSION['currentPageSummaryMember']['periodeakhir']);
        }
        $this->finance->setMemberID($this->member_id);                
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

        $latestmember=$this->member->getLastMember();
        $this->dataMember['latest_left']=$latestmember['left'];
        $this->dataMember['latest_right']=$latestmember['right'];                
        $_SESSION['currentPageSummaryMember']['datamember']=$this->dataMember;
        $this->populateDataRight();
        $this->populateDataLeft();
	}
    public function searchClient ($sender,$param) {
        $_SESSION['currentPageSummaryMember']['search']=true;
        if ($this->cmbPosisiFilter->Text==1) {//kiri
            $this->populateDataLeft($_SESSION['currentPageSummaryMember']['search']);
        }else {
            $this->populateDataRight($_SESSION['currentPageSummaryMember']['search']);
        }       
	}
    public function renderCallbackRight ($sender,$param) {
		$this->RepeaterRight->render($param->NewWriter);	
	}	
	public function Page_ChangedRight ($sender,$param) {
		$_SESSION['currentPageSummaryMember']['page_num_right']=$param->NewPageIndex;
		$this->populateDataRight();
	}
	protected function populateDataRight($search=false) {
		$this->idProcess='view';        
        $member_id=$this->member_id;        
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
        $this->RepeaterRight->CurrentPageIndex=$_SESSION['currentPageSummaryMember']['page_num_right'];        		
		$this->RepeaterRight->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterRight->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterRight->PageSize;		
		$itemcount=$this->RepeaterRight->VirtualItemCount;
		$limit=$this->RepeaterRight->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=5;$_SESSION['currentPageSummaryMember']['page_num_right']=0;}
		$str = "$str ORDER BY date_reg DESC,member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','lft','rgt','enabled','date_reg'));
		$r=$this->DB->getRecord($str,$offset+1);
        $result=array();
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
		$_SESSION['currentPageSummaryMember']['page_num_left']=$param->NewPageIndex;
		$this->populateDataLeft();
	}
	protected function populateDataLeft($search=false) {
		$this->idProcess='view';        
        $member_id=$this->member_id;
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
        $this->RepeaterLeft->CurrentPageIndex=$_SESSION['currentPageSummaryMember']['page_num_left'];        
		$this->RepeaterLeft->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterLeft->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterLeft->PageSize;		
		$itemcount=$this->RepeaterLeft->VirtualItemCount;
		$limit=$this->RepeaterLeft->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=5;$_SESSION['currentPageSummaryMember']['page_num_left']=0;}
		$str = "$str ORDER BY date_reg DESC,member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','lft','rgt','enabled','date_reg'));
		$r=$this->DB->getRecord($str,$offset+1);
        $result=array();
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
        $_SESSION['currentPageSummaryMember']['page_num_right']=0;
        $_SESSION['currentPageSummaryMember']['page_num_left']=0;
        $this->member->redirect('m.members.SummaryMember',array('id'=>$this->member_id));
    }
}
		
