<?php
prado::using ('Application.pages.m.members.MainPageMembers');
class Members extends MainPageMembers {
	public function onLoad($param) {			
		parent::onLoad($param);				
		$this->showMembersHome = true;
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
			if (!isset($_SESSION['currentPageMember'])||$_SESSION['currentPageMember']['page_name']!='m.member') {
				$_SESSION['currentPageMember']=array('page_name'=>'m.member','page_num'=>0,'search'=>false);												
			}		
            $_SESSION['currentPageMember']['search']=false;
			$this->populateData();
		}	        
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageMember']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageMember']['search']);
	}
	public function searchMember ($sender,$param) {
		$_SESSION['currentPageMember']['search']=true;
        $this->populateData($_SESSION['currentPageMember']['search']);
	}
	public function populateData($search=false) {	        
        $str = 'SELECT member_id,ibo,member_name,lft,rgt,enabled FROM members';
        if ($search){            
            $status=$this->cmbStatusFilter->Text;
            $cluasa="WHERE enabled=$status";            
            $ibo=addslashes($this->noiboFilter->Text);
            if ($ibo != '')$cluasa .= " AND ibo='$ibo'"; 
            $membername=addslashes($this->membernameFilter->Text);
            if ($membername != '')$cluasa .= " AND member_name LIKE '%$membername%'";
            $email=addslashes($this->emailFilter->Text);
            if ($email != '')$cluasa .= " AND email LIKE '%$email%'";            
            $str = "$str $cluasa";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("members $cluasa",'member_id');
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('members','member_id');
        } 
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageMember']['page_num'];				
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageMember']['page_num']=0;}
		$str = "$str ORDER BY date_modified DESC,member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','lft','rgt','enabled','date_reg'));
		$r=$this->DB->getRecord($str,$offset+1);
        $result=array();
		foreach ($r as $k=>$v) {
            $this->member->setMemberID($v['member_id']);
            $this->member->dataMember=array('member_id'=>$v['member_id'],'lft'=>$v['lft'],'rgt'=>$v['rgt']);            
            $v['downline']=$this->member->getCountDownline();
            $this->finance->setMemberID($v['member_id']);
            $v['totalomset']=$this->finance->toRupiah($this->finance->getTotalOmset());
            $v['enabled']=$this->member->getLabelMemberStatus($v['enabled']);
            $result[$k]=$v;
        }
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();		
		
	}  
}