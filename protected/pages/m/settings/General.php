<?php
prado::using ('Application.pages.m.settings.MainPageSetting');
class General extends MainPageSetting {    
	public function onLoad($param) {		
		parent::onLoad($param);				        
		$this->showSettingGeneral=true;              
		if (!$this->IsPostBack&&!$this->IsCallBack) {	           
            if (!isset($_SESSION['currentPageGeneral'])||$_SESSION['currentPageGeneral']['page_name']!='m.settings.General') {
				$_SESSION['currentPageGeneral']=array('page_name'=>'m.settings.General','page_num'=>0);												
			}            
            $this->populateData (); 
		}
	}    
    public function populateData () {       
        $this->txtNamaPerusahaan->Text=$this->setup->getSettingValue('config_name');
        $this->txtNamaPemilik->Text=$this->setup->getSettingValue('config_owner');
        $this->txtAlamat->Text=$this->setup->getSettingValue('config_address');
        $this->txtEmail->Text=$this->setup->getSettingValue('config_email');
        $this->txtTelepon->Text=$this->setup->getSettingValue('config_telephone');
        $this->txtFax->Text=$this->setup->getSettingValue('config_fax');; 
    }
    public function saveData ($sender,$param) {
        if ($this->IsValid) {
            $nama_perusahaan=  addslashes($this->txtNamaPerusahaan->Text);
            $nama_pemilik=  addslashes($this->txtNamaPemilik->Text);
            $alamat=  addslashes($this->txtAlamat->Text);
            $email=  addslashes($this->txtEmail->Text);
            $telepon=  addslashes($this->txtTelepon->Text);
            $fax=  addslashes($this->txtFax->Text);            
            
            $str = "UPDATE setting SET value='$nama_perusahaan' WHERE setting_id=649";
            $this->DB->updateRecord($str);
            $str = "UPDATE setting SET value='$nama_pemilik' WHERE setting_id=650";
            $this->DB->updateRecord($str);
            $str = "UPDATE setting SET value='$alamat' WHERE setting_id=651";
            $this->DB->updateRecord($str);
            $str = "UPDATE setting SET value='$email' WHERE setting_id=652";
            $this->DB->updateRecord($str);
            $str = "UPDATE setting SET value='$telepon' WHERE setting_id=653";
            $this->DB->updateRecord($str);
            $str = "UPDATE setting SET value='$fax' WHERE setting_id=654";                       
            $this->DB->updateRecord($str);
            
            $this->setup->loadSetting(true);            
            $this->redirect('m.settings.General');
        }
    }
}