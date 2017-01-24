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

namespace msaulohenrique\wall\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.page_header'						=> 'add_page_links',
			'core.modify_username_string'	    	=> 'username_string',	
			'core.memberlist_view_profile'	    	=> 'wall_profile',	
		);
	}

	/* @var \phpbb\config\config */
	protected $config;

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
	* Constructor
	*
	* @param \phpbb\config\config		$config
	* @param \phpbb\controller\helper	$helper
	* @param \phpbb\template\template	$template
	* @param \phpbb\user				$user
	* @param \phpbb\db\driver\driver_interface			$db		db object
	*/
	public function __construct($config, $helper, $template, $user, $db, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
	    $this->root_path = $root_path;	
		$this->php_ext = $php_ext;	
	}
	
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'msaulohenrique/wall',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function add_page_links($event)
	{
		$this->template->assign_vars(array(
			'WALL_PAGE'	     => $this->helper->route('wall_page'),
			'S_WALL_ENABLED' => $this->config['wall_enabled'] ? true : false,
		));
	}
	
		public function username_string($event)
	{
	
		if ($this->config['wall_enabled'] & $this->config['wall_profile']){
			
  	     // If enable_mod_rewrite is false we need to replace the current front-end by app.php, otherwise we need to remove it.
         $base_url = $this->config['enable_mod_rewrite'] ? 'wall/' : 'app.' . $this->php_ext . '/wall/';

         $string =  $event['username_string'];
		 $match_profile =  '/memberlist\.' . $this->php_ext . '\?(mode=viewprofile)?(&amp;|&)?(un?=[^&"]{1,})([^"\']*?)/si';

		  if (preg_match($match_profile, $string)){
		     $string = preg_replace($match_profile, $base_url.$event['username'], $string, 1);
		     $event['username_string']  = $string;	
		     }
	     }
		else{
		// We have to make sure the profile URL uses the forum path.
		   $string =  $event['username_string'];
		   preg_match('/(href=\")(.+?)(memberlist)/', $string, $matches);
		 
		   if ($matches){		
		      $event['username_string'] = preg_replace('#('.$matches[2].')#', generate_board_url(). '/', $string, 1);
		   }
		}
      
	}
	
		public function wall_profile($event)
	{
		$user_id = request_var('u', ANONYMOUS);
			    			// What colour is the zebra
                    $sql = "SELECT * FROM " . ZEBRA_TABLE . "
                            WHERE zebra_id IN (".$user_id .",". $this->user->data['user_id'].")
                            AND user_id IN (".$user_id .",". $this->user->data['user_id'].")";
                    $result = $this->db->sql_query($sql);
                    $row = $this->db->sql_fetchrow($result);
					$friends= $row['friend'] ? true : false;
					
		$this->template->assign_vars(array(
			'WALL_PATH'           => $this->helper->route('wall_page'),	
			'WALL_ID'             => $user_id,
	        'S_OWN_PROFILE'		  => ($user_id === (int) $this->user->data['user_id']) ? true : false,
			'WALL_IS_FRIEND'	  => ($friends || $user_id === (int) $this->user->data['user_id']) ? true : false,
			'S_ON_PHPBB_PROFILE'  => true,		
			
            /*	Music tab	*/
    	    'WALL_MUSIC_ENABLED'  => ($this->config['wall_music_enabled']) ? true : false,
            'M_MUSIC_USER' 	      => $user_id,				
		));
	}
	
}
