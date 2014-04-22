<?php

class LogicFactory extends TModule {	
	/**
	*
	* objek db
	*/
	private $db;
	
	public function init ($config) {
		$this->db = $this->Application->getModule ('db')->getLink();	
	}			
	
	/**
	* get instance of 
	*
	*/
	public function getInstanceOfClass ($className) {		
		switch ($className) {
			case 'Users' :
				prado::using ('Application.logic.Logic_Users');
				return new Logic_Users ($this->db);
			break;	
			case 'DMaster' :
				prado::using ('Application.logic.Logic_DMaster');
				return new Logic_DMaster ($this->db);
			break;
			case 'Penanggalan' :
				prado::using ('Application.logic.Logic_Penanggalan');
				return new Logic_Penanggalan ($this->db);
			break;			
			case 'Member' :
				prado::using ('Application.logic.Logic_Member');
				return new Logic_Member ($this->db);
			break;
            case 'Product' :
				prado::using ('Application.logic.Logic_Product');
				return new Logic_Product ($this->db);
			break;			           
            case 'Finance' :
				prado::using ('Application.logic.Logic_Finance');
				return new Logic_Finance ($this->db);
			break;
			case 'DataStructure' :
				prado::using ('Application.logic.Logic_DataStructure');
				return new Logic_DataStructure ($this->db);
			break;
			case 'Setup' :
				prado::using ('Application.logic.Logic_Setup');
				return new Logic_Setup ($this->db);
			break;
			default :
				throw new Exception ("Logic_Factory.php :: $className tidak di ketahui");
		}
	}
}
?>