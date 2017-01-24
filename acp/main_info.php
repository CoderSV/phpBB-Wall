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

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\msaulohenrique\wall\acp\main_module',
			'title'		=> 'ACP_WALL_TITLE',
			'version'	=> '1.0.0',
			'modes'		=> array(
		    'settings'	=> array('title' => 'ACP_WALL_GENERAL', 'auth' => 'ext_msaulohenrique/wall && acl_a_board', 'cat' => array('ACP_WALL_TITLE')),
			'comments'	=> array('title' => 'ACP_WALL_COMMENTS', 'auth' => 'ext_msaulohenrique/wall && acl_a_board', 'cat' => array('ACP_WALL_TITLE')),
			'blocks'	=> array('title' => 'ACP_WALL_BLOCKS', 'auth' => 'ext_msaulohenrique/wall && acl_a_board', 'cat' => array('ACP_WALL_TITLE')),
			),
		);
	}
}
