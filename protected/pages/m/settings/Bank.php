<?php
prado::using ('Application.pages.m.settings.MainPageSetting');
class Bank extends MainPageSetting {
	public function onLoad ($param) {		
		parent::onLoad ($param);  
        $this->showBank = true;
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageBank'])||$_SESSION['currentPageBank']['page_name']!='m.settings.Bank') {
                $_SESSION['currentPageBank']=array('page_name'=>'m.settings.Bank','page_num'=>0);												
			}     
			$this->populateData ();			
		}
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageBank']['page_num']=$param->NewPageIndex;
		$this->populateData();
	} 
	protected function populateData () {
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageBank']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ('bank','norek');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageBank']['page_num']=0;}
        $str = "SELECT norek,nama_pemilik,nama_bank FROM bank";        
		$this->DB->setFieldTable(array('norek','nama_pemilik','nama_bank'));
		$r=$this->DB->getRecord($str);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();      		
	}   
    public function saveData($sender,$param) {		
        if ($this->Page->IsValid) {		
            $namabank=$this->txtAddNamaBank->Text;            
            $norekening=$this->txtAddNoRekening->Text;            
            $namapemilik=$this->txtAddNamaPemilik->Text;                                    
            $str = "INSERT INTO bank (norek,nama_pemilik,nama_bank) VALUES ('$norekening','$namapemilik','$namabank')";
            $this->DB->insertRecord($str);
            $this->redirect('m.settings.Bank');
        }
	}
    public function editRecord ($sender,$param) {		
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddennorek->Value=$id;
        $str = "SELECT norek,nama_pemilik,nama_bank FROM bank WHERE norek='$id'";
        $this->DB->setFieldTable(array('norek','nama_pemilik','nama_bank'));
        $r=$this->DB->getRecord($str);    
		$result = $r[1];        				        
		$this->txtEditNamaBank->Text=$result['nama_bank'];		
		$this->txtEditNoRekening->Text=$result['norek'];		        
		$this->txtEditNamaPemilik->Text=$result['nama_pemilik'];		        
	}
    public function updateData($sender,$param) {		
        if ($this->Page->IsValid) {		
            $id=$this->hiddennorek->Value;
            $namabank=$this->txtEditNamaBank->Text;            
            $norekening=$this->txtEditNoRekening->Text;            
            $namapemilik=$this->txtEditNamaPemilik->Text;                                    
            $str = "UPDATE bank SET norek='$norekening',nama_pemilik='$namapemilik',nama_bank='$namabank' WHERE norek='$id'";            
            $this->DB->updateRecord($str);           
            $this->redirect('m.settings.Bank');
        }
	}
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
		$this->DB->deleteRecord("bank WHERE norek='$id'");		
		$this->populateData();		
	}
}

?>