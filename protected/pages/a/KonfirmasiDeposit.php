<?php

class KonfirmasiDeposit extends MainPageAccount {
	public function onLoad($param) {		
		parent::onLoad($param);		
		$this->showKonfirmasiDeposit=true;              
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageKonfirmasiDeposit'])||$_SESSION['currentPageKonfirmasiDeposit']['page_name']!='a.KonfirmasiDeposit') {
                $_SESSION['currentPageKonfirmasiDeposit']=array('page_name'=>'a.KonfirmasiDeposit','page_num_right'=>0,'page_num_left'=>0,'withdate'=>false,'periodeawal'=>date('Y-m-01'),'periodeakhir'=>date('Y-m-t'),'search'=>false,'datamember'=>array());												
            }                   
            $this->cmbBankTujuan->DataSource=$this->setup->removeIdFromArray($this->setup->getLinkBank(),'none');
            $this->cmbBankTujuan->dataBind();
            $this->cmbCaraPembayaran->DataSource=$this->finance->getCaraPembayaran();
            $this->cmbCaraPembayaran->dataBind();
		}
	}
    public function changeCaraPembayaran($sender,$param) {
        if ($this->cmbCaraPembayaran->Text == 'tunai') {
            $this->cmbBankTujuan->Enabled=false;            
            $this->txtBankPengirim->Enabled=false;
            $this->txtNoRekPengirim->Enabled=false;
            $this->txtNamaPengirim->Enabled=false;            
        }else {
            $this->cmbBankTujuan->Enabled=true;            
            $this->txtBankPengirim->Enabled=true;
            $this->txtNoRekPengirim->Enabled=true;
            $this->txtNamaPengirim->Enabled=true;            
        }
    }
    public function saveData($sender,$param) {
        if ($this->IsValid) {
            $member_id=$this->Pengguna->getDataUser('member_id');
            $carapembayaran=$this->cmbCaraPembayaran->Text;
            $banktujuan=$carapembayaran == 'tunai'?'':$this->cmbBankTujuan->Text;
            $tanggaltranfer=date('Y-m-d',$this->cmbTanggalTranfer->TimeStamp);
            $bankpengirim=$this->txtBankPengirim->Text;
            $norekpengirim=$this->txtNoRekPengirim->Text;
            $namapengirim=$this->txtNamaPengirim->Text;
            $jumlahtransfer=$this->finance->toInteger($this->txtJumlahTransfer->Text);
            $keterangan=$this->txtKeterangan->Text;
            
            $str = "INSERT INTO konfirmasideposit (idkonfirmasideposit,member_id,tanggaltransfer,carapembayaran,namabankpengirim,norekeningpengirim,namapengirim,jumlahditransfer,norek,keterangan) VALUES (NULL,$member_id,'$tanggaltranfer','$carapembayaran','$bankpengirim','$norekpengirim','$namapengirim','$jumlahtransfer','$banktujuan','$keterangan')";
            $this->DB->insertRecord($str);
            
            $this->redirect('a.KonfirmasiDeposit');
            
        }
    }
}
		