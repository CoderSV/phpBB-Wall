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

class zebra
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
	 * @param \wall\wall\gallery $gallery
	 * @param \phpbb\config\config			$config		Config object
	 * @param \phpbb\db\driver\driver_interface			$db		db object	 
	 */
	public function __construct($user, $helper, $wall_helper, $gallery, $config, $db, $root_path, $php_ext, $table_prefix)
	{	
	    $this->user = $user;
		$this->helper = $helper;
		$this->wall_helper = $wall_helper;
	    $this->gallery = $gallery;
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


 	    public function loadFriends($wall_id)
                   {
					 $sql = "SELECT z.*, u.username, u.username_clean, u.user_id, u.user_avatar, u.user_avatar_type, u.user_colour
					         FROM " . ZEBRA_TABLE . " AS z, " . USERS_TABLE . " AS u
					         WHERE z.user_id = $wall_id AND z.friend = 1
					         AND u.user_id = z.zebra_id
			                 AND u.user_type NOT IN (" . USER_INACTIVE . "," . USER_IGNORE . ")
					         ORDER BY u.username_clean ASC";
		             $result = $this->db->sql_query($sql);
			         while ($row = $this->db->sql_fetchrow($result))
					       {	
	                        $data[] = array(
                                            "name"      => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),					
                                            "avatar"    => $this->wall_helper->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 80, 80), 					
                                            "f_id"      => $row['user_id'],	
			                                );
			
                            }
                     return $data;
   
                   }
   
 	    public function friendRequest($wall_id)
                   {
                     $wall_id = ($wall_id != 0 ? $wall_id :  $this->user->data['user_id']);
	 		         $sql = "SELECT u.user_id, u.username, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_colour
			             	 FROM " . ZEBRA_TABLE . " AS z, " . USERS_TABLE . " AS u
				             WHERE z.approval = 1
				             AND z.zebra_id = {$wall_id}
				             AND u.user_id = z.user_id
				             AND u.user_type NOT IN (" . USER_INACTIVE . "," . USER_IGNORE . ")";
                   	 $result = $this->db->sql_query_limit($sql, 4);
					 
			         while ($row = $this->db->sql_fetchrow($result))
					       {	
	                        $data[] = array(	
			                                "id"      => $row['user_id'],
          								    "name"    => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),					
            								"avatar"  => $this->wall_helper->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 40, 40), 
											);
		                   }
                     return $data;
   
                   }
      
 	    public function friendAction($wall_id, $poster)
                   {
                    $sql = "SELECT * FROM " . ZEBRA_TABLE . "
                            WHERE zebra_id IN ($wall_id, $poster)
                            AND user_id IN ($wall_id, $poster)";
                    $result = $this->db->sql_query($sql);
                    $row = $this->db->sql_fetchrow($result);
					
		            if ($row['user_id'] == $wall_id && $row['zebra_id'] == $poster && $row['approval'] == 1)
					   {
			  		    $sql = 'UPDATE ' . ZEBRA_TABLE . ' SET friend = 1, approval = 0 
						        WHERE user_id = '.$wall_id.'
			                    AND zebra_id = '.$poster;
                        $this->db->sql_query($sql); 
			            $sql_arr = array(
			                             'user_id'	 => $poster,
										 'zebra_id'   => $wall_id,			
										 'friend'	 => 1,
										 'foe'	     => 0,
										 'approval'   => 0,
		                                );
		                $sql = "INSERT INTO " . ZEBRA_TABLE . $this->db->sql_build_array('INSERT', $sql_arr);
		                $this->db->sql_query($sql);
		                $result = array(
										"text"  => $this->user->lang('WALL_FRIENDS'),
										"icon"  => 'glyphicon-ok',		
		    							);
			           }
		            else if (!($row['friend'] || $row['approval']))
                            {	
			                 $sql_arr = array(
			                                  'user_id'	 => $poster,
											  'zebra_id'   => $wall_id,			
											  'friend'	 => 0,
											  'foe'	     => 0,
											  'approval'   => 1,
		                                      );
		                     $sql = "INSERT INTO " . ZEBRA_TABLE . $this->db->sql_build_array('INSERT', $sql_arr);
		                     $this->db->sql_query($sql);
		                     $result = array(
			                                 "text"  => $this->user->lang('WALL_FRIENDS_CANCEL_REQ'),
											 "icon"  => 'glyphicon-remove',		
		    								 );
							}
			        else
					        {
			                 $sql = 'DELETE FROM ' . ZEBRA_TABLE . ' 
			                         WHERE (zebra_id = '.$wall_id.' AND user_id = '.$poster.')
			                         OR (user_id = '.$wall_id.' AND zebra_id = '.$poster.')';
                             $this->db->sql_query($sql);
	                         $result = array(
											 "text"  => $this->user->lang('WALL_FRIENDS_ADD'),
											 "icon"  => 'glyphicon-plus',			
		    								 );
			                }
                     return $result;
                   }		 
		 
 	    public function deleteFriend($wall_id, $poster)
                   {
                     $sql = 'DELETE FROM ' . ZEBRA_TABLE . ' 
			                 WHERE (zebra_id = '.$wall_id.' AND user_id = '. $poster .')
			                 OR (user_id = '.$wall_id.' AND zebra_id = ' . $poster .')';
                     $this->db->sql_query($sql);
	                 $result = array(
									 "text"  => $this->user->lang('WALL_FRIENDS_ADD'),
									 "icon"  => 'glyphicon-plus',			
		                            );
                     return $result;
                    }	
					
 	    public function countFriends($user_id)
                   {
                    $sql = 'SELECT COUNT(z.zebra_id) AS num_friends
		                    FROM ' . ZEBRA_TABLE . ' z, ' . USERS_TABLE . ' u
		                    WHERE z.user_id = ' . $user_id . '
			                AND z.friend = 1 AND u.user_id = z.zebra_id
			                AND u.user_type NOT IN (' . USER_INACTIVE . ',' . USER_IGNORE . ')';
                    $result = $this->db->sql_query($sql);
                    $total_friends = $this->db->sql_fetchfield('num_friends');
                    $this->db->sql_freeresult($result);
                    return $total_friends;
                    }	
					
 	    public function zebraLinks()
                   {
					$zebra_enabled = $friends_enabled = $foes_enabled = false;
	               if (!class_exists('p_master'))
	                  {
		               include_once($this->root_path . 'includes/functions_module.' . $this->php_ext);
	                  }
			       $module = new \p_master();
                   $module->list_modules('ucp');
			       $module->list_modules('mcp');
			       
				   $data = array(
                                 "zebra"   => ($module->loaded('ucp_zebra')) ? true : false,		
                                 "friends" => ($module->loaded('ucp_zebra', 'friends')) ? true : false,	
								 "foes"    => ($module->loaded('ucp_zebra', 'foes')) ? true : false,							
			                     );					   
			       unset($module);
				   
                   return $data;
                   }										 
		 
 	    public function searchMembers($string)
                   {
	    			 $sql = 'SELECT username, user_id, user_avatar, user_avatar_type, user_avatar_width, user_avatar_height, user_colour
							 FROM ' . USERS_TABLE . '
							 WHERE user_type IN (' . USER_NORMAL . ', ' . USER_FOUNDER . ')
							 AND username_clean ' . $this->db->sql_like_expression($this->db->get_any_char() . $string . $this->db->get_any_char()).' 
							 order by username ASC LIMIT 5';
		             $result = $this->db->sql_query($sql);
					 $return_arr = $row_array = array();
                     while ($row = $this->db->sql_fetchrow($result))
                           {
                            $data[] = array(
            								"name"      => $final_name = str_ireplace($string, $string, $username = $row['username']),		
            								"url"       => get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),							
            								"avatar"    => $this->wall_helper->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 25, 25), 	
											"profesion" => ($row['user_occ'] ? $row['user_occ'] : 'sin definir'), 	
											);
						   }
                     if($data) 
					         {
		                     $return = $data;
			                 }
			         else
					    {
			             $return []= array(
                                           "name"    => $this->user->lang('WALL_SEARCH_NR'),		
                                           "url"     => $this->helper->route('wall_page'),							
                                           "avatar"  => $this->wall_helper->wall_avatar($row['user_avatar'], $row['user_avatar_type'], 25, 25), 	
			                               );	
			             }
		
                     return $return;
   
                   }
				      
 	    public function friendStatus($user_id)
                   {
	    			// What colour is the zebra
                    $sql = "SELECT * FROM " . ZEBRA_TABLE . "
                            WHERE zebra_id IN (".$user_id .",". $this->user->data['user_id'].")
                            AND user_id IN (".$user_id .",". $this->user->data['user_id'].")";
                    $result = $this->db->sql_query($sql);
                    $row = $this->db->sql_fetchrow($result);
                    if ($row['user_id'] == $user_id && $row['zebra_id'] == $this->user->data['user_id'] && $row['approval'] == 1)
					   {
			             $data = array(
                                       "status"  => $this->user->lang('WALL_FRIENDS_ACCEPT_REQ'),		
                                       "icon"    => 'check',								
			                           );	
                       }
                     else
                        {
			             $data = array(
                                       "status"  => ($row['friend'] ? $this->user->lang('WALL_FRIENDS') : ($row['approval'] ? $this->user->lang('WALL_FRIENDS_CANCEL_REQ') : ($row['foe'] ? $this->user->lang('WALL_FRIENDS_REMOVE_BLOCK') : $this->user->lang('WALL_FRIENDS_ADD')))),		
                                       "icon"    => ($row['friend'] ? 'ok' : ($row['approval'] ? 'remove' : ($row['foe'] ? 'ban-circle' : 'plus'))),		
                                       "friends" => $row['friend'] ? true : false,								
			                           );	
	                    }
                        $this->db->sql_freeresult($result);
						
					return $data;	
                     }
   
}


