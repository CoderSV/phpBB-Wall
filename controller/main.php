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

class main
{
	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\request\request */
	protected $request;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;
	
	/* @var string phpBB root path */
	protected $root_path;	
	
	/* @var string phpEx */
	protected $php_ext;
    /**
    * Wall gallery path
    * @var string
    */
    protected $includes_path;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth		$auth	
	* @param \phpbb\config\config		$config
	* @param \phpbb\controller\helper	$helper
	* @param \phpbb\template\template	$template
	* @param \wall\controller\notifyhelper $notifyhelper Notification helper.
	* @param \phpbb\user				$user
	* @param \phpbb\db\driver\driver_interface	$db	db object	
	* @param \phpbb\request\request	$request	
	* @param \wall\\controller\helper $wall_helper	 	
	* @param \wall\wall\status $status	 	 	
	* @param \wall\wall\comments $comments	 	
	* @param \wall\wall\gallery $gallery	 	
	* @param \wall\wall\zebra $zebra	 	
	* @param \wall\music\music $music 
	*/
	public function __construct($auth, $config, $db, $request, $helper, $wall_helper, $status, $comments, $gallery, $zebra, $music, $template, $notifyhelper, $user, $root_path, $php_ext, $table_prefix)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->helper = $helper;
		$this->wall_helper = $wall_helper;	
		$this->template = $template;
		$this->notify = $notifyhelper;
		$this->user = $user;
	    $this->root_path = $root_path;	
		$this->php_ext = $php_ext;
	    $this->status = $status;
	    $this->comments = $comments;
	    $this->gallery = $gallery;
	    $this->zebra = $zebra;
	    $this->music = $music;
        $this->table_prefix = $table_prefix;					
	    $this->wall_path = $this->root_path.'ext/msaulohenrique/wall';			
		
