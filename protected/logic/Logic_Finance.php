<?php
prado::using ('Application.logic.Logic_Member');
class Logic_Finance extends Logic_Member {	
    private $caraPembayaran = array('none'=>'----- cara pembayaran ------','tunai'=>'SETORAN TUNAI','transfer'=>'TRANSFER ATM','ibanking'=>'INTERNET BANKING','mbanking'=>'MOBILE BANKING');
    /**
     *
     * @var account balance integer
     */
    private $accountbalance = 0;
    /**
     *
     * @var jumlah bonus
     */
    private $jumlahbonus = 0;
	public function __construct ($db) {
		parent::__construct ($db);	
	}    
    /**
	* digunakan untuk mendapatkan cara pembayaran
	*/
    public function getCaraPembayaran ($id=null) {
        if ($id === NULL) 
            return $this->caraPembayaran;
        else
            return $this->caraPembayaran[$id];
    }
	/**
	* casting ke integer	
	*/
	public function toInteger ($stringNumeric) {
		return str_replace('.','',$stringNumeric);
	}
	/**
	* Untuk mendapatkan uang dalam format rupiah
	* @param angka	
	* @return string dalam rupiah
	*/
	public function toRupiah($angka,$tanpa_rp=true)  {
		if ($angka == '') {
			$angka=0;
		}
		$rupiah='';
        $angka=explode('.',$angka);
        $bilangan=$angka[0];     
        $pecahan=($angka[1]==''||$angka[1]=='00')?'':','.$angka[1];
		$rp=strlen($bilangan);
		while ($rp>3){
			$rupiah = ".". substr($bilangan,-3). $rupiah;
			$s=strlen($bilangan) - 3;
			$bilangan=substr($bilangan,0,$s);
			$rp=strlen($bilangan);
		}
		if ($tanpa_rp) {
			$rupiah = $bilangan . $rupiah.$pecahan;
		}else {
			$rupiah = "Rp. " . $bilangan . $rupiah.$pecahan;
		}
		return $rupiah;
	}
    /**
     * digunakan untuk mendapatkan jumlah bonus
     */
    public function getJumlahBonus () {
        return $this->jumlahbonus;
    }
    /**
	* insert new omset
	*/
	public function insertNewOmset ($orderid,$totalOmset) {		
        $member_id=$this->member_id;
        $pribadi=(25/100)*$totalOmset;
        $group=(25/100)*$totalOmset;
        $lft=$this->dataMember['lft'];
        $rgt=$this->dataMember['rgt'];
        if ($pribadi > 0) {
            $str_pribadi = "INSERT INTO omset (member_id,order_id,komisi,date_added,date_modified) VALUES('$member_id',$orderid,$pribadi,NOW(),NOW())";
            $this->db->insertRecord($str_pribadi);
            $kaki=$group/2;            
            $str_kiri = "INSERT INTO omset_group (member_id,order_id,komisi,date_added,date_modified) SELECT member_id,$orderid,$kaki,NOW(),NOW() FROM members WHERE member_id>$member_id AND lft=$lft";
            $this->db->insertRecord($str_kiri);
            $str_kanan = "INSERT INTO omset_group (member_id,order_id,komisi,date_added,date_modified) SELECT member_id,$orderid,$kaki,NOW(),NOW() FROM members WHERE member_id>$member_id AND rgt=$rgt";
            $this->db->insertRecord($str_kanan);
        }       
    }
    /**
	* digunkana untuk mendapatkan nilai omset belanja member
	*/
	public function getTotalOmsetBelanja () {
        $member_id=$this->member_id==''?$this->dataMember['member_id']:$this->member_id;
        $str_date='';
        if ($this->startDate !== null && $this->endDate !== null){
            $awal=$this->startDate;
            $akhir=$this->endDate;
            $str_date = "AND CAST(date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";
        }
        $str = "SELECT SUM(totalomset) AS jumlah FROM `order` WHERE member_id=$member_id AND order_status_id=5 $str_date";
        $this->db->setFieldTable(array('jumlah'));				
		$r=$this->db->getRecord($str);        
        $omsetbelanja=$r[1]['jumlah']!=''?$r[1]['jumlah']:0;        
        return $omsetbelanja;
    }
    /**
	* digunkana untuk mendapatkan nilai omset belanja toko
	*/
	public function getTotalOmsetBelanjaToko () {        
        $str_date='';
        if ($this->startDate !== null && $this->endDate !== null){
            $awal=$this->startDate;
            $akhir=$this->endDate;
            $str_date = "AND CAST(date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";
        }
        $str = "SELECT SUM(totalomset) AS jumlah FROM `order` WHERE order_status_id=5 $str_date";
        $this->db->setFieldTable(array('jumlah'));				
		$r=$this->db->getRecord($str);        
        $omsetbelanja=$r[1]['jumlah']!=''?$r[1]['jumlah']:0;        
        return $omsetbelanja;
    }
    /**
	* digunkana untuk mendapatkan nilai omset member
	*/
	public function getTotalOmset ($mode='all') {	        
        $member_id=$this->member_id==''?$this->dataMember['member_id']:$this->member_id;
        $lft=$this->dataMember['lft'];
        $rgt=$this->dataMember['rgt'];                                
        $totalomset=0;             
        if ($this->startDate !== null && $this->endDate !== null){
            $awal=$this->startDate;
            $akhir=$this->endDate;            
            $str_all="SELECT SUM(komisi) AS total FROM `order` o,omset om WHERE om.order_id=o.order_id AND o.member_id=$member_id AND udah_dibagi=0 AND CAST(o.date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";                        
            $str_right="SELECT (SUM(komisi)/2) AS totalomsetpribadi FROM members m,omset o WHERE o.member_id=m.member_id AND m.rgt=$rgt AND udah_dibagi=0 AND CAST(o.date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";            
            $str_left="SELECT (SUM(komisi)/2) AS totalomsetpribadi FROM members m,omset o WHERE o.member_id=m.member_id AND m.lft=$lft  AND udah_dibagi=0 AND CAST(o.date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";                        
        }else {
            $str_all="SELECT SUM(komisi) AS total FROM `order` o,omset om WHERE om.order_id=o.order_id AND o.member_id=$member_id AND udah_dibagi=0";                        
            $str_right="SELECT (SUM(komisi)/2) AS totalomsetpribadi FROM members m,omset o WHERE o.member_id=m.member_id AND m.rgt=$rgt AND udah_dibagi=0";            
            $str_left="SELECT (SUM(komisi)/2) AS totalomsetpribadi FROM members m,omset o WHERE o.member_id=m.member_id AND m.lft=$lft  AND udah_dibagi=0";                        
        }    
        if ($mode == 'all') {            
            $this->db->setFieldTable(array('total'));
            $r=$this->db->getRecord($str_all);            
            if ($r[1]['total'])$totalomset=$r[1]['total'];
        }elseif ($mode == 'right') {            
            $this->db->setFieldTable(array('totalomsetpribadi'));
            $r=$this->db->getRecord($str_right);            
            if ($r[1]['totalomsetpribadi'] != '') $totalomset=round($r[1]['totalomsetpribadi'],2);
        }elseif ($mode == 'left') {            
            $this->db->setFieldTable(array('totalomsetpribadi'));
            $r=$this->db->getRecord($str_left);            
            if ($r[1]['totalomsetpribadi'] != '') $totalomset=round($r[1]['totalomsetpribadi'],2);
        }elseif ($mode == 'split'){
            $totalomset=array('left'=>0,'right'=>0);            
            $totalomset['left']=$this->getTotalOmset('left');                                                                            
            $totalomset['right']=$this->getTotalOmset('right');;
        }        
        return $totalomset;
    }    
    /**
	* digunkana untuk mendapatkan nilai omset member
	*/
	public function getTotalOmsetToko ($mode='all') {	                
        $lft=$this->dataMember['lft'];
        $rgt=$this->dataMember['rgt'];                                
        $totalomset=0;             
        if ($this->startDate !== null && $this->endDate !== null){
            $awal=$this->startDate;
            $akhir=$this->endDate;            
            $str_all="SELECT SUM(komisi) AS total FROM `order` o,omset om WHERE om.order_id=o.order_id AND udah_dibagi=0 AND CAST(o.date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";                        
            $str_right="SELECT SUM(komisi) AS totalomsetpribadi FROM members m,omset o WHERE o.member_id=m.rgt AND udah_dibagi=0 AND CAST(o.date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";            
            $str_left="SELECT SUM(komisi) AS totalomsetpribadi FROM members m,omset o WHERE o.member_id=m.lft AND udah_dibagi=0 AND CAST(o.date_modified AS DATE) BETWEEN CAST('$awal' AS DATE) AND CAST('$akhir' AS DATE)";                        
        }else {
            $str_all="SELECT SUM(komisi) AS total FROM `order` o,omset om WHERE om.order_id=o.order_id AND udah_dibagi=0";                        
            $str_right="SELECT (SUM(komisi)/2) AS totalomsetpribadi FROM members m,omset o WHERE o.member_id=m.rgt AND udah_dibagi=0";            
            $str_left="SELECT (SUM(komisi)/2) AS totalomsetpribadi FROM members m,omset o WHERE o.member_id=m.lft AND udah_dibagi=0";                        
        }        
        if ($mode == 'all') {            
            $this->db->setFieldTable(array('total'));
            $r=$this->db->getRecord($str_all);            
            if ($r[1]['total'])$totalomset=$r[1]['total'];
        }elseif ($mode == 'right') {            
            $this->db->setFieldTable(array('totalomsetpribadi'));
            $r=$this->db->getRecord($str_right);            
            if ($r[1]['totalomsetpribadi'] != '') $totalomset=round($r[1]['totalomsetpribadi'],2);
        }elseif ($mode == 'left') {            
            $this->db->setFieldTable(array('totalomsetpribadi'));
            $r=$this->db->getRecord($str_left);            
            if ($r[1]['totalomsetpribadi'] != '') $totalomset=round($r[1]['totalomsetpribadi'],2);
        }elseif ($mode == 'split'){
            $totalomset=array('left'=>0,'right'=>0);            
            $totalomset['left']=$this->getTotalOmset('left');                                                                            
            $totalomset['right']=$this->getTotalOmset('right');;
        }        
        return $totalomset;
    }
    /**
     * digunakan untuk mendapatkan total order sesuai kode dan metode pembayaran
     * 
     */
    public function getTotalOrder ($order_status_id=5,$payment_method=null) {
        $str_date='';
        if ($this->startDate !== null && $this->endDate !== null){
            $awal=$this->startDate;
            $akhir=$this->endDate;
            $str_date = "AND date_modified BETWEEN CAST('$awal' AS DATETIME) AND CAST('$akhir' AS DATETIME)";
        }
        $member_id=$this->member_id; 
        $str = "SELECT SUM(totalprice) AS totalorder FROM `order` WHERE member_id=$member_id AND order_status_id=$order_status_id $str_date";        
        $str = $payment_method===null?'':"$str AND idpayment_method=$payment_method";
        $this->db->setFieldTable(array('totalorder'));				
        $r=$this->db->getRecord($str); 
        $jumlah=0;
        if ($r[1]['totalorder']!='') {
            $jumlah=$r[1]['totalorder'];            
        }
        return $jumlah;
    }
    /**
     * digunakan untuk mendapatkan saldo terakhir
     */
    public function getAccountBalance($mode='saldo_deposit') {
        $member_id=$this->member_id;
        $str="SELECT d.sisa_bonus,d.saldo_deposit FROM deposit d WHERE iddeposit=(SELECT MAX(d2.iddeposit) FROM deposit d2 WHERE d2.member_id=$member_id)";        
        $this->db->setFieldTable(array('sisa_bonus','saldo_deposit'));				
		$r=$this->db->getRecord($str);                 
        if (isset($r[1])) {
            switch ($mode) {
                case 'all':
                    $accountbalance=$r[1];                    
                break;
                case 'sisa_bonus' :
                    $accountbalance=$r[1]['sisa_bonus'];
                break;
                default :
                    $accountbalance=$r[1]['saldo_deposit'];
            }
        }else {
            $accountbalance=$mode=='all'?array('sisa_bonus'=>0,'saldo_deposit'=>0):0;                                
        }
        $this->accountbalance=$accountbalance;
        return $accountbalance;
    }   
    /**
     * digunakan untuk menghitung Bonus Pribadi
     */
    public function calculateBonusPribadi ($totalomset=0) {
        $bonuspribadi=$totalomset >0?($totalomset/100)*100:0;        
        return $bonuspribadi;
    }
    /**
     * digunakan untuk menghitung Bonus Group
     */
    public function calculateBonusGroup ($groupright=0,$groupleft=0) {
        $bonusgroup=0;
        if ($groupleft >0 && $groupright > 0) {
            $bonusgroup=$groupright <= $groupleft?($groupright/100)*100:($groupleft/100)*100; 
        }
        return $bonusgroup;
    }
    /**
     * digunakan untuk menghitung bonus deposit
     */
    public function calculateBonusDeposit ($totalbonus=0) {
        $totalbonusdeposit=$totalbonus < 2500000?$totalbonus:0.25*$totalbonus;        
        return $totalbonusdeposit;
    }
    /**
     * digunakan untuk menghitung mengexpire-kan bonus     
     */
    public function doExpiringBonus ($transaction=false) {
        $member_id=$this->member_id;
        $accountbalance=is_array($this->accountbalance)?$this->accountbalance:$this->getAccountBalance('all');        
        $deposit_terakhir=$accountbalance['saldo_deposit'];
        $sisa_bonus=$accountbalance['sisa_bonus'];                  
        $saldo_deposit=$deposit_terakhir-$sisa_bonus;
        $str = "SELECT iddeposit,jumlah_transfer FROM deposit WHERE iddeposit=(SELECT MAX(iddeposit) FROM deposit WHERE member_id=$member_id AND jenis='bonus' AND expires=0)";
        $this->db->setFieldTable(array('iddeposit','jumlah_transfer'));
        $r=$this->db->getRecord($str);
        if (isset($r[1]) && $transaction==true) {
            $iddeposit=$r[1]['iddeposit'];
            $str = "UPDATE deposit SET expires=1 WHERE iddeposit=$iddeposit";
            $this->DB->query('BEGIN');         
            if ($this->db->updateRecord($str)) { 
                $jumlah_bonus=$r[1]['jumlah_transfer'];
                $this->jumlahbonus=$jumlah_bonus;
                $str = "INSERT INTO deposit (member_id,jumlah_transfer,sisa_bonus,jumlah_expires,saldo_deposit,jenis,expires,tanggal_expires) VALUES($member_id,$jumlah_bonus,0,$sisa_bonus,$saldo_deposit,'exp',1,NOW())";            
                $this->db->insertRecord($str);
                $this->DB->query('COMMIT');     
                return true;
            }else {
                $this->DB->query('ROLLBACK');     
                return false;
            }            
        }elseif (isset($r[1]) && $transaction==false) {
            $iddeposit=$r[1]['iddeposit'];
            $str = "UPDATE deposit SET expires=1 WHERE iddeposit=$iddeposit";
            $this->db->updateRecord($str);
            $jumlah_bonus=$r[1]['jumlah_transfer'];
            $this->jumlahbonus=$jumlah_bonus;
            $str = "INSERT INTO deposit (member_id,jumlah_transfer,sisa_bonus,jumlah_expires,saldo_deposit,jenis,expires,tanggal_expires) VALUES($member_id,$jumlah_bonus,0,$sisa_bonus,$saldo_deposit,'exp',1,NOW())";            
            $this->db->insertRecord($str);
            return true;
        }else {
            return false;
        }        
    }
    /**
     * digunakan untuk mendapatkan jumlah deposit
     */
    public function getJumlahDeposit() {
        $str = "SELECT SUM(d.saldo_deposit) AS jumlah FROM deposit d LEFT JOIN members m ON (m.member_id=d.member_id) WHERE iddeposit=(SELECT MAX(iddeposit) FROM deposit d2 WHERE d2.member_id=m.member_id)";
        $this->db->setFieldTable(array('jumlah'));
        $r=$this->db->getRecord($str);
        $jumlah = $r[1]['jumlah']==''?0:$r[1]['jumlah'];
        return $jumlah;
    }
}
?>
		