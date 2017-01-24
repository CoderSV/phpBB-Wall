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


namespace msaulohenrique\wall;

/**
* Extension class for board announcements
*/

class ext extends \phpbb\extension\base
{
	/**
	* Single enable step that installs any included migrations
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet

				// Enable board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->enable_notifications('notification.type.wall_status');
				$phpbb_notifications->enable_notifications('notification.type.wall_comments');
				$phpbb_notifications->enable_notifications('notification.type.wall_likes');				
				return 'notifications';

			break;

			default:

				// Run parent enable step method
				return parent::enable_step($old_state);

			break;
		}
	}

	/**
	* Single disable step that does nothing
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet

				// Disable board rules notifications
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->disable_notifications('notification.type.wall_status');
				$phpbb_notifications->disable_notifications('notification.type.wall_comments');
				$phpbb_notifications->disable_notifications('notification.type.wall_likes');				
				return 'notifications';

			break;

			default:

				// Run parent disable step method
				return parent::disable_step($old_state);

			break;
		}
	}

	/**
	* Single purge step that reverts any included and installed migrations
	*
	* @param mixed $old_state State returned by previous call of this method
	* @return mixed Returns false after last step, otherwise temporary state
	*/
	function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '': // Empty means nothing has run yet

				/**
				* @todo Remove this try/catch condition once purge_notifications is fixed
				* in the core to work with disabled extensions without fatal errors.
				* https://tracker.phpbb.com/browse/PHPBB3-12435
				*/
				try
				{
					// Purge board rules notifications
					$phpbb_notifications = $this->container->get('notification_manager');
					$phpbb_notifications->purge_notifications('notification.type.wall_status');
					$phpbb_notifications->purge_notifications('notification.type.wall_comments');
					$phpbb_notifications->purge_notifications('notification.type.wall_likes');					
				}
				catch (\phpbb\notification\exception $e)
				{
					// continue
				}

				return 'notifications';

			break;

			default:

				// Run parent purge step method
				return parent::purge_step($old_state);

			break;
		}
	}
}
