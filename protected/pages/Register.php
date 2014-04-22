<?php
class Register extends MainPageStore {
	public function onLoad($param) {		        
		parent::onLoad($param);
        $this->createObjMember();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageRegister'])||$_SESSION['currentPageRegister']['page_name']!='Register') {
                $sponsor_id=$this->setup->getDefaultSponsor();
                $this->member->setMemberID($sponsor_id,true,5);                
                $posisi=$this->member->getCountDownline('split');     
                $pos = $posisi['left']<$posisi['right'] ? 0 :1;               
				$_SESSION['currentPageRegister']=array('page_name'=>'Register','dataMember'=>$this->member->dataMember,'posisi'=>$pos);												
			}   
            $this->cmbNegara->DataSource=$this->member->getList('country',array('country_id','country_name'),'country_name',null,5);
			$this->cmbNegara->Text=105;
			$this->cmbNegara->dataBind(); 
            
            $this->populateTopCart();
		}
	}
}
		