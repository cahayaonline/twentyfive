<?php

class MainAccount extends TTemplateControl {
	public function onLoad($param) {		
		parent::onLoad($param);				
		$this->Page->createObjFinance();
		if (!$this->Page->IsPostBack&&!$this->Page->IsCallBack) {
			$dataMember=$this->Page->Pengguna->getDataUser();       
			$this->Page->finance->setMemberID($dataMember['member_id'],true,1);			
			$deposit_akhir=$this->Page->finance->getAccountBalance('all');
			$this->literalDepositBonus->Text=$this->Page->finance->toRupiah($deposit_akhir['sisa_bonus']);
			$this->literalSaldoDeposit->Text=$this->Page->finance->toRupiah($deposit_akhir['saldo_deposit']);
		}
	}
}		
		
?>