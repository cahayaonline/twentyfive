<?php
class UserManager extends TAuthManager {
	/**
	* Obj DB
	*/
	private $db;		
	/**
	* Username
	*/
	private $username;				
	/**
	* page
	*/
	private $page;
	/**
	* data user
	*/
	private $dataUser=array('data_user'=>array(),'hak_akses'=>array());
	
	public function __construct () {
		$this->db = $this->Application->getModule('db')->getLink();						
	}
		
	/**
	* digunakan untuk mengeset username serta mensplit username dan page	
	*/
	public function setUser ($username) {
		$username = explode('/',$username);        
		$this->username=$username[0];
		$this->page=$username[1];				
	}
	/**
	* get roles username	
	*/
	public function getDataUser () {				
		$username=$this->username;
		$dataUser=array();
		if ($this->page == 'm') {
			$str = "SELECT userid,username,userpassword,salt,page,active FROM user WHERE username='$username'";
			$this->db->setFieldTable (array('userid','username','userpassword','salt','page','active'));							
			$r = $this->db->getRecord($str);				
			$dataUser=$r[1];			
			$dataUser['page']='m';
			$this->dataUser['data_user']=$dataUser;	
		}elseif ($this->page == 'a'){
            $str = "SELECT member_id AS userid,member_id,ibo,member_name,address,city,email,mobile_phone,sponsor_id,lft,rgt,position,date_reg FROM members WHERE mobile_phone='$username' OR email='$username'";
            $this->db->setFieldTable(array('userid','member_id','ibo','member_name','address','city','email','mobile_phone','sponsor_id','lft','rgt','date_reg','position'));
            $r=$this->db->getRecord($str);                
            $result=array();
            if (isset($r[1])) {
                $result=$r[1];
                if ($result['sponsor_id']==0){
                    $result['sponsor_name']='-';
                    $result['position']='-';
                }else{
                    $str = "SELECT member_name FROM members WHERE member_id='{$result['sponsor_id']}'";
                    $this->db->setFieldTable(array('member_name'));
                    $r=$this->db->getRecord($str);
                    $result['sponsor_name']=$r[1]['member_name'];                        
                    $result['position']=$result['position']=='1'?'Kanan':'Kiri';
                }
                $dataUser=$result;			
                $dataUser['page']='a';
                $this->dataUser['data_user']=$dataUser;
            }			
        }
		return $dataUser;
	}
	/**
	* digunakan untuk mendapatkan data user	
	*/
	public function getUser () {	
		if ($this->page == 'm') {	
			$str = "SELECT userpassword,page,salt FROM user WHERE username='{$this->username}'";
			$this->db->setFieldTable (array('userpassword','salt','page'));							
			$r = $this->db->getRecord($str);				
			$result=isset($r[1])?$r[1]:array();							
		}elseif ($this->page == 'a') {
            $str = "SELECT password AS userpassword,salt FROM members WHERE mobile_phone='{$this->username}' OR email='{$this->username}'";
			$this->db->setFieldTable (array('userpassword','salt'));							
			$r = $this->db->getRecord($str);				            
			$result=isset($r[1])?$r[1]:array();							
        }
		return $result;
	}	
}

?>