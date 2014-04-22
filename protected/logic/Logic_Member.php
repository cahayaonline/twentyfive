<?php
/**
*
* digunakan untuk memproses Data Master 
*
*/
prado::using ('Application.logic.Logic_Global');
class Logic_Member extends Logic_Global {
    /**
	* object data structure
	*/
	public $structure;	
    /**
	* start_date
	*/
	public $startDate=null;	
    /**
	* end_date
	*/
	public $endDate=null;	
	public function __construct ($db) {
		parent::__construct ($db);	
	}
    /**
	* set object structure
	*/
	public function setStructure () {				
		$this->structure = $this->getLogic('DataStructure');
		$this->structure->dataMember = $this->dataMember;
	}
	/**
	* set member id	
	*/	
	public function setMemberID($member_id,$load=false,$mode=0) {
		$this->member_id=$member_id;
		if ($load) {
			$this->dataMember=$this->getDataMember(array('select'=>'member_id','value'=>$member_id,'mode'=>$mode));
		}
	}
	/**
	* set ibo
	*/	
	public function setIBONumber($ibo,$load=false,$mode=0) {
		$this->ibo=$ibo;
		if ($load) {
			$this->dataMember=$this->getDataMember(array('select'=>'member_id','value'=>$ibo,'mode'=>$mode));
		}
	}
    /**
     * 
     * @param type $start_date
     * @param type $end_date
     */
	public function setPeriode($start_date=null,$end_date=null) {
		$this->startDate = $start_date;
        $this->endDate=$end_date;
	}
	/**
	* get data member
	* @param param
	* @return boolean
	*/
	public function getDataMember($param) {
		$result=array();
		$select=$param['select'];
		$value=$param['value'];
		switch($param['mode']) {
			case 0 :
				$str = "SELECT ibo,member_id FROM members WHERE $select='$value'";
				$this->db->setFieldTable(array('ibo','member_id'));
				$r=$this->db->getRecord($str);
				$result=isset($r[1])?$r[1]:array();				
			break;
			case 1 :
				$str = "SELECT member_id,ibo,member_name,date_reg FROM members WHERE $select='$value'";
				$this->db->setFieldTable(array('member_id','ibo','member_name','date_reg'));
				$r=$this->db->getRecord($str);
				$result=isset($r[1])?$r[1]:array();
			break;
			case 2 :
				$str = "SELECT member_id,ibo,member_name,address,city,email,mobile_phone,sponsor_id,lft,rgt,position,date_reg FROM members WHERE $select='$value'";
				$this->db->setFieldTable(array('member_id','ibo','member_name','address','city','email','mobile_phone','sponsor_id','lft','rgt','date_reg','position'));
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
                }                
			break;
            case 3 :
				$str = "SELECT member_id,lft,rgt FROM members WHERE $select='$value'";
				$this->db->setFieldTable(array('member_id','lft','rgt'));
				$r=$this->db->getRecord($str);
                $result=array();
                if (isset($r[1])) {
                    $result=$r[1];                    
                }                
			break;
            case 4 :
                $str = "SELECT member_id,address,city,postal_code FROM members WHERE $select='$value'";
                $this->db->setFieldTable(array('member_id','address','city','postal'));
				$r=$this->db->getRecord($str);
                $result=array();
                if (isset($r[1])) {
                    $result=$r[1];                    
                } 
            break;
            case 5 :
				$str = "SELECT member_id,ibo,member_name,lft,rgt,date_reg FROM members WHERE $select='$value'";
				$this->db->setFieldTable(array('member_id','ibo','member_name','lft','rgt','date_reg'));
				$r=$this->db->getRecord($str);
				$result=isset($r[1])?$r[1]:array();
			break;
		}		
		return $result;
	}	    
	/**
	* mendapatkan jumlah downline
	*/
	public function getCountDownline ($mode='all') {         
        if ($this->member_id=='') {
            $member_id=$this->dataMember['member_id'];
            $lft=$this->dataMember['lft'];
            $rgt=$this->dataMember['rgt'];
        }else {
            $member_id=$this->member_id;
            $datamember=$this->getDataMember(array('select'=>'member_id','value'=>$member_id,'mode'=>3));
            $lft=$datamember['lft'];
            $rgt=$datamember['rgt'];
        }                                              
        if ($mode == 'all') {
            $totaldownline=$this->db->getCountRowsOfTable ("members WHERE (lft=$lft OR rgt=$rgt) AND member_id > $member_id",'member_id');                                                                
        }elseif ($mode == 'right') {
            $totaldownline=$this->db->getCountRowsOfTable ("members WHERE rgt=$rgt AND member_id > $member_id",'member_id');                                                                
        }elseif ($mode == 'left') {
            $totaldownline=$this->db->getCountRowsOfTable ("members WHERE lft=$lft AND member_id > $member_id",'member_id');                                                                            
        }elseif ($mode == 'split'){
            $totaldownline=array('left'=>0,'right'=>0);
            $str="members WHERE lft=$lft AND member_id > $member_id";
            $totaldownline['left']=$this->db->getCountRowsOfTable ($str,'member_id');                                                                
            $str2="members WHERE rgt=$rgt AND member_id > $member_id";
            $totaldownline['right']=$this->db->getCountRowsOfTable ($str2,'member_id');                                                                
        }
        return $totaldownline;	
	}
    /**
	* mendapatkan jumlah sponsor
	*/
	public function getCountSponsoring () {						
		return $this->db->getCountRowsOfTable ("members WHERE sponsor_id={$this->member_id}",'member_id');
	}
    /**
	* mendapatkan last member
	*/
	public function getLastMember ($mode='all') {						
		$member_id=$this->dataMember['member_id'];
        $lft=$this->dataMember['lft'];
        $rgt=$this->dataMember['rgt'];                  
        $lastmember=array('left'=>'-','right'=>'-');
        $str_kanan="SELECT MAX(member_id) AS max FROM members WHERE rgt=$rgt  AND member_id > $member_id";
        $str_kiri="SELECT MAX(member_id) AS max FROM members WHERE lft=$lft  AND member_id > $member_id";
        $this->db->setFieldTable(array('max'));
        if ($mode == 'all') {                        
            $r1=$this->db->getRecord($str_kanan);
            $lastmember['right']=$r1[1]['max']==''?'-':$r1[1]['max'];                       
            $r2=$this->db->getRecord($str_kiri);
            $lastmember['left']=$r2[1]['max']==''?'-':$r2[1]['max'];
        }elseif ($mode == 'left'){
            $r2=$this->db->getRecord($str_kiri);
            $lastmember['left']=$r2[1]['max']==''?'-':$r2[1]['max'];                                                              
        }elseif ($mode == 'right'){
            $r1=$this->db->getRecord($str_kanan);
            $lastmember['right']=$r1[1]['max']==''?'-':$r1[1]['max'];
        }
        return $lastmember;
	}   
    /**
     * digunakan untuk mendapatkan label dan icon status member
     * @param type $id
     */
    public function getLabelMemberStatus($id,$mode='text') {
        switch ($id) {
            case 1 :
                $label=$mode=='text'?'ACTIVE':'';
            break;
            case 2 :
                $label=$mode=='text'?'INACTIVE':'';
            break;
            case 3 :
                $label=$mode=='text'?'CLOSED':'';
            break;
        }        
        return $label;
    }
}
?>