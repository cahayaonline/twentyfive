<?php
prado::using ('Application.pages.m.members.MainPageMembers');
class AddNewMember extends MainPageMembers {
	public function onLoad($param) {		
		parent::onLoad($param);						
        $this->showAddNewMember=true;
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageAddNewMember'])||$_SESSION['currentPageAddNewMember']['page_name']!='m.member.AddNewMember') {
                $_SESSION['currentPageAddNewMember']=array('page_name'=>'m.member.AddNewMember','page_num'=>0,'search'=>false);												
            }	
            $_SESSION['currentPageAddNewMember']['search']=false;            
			$this->cmbCountry->DataSource=$this->member->getList('country',array('country_id','country_name'),'country_name',null,5);
			$this->cmbCountry->Text=105;
			$this->cmbCountry->dataBind();            
		}
	}
    public function lookupMember ($sender,$param) {
        $this->modalLookupMember->Show();
        $this->populateData();
    }
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageAddNewMember']['page_num']=$param->NewPageIndex;
		$this->populateData();
	}
    public function searchClient ($sender,$param) {
		$_SESSION['currentPageAddNewMember']['search']=true;
        $this->populateData($_SESSION['currentPageAddNewMember']['search']);
	}
    public function populateData($search='') {			
        $str = 'SELECT member_id,ibo,member_name,lft,rgt,enabled,enabled,date_reg FROM members';
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
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageAddNewMember']['page_num'];		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageAddNewMember']['page_num']=0;}
		$str = "$str ORDER BY date_reg DESC,member_name ASC LIMIT $offset,$limit";
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
    public function setMemberUplineInformation ($sender,$param) {        
        $infoupline=explode('_',$sender->CommandParameter);        
        $this->hdnMemberID->Value=$infoupline[0];
        $this->txtMemberID->Text=$infoupline[0];
        $this->txtNoIBOSponsorID->Text=$infoupline[1];
        $this->hdnIBOSponsorID->Value=$infoupline[1];
        $this->txtNamaMember->Text=$infoupline[2];
        $this->hdnNamaMember->Value=$infoupline[2];
        $this->modalLookupMember->hide();
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
			$this->member->redirect('m.Members');
		}
	}
	
	public function checkMemberID($sender,$param) {						
		$member_id=$param->Value;		
		if ($member_id != '0' && $member_id != '') {
			try {						
				if (!$this->DB->checkRecordIsExist('member_id','members',$member_id))
					throw new Exception ("Member ID ($member_id) has not available");														
			}catch (Exception $e) {
				$param->IsValid=false;
				$sender->Text=$e->getMessage();
			}	
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