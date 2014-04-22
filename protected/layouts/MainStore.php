<?php
class MainStore extends TTemplateControl {
    private $parentCategory=array();    
    private $parent_id;
    private $category_id;  
    public function onLoad ($param) {
		parent::onLoad($param);        
        $this->parent_id = addslashes(trim($this->request['parent_id']));
        $this->category_id = addslashes(trim($this->request['id']));
        if (!$this->Page->IsPostBack&&!$this->Page->IsCallBack) {            
            $this->setParentCategory();
            $this->createTopMenu();  
        }
        
	}
    private function setParentCategory() {
        $str="SELECT c.category_id,cd.name FROM category c,category_description cd WHERE c.category_id=cd.category_id AND parent_id=0 AND top=1";        
        $this->page->DB->setFieldTable(array('category_id','name'));
        $r=$this->page->DB->getRecord($str);        
        $this->parentCategory=$r;      
    }       	    
    public function createTopMenu() {
        $maincategory=$this->parentCategory;        
        $page=$_SESSION['currentMainPageStore']['catalogmenu']==='grid'?'CatalogGrid':'CatalogList';
        $navlist='<ul>';
        if (isset($maincategory[1])) {         
            foreach ($maincategory as $v) {
                $parent_id_default=$v['category_id'];
                $parent_name=$v['name'];                
                $activeclasscss=$this->parent_id==$parent_id_default?' class="curent"':'';
                $parent_url='<a href="'.$this->Service->constructUrl($page,array('parent_id'=>$parent_id_default,'id'=>$parent_id_default)).'">'.$parent_name.'</a>';
                $navlist.="<li$activeclasscss>$parent_url";
                
                //sub menu
                $str="SELECT c.category_id,cd.name,parent_id FROM category c,category_description cd WHERE c.category_id=cd.category_id AND parent_id=$parent_id_default";        
                $this->page->DB->setFieldTable(array('category_id','name','parent_id'));
                $r=$this->page->DB->getRecord($str);        
                if (isset($r[1])) {
                    $navlist.='<ul class="sub">';
                    foreach ($r as $m) {
                        $url=$this->Service->constructUrl($page,array('parent_id'=>$parent_id_default,'id'=>$m['category_id']));
                        $navlist.='<li><a href="'.$url.'">'.$m['name'].'</a></li>';                        
                    }
                    $navlist.="</ul>";
                    $navlist.="</li>";
                }else {
                    $navlist.="</li>";
                }
            }
            $navlist=$navlist.'</ul>';
            $this->TopMenu->Text=$navlist;
        }
    }    
}
?>