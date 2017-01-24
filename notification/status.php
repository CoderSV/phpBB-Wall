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

namespace msaulohenrique\wall\notification;

/**
*
* @package notifications
*/
class status extends \phpbb\notification\type\base
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/**
	* Notification Type Boardrules Constructor
	*
	* @param \phpbb\user_loader $user_loader
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\cache\driver\driver_interface $cache
	* @param \phpbb\user $user
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\controller\helper $helper
	* @param string $phpbb_root_path
	* @param string $php_ext
	* @param string $notification_types_table
	* @param string $notifications_table
	* @param string $user_notifications_table
	* @return \phpbb\notification\type\base
	*/
	public function __construct($user_loader, $db, $cache, $user, $auth, $config, $helper, $phpbb_root_path, $php_ext, $notification_types_table, $notifications_table, $user_notifications_table)
	{
		$this->user_loader = $user_loader;
		$this->db = $db;
		$this->cache = $cache;
		$this->user = $user;
		$this->auth = $auth;
		$this->config = $config;
		$this->helper = $helper;

		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;

		$this->notification_types_table = $notification_types_table;
		$this->notifications_table = $notifications_table;
		$this->user_notifications_table = $user_notifications_table;
	}

	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'notification.type.wall_status';
	}

	/**
	* Notification option data (for outputting to the user)
	*
	* @var bool|array False if the service should use it's default data
	* 					Array of data (including keys 'id', 'lang', and 'group')
	*/
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_WALL_STATUS',
	);

	/**
	* Is this type available to the current user (defines whether or not it will be shown in the UCP Edit notification options)
	*
	* @return bool True/False whether or not this is available to the user
	*/
	public function is_available()
	{
		return true;
	}

	/**
	* Get the id of the rule
	*
	* @param array $data The data for the updated rules
	*/
	public static function get_item_id($data)
	{
		return $data['status_id'];
	}

	/**
	* Get the id of the parent
	*
	* @param array $data The data for the updated rules
	*/
	public static function get_item_parent_id($data)
	{
		// No parent
		return $data['poster_id'];
	}

	/**
	* Find the users who will receive notifications
	*
	* @param array $data The data for the updated rules
	*
	* @return array
	*/
	public function find_users_for_notification($data, $options = array())
	{

		$users = array();
		$users[$data['user_id']] = array('');

		$this->user_loader->load_users(array_keys($users));
		return $this->check_user_notification_options(array_keys($users), $options);
	}

	/**
	* Users needed to query before this notification can be displayed
	*
	* @return array Array of user_ids
	*/
	public function users_to_query()
	{
		return array();
	}

	/**
	* Get the user's avatar
	*/
	public function get_avatar()
	{
		$users = array($this->get_data('poster_id'));
		$this->user_loader->load_users($users);
		return $this->user_loader->get_avatar($this->get_data('poster_id'));
	}

	/**
	* Get the HTML formatted title of this notification
	*
	* @return string
	*/
	public function get_title()
	{
		$users = array($this->get_data('poster_id'));
		$this->user_loader->load_users($users);
		$username = $this->user_loader->get_username($this->get_data('poster_id'), 'no_profile');
		return $this->user->lang($this->get_data('lang'), $username, $this->get_data('status'));
	}

	/**
	* Get the url to this item
	*
	* @return string URL
	*/
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . ($this->config['enable_mod_rewrite'] ? 'wall/' : 'app.' . $this->php_ext . '/wall#') . $this->get_data('status_id'));
	}

	/**
	* Get email template
	*
	* @return string|bool
	*/
	public function get_email_template()
	{
		return false;
	}

	/**
	* Get email template variables
	*
	* @return array
	*/
	public function get_email_template_variables()
	{
      return array();
	}
	
   /**
   * Get the HTML formatted reference of the notification
   *
   * @return string
   */

	/**
	* Function for preparing the data for insertion in an SQL query
	* (The service handles insertion)
	*
	* @param array $data The data for the updated rules
	* @param array $pre_create_data Data from pre_create_insert_array()
	*
	* @return array Array of data ready to be inserted into the database
	*/
	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('poster_id', $data['poster_id']);
		$this->set_data('status_id', $data['status_id']);
		$this->set_data('status', $data['status']);
		$this->set_data('lang', $data['lang']);

		return parent::create_insert_array($data, $pre_create_data);
	}
	
	   /**
   * Function for preparing the data for insertion in an SQL query
   * (The service handles insertion)
   *
   * @param array $my_notification_data Data from insert_thanks
   * @param array $pre_create_data Data from pre_create_insert_array()
   *
   * @return array Array of data ready to be inserted into the database
   */

	
	
	
}
