<?php
prado::using ('Application.pages.m.report.MainPageReport');
class Reports extends MainPageReport {
	public function onLoad($param) {			
		parent::onLoad($param);				
		$this->showReportHome = true;
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageSummaryReports'])||$_SESSION['currentPageSummaryReports']['page_name']!='m.Reports') {
                $_SESSION['currentPageSummaryReports']=array('page_name'=>'m.Reports','page_num_right'=>0,'page_num_left'=>0,'withdate'=>false,'periodeawal'=>date('Y-m-d'),'periodeakhir'=>date('Y-m-d'));												
            }            
            $this->chkWithDate->Checked=$_SESSION['currentPageSummaryReports']['withdate'];  
            $this->cmbPeriodAwal->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageSummaryReports']['periodeawal']);
            $this->cmbPeriodAkhir->Text=$this->TGL->tanggal('d-m-Y',$_SESSION['currentPageSummaryReports']['periodeakhir']);
			$this->populateData ();
		}	        
	}
    public function filterReports ($sender,$param) {
		$_SESSION['currentPageSummaryReports']['search']=true; 
        $_SESSION['currentPageSummaryReports']['withdate']=$this->chkWithDate->Checked; 
        $_SESSION['currentPageSummaryReports']['periodeawal']=date('Y-m-d',$this->cmbPeriodAwal->TimeStamp);
        $_SESSION['currentPageSummaryReports']['periodeakhir']=date('Y-m-d',$this->cmbPeriodAkhir->TimeStamp);
        $this->populateData();
	}
    public function populateData () {                
        if ($_SESSION['currentPageSummaryReports']['withdate']) {            
            $this->finance->setPeriode($_SESSION['currentPageSummaryReports']['periodeawal'],$_SESSION['currentPageSummaryReports']['periodeakhir']);
        }                    
        $totalbelanja=$this->finance->getTotalOmsetBelanjaToko ();        
        $this->dataToko['totalbelanja']=$totalbelanja;                      
        $totalomset=$this->finance->getTotalOmsetToko ();        
        $this->dataToko['totalomset']=$totalomset;                      
        
        $str = "SELECT member_id FROM members";
        $this->DB->setFieldTable(array('member_id'));
        $r=$this->DB->getRecord($str);
        $totalbonuspribadi=0;
        $totalbonusgroup=0;
        while (list($k,$v)=each($r)) {
            $member_id=$v['member_id'];
            $this->finance->setMemberID($member_id,true,3);    
            $totalomset=$this->finance->getTotalOmset ();
            $bonuspribadi=$this->finance->calculateBonusPribadi($totalomset);
            $totalbonuspribadi+=$bonuspribadi;            
            
            $groupright=$this->finance->getTotalOmset ('right');
            $groupleft=$this->finance->getTotalOmset ('left');            
            $bonusgroup=$this->finance->calculateBonusGroup($groupright,$groupleft);            
            $totalbonusgroup+=$bonusgroup;
        }
        
        $this->dataToko['bonuspribadi']=$totalbonuspribadi;                
        $this->dataToko['bonusgroup']=$totalbonusgroup;                
        
        $totalbonus=$totalbonuspribadi+$totalbonusgroup;
        $this->dataToko['totalbonus']=$totalbonus;
        $totalbonusdeposit=$this->finance->calculateBonusDeposit($totalbonus);
        $this->dataToko['totalbonusdeposit']=$totalbonusdeposit;
        $totalbonuscash = $totalbonus < 2500000?0:0.75*$totalbonus;               
        $this->dataToko['totalbonuscash']=$totalbonuscash;
        $this->dataToko['totalomsettoko']=$totalbelanja-$totalbonus;
        $totaldepositexpired=0;
        $this->dataToko['totalpendapatan']=$this->dataToko['totalomsettoko']+$totaldepositexpired;
	}
    public function populateData2 () {                
       $downline=$this->member->getCountDownline('split');                 
        $this->dataMember['left']=$downline['left'];
        $this->dataMember['right']=$downline['right'];
        if ($_SESSION['currentPageSummaryMember']['withdate']) {            
            $this->finance->setPeriode($_SESSION['currentPageSummaryMember']['periodeawal'],$_SESSION['currentPageSummaryMember']['periodeakhir']);
        }
        $this->finance->setMemberID($this->member_id);                
        $totalomset=$this->finance->getTotalOmset ();
        $omsetgroup=round(($totalomset/2),2);
        $this->dataMember['totalomset']=$totalomset;                
        $this->dataMember['omsetpribadigroupleft']=$omsetgroup;
        $this->dataMember['omsetpribadigroupright']=$omsetgroup;

        $groupright=$this->finance->getTotalOmset ('right');               
        $this->dataMember['totalomsetgroupright']=$groupright;

        $groupleft=$this->finance->getTotalOmset ('left');               
        $this->dataMember['totalomsetgroupleft']=$groupleft;

        $bonuspribadi=($totalomset/100)*100;
        $this->dataMember['bonuspribadi']=$bonuspribadi;

        if ($groupright <= $groupleft)
            $bonusgroup=($groupright/100)*100;
        else
            $bonusgroup=($groupleft/100)*100;
        $this->dataMember['bonusgroup']=$bonusgroup;

        $totalbonus=$bonuspribadi+$bonusgroup;
        $this->dataMember['totalbonus']=$totalbonus;

        if ($totalbonus < 2500000)
            $totalbonusdeposit=$totalbonus;
        else
            $totalbonusdeposit=0.25*$totalbonus;

        $this->dataMember['totalbonusdeposit']=$totalbonusdeposit;

        if ($totalbonus < 2500000)
            $totalbonuscash=0;
        else
            $totalbonuscash=0.75*$totalbonus;

        $this->dataMember['totalbonuscash']=$totalbonuscash;

        $latestmember=$this->member->getLastMember();
        $this->dataMember['latest_left']=$latestmember['left'];
        $this->dataMember['latest_right']=$latestmember['right'];                

        $this->populateDataRight();
        $this->populateDataLeft();      
	}
}