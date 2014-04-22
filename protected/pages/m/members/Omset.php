<?php
prado::using ('Application.pages.m.members.MainPageMembers');
class Omset extends MainPageMembers {
	public function onLoad($param) {			
		parent::onLoad($param);				
		$this->showOmset = true;
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
			if (!isset($_SESSION['currentPageOmset'])||$_SESSION['currentPageOmset']['page_name']!='m.member') {
				$_SESSION['currentPageOmset']=array('page_name'=>'m.member','page_num'=>0,'search'=>false);												
			}		
            $_SESSION['currentPageOmset']['search']=false;
			$this->populateData();
		}	        
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageOmset']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageOmset']['search']);
	}
	public function searchClient ($sender,$param) {
		$_SESSION['currentPageOmset']['search']=true;
        $this->populateData($_SESSION['currentPageOmset']['search']);
	}
	public function populateData($search=false) {	
        $jumlah_baris=$this->DB->getCountRowsOfTable ('members','member_id');
        $str = 'SELECT member_id,ibo,member_name,lft,rgt FROM members';
        if ($search){            
            $membername=$this->membername->Text;
            $cluasa="WHERE member_name LIKE '%$membername%'";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("members $cluasa",'member_id');
            $str = "$str $cluasa";
        }
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageOmset']['page_num'];				
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageOmset']['page_num']=0;}
		$str = "$str ORDER BY date_reg DESC,member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','lft','rgt'));
		$r=$this->DB->getRecord($str,$offset+1);
        $result=array();
		foreach ($r as $k=>$v) {                       
            $this->finance->dataMember=array('member_id'=>$v['member_id'],'lft'=>$v['lft'],'rgt'=>$v['rgt']);
            $totalomset=$this->finance->getTotalOmset('split');
            $v['omsetkanan']=$this->finance->toRupiah($totalomset['right']);
            $v['omsetkiri']=$this->finance->toRupiah($totalomset['left']);
            $v['totalomset']=$this->finance->toRupiah($totalomset['left']+$totalomset['right']);
            $result[$k]=$v;
        }
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();		
		
	}  
}