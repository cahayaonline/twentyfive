<?php
prado::using ('Application.pages.m.report.MainPageReport');
class ReportBonusDeposit extends MainPageReport {    
	public function onLoad($param) {		
		parent::onLoad($param);								
        $this->showReportBonusDeposit=true;        
        $this->createObjFinance();        
		if (!$this->IsPostBack&&!$this->IsCallBack) {            				            
            if (!isset($_SESSION['currentPageReportBonusDeposit'])||$_SESSION['currentPageReportBonusDeposit']['page_name']!='m.member.ReportBonusDeposit') {                
                $_SESSION['currentPageReportBonusDeposit']=array('page_name'=>'m.member.ReportBonusDeposit','periodeawal'=>date('Y-m-01'),'search'=>false);												
            }
            $_SESSION['currentPageReportBonusDeposit']['search']=false;
            $this->cmbPeriodAwal->Text=$this->TGL->tanggal('m-Y',$_SESSION['currentPageReportBonusDeposit']['periodeawal']);            
            $this->populateData();
                        
		}
	}      
    public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageReportBonusDeposit']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageReportBonusDeposit']['search']);
	}
    public function filterRecord ($sender,$param) {
        $_SESSION['currentPageReportBonusDeposit']['search']=true;
        $_SESSION['currentPageReportBonusDeposit']['periodeawal']=date('Y-m-01',$this->cmbPeriodAwal->TimeStamp);
        $this->populateData($_SESSION['currentPageReportBonusDeposit']['search']);
    }
    public function populateData($search=false) {	
        $bulan=$this->TGL->tanggal('Y-m',$_SESSION['currentPageReportBonusDeposit']['periodeawal']);                    
        $str = "SELECT d.member_id,m.ibo,m.member_name,SUM(d.sisa_bonus) AS sisa_bonus,SUM(d.jumlah_expires) AS jumlah_expires,SUM(d.saldo_deposit) AS saldo_deposit FROM deposit d,members m WHERE m.member_id=d.member_id AND DATE_FORMAT(tanggal_transfer,'%Y-%m')='$bulan' ";
        if ($search){            
            $membername=  addslashes(trim($this->membername->Text));
            $ibo=addslashes(trim($this->noibo->Text));             
            if ($ibo == '' && $membername == '') {
                $cluasa='';
            }elseif($ibo == '' && $membername != ''){
                $cluasa="AND member_name LIKE '%$membername%'";
            }elseif($ibo != '' && $membername == ''){ 
                $cluasa="AND ibo='$ibo'";
            }else {
                $cluasa="AND ibo=='%$ibo%' AND member_name LIKE '%$membername%'";
            }            
            $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT COUNT(d.member_id) AS jumlah_baris FROM deposit d,members m WHERE m.member_id=d.member_id AND DATE_FORMAT(tanggal_transfer,'%Y-%m')='2014-02' $cluasa GROUP BY d.member_id) AS temp");        
            $str = "$str $cluasa";
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ("(SELECT COUNT(d.member_id) AS jumlah_baris FROM deposit d,members m WHERE m.member_id=d.member_id AND DATE_FORMAT(tanggal_transfer,'%Y-%m')='2014-02' GROUP BY d.member_id) AS temp");        
        }
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageReportBonusDeposit']['page_num'];				
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageReportBonusDeposit']['page_num']=0;}
		$str = "$str GROUP BY d.member_id ORDER BY member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','sisa_bonus','jumlah_expires','saldo_deposit'));
		$r=$this->DB->getRecord($str,$offset+1);         
        $result=array();
        while (list($k,$v)=each($r)) {
            $result[$k]=$v;
        }
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();		
	}       
}
		
