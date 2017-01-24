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

class status
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
	 * @param \phpbb\user				$user	 
	 * @param \phpbb\controller\helper		$helper		Controller helper object
	 * @param \phpbb\config\config			$config		Config object	
	 * @param \phpbb\db\driver\driver_interface	$db	db object		
	 * @param \wall\\controller\helper $wall_helper	 	
	 * @param \wall\wall\comments $comments		  	
	 * @param \wall\wall\gallery $gallery 
	 * @param \wall\controller\notifyhelper $notifyhelper Notification helper.	 
	 */
	public function __construct($user, $helper, $config, $db, $wall_helper, $comments, $gallery, $notifyhelper, $root_path, $php_ext, $table_prefix)
	{	
	    $this->user = $user;
		$this->helper = $helper;
		$this->wall_helper = $wall_helper;
	    $this->comments = $comments;
		$this->config = $config;
		$this->db = $db;
	    $this->gallery = $gallery;
		$this->notify = $notifyhelper;
	    $this->root_path = $root_path;	
		$this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;	
			
		 if (!defined('WALL_STATUS'))
         {
          include($this->root_path . 'ext/msaulohenrique/wall/wall/constants.' . $this->php_ext);	
         }
	}

	public function getStatus($wall_id, $lsta =  0){
		 
		  $allow_bbcode = $this->config['wall_status_bbcode'];
		  $allow_urls = $this->config['wall_status_url'];
		  $allow_smilies = $this->config['wall_status_smilies'];
		  $u_id = (int) $this->user->data['user_id'];
		  $flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) +
          (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) +
          (($allow_urls) ? OPTION_FLAG_LINKS : 0);
		   
		    if($list = $this->wall_helper->view_permissions($wall_id)){
		   
			$sql = "SELECT w.*, u.username, u.user_id, u.user_avatar, u.user_avatar_type, u.user_colour
					FROM " . $this->table_prefix . 'wall_status' . " AS w, " . USERS_TABLE . " AS u
					WHERE ".($wall_id != $u_id ? 'w.wall_id in (' . $list . ') AND ' : ($wall_id != 0 ? 'w.wall_id = '. $wall_id . ' AND ': ''))." 
					u.user_id = w.poster_id ".($lsta != 0 ? ' AND w.status_id < '. $lsta : '')." ORDER BY w.status_id DESC";
					
		    $result = $this->db->sql_query_limit($sql, 5);
			
	        while ($row = $this->db->sql_fetchrow($result)){
			$user_id = $row['user_id'];	
			$id = $row['status_id'];
			$item_type = $row['status_type'];	
			$image = false;
			if ($item_type == WALL_IMG){
			$image = $this->gallery->status_img($row['wall_id'], $row['status_extra']);	
			}
		    $liked = $this->wall_helper->_item_liked($id, $item_type, (int) $this->user->data['user_id']);
			$likes = $this->wall_helper->_count_item_likes($id, $item_type);
			$comments = $this->comments->count_comments($id);
			$lastid =  $this->wall_helper->last_status($wall_id, $lsta);
	        $data[] = array(
			"id"            => $id,
			"wall_id"       => $row['wall_id'],			
			"type"          => $item_type,
			"lstatus"       => ($lastid == $id) ? $lastid : false,
            "msg"           => generate_text_for_display($row['status'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags),
			"image"         => ($image ? $image : false),
            "time"          => $this->wall_helper->time_ago($row['time']),
            "poster"        => $this->wall_helper->wall_user($row['wall_id'], $row['user_id'], $row['username'], $row['user_colour'], $item_type),					
            "avatar"        => $this->wall_helper->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 64, 64), 	
			"commentsT"     => (($comments == 0) ? '' : $this->user->lang('WALL_COMMENTS_COUNT', $comments)),
		    "showbox"       => (($comments < 2) ? false : true),
			"comments"      => $this->comments->getComments($id),			
			"likes"         => $this->wall_helper->_likes_list($likes, $id, $item_type),	
			"ltext"         => ($liked != false) ? $this->user->lang('WALL_LIKE') : $this->user->lang('WALL_UNLIKE'),	
			"limg"          => ($liked != false) ? true : false, 	
			"mine"          => ($wall_id === $this->user->data['user_id'] || $row['poster_id'] === $this->user->data['user_id']) ? true : false,
			);  
			
            }
		    return $data;
			}
        }
		
			
	public function addStatus($wall_id, $type, $msg, $extra = false){
			
          $allow_bbcode = $this->config['wall_status_bbcode'];
		  $allow_urls = $this->config['wall_status_url'];
		  $allow_smilies = $this->config['wall_status_smilies'];
		   $poster = (int) $this->user->data['user_id'];
		   $wall_id = (($wall_id == 0) ? (int) $this->user->data['user_id'] : $wall_id);
		   $plaintext = $msg;
  		    generate_text_for_storage($msg, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);	
			
			if($type == WALL_IMG){
			$image = $this->gallery->addPhoto($wall_id, $poster, $extra);		
			}
			
            $time = time();
			$sql_arr = array(
			'status'	      => $msg,
			'wall_id'	      => $wall_id,
			'poster_id'	      => $poster,
			'time'	          => $time,
			'bbcode_uid'      => $uid,
      	    'bbcode_bitfield' => $bitfield,
			'status_type'     => $type,
        	'status_extra'    => $image['id'],
		    );
		
		    $sql = "INSERT INTO " . $this->table_prefix . 'wall_status' . $this->db->sql_build_array('INSERT', $sql_arr);
		    $this->db->sql_query($sql);

			$sql = "SELECT w.*, u.username, u.user_id, u.user_avatar, u.user_avatar_type, u.user_colour
					FROM " . $this->table_prefix . 'wall_status' . " AS w, " . USERS_TABLE . " AS u
					WHERE w.time = {$time}
					AND w.poster_id = u.user_id";
			$rs = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow();	    
			$id = $row['status_id'];
			
			$this->notify->notify('add_status', $id, $plaintext, $wall_id, (int) $this->user->data['user_id'],'NOTIFICATION_WALL_ADD_STATUS');

           $result = array(
			"id"            => $id,
			"type"          => $type,			
			"msg"           => generate_text_for_display($msg, $uid, $bitfield, $flags),
			"image"         => $image['url'],
			"time"          => $this->wall_helper->time_ago($time),
            "poster"        => $this->wall_helper->wall_user($wall_id, $row['user_id'], $row['username'], $row['user_colour'], $type),
			"wall_id"       => $wall_id,						
            "avatar"        => $this->wall_helper->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 64, 64), 	
			"showbox"       => false,
			"comments"      => $this->comments->getComments($id),		
			"likes"         => '',
			"ltext"         => $this->user->lang('WALL_LIKE'),	
			"limg"          => '<i class="glyphicon glyphicon-thumbs-up"></i>',	 	
			"mine"          =>  true,									
		    );
			
			if($type == WALL_IMG){
			 $sql = 'UPDATE ' . $this->table_prefix . 'wall_photos' . ' SET status_id = '.$id.' WHERE photo_id = ' . $image['id'];
             $this->db->sql_query($sql); 
		     }
			
			$this->db->sql_freeresult($rs);	
            
            
		    return $result;
       }
	   		
			
		public function deleteStatus($id, $type, $user_id){
	   
          // Remoce images if they exists
	     if ($item_type = WALL_IMG)
		 {
	     $this->gallery->removeImg($id);
		 }
          // Lests delete comments and likes associated to the status		 
	    $this->comments->deleteComment($id);
		$this->wall_helper->_itemLike($id, $type, $user_id);
		 
         $del = $sql = 'DELETE FROM ' . $this->table_prefix . 'wall_status' . '
         WHERE status_id = ' . $id;
         $this->db->sql_query($sql);
            
		  if($del){
          $return = true;
			
		  $this->notify->notify('remove_status', $id, $plaintext, $wall_id, (int) $this->user->data['user_id'],'NOTIFICATION_WALL_ADD_STATUS');
		  
		  }
		  else{
			  $return = false;
		  }
     }	
			

   
}


