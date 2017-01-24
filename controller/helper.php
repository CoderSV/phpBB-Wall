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


namespace msaulohenrique\wall\controller;

class helper
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
	 * @param \wall\controller\notifyhelper $notifyhelper Notification helper.	 
	 */
	public function __construct($user, $helper, $config, $db, $notifyhelper, $root_path, $php_ext, $table_prefix)
	{	
	    $this->user = $user;
		$this->helper = $helper;
		$this->config = $config;
		$this->db = $db;
	    $this->root_path = $root_path;	
		$this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
		$this->notify = $notifyhelper;	
	}

		
	public function wall_user($wall_id, $user_id, $name, $color, $type)
		{
			
			$type = $this->user->lang['WALL_STATUS_TYPE'][$type];

			if($user_id != $wall_id) {
				
			$sql = "SELECT username, user_colour FROM " . USERS_TABLE . "
			WHERE user_id = ".$wall_id;
			$rs = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($rs);
			
			$user1 =  get_username_string('full', $user_id, $name, $color); 
			$user2 =  get_username_string('full', $wall_id, $row['username'], $row['user_colour']); 
			$this->db->sql_freeresult($rs);

			
		return $this->user->lang('WALL_STATUS_SHARE_INFO', $user1, $type, $user2);
			}
		else{
		return get_username_string('full', $user_id, $name, $color);
			}

		}		
		
    	public function wall_avatar($user, $type, $w, $h)
   {
   $data = array(
			"user_avatar"         => $user,
			"user_avatar_type"    => $type,
			"user_avatar_width"   => $h,
			"user_avatar_height"  => $w,
			);
			
         $core_avatar =  phpbb_get_user_avatar($data);
     	// We have to make sure the profile URL uses the forum path.
		  
		   preg_match('#(src=")(.+?)(download|images)#', $core_avatar, $matches);
		 
		   if ($matches){		
		      $core_avatar = preg_replace('#('.$matches[2].')#', $base_url = generate_board_url(). '/', $core_avatar, 1);
		   }
      
   $wall_avatar = '<img src="' . generate_board_url().'/ext/msaulohenrique/wall/images/no_avatar.png'.'" width="' .$w.'px" height="' . $h . 'px" />';
  
   return ($core_avatar) ? $core_avatar : $wall_avatar;
   
  
    }		

	public function last_status($wall_id = 0, $lsta = 0)
		{
			$u_id = (int) $this->user->data['user_id'];
			$list = $this->view_permissions($wall_id);
		    $status = "SELECT status_id FROM " . $this->table_prefix . 'wall_status' . "
			WHERE " . (($lsta != 0 )? 'status_id < ' .$lsta. ' AND ' : '') . ($wall_id != $u_id ? 'wall_id in (' . $list . ')' : ($wall_id != 0 ? 'wall_id = ' . $wall_id : ''))." ORDER BY status_id DESC LIMIT 5";
			
			
			$sql = "SELECT w.status_id FROM (".$status.")w ORDER BY w.status_id ASC";
			$rs = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($rs);
			$lstatus = $row['status_id'];
			$this->db->sql_freeresult($rs);
			
			$sql = "SELECT status_id FROM " . $this->table_prefix . 'wall_status' . "
			" . ($wall_id != $u_id ? 'WHERE wall_id in (' . $list . ')' : ($wall_id != 0 ? ' WHERE wall_id = ' . $wall_id : ''))." ORDER BY status_id ASC";
			$rs = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($rs);
			$staid = $row['status_id'];
			$this->db->sql_freeresult($rs);
			
			if ($lstatus == $staid )
			{
					return false;
			}
			else{
					return $lstatus;	
			}
		}
		
	public function view_permissions($wall_id)
		{
			
		   // Lets pull the friend list for statues view permissions  ".($wall_id != $u_id ? 'w.poster_id = (' . $list . ') AND' : '')."
		    $u_id = $this->user->data['user_id'];
		    $user = ($wall_id != 0 ? $wall_id : $u_id);
		    $sql = "SELECT z.*, u.user_id
			        FROM " . ZEBRA_TABLE . " AS z, " . USERS_TABLE . " AS u
			        WHERE z.user_id = $user AND z.friend = 1
			        AND u.user_id = z.zebra_id
                    AND u.user_type NOT IN (" . USER_INACTIVE . "," . USER_IGNORE . ")
			        ORDER BY u.user_id ASC";
		   $result = $this->db->sql_query($sql);
		   
	       while ($row = $this->db->sql_fetchrow($result))
					       {	
	                        $list[]=  $row['user_id'];
            }
	       $this->db->sql_freeresult($result);	
		   if($list)
		   {
		    if (in_array($u_id, $list)) {
             $friend = $u_id;
            }
		   $list = rtrim(implode(',', $list), ',');
		   }
		   else{
			  $list = '' ;
		   }
		   return $list = ($list && $wall_id == 0) ? $list.','.$u_id : (($wall_id == 0) || ($wall_id == $u_id) ? $u_id : ($friend ? $wall_id : ''));
		}
		
	public function _itemLike($item_id, $item_type, $user_id, $wall_id = false, $msg = false){
		
			if ($this->_item_liked($item_id, $item_type, $user_id)!= false)
            {	
					
            $time = time();
			$sql_arr = array(
			'item_id'	  => $item_id,
			'item_type'   => $item_type,			
			'user_id'	  => $user_id,
			'like_date'	  => $time
		    );
		
		    $sql = "INSERT INTO " . $this->table_prefix . 'wall_likes' . $this->db->sql_build_array('INSERT', $sql_arr);
		    $this->db->sql_query($sql);
            $likes = $this->_count_item_likes($item_id, $item_type);
		    $result = array(
			"likes"  => $this->_likes_list($likes, $item_id, $item_type),
			"ltext"  => $this->user->lang('WALL_UNLIKE'),	
			"limg"   => false,		
		    );
		    $this->notify->notify('add_like', $item_id, $msg, $wall_id, $user_id,'NOTIFICATION_WALL_LIKE_STATUS');				
			}
			else{
			$sql = 'DELETE FROM ' . $this->table_prefix . 'wall_likes' . ' 
			WHERE item_id = '.$item_id.' AND item_type = '.$item_type.' AND user_id = '.$user_id;
$this->db->sql_query($sql);

            $likes = $this->_count_item_likes($item_id, $item_type);
	       
		    $result = array(
			"likes"  => $this->_likes_list($likes, $item_id, $item_type),
			"ltext"  => $this->user->lang('WALL_LIKE'),	
			"limg"   => true,		
		    );		
		    $this->notify->notify('remove_like', $item_id, $msg, $wall_id, $user_id,'NOTIFICATION_WALL_LIKE_STATUS');				
			}
            return $result;
         
        }


					
	public function _count_item_likes($item_id, $item_type)
		{
	
	    $sql = 'SELECT COUNT(like_id) AS count
        FROM ' . $this->table_prefix . 'wall_likes' . '
        WHERE item_id = '.$item_id.' AND item_type = '.$item_type;

        $result = $this->db->sql_query($sql);
       // The number of likes
        $count= (int) $this->db->sql_fetchfield('count');
        $this->db->sql_freeresult($result);

		return $count;

		}
		
	public function loadLikes($item_id, $item_type)
		{
		$sql = 'SELECT l.*, u.username, u.username_clean, u.user_id, u.user_avatar, u.user_avatar_type, u.user_colour 
				FROM ' . $this->table_prefix . 'wall_likes' . ' as l INNER JOIN ' . USERS_TABLE . ' as u ON u.user_id = l.user_id 
				WHERE l.item_id = '.$item_id.' AND l.item_type = ' . $item_type. ' ORDER BY u.username_clean ASC';
		             $result = $this->db->sql_query_limit($sql, 5);
			         while ($row = $this->db->sql_fetchrow($result))
					       {	
	                        $data[] = array(
                                            "name"        => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),					
                                            "avatar"      => $this->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 80, 80), 	
			                                );
			
                            }
                     return $data;

		}		
		
		
	public function _likes_list($count, $item_id, $item_type)
		{
			$limit = 2;
			$own = (int) $this->user->data['user_id'];
		    $sql = 'SELECT l.*, u.username, u.user_colour 
				FROM ' . $this->table_prefix . 'wall_likes' . ' as l INNER JOIN ' . USERS_TABLE . ' as u ON u.user_id = l.user_id 
				WHERE l.item_id = '.$item_id.' AND l.item_type = ' . $item_type. ' ORDER BY CASE WHEN l.user_id = '.$own.' THEN 0 ELSE 1 END, user_id ASC';
			
			$result = $this->db->sql_query_limit($sql, $limit);
			$i = 0;
			while($row = $this->db->sql_fetchrow($result))
			{
		  	   $i++;
		       $space = ' ';
			   $others = '<a href="javascript:void(0);" ng-click="loadLikes(' . $this->user->lang['WALL_STATUS_TYPE'][$item_type] . ')" >' . $this->user->lang('WALL_LIKE_AND_OTHERS', ($count-$i)) . '</a>';
			   $list = (($own == $row['user_id']) ? $this->user->lang('WALL_LIKE_U') : (get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'])));
			 $like .=  $list.(($count > 1) ? (($i == $count -1 ) ? (($count > $limit) ? $others : $this->user->lang('WALL_LIKE_AND')) : (($i == $count) ? $space : ", ")) : $space);
			 
			 
			
			$data = $like.(($count == 0) ? '' : (($count > 1) ? $this->user->lang('WALL_LIKE_THIS') : ($own == $row['user_id']) ? $this->user->lang('WALL_LIKE_THIS') : $this->user->lang('WALL_LIKE_THIS_S')));
			}

			$this->db->sql_freeresult($result);
		
		   return $data;

		}		
		
		
	public function _like_id($item_id, $user_id)
		{

		    $sql = 'SELECT like_id
				FROM ' . $this->table_prefix . 'wall_likes' . '
				WHERE item_id = ' .$item_id. ' AND user_id = ' . $user_id;
			
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);

			$this->db->sql_freeresult($result);
		
		   return $row['like_id'];

		}			
		
		
		
	
		
		
// Check if like exists 
       		
	public function _item_liked($item_id, $item_type, $user_id)
		{
	
	  $sql = 'SELECT COUNT(like_id) AS likes
        FROM ' . $this->table_prefix . 'wall_likes' . '
        WHERE item_id = '.$item_id.' AND item_type = ' . $item_type. ' AND user_id = '. $user_id;

        $result = $this->db->sql_query($sql);
       // Likes
        $likes = (int) $this->db->sql_fetchfield('likes');
        $this->db->sql_freeresult($result);

		if ($likes == 0)
        {
        return true;
        }
		else{
		return false;
		}

		}		
								  
	public function time_ago($from, $to = 0)
	{
				
	$periods = array(
		"SECOND",
		"MINUTE",
		"HOUR",
		"DAY",
		"WEEK",
		"MONTH",
		"YEAR",
		"DECADE",
	);
	$lengths = array(
		"60",
		"60",
		"24",
		"7",
		"4.35",
		"12",
		"10",
	);
		if ($to == 0)
		{
			$to = time();
		}

		if ($to > $from)
		{
			$difference = $to - $from;
			$tense = 'WALL_TIME_AGO';
		}
		else
		{
			$difference = $from - $to;
			$tense = 'WALL_TIME_FROM_NOW';
		}


		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++)
		{
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		$period = $periods[$j];

		if ($difference != 1)
		{
			$period .= "S";
		}

		return sprintf($this->user->lang[$tense], $difference, $this->user->lang['WALL_TIME_PERIODS'][$period]);
	} 	
	
	
		public function generate_wall_url($mode)
		       {
	           switch($mode)
	                      {
		                   case 'images':
			                    $link =  generate_board_url().'/ext/msaulohenrique/wall/images/gallery/'; 
			               break;
						 
		                   case 'music':
			                    $link =  generate_board_url().'/ext/msaulohenrique/wall/music/mp3/'; 
			               break;			
	                     }
              return $link;
		    }	
			
		public function urlFetch($url)
		       {
		              include_once($this->root_path . 'ext/msaulohenrique/wall/simple_html_dom.inc.' . $this->php_ext);	
		              //get URL content
		              $get_content = file_get_html($url); 
                      //Get Page Title 
		              foreach($get_content->find('title') as $element) 
					         {
			                  $page_title = $element->plaintext;
	                         }
	        		  //Get Body Text
		              foreach($get_content->find('body') as $element) 
		                     {
			                  $page_body = trim($element->plaintext);
			                  $pos=strpos($page_body, ' ', 200); //Find the numeric position to substract
			                  $page_body = substr($page_body,0,$pos ); //shorten text to 200 chars
		                     }
	
		              $image_urls = array();
		              //get all images URLs in the content
		              foreach($get_content->find('img') as $element) 
		                     {
				      /* check image URL is valid and name isn't blank.gif/blank.png etc..
				      you can also use other methods to check if image really exist */
				              if(!preg_match('/blank.(.*)/i', $element->src) && filter_var($element->src, FILTER_VALIDATE_URL))
				                {
					             $image_urls[] =  $element->src;
				                }
		                      }
	                 //prepare for JSON 
		             $return = array(
		                             'title'   => $page_title, 
		                             'images'  => $image_urls, 
		                             'content' => $page_body
		                             );
              return $return;
		    }				
	
	
			/**
	 * @param string $message
	 * @param int $status_code
	 * @param int $redirect_time
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function finish($message, $status_code, $redirect_time = 10)
	{
		$this->meta_refresh($redirect_time);
		return $this->helper->error($this->user->lang($message), $status_code);
	}

	/**
	 * @param int $redirect_time
	 * @return null
	 */
	public function meta_refresh($redirect_time)
	{
		meta_refresh($redirect_time, $this->helper->route('wall_page'));
	}
	
}
