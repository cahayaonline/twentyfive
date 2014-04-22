<?php
prado::using('Application.ModalBoxPage');
class MembersLookup extends ModalBoxPage {
	public function onLoad($param) {		
		parent::onLoad($param);
		if (!isset($_SESSION['currentPageMemberLookup'])||$_SESSION['currentPageMemberLookup']['page_name']!='m.member.MembersLookup') {
			$_SESSION['currentPageMemberLookup']=array('page_name'=>'m.member.MembersLookup','page_num'=>0);												
		}		
		$this->populateData();
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageMemberLookup']['page_num']=$param->NewPageIndex;
		$this->populateData();
	}
	public function searchClient ($sender,$param) {
		
	}
	public function populateData($str='') {				
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageMemberLookup']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ('members');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageMemberLookup']['page_num']=0;}
		$str = "SELECT member_id,ibo,member_name,email,date_reg FROM members ORDER BY date_reg DESC,member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','email','date_reg'));
		$result=$this->DB->getRecord($str,$offset+1);
		
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();
	}
}
?>