<?php
/**
* digunakan untuk setup aplikasi
*
*/
prado::using ('Application.logic.Logic_Global');
class Logic_Setup extends Logic_Global {	
    /**
     *
     * file configurasi utama
     */
    public $mainConfig;
    /**
     *
     * file parameters xpath
     */
    private $parameters;
    /**
	* object image created by user file
	*/
	private $image;
	/**
	* tipe image
	*/
	private $image_type;
    /**
     *
     * setting application
     */
    private $settings;
	public function __construct ($db) {
		parent::__construct ($db);	
        $this->mainConfig=$this->Application->getConfigurationFile();
		$this->parameters=$this->Application->getParameters ();
         $this->loadSetting();       
	}
     /**
     * digunakan untuk meload setting
     */
    public function loadSetting ($flush=false) {     
        if ($flush) {
            $this->settings=$this->populateSetting ();
            $this->settings['loaded']=true;
            if ($this->Application->Cache) {                
                $this->Application->Cache->set('settings',$this->settings);
            }else {
                $_SESSION['settings']=$this->settings;                
            }
        }elseif ($this->Application->Cache) {
            $this->settings=$this->Application->Cache->get('settings');
            if (!$this->settings['loaded']) $this->loadSetting (true);
        }else {
            $this->settings=$_SESSION['settings'];
            if (!$this->settings['loaded']) $this->loadSetting (true);
        }        
    }
    /**
     * digunakan untuk populate setting
     */
    private function populateSetting () {
        $str = 'SELECT setting_id,`group`,`key`,`value` FROM setting';
        $this->db->setFieldTable(array('setting_id','group','key','value'));
        $r=$this->db->getRecord($str);
        $result=array();
        while (list($k,$v)=each($r)) {
            $result[$v['key']]=array('setting_id'=>$v['setting_id'],'group'=>$v['group'],'value'=>$v['value']);
        }
        return $result;
    }
    /**
     * digunakan untuk mendapat nilai setting
     * @param type $mode
     * @return type
     */
    public function getSettingValue($keys,$mode='value') {                        
        return $this->settings[$keys][$mode];
    }
    /**
     * digunakan untuk mendapat daftar bank
     * @param type $mode
     * @return type
     */
	/**
	* default sponsor
	*/
	public function getLinkBank() {		
        $str = 'SELECT norek,`nama_pemilik`,`nama_bank` FROM bank';
        $this->db->setFieldTable(array('norek','nama_pemilik','nama_bank'));
        $r=$this->db->getRecord($str);        
        $result=array('none'=>' ');
        while (list($k,$v)=each($r)) {
            $result[$v['norek']]=$v['nama_bank'].' No. Rekening ['.$v['norek'].'] a.n '.$v['nama_pemilik'];
        }
        return $result;		
	}
    /**
	* digunakan untuk mendapatkan images product
	*/
	public function getImagesProduct($images_product) {		
        $url=$this->getAddress().'/'.$this->parameters['product_images_dir'].$images_product;
		return $url;
	}
    /**
	* digunakan untuk mendapatkan images toko
	*/
	public function getStoreProduct($images_store) {		
        $url=$this->getAddress().'/'.$this->parameters['store_images_dir'].$images_store;
		return $url;
	}
    /**
     * mengembalikan url photo user
     */
    public function getUrlPhotoUser() {
        $url=$this->getAddress().'/'.$this->parameters['user_images_dir'];
        return $url;
    }
    /**
	* digunakan untuk mendapatkan images product
	*/
	public function getDirImagesProduct() {
        $dir=str_replace('protected','',$this->Application->getBasePath());
		return $dir.$this->parameters['product_images_dir'];
	}
    /**
     * digunakan untuk mendapatkan alamat aplikasi
     * 
     */
    public function getAddress () {       
		$ip=explode('.',$_SERVER['REMOTE_ADDR']);		
		$ipaddress=$ip[0];	
        $apps=$this->Application->getParameters ();	
		if ($ipaddress == '127' || $ipaddress == '::1') {
			$url=$apps['address_lokal'];
		}elseif ($ipaddress == '192' || $ip=='10'||$ip=='172'){
			$url=$apps['address_lan'];
		}else {
			$url=$apps['address_internet'];
		}				
		return $url;
    }    
    /**
	* load image
	*
	*/
	public function load($filename) {
		$image_info = @ getimagesize($filename);
		if ($image_info) {
			$this->image_type = $image_info[2];			
			if( $this->image_type == IMAGETYPE_JPEG ) {
				$this->image = imagecreatefromjpeg($filename);
			} elseif( $this->image_type == IMAGETYPE_GIF ) {
				$this->image = imagecreatefromgif($filename);
			} elseif( $this->image_type == IMAGETYPE_PNG ) {
				$this->image = imagecreatefrompng($filename);
			}
			return $image_info;
		}else{
			return false;
		}
	}	
    public function getImageFileType ($filetype) {
        $filetype=explode('.',$filetype);
        $type=$filetype[count($filetype)-1];
        return $type;
    }
	/**
	* save new image
	*
	*/
	public function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);         
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		}   
		if( $permissions != null) {
			chmod($filename,$permissions);
		}		
	}
	public function destroyImage () {
		imagedestroy($this->image);
	}
	public function output($image_type=IMAGETYPE_JPEG) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);         
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}		
	}	
	public function  getWidth() {
		return imagesx($this->image);
	}	
	public function  getHeight() {
		return imagesy($this->image);
	}	
	private function  resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}	
	private function  resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}	
	public function  scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100; 
		$this->resize($width,$height);
	}	
	public function  resize($width,$height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;   
	}   			
	/**
	* hapus photo
	*/
	public function removeImage ($image) {						
		if (file_exists($image)) {
			unlink($image);	
		}	        
    }	
}
?>