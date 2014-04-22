<?php
prado::using ('Application.pages.m.members.MainPageMembers');
class EditMember extends MainPageMembers {
	public function onLoad($param) {		
		parent::onLoad($param);						
		$this->showEditMember = true;
		if (!$this->IsPostBack&&!$this->IsCallBack) {
			$this->populateData();
		}
	}
	public function populateData() {
		$member_id=$this->member_id;
		$str = "SELECT member_id,position, member_name, identity_num, gender, birthday, status, patner_name, identity_num_patner, address, city, postal_code, country_id, phone_number, email, mobile_phone, mobile_phone2, mother_name, family_name, relationship_family, account_number, account_name, bank_name, branch, bank_location, tax_number, tax_status, responsible_number, notes, parent_id FROM members WHERE member_id=$member_id";
		$this->DB->setFieldTable(array('member_id','member_name','identity_num','gender','birthday','status','patner_name','identity_num_patner','address','city','postal_code','country_id','phone_number','email','mobile_phone','mobile_phone2','mother_name','family_name','relationship_family','account_number','account_name','bank_name','branch','bank_location','tax_number','tax_status','responsible_number','notes','parent_id'));
		$r=$this->DB->getRecord($str);
        if (isset($r[1])) {
            $this->idProcess='edit';
            $r=$r[1];							
            $this->dataMember=$r;
            $this->hdnMemberID->Value=$r['member_id'];
            $this->txtMemberName->Text='(#'.$r['member_id'].' - '.$r['member_name'].')';
            $this->txtNamaPemohon->Text=$r['member_name'];			
            $this->txtNoIdentitas->Text=$r['identity_num'];
            $this->cmbJK->Text=$r['gender'];		
            $this->cmbTanggalLahir->Date=$this->TGL->tukarTanggal($r['birthday'],'entoid');
            $this->cmbStatus->Text=$r['status'];            
            $this->txtNamaPasangan->Text=$r['patner_name'];
            $this->txtNoKTPPasangan->Text=$r['identity_num_patner'];	
            $this->txtNoAlamatSuratMenyurat->Text=$r['address'];
            $this->txtKota->Text=$r['city'];			
            $this->txtKodePos->Text=$r['postal_code'];
            $this->cmbCountry->Text=$r['country_id'];
            $this->cmbCountry->DataSource=$this->member->getList('country',array('country_id','country_name'),'country_name',null,5);
            $this->cmbCountry->Text=$r['country_id'];
            $this->cmbCountry->dataBind();
            $this->txtTeleponRumah->Text=$r['phone_number'];
            $this->hdnEmail->Value=$r['email'];
            $this->txtEmail->Text=$r['email'];
            $this->txtHP->Text=$r['mobile_phone'];		
            $this->hdnNoHP->Value=$r['mobile_phone'];
            $this->txtHP2->Text=$r['mobile_phone2'];
            $this->txtNamaIbu->Text=$r['mother_name'];
            $this->txtNamaAhliWaris->Text=$r['family_name'];
            $this->txtHubunganAhliWaris->Text=$r['relationship_family'];
            $this->txtNoRekening->Text=$r['account_number'];			
            $this->txtAtasNama->Text=$r['account_name'];
            $this->txtNamaBank->Text=$r['bank_name'];
            $this->txtCabang->Text=$r['branch'];
            $this->txtLokasiBank->Text=$r['bank_location'];
            $this->txtNPWPW->Text=$r['tax_number'];
            $this->cmbStatusPajak->Text=$r['tax_status'];
            $this->txtJumlahTanggungan->Text=$r['responsible_number'];
            $this->AdminNote->Text=$r['notes'];		
        }
				
	}
	public function checkNoHP($sender,$param) {
		$nohp=$param->Value;				
		if ($this->hdnNoHP->Value!=$nohp) {
			if ($this->DB->checkRecordIsExist('mobile_phone','members',$nohp)) {
				$sender->Text="Mobile Phone ($nohp) has not available";
				$param->IsValid=false;
			}
		}
	}
	public function checkEmailAddress($sender,$param) {
		$emailaddress=$param->Value;				
		if ($this->hdnEmail->Value!=$emailaddress) {
			if ($this->DB->checkRecordIsExist('email','members',$emailaddress)) {
				$sender->Text="Email Address ($emailaddress) has not available";
				$param->IsValid=false;
			}
		}
	}
	public function saveData ($sender,$param) {
		if ($this->IsValid) {							
			$member_id=$this->hdnMemberID->Value;
			$nama=strtoupper(addslashes($this->txtNamaPemohon->Text));			
			$ktp=addslashes($this->txtNoIdentitas->Text);
			$jk=$this->cmbJK->Text;
			$tgl=$this->TGL->tukarTanggal ($this->cmbTanggalLahir->Text);
			$status=$this->cmbStatus->Text;
			$nama_pasangan=addslashes($this->txtNamaPasangan->Text);
			$ktp_pasangan=addslashes($this->txtNoKTPPasangan->Text);
			$alamat=addslashes($this->txtNoAlamatSuratMenyurat->Text);
			$kota=addslashes($this->txtKota->Text);			
			$kodepos=addslashes($this->txtKodePos->Text);
			$negara=$this->cmbCountry->Text;
			$telp=addslashes($this->txtTeleponRumah->Text);
			$email=addslashes($this->txtEmail->Text);
			$hp=addslashes($this->txtHP->Text);
            $no_ibo=$hp;
			$hp2=addslashes($this->txtHP2->Text);
			$nama_ibu=addslashes($this->txtNamaIbu->Text);
			$ahli_waris=addslashes($this->txtNamaAhliWaris->Text);
			$hubungan_ahli=addslashes($this->txtHubunganAhliWaris->Text);
			$norek=addslashes($this->txtNoRekening->Text);			
			$atasnama=addslashes($this->txtAtasNama->Text);
			$namabank=addslashes($this->txtNamaBank->Text);
			$cabang=addslashes($this->txtCabang->Text);
			$lokasibank=addslashes($this->txtLokasiBank->Text);
			$npwp=addslashes($this->txtNPWPW->Text);
			$pajakmenikah=$this->cmbStatusPajak->Text;
			$jumlah_tanggungan=addslashes($this->txtJumlahTanggungan->Text);			
			$notes=addslashes($this->AdminNote->Text);
			$password=$this->txtPassword1->Text;
			if ($password =='') {
				$str="UPDATE `members` SET `member_name`='$nama',ibo='$no_ibo',`identity_num`='$ktp', `gender`='$jk', `birthday`='$tgl', `status`=$status, `patner_name`='$nama_pasangan', `identity_num_patner`='$ktp_pasangan', `address`='$alamat', `city`='$kota', `postal_code`='$kodepos', `country_id`=$negara, `phone_number`='$telp', `email`='$email', `mobile_phone`='$hp', `mobile_phone2`='$hp2', `mother_name`='$nama_ibu',`family_name`='$ahli_waris', `relationship_family`='$hubungan_ahli', `account_number`='$norek', `account_name`='$atasnama', `bank_name`='$namabank',`branch`='$cabang', `bank_location`='$lokasibank', `tax_number`='$npwp', `tax_status`=$pajakmenikah, `responsible_number`='$jumlah_tanggungan', `notes`='$notes',date_modified=NOW() WHERE member_id=$member_id";
			}else {
				$password=$this->Pengguna->createHashPassword($this->txtPassword1->Text);
				$str="UPDATE `members` SET `member_name`='$nama',ibo='$no_ibo',`identity_num`='$ktp', `gender`='$jk', `birthday`='$tgl', `status`=$status, `patner_name`='$nama_pasangan', `identity_num_patner`='$ktp_pasangan', `address`='$alamat', `city`='$kota', `postal_code`='$kodepos', `country_id`=$negara, `phone_number`='$telp', `email`='$email', `mobile_phone`='$hp', `mobile_phone2`='$hp2', `mother_name`='$nama_ibu',`family_name`='$ahli_waris', `relationship_family`='$hubungan_ahli', `account_number`='$norek', `account_name`='$atasnama', `bank_name`='$namabank',`branch`='$cabang', `bank_location`='$lokasibank', `tax_number`='$npwp', `tax_status`=$pajakmenikah, `responsible_number`='$jumlah_tanggungan', `notes`='$notes',salt='{$password['salt']}', password='{$password['password']}',date_modified=NOW() WHERE member_id=$member_id";
			}		
			$this->DB->updateRecord($str);
            $this->DB->updateRecord("UPDATE `order` SET member_name='$nama',ibo='$no_ibo' WHERE member_id='$member_id'");
			$this->member->redirect('m.members.EditMember',array('id'=>$member_id));
		}
	}
}
		