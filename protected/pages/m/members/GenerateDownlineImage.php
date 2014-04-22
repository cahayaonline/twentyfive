<?php
prado::using('Application.ChartPage');
require_once BASEPATH.'/lib/pChart/class/pDraw.class.php';
require_once BASEPATH.'/lib/pChart/class/pImage.class.php';
class GenerateDownlineImage extends ChartPage {
    public function OnPreInit ($param) {	
		parent::onPreInit ($param);
        $this->generateImage();
    }
    private function generateImage () {
         /* Add data in your dataset */  
        $this->dataSet->addPoints(array(1,3,4,3,5,6,7,8,9));
        /* Create a pChart object and associate your dataset */  
        $myPicture = new pImage(700,230,$this->dataSet);
         /* Choose a nice font */ 
        $myPicture->setFontProperties(array("FontName"=>BASEPATH.'/lib/pChart/fonts/Forgotte.ttf','FontSize'=>11));
         /* Define the boundaries of the graph area */ 
        $myPicture->setGraphArea(60, 40, 670,  190);
         /* Draw the scale, keep everything automatic */  
        $myPicture->drawScale();
         /* Draw the scale, keep everything automatic */  
        $myPicture->drawSplineChart();        
        /* Render the picture (choose the best way) */ 
        $myPicture->autoOutput('test.png');
    }
}
?>
