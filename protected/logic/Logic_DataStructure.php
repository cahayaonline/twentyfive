<?php
/**
*
* digunakan untuk memproses Data Master 
*
*/
prado::using ('Application.logic.Logic_Global');

class Logic_DataStructure extends Logic_Global {    
    
	public function __construct ($db) {
		parent::__construct ($db);	
	}	
	/**
	* untuk mengecek daftar downline 
	* baik yang kiri atau kanan	 
	* @return boolean
	*/
	public function getDirectDownlineAvailable($param,$value) {
		$str = "SELECT position,member_id FROM v_direct_downline WHERE $param=$value";
		$this->db->setFieldTable(array('position','member_id'));
		$r=$this->db->getRecord($str);			
		return $r;
	}
	/**
	* untuk mengecek apakah downline langsung sudah penuh atau belum
	* baik yang kiri atau kanan	 
	* @return boolean
	*/
	public function isDirectDownlineAvailable($param,$value,$position='') {						
		$str = "SELECT position FROM v_direct_downline WHERE $param=$value $position";
		$this->db->setFieldTable(array('position'));
		$r=$this->db->getRecord($str);		
		if (isset($r[1]) && isset($r[2]))
			return false;		
		elseif (isset($r[1]))
			return false;
		else
			return true;		
	}
	/**
	* digunakan untuk mendapatkan member id terakhir dari seorang sponsor
	* @return member id
	*/
	public function getLastMemberOfSponsor($position) {
		$table=$position==1?'v_last_member_right':'v_last_member_left';
		$str = "SELECT last_member_id FROM $table WHERE sponsor_id={$this->member_id}";
		$this->db->setFieldTable(array('last_member_id'));
		$r=$this->db->getRecord($str);
		return isset($r[1])?$r[1]['last_member_id']:false;
	}
    /**
     * digunakan untuk mendapat nilai left dan right terakhir dari member
     * 
     */
    public function getLastPosition ($position=null) {
        if ($position=='right'){
            $str = 'MAX(rgt) AS lastright';
            $field=array('lastright');
        }else if($position=='left') {
            $str = 'MAX(lft) AS lastleft';
            $field=array('lastleft');
        }else {
            $str = 'MAX(rgt) AS lastright,MAX(lft) AS lastleft';
            $field=array('lastright','lastleft');
        }        
        $str = "SELECT $str FROM members";
        $this->db->setFieldTable($field);
        $r=$this->db->getRecord($str);
        return $r[1];
    }
     /**
     * digunakan untuk mendapat nilai left dan right terakhir dari member
     * 
     */
    public function getLastPositionByMember ($position=null) {
        if ($position=='right'){
            $str = 'MAX(rgt) AS lastright';
            $field=array('lastright');
        }else if($position=='left') {
            $str = 'MAX(lft) AS lastleft';
            $field=array('lastleft');
        }else {
            $str = 'MAX(rgt) AS lastright,MAX(lft) AS lastleft';
            $field=array('lastright');
        }        
        $str = "SELECT $str  FROM members WHERE member_id={$this->parent_id}";
        $this->db->setFieldTable($field);
        $r=$this->db->getRecord($str);
        return $r[1];
    }        
}
?>