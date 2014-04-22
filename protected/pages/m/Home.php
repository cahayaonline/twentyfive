<?php

class Home extends MainPageSystem {
    public $pendapatanHariIni=0;
    public $pendapatanBulanIni=0;
    public $pendapatanTahunIni=0;
	public function onLoad($param) {		
		parent::onLoad($param);		
		$this->showDashboard=true;
        $this->createObjFinance();
		if (!$this->IsPostBack&&!$this->IsCallBack) {
            if (!isset($_SESSION['currentPageHome'])||$_SESSION['currentPageHome']['page_name']!='m.Home') {
                $_SESSION['currentPageHome']=array('page_name'=>'m.Home','chartOrder'=>$this->getChartOrder(),'chartMember'=>$this->getChartMember());
            }            
            $this->populateChart();
            $this->populateData ();
            $this->populateSummary();
		}
	}
    public function populateSummary() {
        $tanggal_skr=date('Y-m-d');
        $bulan_skr=date('Y-m');
        $tahun_skr=date('Y');
        $this->pendapatanHariIni=$this->DB->getSumRowsOfTable('totalprice',"`order` WHERE order_status_id=5 AND DATE_FORMAT(date_modified,'%Y-%m-%d')='$tanggal_skr'");
        $this->pendapatanBulanIni=$this->DB->getSumRowsOfTable('totalprice',"`order` WHERE order_status_id=5 AND DATE_FORMAT(date_modified,'%Y-%m')='$bulan_skr'");
        $this->pendapatanTahunIni=$this->DB->getSumRowsOfTable('totalprice',"`order` WHERE order_status_id=5 AND DATE_FORMAT(date_modified,'%Y')='$tahun_skr'");
    }
    public function populateData () {
        $str = "SELECT o.order_id,m.member_name,os.name,o.date_added,totalprice FROM `order`o,members m,order_status os WHERE o.member_id=m.member_id  AND o.order_status_id=os.order_status_id ORDER BY date_added DESC LIMIT 10";
        $this->DB->setFieldTable(array('order_id','member_name','name','date_added','totalprice'));
        $r=$this->DB->getRecord($str);
        $this->RepeaterS->DataSource=$r;
        $this->RepeaterS->dataBind();
    }
    private function getChartOrder() {
        $year=date('Y');
        $str = "SELECT DATE_FORMAT(date_modified,'%m') AS bulan,COUNT(order_id) AS jumlah FROM `order` WHERE DATE_FORMAT(date_modified,'%Y')='$year'  GROUP BY DATE_FORMAT(date_modified,'%Y-%m')";
        $this->DB->setFieldTable(array('bulan','jumlah'));
        $r=$this->DB->getRecord($str);        
        $bulan=$this->TGL->getMonth();                
        $i=1;        
        foreach ($bulan as $k=>$v) {
            $jumlah=0;
            foreach ($r as $m=>$n) {                
                if ($k == $n['bulan']) {
                    $jumlah=$n['jumlah'];                    
                    break;
                }
            }
            if (13 > $i+1) {
                $var="$var [$i,$jumlah],";
            }else {
                $var="$var [$i,$jumlah]";
            }            
            $i+=1;
        }        
        return $var;
    }
    private function getChartMember() {
        $year=date('Y');
        $str = "SELECT DATE_FORMAT(date_reg,'%m') AS bulan,COUNT(member_id) AS jumlah FROM `members` WHERE DATE_FORMAT(date_reg,'%Y')='$year' GROUP BY DATE_FORMAT(date_reg,'%Y-%m')";
        $this->DB->setFieldTable(array('bulan','jumlah'));
        $r=$this->DB->getRecord($str);        
        $bulan=$this->TGL->getMonth();                
        $i=1;        
        foreach ($bulan as $k=>$v) {
            $jumlah=0;
            foreach ($r as $m=>$n) {                
                if ($k == $n['bulan']) {
                    $jumlah=$n['jumlah'];                    
                    break;
                }
            }
            if (13 > $i+1) {
                $var="$var [$i,$jumlah],";
            }else {
                $var="$var [$i,$jumlah]";
            }            
            $i+=1;
        }        
        return $var;
    }
    private function populateChart () {        
        $script='<script type="text/javascript">';
        $script.='jQuery(function() {
                    if (jQuery().plot) {
                        var placeholder = jQuery("#statistik-chart");
                        if (jQuery(placeholder).size() == 0) {
                            return;
                        }';
        $d1=$_SESSION['currentPageHome']['chartOrder'];
        $script.="var d1 = [$d1];";
        $d2=$_SESSION['currentPageHome']['chartMember'];
        $script.="var d2 = [$d2];";        
        $script.="var chartColours = ['#88bbc8', '#ed7a53', '#9FC569', '#bbdce3', '#9a3b1b', '#5a8022', '#2c7282'];";
        //graph options
        $script.='var options = {
                      grid: {
                      show: true,
                      aboveData: true,
                      color: "#3f3f3f" ,
                      labelMargin: 5,
                      axisMargin: 0, 
                      borderWidth: 0,
                      borderColor:null,
                      minBorderMargin: 5 ,
                      clickable: true, 
                      hoverable: true,
                      autoHighlight: true,
                      mouseActiveRadius: 20
                  },
                  series: {
                      grow: {
                          active: false,
                          stepMode: "linear",
                          steps: 50,
                          stepDelay: true
                      },
                      lines: {
                          show: true,
                          fill: true,
                          lineWidth: 3,
                          steps: false
                      },
                      points: {
                          show:true,
                          radius: 4,
                          symbol: "circle",
                          fill: true,
                          borderColor: "#fff"
                      }
                  },
                  legend: { 
                      position: "ne", 
                      margin: [0,-25], 
                      noColumns: 0,
                      labelBoxBorderColor: null,
                      labelFormatter: function(label, series) {
                          // just add some space to labes
                          return label+\'&nbsp;&nbsp;\';
                      }
                  },
                        yaxis: { min: 0 },
                        xaxis: {ticks:11, tickDecimals: 0},
                        colors: chartColours,
                        shadowSize:1,
                        tooltip: true, //activate tooltip
                        tooltipOpts: {
                            content: "%s : %y.0",
                            defaultTheme: false,
                            shifts: {
                                x: -30,
                                y: -50
                            }
                        }
                    };
                    jQuery.plot(placeholder, [
                    {
                        label: "Jumlah Order", 
                        data: d1,
                    }, 
                    {
                        label: "Jumlah Member", 
                        data: d2,
                    } 

                ], options);
            };
        });';
        $script.='</script>';
        $this->statistikChart->Text=$script;
    }
    public function refreshPage ($sender,$param) {
        unset($_SESSION['currentPageHome']);
        $this->redirect('m.Home');
    }
}
		