		 if (!defined('WALL_STATUS'))
         {
          include($this->root_path . 'ext/msaulohenrique/wall/wall/constants.' . $this->php_ext);	
         }
	}

	/**
	* Wall controller for route /wall/{name}
	*
	* @param string		$name
	* @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	*/
	
   public function handle($name)
   {
    if (!$this->config['wall_enabled'])
       {
        redirect(append_sid(generate_board_url()));
       }

		
    // Can this user view profiles/memberlist?
    if (!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
       {
        if ($this->user->data['user_id'] != ANONYMOUS)
           {
            trigger_error('NO_VIEW_USERS');
           }
           login_box('', ((isset($this->user->lang['LOGIN_EXPLAIN_' . strtoupper('viewprofile')])) ? $this->user->lang['LOGIN_EXPLAIN_' . strtoupper('viewprofile')] : $this->user->lang['LOGIN_EXPLAIN_MEMBERLIST']));
       }				

		$mode           = $this->request->variable('mode', '');
	    $string         = $this->request->variable('string', '');	
        $poster         = (int) $this->user->data['user_id'];
	    $msg   		    = utf8_normalize_nfc($this->request->variable('msg', '', true));
	    $wall_id 	    = $this->request->variable('wall_id', '');		
	    $id   		    = $this->request->variable('id', '');	
        $lsta   		= $this->request->variable('lstatus', '');		
        $type   		= $this->request->variable('type', '');	
        $top    		= $this->request->variable('top', '');		
        $left   		= $this->request->variable('left', '');		
	    $url            = $this->request->variable('url', '');
		
	// Check if username exist and get ID	
   if ($name != 'mp') 
      {		
	   $sql = 'SELECT user_id
               FROM ' . USERS_TABLE . "
               WHERE user_type IN (". USER_NORMAL . ", " . USER_FOUNDER . ")
			   AND username_clean = '" . $this->db->sql_escape(utf8_clean_string($name)) . "'";
               $result = $this->db->sql_query($sql);
               $row = $this->db->sql_fetchrow($result);
		       $wall_id = $row['user_id'];		
               $this->db->sql_freeresult($result); 
               if (empty($wall_id))
			      {		
			       return $this->wall_helper->finish('NO_USER', 400);		
                  }
       }
			
								
		
		switch ($mode) 	
		       {
                case 'getStatus':	
                case 'moreStatus':	
                     $return = $this->status->getStatus($wall_id, $lsta);
			         header('Content-type: application/json');
			         die(json_encode($return));
			    break;
                
				case 'addStatus':
			         $return = $this->status->addStatus($wall_id, $type, $msg);
			         header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
			         header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			         die(json_encode($return));
		        break;

                case 'statusImg':
   	            //let's access these values by using their index position
	                 $files  = $this->request->file('ImageFile');
                     $return = $this->status->addStatus($wall_id, $type, $msg, $files);
			         header('Content-type: application/json');
			         die(json_encode($return));
	            break;
				
				case 'deleteStatus':
                     $return = $this->status->deleteStatus($id, $type, $poster);
                     header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
			         header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			         die(json_encode($return));
		        break;
		       
                case 'urlFetch':
                     $return = $this->wall_helper->urlFetch($url);
			         header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
			         header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			         die(json_encode($return));
                break;		

                case 'likeStatus':
                     $return = $this->wall_helper->_itemLike($id, $type, $poster, $wall_id, $msg);
		             header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
			         header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			         die(json_encode($return));
                break;		

                case 'loadLikes':
                     $return = $this->wall_helper->loadLikes($id, $type);
		             header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
			         header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			         die(json_encode($return));
                break;   
                // Add comments
                case 'addComment':		
                     $return = $this->comments->addComment($wall_id, $id, $msg);
                     header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
			         header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			         die(json_encode($return));
		        break;
   
                case 'editComment':
	                 $return = $this->comments->editComment($poster, $id, $msg);
                     header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
				   	 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					 die(json_encode($return));
				break;
               // Delete Commment
				case 'deleteComment':
		    		 $return = $this->comments->deleteComment($id);
            		 header('Content-type: application/json');
					 header("Cache-Control: no-cache, must-revalidate");
					 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					 die(json_encode($return));
				break;
                // Get photos
				case 'getPhotos':
             		 $return = $this->gallery->get_photos($wall_id);	
             		 header('Content-type: application/json');
					 header("Cache-Control: no-cache, must-revalidate");
					 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					 die(json_encode($return));
    			break; 
   
     			case 'updateCover':
                     $return = $this->gallery->update_cover($id, $top);
			         header('Content-type: application/json');
			         die(json_encode($return));		
     			break;   
	            // Get Music
				case 'getMusic':
                     $return = $this->music->get_music($wall_id);
				     header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
			         header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			         die(json_encode($return));
    			break; 
	
				case 'addMusic':
	                 $files  = $this->request->file('music');
                     $return = $this->music->add_music($files);	
                     header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
		           	 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			         die(json_encode($return));
    			break; 
	
      			case 'removeMusic':
				     $return = $this->music->remove_music($wall_id, $id, $url);	
            		 header('Content-type: application/json');
					 header("Cache-Control: no-cache, must-revalidate");
					 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					 die(json_encode($return));
			   	break;	
	
     			case 'loadFriends':
				
				     $return = $this->zebra->loadFriends($wall_id);	
             		 header('Content-type: application/json');
					 header("Cache-Control: no-cache, must-revalidate");
					 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					 die(json_encode($return));
                break;
    
     			case 'friendRequest':
                     $return = $this->zebra->friendRequest($wall_id);	
			         header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
                     header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					 die(json_encode($return));
                break;  
   
                case 'friendAction':
                     $return = $this->zebra->friendAction($wall_id, $poster);	
			         header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
                     header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					 die(json_encode($return));
                break;  
   
                case 'deleteFriend':
                     $return = $this->zebra->deleteFriend($wall_id, $poster);	
					 header('Content-type: application/json');
					 header("Cache-Control: no-cache, must-revalidate");
					 header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
					 die(json_encode($return));
			    break;  
   
     			case 'search':
	                 $return = $this->zebra->searchMembers($string);	
					 header('Content-type: application/json');
			         header("Cache-Control: no-cache, must-revalidate");
			         header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			         die(json_encode($return));   
                break; 
               }
   
                // Load features depending of the page
                if 	($name == 'mp')
			    /* Mail wall body */
			        {
                     // newest registered users block
					 if ($this->config['wall_blocks_new_users'])
					    {
	                     $sql = 'SELECT user_id, username, user_colour, user_avatar, user_avatar_type, user_regdate
                                 FROM ' . USERS_TABLE . '
                                 WHERE user_type NOT IN (' . USER_INACTIVE .',' . USER_IGNORE . ')
                                 AND user_inactive_reason = 0
                                 ORDER BY user_regdate DESC';
                                 $result = $this->db->sql_query_limit($sql, $this->config['wall_blocks_new_users_max']);
			                     while ($row = $this->db->sql_fetchrow($result))
	                             {
                		          $this->template->assign_block_vars('newest_users' ,array(
   		                          'DATE'	  => $this->user->format_date($row['user_regdate']),
                                  'USERNAME'  => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),		
			                      'AVATAR'    => $this->wall_helper->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 80, 80),
			                      ));	
                                 }
	                             $this->db->sql_freeresult($result);
						 }
                     // recent discussions block						 
					  if ($this->config['wall_blocks_latest_topics'])
	                     {
                          $a_f_auth_read = $this->auth->acl_getf('f_read');
		                  $a_f_read = array();
		                  if (!empty($a_f_auth_read))
		                   {
			                foreach ($a_f_auth_read as $i_f_id => $a_auth)
			                {
				             if ($a_auth['f_read'] == 1)
				                {
					             $a_f_read[] = $i_f_id;
				                }
			                }
		                   }

		                  $last_topics = 'SELECT p.post_id, p.topic_id, p.post_time, p.forum_id, t.topic_title, f.forum_name
										 FROM ' . POSTS_TABLE . ' p
										 LEFT JOIN ' . TOPICS_TABLE . ' t ON p.topic_id = t.topic_id
										 LEFT JOIN ' . FORUMS_TABLE . ' f ON p.forum_id = f.forum_id
										 WHERE p.post_visibility = 1
										 AND ' . $this->db->sql_in_set('p.forum_id', $a_f_read, false, true) . '
										 ORDER BY p.post_id DESC';

		                  $last_topics = 'SELECT t.topic_id, t.topic_title, t.topic_last_post_id, t.topic_last_post_time, t.forum_id, f.forum_name
										 FROM ' . TOPICS_TABLE . ' t
										 LEFT JOIN ' . FORUMS_TABLE . ' f ON t.forum_id = f.forum_id
										 LEFT JOIN ' . POSTS_TABLE . ' p ON t.topic_last_post_id = p.post_id
										 WHERE p.post_visibility = 1
										 AND ' . $this->db->sql_in_set('t.forum_id', $a_f_read, false, true) . '
										 ORDER BY t.topic_last_post_id DESC';
		                  $last_topics_result = $this->db->sql_query_limit($last_topics, $this->config['wall_blocks_latest_topics_max']);
		                  $last_topics_rowset = $this->db->sql_fetchrowset($last_topics_result);
		                  $this->db->sql_freeresult($last_topics_result);

	                      for ($i = 0; isset($last_topics_rowset[$i]); $i++)
		                      {
		                	   $last_topics = $last_topics_rowset[$i];
			                   $this->template->assign_block_vars('last_topics', array(
				                'TOPIC_TITLE'		 => $last_topics['topic_title'],
				                'POST_LINK'			 => append_sid($this->root_path. "viewtopic.". $this->php_ext, "t=" . $last_topics['topic_id'] . "&amp;p=" . $last_topics['topic_last_post_id'] . "#p" . $last_topics['topic_last_post_id']),
				                'TOPIC_FORUM'		 => $last_topics['forum_name'],
				                'TOPIC_FORUM_LINK'	 => append_sid($this->root_path. "viewforum.". $this->php_ext, "f=" . $last_topics['forum_id']),
				                'POST_TIME'			 => $this->wall_helper->time_ago($last_topics['topic_last_post_time']),
			                    ));
		                       }
	                      }
                         $this->template->assign_vars(array(
                          'S_ON_WALL'		         => true,	
	                      'WALL_PATH'                => $this->helper->route('wall_page', array('name' => $name)),	
	                      'S_MY_WALL_AVATAR'	     => $this->wall_helper->wall_avatar($this->user->data['user_avatar'], $this->user->data['user_avatar_type'], 150, 150),			                          'U_PROFILE_AVATAR'         => append_sid($this->root_path. "ucp.". $this->php_ext, 'i=profile&amp;mode=avatar'),
						  'WALL_NEW_USERS_BLOCK'	 => ($this->config['wall_blocks_new_users']) ? true : false,	
						  'WALL_FRIEND_REQ_BLOCK'	 => ($this->config['wall_blocks_friends_req']) ? true : false,	
						  'WALL_LATEST_TOPICS_BLOCK' => ($this->config['wall_blocks_latest_topics']) ? true : false,
                           ));
         
			              return $this->helper->render('wall_body.html', $this->user->lang('WALL_MAIN'));
				    }
			       
			    else
                    {
				     /* Profile body */	
			
	                 // Profile data
                     if ( ($name && utf8_clean_string($name) == $this->user->data['username_clean']) || $user_id == $this->user->data['user_id'] )
                          {
	                       $member = array(
		                                   'user_id'	            => $this->user->data['user_id'],
		                                   'username'	            => $this->user->data['username'],
		                                   'user_type'	            => $this->user->data['user_type'],
		                                   'user_colour'	        => $this->user->data['user_colour'],
		                                   'user_inactive_reason'	=> $this->user->data['user_inactive_reason'],
		                                   'user_avatar'	        => $this->user->data['user_avatar'],
		                                   'user_avatar_type'	    => $this->user->data['user_avatar_type'],
		                                   'user_avatar_width'	    => $this->user->data['user_avatar_width'],
		                                   'user_avatar_height'	=> $this->user->data['user_avatar_height'],
		                                   'user_allow_pm'	        => $this->user->data['user_allow_pm'],
										   );
	                        }
                        else
                            {
	                         $sql = 'SELECT *
			                         FROM ' . USERS_TABLE . '
			                         WHERE ' . (($name) ? "username_clean = '" . $this->db->sql_escape(utf8_clean_string($name)) . "'" : "user_id = $user_id");
	                         $result = $this->db->sql_query($sql);
							 $member = $this->db->sql_fetchrow($result);
							 $this->db->sql_freeresult($result);
							}
                        if (!$member && ($name != 'mp'))
                            {
	                         trigger_error('NO_USER');
							}
                        // Admins and founder are able to view inactive users and bots to be able to manage them more easily.
                        if (!$this->auth->acl_get('a_user') && $this->user->data['user_type'] != USER_FOUNDER)
                            {
							 if (($member['user_type'] == USER_IGNORE) || ($member['user_type'] == USER_INACTIVE && $member['user_inactive_reason'] != INACTIVE_PROFILE) && $name != 'mp')             
							  {
		                       trigger_error('NO_USER');
	                          }
							 }
                        $user_id = (int) $member['user_id'];
                        $online = false;
                        if ($this->config['load_onlinetrack'])
                            {
 	                         $sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
                                     FROM ' . SESSIONS_TABLE . "
                                     WHERE session_user_id = " . $user_id;
	                         $result = $this->db->sql_query($sql);
	                         $session = $this->db->sql_fetchrow($result);
	                         $this->db->sql_freeresult($result);

	                         $member['session_time'] = (isset($session['session_time'])) ? $session['session_time'] : 0;
	                         $member['session_viewonline'] = (isset($session['session_viewonline'])) ? $session['session_viewonline'] : 0;
	                         unset($session);

	                         $update_time = $this->config['load_online_time'] * 60;
	                         $online = (time() - $update_time < $member['session_time'] && ((isset($member['session_viewonline']) && $member['session_viewonline']) || $this->auth->acl_get('u_viewonline'))) ? true : false;
                             }
							 
						 
				            // Custom fields
                            $sql = 'SELECT *
			                        FROM ' . PROFILE_FIELDS_DATA_TABLE . '
			                        WHERE user_id = ' . $user_id . ' ';
	                        $result = $this->db->sql_query($sql);
	                        $field = $this->db->sql_fetchrow($result);
	                        $this->db->sql_freeresult($result);									 
							 
							 // Check zebra only check if the user is logged in
                            if ($this->user->data['is_registered'])
                                {
                                 $zebra = $this->zebra->zebraLinks();
		                        }
		                     // Friend request status
		                     $friends = $this->zebra->friendStatus($user_id);
		                     // Lets pull profile the cover data
		                     $cover = $this->gallery->profile_cover($user_id);
							 
							 
		                     $this->template->assign_vars(array(			
	                         'WALL_ID'              => $wall_id,
	                         'WALL_PATH'            => $this->helper->route('wall_page', array('name' => $name)),
	                         'S_WALL_PROFILE_AVATAR'=> $this->wall_helper->wall_avatar($member['user_avatar'], $member['user_avatar_type'], 160, 160),		      
	                         'U_PROFILE_AVATAR'     => append_sid($this->root_path. "ucp.". $this->php_ext, 'i=profile&amp;mode=avatar'),
                             'S_OWN_PROFILE'		=> ($user_id === (int) $this->user->data['user_id']) ? true : false,
 	                         'WALL_USERNAME'		=> get_username_string('username', $user_id, $member['username'], $member['user_colour']),
                             'LOCATION'		        => $field['pf_phpbb_location'],	
                             'OCCUPATION'   	    => $field['pf_phpbb_occupation'],
							 'FACEBOOK'		        => $field['pf_phpbb_facebook'],	
                             'WEBSITE'  		    => $field['pf_phpbb_website'],
                             'PROFILE_BANNER_IMG'   => $cover['cover'],			
                             'PROFILE_BANNER_TOP'   => $cover['top'],				
                             'S_ZEBRA'				=> ($this->user->data['user_id'] != $user_id && $this->user->data['is_registered'] && $zebra['zebra']) ? true : false,
							 'WALL_IS_FRIEND'	    => ($friends['friends'] || $user_id === (int) $this->user->data['user_id']) ? true : false,
                             'WALL_FRIEND_ACTION'	=> $friends['status'],
                             'WALL_FRIEND_ACT_ICON'	=> $friends['icon'],	
                             'U_PM'				    => ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm') && ($member['user_allow_pm'] || $this->auth->acl_gets('a_', 'm_') || $this->auth->acl_getf_global('m_'))) ? append_sid($this->root_path."ucp.". $this->php_ext, 'i=pm&amp;mode=compose&amp;u=' . $user_id) : '',
	                         'WALL_TOTAL_FRIENDS'	=> $this->zebra->countFriends($user_id),
	                         'WALL_TOTAL_PHOTOS'    => $this->gallery->total_photos($user_id),
	                         'S_ONLINE'				=> ($this->config['load_onlinetrack'] && $online) ? true : false,
                             'S_ON_PROFILE'		    => true,		
	
                             /*	Music tab	*/
							 'WALL_MUSIC_ENABLED'   => ($this->config['wall_music_enabled']) ? true : false,
                             'M_SONG_EXIST'         => ($this->music->get_music($user_id) > 0 ) ? true : false,
                             'M_MUSIC_USER' 	    => $user_id,
	
		                    ));
          
		                    return $this->helper->render('wall_profile_body.html', $name);							 
					
                    }
    }
	
}
