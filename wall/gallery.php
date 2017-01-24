<?php

/**
*
* @package phpBB Extension - phpBB Wall
* @author Saulo Henrique
* @version 1.0.0
* @copyright (c) 2017 http://www.suportephpbb.com.br
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/


namespace msaulohenrique\wall\wall;

class gallery
{
	/* @var \phpbb\user */
	protected $user;

	/**
	 * Controller helper object
	 * @var \phpbb\controller\helper
	 */
	protected $helper;

	/**
	 * Config object
	 * @var \phpbb\config\config
	 */
	protected $config;
	
	/* @var string phpBB root path */
	protected $root_path;	
	
	/* @var string phpEx */
	protected $php_ext;	

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config phpBB Config object	 
	 * @param \phpbb\controller\helper		$helper		Controller helper object	
	 * @param \wall\controller\helper $wall_helper
	 * @param \phpbb\config\config			$config		Config object
	 * @param \phpbb\db\driver\driver_interface			$db		db object	 
	 */
	public function __construct($user, $helper, $wall_helper, $config, $db, $root_path, $php_ext, $table_prefix)
	{	
	    $this->user = $user;
		$this->helper = $helper;
		$this->wall_helper = $wall_helper;
		$this->config = $config;
		$this->db = $db;
	    $this->root_path = $root_path;	
		$this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;	
	    $this->wall_path = $this->root_path.'/ext/msaulohenrique/wall';	
		      
		 if (!defined('WALL_STATUS'))
         {
          include($this->root_path . 'ext/msaulohenrique/wall/wall/constants.' . $this->php_ext);
         }
		
	}


 	public function total_photos($user_id)
   {
// Load total of photos
   $sql = 'SELECT COUNT(photo_id) AS photos
		FROM ' . $this->table_prefix . 'wall_photos' . ' 
		WHERE wall_id = ' . $user_id ;
   $result = $this->db->sql_query($sql);
   $total_photos = $this->db->sql_fetchfield('photos');
   $this->db->sql_freeresult($result);
   return $total_photos;
   
   }
   
    	public function profile_cover($user_id)
   {
// Load profile cover
	$sql = "SELECT * FROM " . $this->table_prefix . 'wall_photos' . "
	WHERE wall_id = " . $user_id." AND cover= 1 ORDER BY photo_id DESC";
	$result = $this->db->sql_query($sql);
	$photo = $this->db->sql_fetchrow($result);
	$this->db->sql_freeresult($result);
	
	return  array(
			"cover"  => ($photo['photo_path'] ? $this->wall_helper->generate_wall_url('images') . $photo['wall_id'].'/'.$photo['photo_path'] : generate_board_url(). '/ext/msaulohenrique/wall/images/no_cover.png'),
			"top"    => $photo['cover_top'],
	);
	
   
   }
   
          	public function get_photos($wall_id)
   {
      // Get photos

		$sql = "SELECT * FROM " . $this->table_prefix . 'wall_photos' . "
		WHERE wall_id = " . $wall_id." ORDER BY photo_id DESC";
		$result = $this->db->sql_query($sql);


	while ($row = $this->db->sql_fetchrow($result))	
	{   

			$data[] = array(
			"id"      => $row['photo_id'],
			"name"    => $row['photo_path'],
			"link"    => $this->wall_helper->generate_wall_url('images') . $row['wall_id'].'/'.$row['photo_path'],
            "status"  => $row['status_id'],
			"poster"  => $row['poster_id'],
			);
			
		   }
	       return $data;
         }
		 
		 
		public function status_img($wall_id = 0, $img_id)
		{
	
		    $sql = "SELECT * FROM " . $this->table_prefix . 'wall_photos' . "
			WHERE photo_id = " . $img_id . "
			".($wall_id != 0 ? ' AND wall_id = '.$wall_id : '');
			$rs = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($rs);
			$image = $this->wall_helper->generate_wall_url('images') . $row['wall_id'].'/'.$row['photo_path'];
			$this->db->sql_freeresult($rs);

		return $image;

		}	 
		 
 	   public function addPhoto($wall_id, $poster_id, $files) {
		   
   	    //let's access these values by using their index position
	    $ImageName 		= str_replace(' ','-',strtolower($files['name'])); 
	    $ImageSize 		= $files['size']; 
	    $TempSrc	 	= $files['tmp_name']; 
	    $ImageType	 	= $files['type']; 
	
    $wall_id = (($wall_id == 0) ? (int) $this->user->data['user_id'] : $wall_id);
	$imageAlbum = $this->wall_path. '/images/gallery/'. $wall_id.'/';
	$targetFolder = $this->root_path.$imageAlbum;
	
	// Create photos folder if doesn't exists.
	if (!file_exists($targetFolder)) 
	{

    mkdir($targetFolder);
    }
	$BigImageMaxSize 		= 1500; //Image Maximum height or width
	$DestinationDirectory	= $targetFolder;
	$Quality 				= 99;
	$now = time();

	// Random number file, will be added after image name
	$RandomNumber 	= rand(0, 9999999999); 

	
	switch(strtolower($ImageType))
	{
		case 'image/png':
			$CreatedImage =  imagecreatefrompng($TempSrc);
			break;
		case 'image/gif':
			$CreatedImage =  imagecreatefromgif($TempSrc);
			break;			
		case 'image/jpeg':			
		   $CreatedImage = imagecreatefromjpeg($TempSrc);
			break;
		
		case 'image/pjpeg':
			$CreatedImage = imagecreatefromjpeg($TempSrc);
			break;
		default:
			die(''.$this->user->lang['ATTACHED_IMAGE_NOT_IMAGE'].''); //output error and exit
	}
	
	//PHP getimagesize() function returns height-width from image file stored in PHP tmp folder.
	//Let's get first two values from image, width and height. list assign values to $CurWidth,$CurHeight
	list($CurWidth,$CurHeight)=getimagesize($TempSrc);
	//Get file extension from Image name, this will be re-added after random name
	$ImageExt = substr($ImageName, strrpos($ImageName, '.'));
  	$ImageExt = str_replace('.','',$ImageExt);
	
	//remove extension from filename
	$ImageName 		= preg_replace("/\\.[^.\\s]{3,4}$/", "", $ImageName); 
	
	//Construct a new image name (with random number added) for our new image.
	$NewImageName = $ImageName.'-'.$RandomNumber.'.'.$ImageExt;
	//set the Destination Image
	$DestRandImageName 			= $DestinationDirectory.$NewImageName; //Name for Big Image
	
	// This function will proportionally resize image 
function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType)
{
	//Check if height is differnt than width to resize it
	if($CurWidth <= $CurHeight) 
	{
	$MaxSize = 1500;
	}
	
	
	
	//Check Image size is not 0
	if($CurWidth <= 0 || $CurHeight <= 0) 
	{
		return false;
	}
	
	//Construct a proportional size of new image
	$ImageScale      	= min($MaxSize/$CurWidth, $MaxSize/$CurHeight); 
	$NewWidth  			= ceil($ImageScale*$CurWidth);
	$NewHeight 			= ceil($ImageScale*$CurHeight);
	$NewCanves 			= imagecreatetruecolor($NewWidth, $NewHeight);
	
	// Resize Image
	if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
	{
		switch(strtolower($ImageType))
		{
			case 'image/png':
				imagepng($NewCanves,$DestFolder);
				break;
			case 'image/gif':
				imagegif($NewCanves,$DestFolder);
				break;			
			case 'image/jpeg':
				imagejpeg($NewCanves,$DestFolder,$Quality);
				break;			
			case 'image/pjpeg':
				imagejpeg($NewCanves,$DestFolder,$Quality);
				break;
			default:
				return false;
		}
	//Destroy image, frees memory	
	if(is_resource($NewCanves)) {imagedestroy($NewCanves);} 
	return true;
	}

}
	
	//Resize image to our Specified Size by calling resizeImage function.
	if(resizeImage($CurWidth,$CurHeight,$BigImageMaxSize,$DestRandImageName,$CreatedImage,$Quality,$ImageType))
	{
		// insert data
	        $sql_arr = array(
			'poster_id'	  => $poster_id,
			'image_time'  => $now,			
			'wall_id'	  => $wall_id,
			'photo_path'  => $NewImageName
		    );
		
		    $sql = "INSERT INTO " . $this->table_prefix . 'wall_photos' . $this->db->sql_build_array('INSERT', $sql_arr);
		    $this->db->sql_query($sql);

		    $sql = "SELECT photo_id FROM " . $this->table_prefix . 'wall_photos' . "
			WHERE poster_id = " . $poster_id . "
			AND wall_id = ". $wall_id ."
			AND image_time = " . $now . "";
			$rs = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($rs);
			$img_id = $row['photo_id'];
			$this->db->sql_freeresult($rs);	
			
		    $image = generate_board_url(). "/" . $imageAlbum .$NewImageName;
			
		    $result = array(
			"url"         => $image,
		    "id"      =>  $img_id,									
		    );

            
			return $result;
   
            }
   }
   
   
   	public function removeImg($id){
	
	 $sql = 'SELECT * FROM ' . $this->table_prefix . 'wall_photos'. ' WHERE status_id = '.$id;
     $result = $this->db->sql_query($sql);
     $row = $this->db->sql_fetchrow($result);
	 $img_name = $row['photo_path'];
	 $this->db->sql_freeresult($row );
	 		
     if(@unlink($this->wall_path. '/images/gallery/'.$row['wall_id'].'/'.$img_name)){
	   $sql = 'DELETE FROM ' . $this->table_prefix . 'wall_photos' . '
               WHERE status_id = '.$id;
               $this->db->sql_query($sql);
		 return true;
		 }
		 else{
	  return false;
		 }

     }
   
   
            public function update_cover($id, $top)
   {
		  if (!empty($id))
		  {
		  	$sql = 'UPDATE ' . $this->table_prefix . 'wall_photos' . ' SET cover = NULL, cover_top= NULL WHERE wall_id = '.$this->user->data['user_id'];
            $this->db->sql_query($sql); 
			
			$sql = 'UPDATE ' . $this->table_prefix . 'wall_photos' . ' SET cover = 1, cover_top= '.$top.' WHERE photo_id = '.$id.'
			AND wall_id = '.$this->user->data['user_id'];
            $this->db->sql_query($sql);
		
     	$data = array(
			"id"      => $id,
			"top"    => $top,
			);
		  }
		  else{
			
            $data = null;
            }
	
   return $data;
   
   } 

   
}


