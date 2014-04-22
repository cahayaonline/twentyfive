<?php
/**
*
* digunakan untuk memproses Product
*
*/
prado::using ('Application.logic.Logic_Global');
class Logic_Product extends Logic_Global {    
    /**
     *
     * @var product id
     */
    private $product_id;   
    /**
     *
     * @var category id
     */
    private $category_id;  
	public function __construct ($db) {
		parent::__construct ($db);	
	}	
     /**
     * setter product id
     * 
     */
    public function setProductID ($product_id) {
        $this->product_id=$product_id;
    }
    /**
     * setter category id
     * 
     */
    public function setCategoryID ($category_id) {
        $this->category_id=$category_id;
    }
    /**
     * mendapatkan daftar seluruh category product dalam berbagai bentuk
     * 
     */
    public function getListCategoryProduct($mode='') {
        $r=$this->getCategories(0);                   
        switch ($mode) {
            case 'dropdownlist' :                      
                $result = array(0=>'--- None ---');
                while (list($k,$v)=each($r)) {                                           
                    $result[$v['category_id']]=$v['name'];                                    
                }                
            break; 
            case 'listbox' :                
                while (list($k,$v)=each($r)) {                                           
                    $result[$v['category_id']]=$v['name'];                                    
                }  
            break;            
            default :

        }
        return $result;
    }
    /**
     * digunakan untuk mendapatkan jumlah stock barang
     */
    public function getStock () {
        $stock=0;
        $product_id=$this->product_id;
        $str = "SELECT SUM(qty) AS jumlah FROM product_to_qty WHERE product_id=$product_id";
        $this->db->setFieldTable(array('jumlah'));
        $r=$this->db->getRecord($str);        
        if (isset($r[1])) {            
            $jumlah=$r[1]['jumlah'];
            $str = "SELECT SUM(op.quantity) AS jumlah FROM `order` o,order_product op WHERE op.order_id=o.order_id AND op.product_id=$product_id AND (o.order_status_id!='2' OR o.order_status_id!='3' OR o.order_status_id!='5' OR o.order_status_id!='12' or o.order_status_id!='1' OR o.order_status_id!='15')";
            $r=$this->db->getRecord($str);        
            $stock=$jumlah-$r[1]['jumlah'];
        }
        return $stock;
    }
    /**
     * digunakan untuk mendapatkan daftar product
     */
    public function getProduct($mode=0) {
        $product_id=$this->product_id;
        $category_id=$this->category_id;
        switch ($mode) {
            case 0 :
                $str = "SELECT product_id,product_name,model,price,default_omset FROM product WHERE product_id=$product_id";
                $this->db->setFieldTable(array('product_id','product_name','model','price','default_omset'));
                $r=$this->db->getRecord($str);        
                $product = isset($r[1])?$r[1]:array();
            break;
            case 1 :
                $str = "SELECT ptc.product_id,p.product_name,p.model,p.price,p.default_omset FROM product p, product_to_category ptc WHERE p.product_id=ptc.product_id AND ptc.category_id=$category_id";
                $this->db->setFieldTable(array('product_id','product_name','model','price','default_omset'));
                $r=$this->db->getRecord($str);        
                $product = $r;
            break;
            case 2 :
                $str = "SELECT product_id,product_name,model,price,default_omset,description,description_details FROM product WHERE product_id=$product_id";
                $this->db->setFieldTable(array('product_id','product_name','model','price','default_omset','description','description_details'));
                $r=$this->db->getRecord($str);        
                $product = isset($r[1])?$r[1]:array();
            break;
        }       
        return $product;
    }
    public function getCategories($parent_id = 0) {
        //nanti di cache
        
        if (!$category_data) {
            $category_data = array();
            $str = "SELECT c.category_id,sort_order FROM category c LEFT JOIN category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '1' ORDER BY c.sort_order, cd.name ASC";            
            $this->db->setFieldTable(array('category_id','images','parent_id','sort_order','date_added','date_modified','category_id','language_id','name','description','meta_description,meta_keyword'));
            $query = $this->db->getRecord($str);	            
            foreach ($query as $result) {                
                $category_data[] = array(                    
                    'category_id' => $result['category_id'],
                    'name'        => $this->getPath($result['category_id']),
                    'status'  	  => $result['status'],
                    'sort_order'  => $result['sort_order']
                );                
                $category_data = array_merge($category_data, $this->getCategories($result['category_id']));
                
            }           
        }        
        return $category_data;
    }
    public function getPath($category_id) {
        $str = "SELECT name, parent_id FROM category c LEFT JOIN category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '1' ORDER BY c.sort_order, cd.name ASC";                
        $this->db->setFieldTable(array('name','parent_id'));
        $query = $this->db->getRecord($str);
        if ($query[1]['parent_id']) {
            return $this->getPath($query[1]['parent_id']) .  '--->' . $query[1]['name'];
        } else {
            return $query[1]['name'];
        }
    }
    /**
     * digunakan untuk mendapatkan daftar status order
     */
    public function getStatusOrder ($id=null) {
        $str = 'SELECT order_status_id,name FROM order_status';
        $this->db->setFieldTable(array('order_status_id','name'));        
        if ($id === null) {
            $r = $this->db->getRecord($str);                        
            while (list($k,$v)=each($r)) {
                $query[$v['order_status_id']]=$v['name'];
            }            
        }else {
            $str = $str . " WHERE order_status_id='$id'";
            $str = $str . ' ORDER BY name ASC';
            $r = $this->db->getRecord($str);
            $query=isset($r)?$r[1]['name']:false;
        }
        return $query;        
    }
    /**
     * digunakan untuk mendapatkan data kategori berdasarkan
     * @param type $id
     */
    public function getDataCategory ($id) {
        $str = "SELECT c.category_id,parent_id,cd.name,cd.description FROM category c,category_description cd WHERE c.category_id=cd.category_id AND c.category_id=$id";
        $this->db->setFieldTable(array('category_id','parent_id','name','description'));
        $r=$this->db->getRecord($str);        
        return isset($r[1])?$r[1]:null;
    }
}
?>