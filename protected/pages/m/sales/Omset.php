<?php
prado::using ('Application.pages.m.sales.MainPageSales');
class Omset extends MainPageSales {
	public function onLoad($param) {			
		parent::onLoad($param);				
		$this->showOmset = true;
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
			if (!isset($_SESSION['currentPageOmset'])||$_SESSION['currentPageOmset']['page_name']!='m.sales.Omset') {
				$_SESSION['currentPageOmset']=array('page_name'=>'m.sales.Omset','page_num'=>0,'search'=>false,'withdate'=>false,'periodeawal'=>date('Y-m-d'),'periodeakhir'=>date('Y-m-d'));												
			}		
            $_SESSION['currentPageOmset']['search']=false;
            $this->chkWithDate->Checked=$_SESSION['currentPageOmset']['withdate'];            
            $this->cmbPeriodAkhir->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageOmset']['periodeakhir']);
            $this->cmbPeriodAwal->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageOmset']['periodeawal']);
			$this->populateData();
		}	        
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageOmset']['page_num']=$param->NewPageIndex;
		$this->populateData($_SESSION['currentPageOmset']['search']);
	}
	public function searchClient ($sender,$param) {
		$_SESSION['currentPageOmset']['search']=true; 
        $_SESSION['currentPageOmset']['withdate']=$this->chkWithDate->Checked; 
        $_SESSION['currentPageOmset']['periodeawal']=date('Y-m-d',$this->cmbPeriodAwal->TimeStamp);
        $_SESSION['currentPageOmset']['periodeakhir']=date('Y-m-d',$this->cmbPeriodAkhir->TimeStamp);
        $this->populateData($_SESSION['currentPageOmset']['search']);
	}
	public function populateData($search=false) {	        
        $str = 'SELECT member_id,ibo,member_name,lft,rgt FROM members';
        if ($search){            
            $membername=$this->membername->Text;
            $cluasa="WHERE member_name LIKE '%$membername%'";
            $jumlah_baris=$this->DB->getCountRowsOfTable ("members $cluasa",'member_id');
            $str = "$str $cluasa";            
        }else {
            $jumlah_baris=$this->DB->getCountRowsOfTable ('members','member_id');
        }
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageOmset']['page_num'];				
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;		
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageOmset']['page_num']=0;}
		$str = "$str ORDER BY date_reg DESC,member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','lft','rgt'));
		$r=$this->DB->getRecord($str,$offset+1);
        $result=array();       
        if ($_SESSION['currentPageOmset']['withdate']) {
            $awal=$this->cmbPeriodAwal->TimeStamp;
            $akhir=$this->cmbPeriodAkhir->TimeStamp;
            if ($awal <= $akhir) {
                $this->finance->setPeriode(date('Y-m-d',$awal),date('Y-m-d',$akhir));
            }
        }
		foreach ($r as $k=>$v) {   
            $this->finance->dataMember=array('member_id'=>$v['member_id'],'lft'=>$v['lft'],'rgt'=>$v['rgt']);
            $totalomset=$this->finance->getTotalOmset();
            $v['totalomset']=$this->finance->toRupiah($totalomset);            
            $omsetbelanja=$this->finance->getTotalOmsetBelanja ();
            $v['omsetbelanja']=$this->finance->toRupiah($omsetbelanja);
            $v['totalomsetgroupkanan']=$this->finance->toRupiah($this->finance->getTotalOmset ('right'));
            $v['totalomsetgroupkiri']=$this->finance->toRupiah($this->finance->getTotalOmset ('left'));
            
            $result[$k]=$v;
        }
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();		
		
	}  
}