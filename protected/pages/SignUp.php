<?php
class SignUp extends MainPageStore {
	public function onLoad($param) {		        
		parent::onLoad($param);
        $this->CssClassBody='no-header signup-page'; 
        $this->createObjMember();
        $process = addslashes(trim($this->request['process']));         
		if (!$this->IsPostBack&&!$this->IsCallBack) {      
            if ($process=='success') {
                $this->idProcess='view';
            }else {
                if (!isset($_SESSION['currentPageSignUp'])||$_SESSION['currentPageSignUp']['page_name']!='SignUp') {
                    $sponsor_id=$this->setup->getDefaultSponsor();
                    $this->member->setMemberID($sponsor_id,true,1);
                    $_SESSION['currentPageSignUp']=array('page_name'=>'SignUp','dataMember'=>$this->member->dataMember);               
                    $this->populateTopCart();
                }            
            }
		}
	}
    public function clickMemberRegister ($sender,$param) {
        if ($this->IsValid) {                  
            $firstname=strtoupper(addslashes(trim($this->txtNama->Text)));            
            $mobilephone=addslashes($this->txtMobilePhone->Text);
            $no_ibo=$mobilephone;
            $email=$this->txtEmail->Text;
            $city=addslashes($this->txtCity->Text);
            $postcode=addslashes($this->txtPostcode->Text);
            $address=addslashes($this->txtPassword->Text);
            $posisi=1;
            $password=$this->Pengguna->createHashPassword($this->txtPassword->Text);            
            $sponsor_id=$_SESSION['currentPageSignUp']['dataMember']['member_id'];            															
			$this->member->setStructure();                        
			$downline=$this->member->structure->getDirectDownlineAvailable('member_id_upline',"'$sponsor_id'");                        
            $this->member->structure->setMemberID($sponsor_id);            
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
                $parent_id=$downline[1]['position']==1?$downline[1]['member_id']:$sponsor_id;  
				$position=$this->member->structure->getLastPosition('left');
                $this->member->structure->setParentID($parent_id);
                $left=$position['lastleft']+2;
                $rightposition=$this->member->structure->getLastPositionByMember('right');                
                $right=$rightposition['lastright'];                                
			}elseif(isset($downline[1])&&$posisi==0){//kiri                                
				$parent_id=$downline[1]['position']==0?$downline[1]['member_id']:$sponsor_id;     
                $position=$this->member->structure->getLastPosition('right');                
                $this->member->structure->setParentID($parent_id);
                $leftposition=$this->member->structure->getLastPositionByMember('left');
                $left=$leftposition['lastleft'];
                $right=$position['lastright']+2;
			}else {                
				$parent_id=$sponsor_id;               
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
            $str="INSERT INTO `members` (`member_id`, `ibo`, `position`, `member_name`, `address`, `city`, `postal_code`, `country_id`, `email`, `mobile_phone`,`salt`,`password`, `sponsor_id`,`parent_id`,`lft`,`rgt`, `date_reg`,`date_modified`) VALUES
					(NULL, '$no_ibo', $posisi, '$firstname', '$address', '$city','$postcode', 105, '$email', '$mobilephone','{$password['salt']}', '{$password['password']}', '$sponsor_id', '$parent_id','$left','$right',NOW(),NOW())";                           
            $this->DB->insertRecord($str);
            $this->redirect('SignUp',array('process'=>'success'));
                
        }   
    }
}
		