<?php

class AddNewMember extends  MainPageAccount {
	public function onLoad($param) {		
		parent::onLoad($param);			
        $this->showMembers = true;
        $this->showAddNewMember=true;
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageAddNewMember'])||$_SESSION['currentPageAddNewMember']['page_name']!='a.member.AddNewMember') {
                $_SESSION['currentPageAddNewMember']=array('page_name'=>'a.member.AddNewMember','page_num'=>0,'search'=>false);												
            }           
            $_SESSION['currentPageAddNewMember']['search']=false;            
			$this->cmbCountry->DataSource=$this->member->getList('country',array('country_id','country_name'),'country_name',null,5);
			$this->cmbCountry->Text=105;
			$this->cmbCountry->dataBind();          
            
            $this->hdnMemberID->Value=$this->dataMember['member_id'];
            $this->txtMemberID->Text=$this->dataMember['member_id'];
            
            $this->hdnIBOSponsorID->Value=$this->dataMember['ibo'];
            $this->txtNoIBOSponsorID->Text=$this->dataMember['ibo'];
            
            $this->hdnNamaMember->Value=$this->dataMember['member_name'];
            $this->txtNamaMember->Text=$this->dataMember['member_name'];
		}
	}
    public function saveData ($sender,$param) {
		if ($this->IsValid) {						
			$posisi=$this->cmbPosisi->Text;									
			$nama=  strtoupper(addslashes($this->txtNamaPemohon->Text));			
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
			$password=$this->Pengguna->createHashPassword($this->txtPassword1->Text);		
			$member_id=$this->hdnMemberID->Value;													
			$sponsor_id=$member_id;
			$this->member->setStructure();                        
			$downline=$this->member->structure->getDirectDownlineAvailable('member_id_upline',"'$member_id'");                        
            $this->member->structure->setMemberID($member_id);            
			if (isset($downline[1]) && isset($downline[2])) {	                
				$parent_id=$this->member->structure->getLastMemberOfSponsor($posisi);
                if ($posisi == '1') {
                    $position=$this->member->structure->getLastPosition('left');
                    $this->member->structure->setParentID($parent_id);
                    $left=$position['lastleft']+2;
                    $rightposition=$this->member->structure->getLastPositionByMember('right');
                    $right=$rightposition['lastright'];
                }elseif ($posisi == '0') {                    
                    $position=$this->member->structure->getLastPosition('right');                
                    $this->member->structure->setParentID($parent_id);
                    $leftposition=$this->member->structure->getLastPositionByMember('left');
                    $right=$position['lastright']+2;
                    $left=$leftposition['lastleft'];
                }
			}elseif(isset($downline[1])&&$posisi==1){//kanan                     
                $parent_id=$downline[1]['position']==1?$downline[1]['member_id']:$member_id;  
				$position=$this->member->structure->getLastPosition('left');
                $this->member->structure->setParentID($parent_id);
                $left=$position['lastleft']+2;
                $rightposition=$this->member->structure->getLastPositionByMember('right');                
                $right=$rightposition['lastright'];                                
			}elseif(isset($downline[1])&&$posisi==0){//kiri                                
				$parent_id=$downline[1]['position']==0?$downline[1]['member_id']:$member_id;     
                $position=$this->member->structure->getLastPosition('right');                
                $this->member->structure->setParentID($parent_id);
                $leftposition=$this->member->structure->getLastPositionByMember('left');
                $left=$leftposition['lastleft'];
                $right=$position['lastright']+2;
			}else {                
				$parent_id=$member_id;               
                if ($parent_id != '0' && $parent_id=='') {                            
                    $position=$this->member->structure->getLastPosition();                
                    $left=$position['lastleft']+2;
                    $right=$position['lastright']+3;   
                }else {
                    if ($posisi == '1') {                        
                        $position=$this->member->structure->getLastPosition('left');
                        $this->member->structure->setParentID($parent_id);
                        $left=$position['lastleft']+2;
                        $rightposition=$this->member->structure->getLastPositionByMember('right');
                        $right=$rightposition['lastright'];
                    }elseif ($posisi == '0') {                                          
                        $position=$this->member->structure->getLastPosition('right');                
                        $this->member->structure->setParentID($parent_id);
                        $leftposition=$this->member->structure->getLastPositionByMember('left');
                        $left=$leftposition['lastleft'];
                        $right=$position['lastright']+2;
                    }
                }               
			}            
            $str="INSERT INTO `members` (`member_id`, `ibo`, `position`, `member_name`, `identity_num`, `gender`, `birthday`, `status`, `patner_name`, `identity_num_patner`, `address`, `city`, `postal_code`, `country_id`, `phone_number`, `email`, `mobile_phone`, `mobile_phone2`, `mother_name`,`family_name`, `relationship_family`, `account_number`, `account_name`, `bank_name`,`branch`, `bank_location`, `tax_number`, `tax_status`, `responsible_number`, `notes`,`salt`,`password`, `sponsor_id`,`parent_id`,`lft`,`rgt`, `date_reg`,`date_modified`) VALUES
					(NULL, '$no_ibo', $posisi, '$nama', '$ktp', $jk, '$tgl', $status, '$nama_pasangan', '$ktp_pasangan', '$alamat', '$kota','$kodepos', $negara, '$telp','$email', '$hp', '$hp2', '$nama_ibu', '$ahli_waris', '$hubungan_ahli', '$norek', '$atasnama', '$namabank','$cabang', '$lokasibank', '$npwp', $pajakmenikah, '$jumlah_tanggungan', '$notes','{$password['salt']}', '{$password['password']}', '$sponsor_id', '$parent_id','$left','$right',NOW(),NOW())";                           
            
            $this->DB->query ('LOCK TABLE members WRITE;');
            $this->DB->query('BEGIN');                                  
            if ($this->DB->insertRecord($str)) {                
                $this->DB->query('COMMIT');                
            }else{
                $this->DB->query('ROLLBACK');
            }
            $this->DB->query ('UNLOCK TABLE');
			$this->member->redirect('a.members.AddNewMember');
		}
	}	
	public function checkNoHP($sender,$param) {
		$nohp=$param->Value;		
		if ($this->DB->checkRecordIsExist('mobile_phone','members',$nohp)) {
			$sender->Text="Mobile Phone ($nohp) has not available";
			$param->IsValid=false;
		}
	}
	public function checkEmailAddress($sender,$param) {
		$emailaddress=$param->Value;		
		if ($this->DB->checkRecordIsExist('email','members',$emailaddress)) {
			$sender->Text="Email Address ($emailaddress) has not available";
			$param->IsValid=false;
		}
	}
	public function processNextButton($sender,$param) {
		if ($param->CurrentStepIndex ==0) {
			$this->txtMemberID->Text=$this->hdnMemberID->Value;
			$this->txtNoIBOSponsorID->Text=$this->hdnIBOSponsorID->Value;
			$this->txtNamaMember->Text=$this->hdnNamaMember->Value;
		}
	}
	public function addNewMemberCompleted($sender,$param) {
		$member_id=$this->hdnMemberID->Value;	
        $this->sponsoriboindonesia->Text='Tidak Informasi Sponsor, karena sebagai TOP MEMBER';
		if ($member_id != '0' && $member_id != '') {			
			$data_member = $this->member->getDataMember(array('select'=>'member_id','value'=>$member_id,'mode'=>1));						
			$data.='<dt>Member ID :</dt>';
			$data.='<dd>';
			$data.=$data_member['member_id'];			
			$data.='</dd>';								
			$data.='<dt>No. IBO :</dt>';
			$data.='<dd>';
			$data.=$data_member['ibo']==''?'-':$data_member['ibo'];			
			$data.='</dd>';						
			$data.='<dt>Nama Member :</dt>';
			$data.='<dd>';
			$data.=$data_member['member_name'];			
			$data.='</dd>';				
            $data.='<dt>Posisi Pemohon :</dt>';
			$data.='<dd>';
			$data.=$this->cmbPosisi->Text=='1'?'Kanan':'Kiri';			
			$data.='</dd>';				
			$this->sponsoriboindonesia->Text=$data;		
		}		
	}
}