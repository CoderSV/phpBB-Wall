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

namespace msaulohenrique\wall\acp;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$user->add_lang('acp/common');
		$this->tpl_name = 'wall_body';
		$this->page_title = $user->lang('ACP_WALL_TITLE');
		add_form_key('msaulohenrique/wall');
		
		switch ($mode)
		{
			case 'comments':
			
		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('msaulohenrique/wall'))
			{
				trigger_error('FORM_INVALID');
			}

			$config->set('wall_cmt_edit', $request->variable('wall_cmt_edit', 0));
			$config->set('wall_cmt_edit_time', $request->variable('wall_cmt_edit_time', 0));
			$config->set('wall_cmt_smilies', $request->variable('wall_cmt_smilies', 0));
			$config->set('wall_cmt_bbcode', $request->variable('wall_cmt_bbcode', 0));
			$config->set('wall_cmt_url', $request->variable('wall_cmt_url', 0));			
			trigger_error($user->lang('ACP_WALL_SETTING_SAVED') . adm_back_link($this->u_action));
		}
		    $template->assign_vars(array(
			'WALL_CMT'				    => true,
			'WALL_CMT_EDIT'				=> $config['wall_cmt_edit'],
			'WALL_CMT_EDIT_TIME'		=> $config['wall_cmt_edit_time'],
			'WALL_CMT_SMILIES'			=> $config['wall_cmt_smilies'],
			'WALL_CMT_BBCODE'			=> $config['wall_cmt_bbcode'],
			'WALL_CMT_URL'				=> $config['wall_cmt_url'],
		));
			break;	
					
			case 'blocks':
			
		  if ($request->is_set_post('submit'))
		  {
			if (!check_form_key('msaulohenrique/wall'))
			{
				trigger_error('FORM_INVALID');
			}

			$config->set('wall_blocks_new_users', $request->variable('wall_blocks_new_users', 0));
			$config->set('wall_blocks_new_users_max', $request->variable('wall_blocks_new_users_max', 0));
			$config->set('wall_blocks_friends_req', $request->variable('wall_blocks_friends_req', 0));
			$config->set('wall_blocks_latest_topics', $request->variable('wall_blocks_latest_topics', 0));
			$config->set('wall_blocks_latest_topics_max', $request->variable('wall_blocks_latest_topics_max', 0));			
			trigger_error($user->lang('ACP_WALL_SETTING_SAVED') . adm_back_link($this->u_action));
		  }
			$template->assign_vars(array(
			'WALL_BLOCKS'		        	=> true,
			'WALL_BLOCKS_NEW_USERS'			=> $config['wall_blocks_new_users'],
			'WALL_BLOCKS_NEW_USERS_MAX'		=> $config['wall_blocks_new_users_max'],
			'WALL_BLOCKS_FRIENDS_REQ'    	=> $config['wall_blocks_friends_req'],
			'WALL_BLOCKS_LATEST_TOPICS'	    => $config['wall_blocks_latest_topics'],
			'WALL_BLOCKS_LATEST_TOPICS_MAX'	=> $config['wall_blocks_latest_topics_max'],
		));
            break;
			
			default:
			
			if ($request->is_set_post('submit'))
		  {
			if (!check_form_key('msaulohenrique/wall'))
			{
				trigger_error('FORM_INVALID');
			}

			$config->set('wall_enabled', $request->variable('wall_enabled', 0));
			$config->set('wall_profile', $request->variable('wall_profile', 0));
			$config->set('wall_friends', $request->variable('wall_friends', 0));
			$config->set('wall_music_enabled', $request->variable('wall_music_enabled', 0));
			$config->set('wall_status_smilies', $request->variable('wall_status_smilies', 0));
			$config->set('wall_status_bbcode', $request->variable('wall_status_bbcode', 0));
			$config->set('wall_status_url', $request->variable('wall_status_url', 0));			
			trigger_error($user->lang('ACP_WALL_SETTING_SAVED') . adm_back_link($this->u_action));
		  }
		    $template->assign_vars(array(
			'WALL_MAIN'				    => true,
		));
			break;
		}

		$template->assign_vars(array(
			'U_ACTION'		    		=> $this->u_action,
			'WALL_ENABLED'				=> $config['wall_enabled'],
			'WALL_VERSION'				=> $config['wall_version'],
			'WALL_PROFILE'				=> $config['wall_profile'],
			'WALL_FRIENDS'				=> $config['wall_friends'],
			'WALL_MUSIC_ENABLED'	    => $config['wall_music_enabled'],
			'WALL_STATUS_SMILIES'		=> $config['wall_status_smilies'],
			'WALL_STATUS_BBCODE'		=> $config['wall_status_bbcode'],
			'WALL_STATUS_URL'			=> $config['wall_status_url'],
		));
	}
}
