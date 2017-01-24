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

class comments
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
	 * @param \wall\wall\gallery $gallery 
	 * @param \wall\controller\notifyhelper $notifyhelper Notification helper.	 
	 */
	public function __construct($user, $helper, $config, $db, $wall_helper, $gallery, $notifyhelper, $root_path, $php_ext, $table_prefix)
	{	
	    $this->user = $user;
		$this->helper = $helper;
		$this->wall_helper = $wall_helper;
		$this->config = $config;
		$this->db = $db;
	    $this->gallery = $gallery;
	    $this->root_path = $root_path;	
		$this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
		$this->notify = $notifyhelper;	
	}

	public function getComments($status_id){
			
		  $allow_bbcode = $this->config['wall_cmt_bbcode'];
		  $allow_urls = $this->config['wall_cmt_url'];
		  $allow_smilies = $this->config['wall_cmt_smilies'];
			$flags = (($allow_bbcode) ? OPTION_FLAG_BBCODE : 0) +
           (($allow_smilies) ? OPTION_FLAG_SMILIES : 0) +
           (($allow_urls) ? OPTION_FLAG_LINKS : 0);
		   
			$sql_ary =  array(
		    'SELECT'	=> ' w.*, u.username, u.user_id, u.user_avatar, u.user_avatar_type, u.user_colour',
		    'FROM'		=> array(
			$this->table_prefix . 'wall_comments'			=> 'w',
		    ),
			'WHERE'	=> 'w.status_id = '.$status_id,
	         
		    'LEFT_JOIN'	=> array(
			array(
		   'FROM'	=> array(USERS_TABLE => 'u'),
		   'ON'	=> 'u.user_id = w.poster_id',
						)		
		    ),
		     'ORDER_BY'	=> 'w.time ASC'
	         );
			$data = array();
			$sql = $this->db->sql_build_query('SELECT', $sql_ary);
			$result = $this->db->sql_query($sql);
	        while ($row = $this->db->sql_fetchrow($result))
            {
			$comment_edit = generate_text_for_edit($row['comment'], $row['bbcode_uid'], $flags);	
		    $data[] = array(
			"id"       => $row['comment_id'],
            "msg"      => generate_text_for_display($row['comment'], $row['bbcode_uid'], $row['bbcode_bitfield'], $flags),
            "editable" => html_entity_decode($comment_edit['text']),		
			"canedit"  => ($row['poster_id'] === $this->user->data['user_id'] && $this->config['wall_cmt_edit'] && (time() <= ($row['time']+$this->config['wall_cmt_edit_time']))) ? true : false,				
			"sta_id"   => $row['status_id'],
            "time"     => $this->wall_helper->time_ago($row['time']),
            "poster"   => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),			
            "avatar"   => $this->wall_helper->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 34, 34), 	
			"mine"     => ($row['wall_id'] === $this->user->data['user_id'] || $row['poster_id'] === $this->user->data['user_id']) ? true : false,
			);
			
            }
		    return $data;
            }
			
	public function addComment($wall_id, $id, $msg){
			
          $allow_bbcode = $this->config['wall_cmt_bbcode'];
		  $allow_urls = $this->config['wall_cmt_url'];
		  $allow_smilies = $this->config['wall_cmt_smilies'];
		  $plaintext = $msg;
  		    generate_text_for_storage($msg, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);	
			
            $time = time();
			$sql_arr = array(
			'status_id'	      => $id,
			'comment'	      => $msg,
			'wall_id'	      => $wall_id,
			'poster_id'	      => (int) $this->user->data['user_id'],
			'time'	          => $time,
			'bbcode_uid'      => $uid,
      	    'bbcode_bitfield' => $bitfield,
		    );
		
		    $sql = "INSERT INTO " . $this->table_prefix . 'wall_comments' . $this->db->sql_build_array('INSERT', $sql_arr);
		    $this->db->sql_query($sql);

			$sql = "SELECT w.comment_id, w.poster_id, u.username, u.user_id, u.user_avatar, u.user_avatar_type, u.user_colour
					FROM " . $this->table_prefix . 'wall_comments' . " AS w, " . USERS_TABLE . " AS u
					WHERE w.time = {$time}
					AND w.status_id = {$id}
					AND w.poster_id = u.user_id 
					ORDER BY w.time ASC";
			$rs = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow();		
			$this->db->sql_freeresult($rs);    
			$id = $row['comment_id'];
			$comment_edit = generate_text_for_edit($msg, $uid, $flags);		
			$comments = $this->count_comments($id);	
			
			$this->notify->notify('add_cmt', $id, $plaintext, $wall_id, (int) $this->user->data['user_id'],'NOTIFICATION_WALL_ADD_CMT');
				
			$result = array(
			"id"        => $id,
			"msg"       => generate_text_for_display($msg, $uid, $bitfield, $flags),
            "editable"  => html_entity_decode($comment_edit['text']),	
			"canedit"   => true,
			'sta_id'    => $id,
			"time"      => $this->wall_helper->time_ago($time),
            "poster"    => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),			
            "avatar"    => $this->wall_helper->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 34, 34),
			"commentsT" => (($comments == 0) ? '' : $comments.' Comments'), 		
		    "hidebox"   => (($comments < 2) ? false : true),				
			"mine"      => true,		
		    );	
			
            
		    return $result;
            }		
			
		public function editComment($poster_id, $id, $msg){
	   
          $allow_bbcode = $this->config['wall_cmt_bbcode'];
		  $allow_urls = $this->config['wall_cmt_url'];
		  $allow_smilies = $this->config['wall_cmt_smilies'];
			
  		    generate_text_for_storage($msg, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);				   
            $sql = "UPDATE " . $this->table_prefix . 'wall_comments' . "
				SET comment = '" . $this->db->sql_escape($msg) . "', bbcode_uid = '" . $uid . "', bbcode_bitfield = '" . $bitfield . "'
				WHERE comment_id = '" . $this->db->sql_escape($id) . "'
				AND poster_id = '" . $this->db->sql_escape($poster_id) . "'";
		    $this->db->sql_query($sql);
			$comment_edit = generate_text_for_edit($msg, $uid, $flags);	
			$result = array(
			"id"       => $id,
			"msg"      => generate_text_for_display($msg, $uid, $bitfield, $flags),	
			"editable" => html_entity_decode($comment_edit['text']),	
		    );
			
            
		    return $result;
            }	
			
		public function deleteComment($id){
	   
	     $del = $sql = 'DELETE FROM ' . $this->table_prefix . 'wall_comments' . '
         WHERE comment_id = "' . $id . '"';
         $this->db->sql_query($sql);
          if($del){
			$comments = $this->count_comments($id); 
			
			$result = array(
			"commentsT"    => (($comments < 2) ? '' : $comments.' Comments'),
		    "hidebox"      => (($comments < 2) ? false : true),	
			 );	
			
			$this->notify->notify('remove_cmt', $id, $plaintext, $wall_id, (int) $this->user->data['user_id'],'NOTIFICATION_WALL_ADD_CMT');
		  }
		    else
			{
		   $return =  false;
          }	 
			
		}
	public function count_comments($sta_id)
		{
        $sql = 'SELECT COUNT(comment_id) AS count
        FROM ' . $this->table_prefix . 'wall_comments' . '
        WHERE status_id = ' . $sta_id;

        $result = $this->db->sql_query($sql);
       // The number of comments
        $count= (int) $this->db->sql_fetchfield('count');
        $this->db->sql_freeresult($result);
		return $count;

		}

   
}


