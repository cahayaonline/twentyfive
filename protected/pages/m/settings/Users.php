<?php
prado::using ('Application.pages.m.settings.MainPageSetting');
class Users extends MainPageSetting {
	public function onLoad ($param) {		
		parent::onLoad ($param);  
        $this->showUsers = true;
		if (!$this->IsPostBack&&!$this->IsCallBack) {	
            if (!isset($_SESSION['currentPageUsers'])||$_SESSION['currentPageUsers']['page_name']!='m.settings.Users') {
                $_SESSION['currentPageUsers']=array('page_name'=>'m.settings.Users','page_num'=>0);												
			}     
			$this->populateData ();			
		}
	}
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageUsers']['page_num']=$param->NewPageIndex;
		$this->populateData();
	} 
	protected function populateData () {
        $this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageUsers']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ('user','userid');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageUsers']['page_num']=0;}
        $str = "SELECT userid,username,page,email,active FROM user";        
		$this->DB->setFieldTable(array('userid','username','page','email','active'));
		$r=$this->DB->getRecord($str);        
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();      		
	}
    public function dataBound ($sender,$param) {
		$item=$param->Item;
		if ($item->ItemType === 'Item' || $item->ItemType === 'AlternatingItem') {	
            if ($item->DataItem['userid']==1){
                $item->btnDelete->Enabled=false;
                $item->btnDelete->CssClass='btn btn-grey btn-small';
            }
        }
    }
    public function checkUsername ($sender,$param) {
		$this->idProcess=$sender->getId()=='addUsername'?'add':'edit';
        $username=$param->Value;		
        if ($username != '') {
            try {   
                if ($this->hiddenusername->Value!=$username) {                    
                    if ($this->DB->checkRecordIsExist('username','user',$username)) {                                
                        throw new Exception ("<span class='error'>Username ($username) sudah tidak tersedia silahkan ganti dengan yang lain.</span>");		
                    }                               
                }                
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }	
    }
    public function checkEmail ($sender,$param) {
		$this->idProcess=$sender->getId()=='addEmail'?'add':'edit';
        $email=$param->Value;		
        if ($email != '') {
            try {   
                if ($this->hiddenemail->Value!=$email) {                    
                    if ($this->DB->checkRecordIsExist('email','user',$email)) {                                
                        throw new Exception ("<span class='error'>Email ($email) sudah tidak tersedia silahkan ganti dengan yang lain.</span>");		
                    }                               
                }                
            }catch (Exception $e) {
                $param->IsValid=false;
                $sender->ErrorMessage=$e->getMessage();
            }	
        }	
    }
    public function saveData($sender,$param) {		
        if ($this->Page->IsValid) {		
            $username=$this->txtAddUsername->Text;            
            $alamatemail=$this->txtAddAlamatEmail->Text;            
            $page=$this->cmbAddRoles->Text;
            $data=$this->Pengguna->createHashPassword($this->txtAddPassword->Text);
            $salt=$data['salt'];
            $password=$data['password'];
            $str = "INSERT INTO user (userid,username,userpassword,salt,page,email,active) VALUES (NULL,'$username','$password','$salt','$page','$alamatemail',1)";
            $this->DB->insertRecord($str);
            $this->redirect('m.settings.Users');
        }
	}
    public function editRecord ($sender,$param) {		
		$this->idProcess='edit';
		$id=$this->getDataKeyField($sender,$this->RepeaterS);        
		$this->hiddenuserid->Value=$id;
        $str = "SELECT username,email,page FROM user WHERE userid='$id'";
        $this->DB->setFieldTable(array('username','email','page'));
        $r=$this->DB->getRecord($str);    
		$result = $r[1];        				
        $this->hiddenusername->Value=$result['username'];
        $this->hiddenemail->Value=$result['email'];
		$this->txtEditUsername->Text=$result['username'];		
		$this->txtEditAlamatEmail->Text=$result['email'];		        
		$this->cmbEditRoles->Text=$result['page'];		
        if ($id == 1) {
            $this->txtEditUsername->Enabled=false;
            $this->cmbEditRoles->Enabled=false;
        }
	}
    public function updateData($sender,$param) {		
        if ($this->Page->IsValid) {		
            $id=$this->hiddenuserid->Value;
            $username=$this->txtEditUsername->Text;            
            $alamatemail=$this->txtEditAlamatEmail->Text;            
            $page=$this->cmbEditRoles->Text;
            if ($this->txtEditPassword->Text == '') {
                $str = "UPDATE user SET username='$username',page='$page',email='$alamatemail',active=1 WHERE userid=$id";
            }else {
                $data=$this->Pengguna->createHashPassword($this->txtEditPassword->Text);
                $salt=$data['salt'];
                $password=$data['password'];
                $str = "UPDATE user SET username='$username',userpassword='$password',salt='$salt',page='$page',email='$alamatemail',active=1 WHERE userid=$id";
            }
            $this->DB->updateRecord($str);           
            $this->redirect('m.settings.Users');
        }
	}
    public function deleteRecord ($sender,$param) {
		$id=$this->getDataKeyField($sender,$this->RepeaterS);
		$this->DB->deleteRecord("user WHERE userid=$id");		
		$this->populateData();		
	}
}

?>