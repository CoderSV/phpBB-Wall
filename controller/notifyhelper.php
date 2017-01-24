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

use Symfony\Component\DependencyInjection\Container;

/**
* Admin controller
*/
class notifyhelper
{
	/**
	* Constructor
	*
	* @param \phpbb\config\config $config                      Config object
	* @param \phpbb\db\driver\driver $db                       Database object
	* @param \phpbb\request\request $request                   Request object
	* @param \phpbb\template\template $template                Template object
	* @param \phpbb\user $user                                 User object
	* @param Container $phpbb_container
	* @param string $root_path                                 phpBB root path
	* @param string $php_ext                                   phpEx
	* @access public
	*/
	public function __construct($config, $db, $request, $template, $user, $phpbb_container, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->db = $db;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->phpbb_container = $phpbb_container;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	public function test($i)
	{
		echo '</pre>';
		print_r($i);
		echo '</pre>';
	}

	/**
	* Main notification function
	* @param type			Type of notification (add/confirm)
	* @param status_id		Status ID
	* @param wall_id	User to notify
	* @param poster_id	User that trigered the action
	*/
	public function notify($type, $status_id, $status, $wall_id, $poster_id, $lang)
	{
		
		$notification_data = array(
			'status_id'	=> (int) $status_id,
			'status'	=>	substr(preg_replace("(\r\n|\n|\r)", " ", utf8_normalize_nfc($status)),0,40).((strlen($status) > 39)?'...':''),
			'user_id'	=> (int) $wall_id,
			'poster_id'	=>  $poster_id,
			'lang'	    => $lang,
		);

		//$this->test($notification_data);
		$phpbb_notifications = $this->phpbb_container->get('notification_manager');
	if ($notification_data['poster_id'] != $notification_data['user_id'])
   {
	switch ($type)
			{
				case 'add_status':
					$phpbb_notifications->add_notifications('notification.type.wall_status', $notification_data);
				break;
				case 'remove_status':
					$phpbb_notifications->delete_notifications('notification.type.wall_status', $notification_data);
				break;
				case 'add_cmt':
					$phpbb_notifications->add_notifications('notification.type.wall_comments', $notification_data);
				break;
				case 'remove_cmt':
					$phpbb_notifications->delete_notifications('notification.type.wall_comments', $notification_data);
				break;				
				case 'add_like':
					$phpbb_notifications->add_notifications('notification.type.wall_likes', $notification_data);
				break;
				case 'remove_like':
					$phpbb_notifications->delete_notifications('notification.type.wall_likes', $notification_data);
				break;
			}
		
	}
 }
}