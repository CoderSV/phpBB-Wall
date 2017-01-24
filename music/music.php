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


namespace msaulohenrique\wall\music;

class music
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
	 * @param \phpbb\request\request	$request	 
	 */
	public function __construct($user, $helper, $wall_helper, $config, $db, $request, $root_path, $php_ext, $table_prefix)
	{	
	    $this->user = $user;
		$this->helper = $helper;
		$this->wall_helper = $wall_helper;	
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
	    $this->root_path = $root_path;	
		$this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;	
	    $this->wall_path = $this->root_path.'ext/msaulohenrique/wall';	 	      	
	    $this->music_path = $this->wall_path.'/music/mp3/';	        
         
		 if (!defined('WALL_STATUS'))
         {
         include($this->root_path . 'ext/msaulohenrique/wall/wall/constants.' . $this->php_ext);
         }
		include($this->root_path . 'includes/functions_display.' . $this->php_ext);
		
	}

    public function add_music($files)
	{
	$tempFile = $files['tmp_name'];
	$fileName = $files['name'];
    $poster   = (int) $this->user->data['user_id'];
    $targetFolder = $this->music_path. '/'. $poster .'/';	
	if (!file_exists($targetFolder)) 
	{
    mkdir($targetFolder);
    }

  //unique file name for the upload file
  
    $filePath = str_replace(' ', '_', $targetFolder . $fileName);
    $fileDB = str_replace(' ', '_', $fileName);
     
	 // Validate the file type
	$fileTypes = array('mp3'); // File extensions
	$fileParts = pathinfo($files['name']);
	if (in_array($fileParts['extension'],$fileTypes)) {
		
if (move_uploaded_file($tempFile, $filePath)) {
	
   if(!get_magic_quotes_gpc()) {

    $fileName = addslashes($fileName);
    $fileDB = addslashes($fileDB);

    }
	// Create final name
	$string = $fileName;
    $keywords = array('.mp3');
    $finalname = str_replace($keywords, '', $string);		
    $now = (int) time();
	
	  $data = array(
         'm_path'    => $fileDB,
		 'm_poster'  => $poster,
		 'm_time'    => $now,
		 'm_name'    => $finalname
       );
           
   $sql = 'INSERT INTO ' . $this->table_prefix . 'wall_music' . ' '.$this->db->sql_build_array('INSERT', $data);
    if ($this->db->sql_query($sql))
	{
   return $this->get_music($poster);
	}
	else
	{
   return false;
	}
	}
	else
	return 'fail';
	}
	}
	
 	public function get_music($user_id)
		{
			
	// Load music
	
		$sql_ary =  array(
		'SELECT'	=> 'm.m_id, m.m_poster, m.m_path, m.m_time, m.m_name',
		'FROM'		=> array(
			$this->table_prefix . 'wall_music'				=> 'm',
		),
		'WHERE'		=> 'm.m_poster = ' . $user_id,
		'ORDER_BY'	=> 'm.m_id DESC'
	);
	
	
	$sql = $this->db->sql_build_query('SELECT', $sql_ary);
	$result	= $this->db->sql_query_limit($sql, 15);	

	
		while ($row = $this->db->sql_fetchrow($result))	
	{   
        	$data[] = array(
			"title"  => $row['m_name'],
			"name"   => $row['m_path'],			
			"url"    => $this->wall_helper->generate_wall_url('music') . $row['m_poster'] .'/' .$row['m_path'],
			"id"     =>  $row['m_id'],
            "date"   => $this->wall_helper->time_ago($row['m_time'])
			);
	}
			$this->db->sql_freeresult($result);
			
   return $data;
	}


 	public function remove_music($user_id, $id, $url)
		{
		 if(@unlink($this->music_path . '/'. $user_id .'/' .$url))
	       {			
            $sql = 'DELETE FROM ' . $this->table_prefix . 'wall_music' . '
                    WHERE m_id = "' . $id . '" AND m_poster = "' . $user_id . '"';
	        if ($this->db->sql_query($sql))
	           {
                return true;
	           }
	        else
	            {
                 return false;
	            }
           }
	  }
	  
	  
 	public function total_music($user_id)
		{
			
          $sql = "SELECT COUNT(m_id) AS total
				  FROM " . $this->table_prefix . 'wall_music' . "
				  WHERE m_poster = " . $user_id."";
		  $result = $this->db->sql_query($sql);
		  $row = $this->db->sql_fetchrow($result);
			
		  return  $row['total'];

	}
		  
}
