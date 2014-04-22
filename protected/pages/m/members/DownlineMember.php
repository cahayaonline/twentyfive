<?php
prado::using ('Application.pages.m.members.MainPageMembers');
class DownlineMember extends MainPageMembers {
    private $dataDownline;
	public function onLoad($param) {		
		parent::onLoad($param);		
		$this->tabdownline=true;
		if (!$this->IsPostBack&&!$this->IsCallBack) {								
            if ($this->session['currentPageDownline']['process']=='default') {
                $this->idProcess='add';
                $this->processDetail();
            }else {
                if (!isset($_SESSION['currentPageDownline'])||$_SESSION['currentPageDownline']['page_name']!='m.member.DownlineMember') {
                    $_SESSION['currentPageDownline']=array('page_name'=>'m.member.DownlineMember','page_num'=>0,'member_id'=>$this->member_id);												
                }
                $this->dataMember=$this->member->getDataMember(array('select'=>'member_id','value'=>$this->member_id,'mode'=>1));
                $this->populateData();
            }			
		}
	}
	public function renderCallback ($sender,$param) {
		$this->RepeaterS->render($param->NewWriter);	
	}	
	public function Page_Changed ($sender,$param) {
		$_SESSION['currentPageDownline']['page_num']=$param->NewPageIndex;
		$this->populateData();
	}
	public function searchClient ($sender,$param) {
		
	}
	public function populateData($str='') {				
		$this->RepeaterS->CurrentPageIndex=$_SESSION['currentPageDownline']['page_num'];
		$jumlah_baris=$this->DB->getCountRowsOfTable ("members WHERE sponsor_id='{$this->member_id}'",'member_id');		
		$this->RepeaterS->VirtualItemCount=$jumlah_baris;
		$currentPage=$this->RepeaterS->CurrentPageIndex;
		$offset=$currentPage*$this->RepeaterS->PageSize;
		$itemcount=$this->RepeaterS->VirtualItemCount;
		$limit=$this->RepeaterS->PageSize;
		if (($offset+$limit)>$itemcount) {
			$limit=$itemcount-$offset;
		}
		if ($limit < 0) {$offset=0;$limit=10;$_SESSION['currentPageDownline']['page_num']=0;}
		$str = "SELECT member_id,ibo,member_name,email,date_reg FROM members WHERE sponsor_id='{$this->member_id}' ORDER BY date_reg DESC,member_name ASC LIMIT $offset,$limit";
		$this->DB->setFieldTable(array('member_id','ibo','member_name','email','date_reg'));
		$r=$this->DB->getRecord($str,$offset+1);        
		$result=array();
		while(list($k,$v)=each($r)) {
            $this->member->setMemberID($v['member_id']);
            $this->member->dataMember=array('member_id'=>$v['member_id'],'lft'=>$v['lft'],'rgt'=>$v['rgt']);            
            $v['downline']=$this->member->getCountDownline();
            $v['totalomset']=$this->finance->toRupiah($this->member->getTotalOmset());
            $result[$k]=$v;
		}
		$this->RepeaterS->DataSource=$result;
		$this->RepeaterS->dataBind();
		
		$jumlah_halaman=round($jumlah_baris/$this->RepeaterS->PageSize);
		$this->recordinformation->Text="$jumlah_baris Records Found, Page ".($currentPage+1) ." of $jumlah_halaman";		
	}
    public function viewDownline ($sender,$param) {
        $_SESSION['currentPageDownline']['member_id']=$this->member_id;
        $_SESSION['currentPageDownline']['process']='default';
        $this->member->redirect('m.members.DownlineMember');
	}
    
    private function processDetail () {        
        $member_id=$this->session['currentPageDownline']['member_id'];
        $this->dataMember=$this->member->getDataMember(array('select'=>'member_id','value'=>$member_id,'mode'=>1));        
               
        $this->dataDownline= '<table border="1" width="400px">';
        $this->dataDownline.= '<tr>';
        $this->dataDownline.= '<td></td>';
        $this->dataDownline.= '<td>'.$this->dataMember['member_name'].'</td>';
        $this->dataDownline.= '<td></td>';
        $this->dataDownline.= '</tr>';
        $this->dataDownline.= '<tr>';
        $this->dataDownline.= '<td>Kiri</td>';
        $this->dataDownline.= '<td></td>';
        $this->dataDownline.= '<td>Kanan</td>';
        $this->dataDownline.= '</tr>';
        $this->recursiveRow($member_id);
        $this->dataDownline.= '</table>';        
        $this->tableDownline->Text=$this->dataDownline;
        
        
    }
    private function recursiveRow ($member_id) {
       $str="SELECT member_id,member_name,position FROM v_direct_downline WHERE member_id_upline=$member_id ORDER BY position ASC";        
        $this->DB->setFieldTable(array('member_id','member_name','position'));
        $r=$this->DB->getRecord($str);
        if (isset($r[1]) ) {                   
            while (list($k,$v)=each($r)) {
                $member_id=$v['member_id'];
                $position=$v['position'];
                $this->dataDownline.= '<tr>';
                if ($position==0) {                    
                    $this->dataDownline.= '<td>'.$v['member_name'].'</td>';
                    $this->dataDownline.= '<td>&nbsp;</td>';
                    $this->dataDownline.= '<td>&nbsp;</td>';                    
                }                
                if ($position==1) {
                    $this->dataDownline.= '<td>&nbsp;</td>';
                    $this->dataDownline.= '<td>&nbsp;</td>';   
                    $this->dataDownline.= '<td>'.$v['member_name'].'</td>';
                }
                $this->dataDownline.= '</tr>';
                $this->recursiveRow($member_id);
            }            
        }        
    }
    public function closeDownline ($sender,$param) {        
        unset($_SESSION['currentPageDownline']['process']);
        $this->member->redirect('m.members.DownlineMember',array('member_id'=>$this->session['currentPageDownline']['member_id']));
    }
    
}
		