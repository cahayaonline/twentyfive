<?php
prado::using ('Application.pages.m.members.MainPageMembers');
class KonfirmasiDeposit extends MainPageMembers {
	public function onLoad($param) {		
		parent::onLoad($param);				
        $this->showKonfirmasiDeposit=true;
        $this->createObjFinance();
        if (!$this->IsPostBack&&!$this->IsCallBack) {           
            if (!isset($_SESSION['currentPageKonfirmasiDeposit'])||$_SESSION['currentPageKonfirmasiDeposit']['page_name']!='m.members.KonfirmasiDeposit') {
                $_SESSION['currentPageKonfirmasiDeposit']=array('page_name'=>'m.members.KonfirmasiDeposit','page_num'=>0,'datamember'=>array(),'filter_date'=>date('Y-m'),'search'=>'false','cmbKriteria'=>'bulanan','tanggalreport'=>date ('Y-m-d'),'bulanreport'=>date ('Y-m'),'cmbBankTujuan'=>'none');												
            }			
            $_SESSION['currentPageKonfirmasiDeposit']['search']=false;            
            $this->cmbBankTujuan->DataSource=$this->setup->getLinkBank();
            $this->cmbBankTujuan->Text=$_SESSION['currentPageKonfirmasiDeposit']['cmbBankTujuan'];
            $this->cmbBankTujuan->dataBind();                    
            $this->cmbKriteria->Text=$_SESSION['currentPageKonfirmasiDeposit']['cmbKriteria'];
            
            switch ($_SESSION['currentPageKonfirmasiDeposit']['cmbKriteria']) {
                case 'harian' :
                    $tanggalreport=$_SESSION['currentPageKonfirmasiDeposit']['tanggalreport'];
                    $this->txtBoxTitle->Text='Tanggal '.$this->TGL->tanggal ('d F Y',$tanggalreport);
                    $this->cmbHarian->Text=$this->TGL->tanggal('d-m-Y',$tanggalreport);
                    $sql="WHERE DATE_FORMAT(tanggaltransfer,'%Y-%m-%d')='$tanggalreport'";
                break;                
                case 'bulanan' :
                    $bulanreport=$_SESSION['currentPageKonfirmasiDeposit']['bulanreport'];
                    $this->txtBoxTitle->Text='Bulan '.$this->TGL->tanggal('F Y',"$bulanreport-01");                    
                    $this->cmbBulanan->Text=$this->TGL->tanggal('m-Y',"$bulanreport-01");
                    $sql="WHERE DATE_FORMAT(tanggaltransfer,'%Y-%m')='$bulanreport'";
                break;                
            }            
            $this->populateData($_SESSION['currentPageKonfirmasiDeposit']['search'],$sql);
        }
	}  
    public function changeBankTujuan ($sender,$param) {
        $_SESSION['currentPageKonfirmasiDeposit']['cmbBankTujuan']=$this->cmbBankTujuan->Text;
        $this->redirect('m.members.KonfirmasiDeposit');
    }
    public function changeCmbKriteria ($sender,$param) {
        $_SESSION['currentPageKonfirmasiDeposit']['cmbKriteria']=$this->cmbKriteria->Text;
        $this->redirect('m.members.KonfirmasiDeposit');
    }
    public function filterTanggal ($sender,$param) {        
        switch ($_SESSION['currentPageKonfirmasiDeposit']['cmbKriteria']) {
            case 'harian' :
                $tanggalreport=date('Y-m-d',$this->cmbHarian->TimeStamp);
                $_SESSION['currentPageKonfirmasiDeposit']['tanggalreport']=$tanggalreport;
                $this->txtBoxTitle->Text='Tanggal '.$this->TGL->tanggal ('d F Y',$tanggalreport);
                $this->cmbHarian->Text=$this->TGL->tanggal('d-m-Y',$tanggalreport);
                $sql="WHERE DATE_FORMAT(tanggaltransfer,'%Y-%m-%d')='$tanggalreport'";
            break;                
            case 'bulanan' :
                $bulanreport=date('Y-m',$this->cmbBulanan->TimeStamp);
                $_SESSION['currentPageKonfirmasiDeposit']['bulanreport']=$bulanreport;                
                $this->txtBoxTitle->Text='Bulan '.$this->TGL->tanggal('F Y',"$bulanreport-01");                    
                $this->cmbBulanan->Text=$this->TGL->tanggal('m-Y',"$bulanreport-01");
                $sql="WHERE DATE_FORMAT(tanggaltransfer,'%Y-%m')='$bulanreport'";
            break;                
        }        
        $this->populateData($_SESSION['currentPageKonfirmasiDeposit']['search'],$sql);
    }
    public function searchRecord ($sender,$param) {        
        $_SESSION['currentPageKonfirmasiDeposit']['search']=true;
        switch ($_SESSION['currentPageKonfirmasiDeposit']['cmbKriteria']) {
            case 'harian' :
                $tanggalreport=$_SESSION['currentPageKonfirmasiDeposit']['tanggalreport'];
                $this->txtBoxTitle->Text='Tanggal '.$this->TGL->tanggal ('d F Y',$tanggalreport);
                $this->cmbHarian->Text=$this->TGL->tanggal('d-m-Y',$tanggalreport);
                $sql="WHERE DATE_FORMAT(tanggaltransfer,'%Y-%m-%d')='$tanggalreport'";
            break;                
            case 'bulanan' :
                $bulanreport=$_SESSION['currentPageKonfirmasiDeposit']['bulanreport'];
                $this->txtBoxTitle->Text='Bulan '.$this->TGL->tanggal('F Y',"$bulanreport-01");                    
                $this->cmbBulanan->Text=$this->TGL->tanggal('m-Y',"$bulanreport-01");
                $sql="WHERE DATE_FORMAT(tanggaltransfer,'%Y-%m')='$bulanreport'";
            break;                
        }        
        $this->populateData($_SESSION['currentPageKonfirmasiDeposit']['search'],$sql);
    }
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageKonfirmasiDeposit']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageKonfirmasiDeposit']['search']);
	}    
    public function populateData($search=false,$sql) {	                
        $idbanktujuan=$_SESSION['currentPageKonfirmasiDeposit']['cmbBankTujuan'];
        $str_banktujuan=$idbanktujuan=='none'?'':" AND kd.norek=$idbanktujuan";        
        if ($search){            
            $kriteria=$this->txtKriteria->Text;
            switch ($this->cmbKriteria2->Text) {
                case 'ibo' :
                    $clausa = " AND m.ibo='$kriteria'";                    
                break;
                case 'member_name' :
                    $clausa = " AND m.member_name LIKE '%$kriteria%'";                    
                break;
                case 'kode_transfer' :
                    $clausa = " AND kd.idkonfirmasideposit='$kriteria'";                    
                break;            
            }
            $str = "SELECT idkonfirmasideposit,m.member_id,m.ibo,m.member_name,tanggaltransfer,carapembayaran,jumlahditransfer,kd.norek,nama_bank FROM konfirmasideposit kd LEFT JOIN members m ON (m.member_id=kd.member_id) LEFT JOIN bank b ON (b.norek=kd.norek) $sql $str_banktujuan $clausa";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("konfirmasideposit kd JOIN members m ON (kd.member_id=m.member_id) $sql $str_banktujuan $clausa",'idkonfirmasideposit');            
        }else {                        
            $str = "SELECT idkonfirmasideposit,m.member_id,m.ibo,m.member_name,tanggaltransfer,carapembayaran,jumlahditransfer,kd.norek,nama_bank FROM konfirmasideposit kd LEFT JOIN members m ON (m.member_id=kd.member_id) LEFT JOIN bank b ON (b.norek=kd.norek) $sql $str_banktujuan";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("konfirmasideposit kd $sql $str_banktujuan",'idkonfirmasideposit');
        }
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageKonfirmasiDeposit']['page_num'];				
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageKonfirmasiDeposit']['page_num']=0;}
		$str = "$str ORDER BY kd.tanggaltransfer DESC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('idkonfirmasideposit','member_id','ibo','member_name','tanggaltransfer','carapembayaran','jumlahditransfer','norek','nama_bank'));
		$r=$this->DB->getRecord($str,$offset+1);      
		
		$this->RepeaterS->DataSource=$r;
		$this->RepeaterS->dataBind();		
		
	}   
    
}