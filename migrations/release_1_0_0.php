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


namespace msaulohenrique\wall\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
    public function effectively_installed()
	{
		return isset($this->config['wall_version']) && version_compare($this->config['wall_version'], '1.0.0', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\alpha2');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('wall_enabled', 1)),
			array('config.add', array('wall_version', '1.0.0')),
			array('config.add', array('wall_profile', 0)),
			array('config.add', array('wall_friends', 1)),
			array('config.add', array('wall_status_smilies', 1)),
			array('config.add', array('wall_status_bbcode', 0)),
			array('config.add', array('wall_status_url', 1)),
			array('config.add', array('wall_cmt_edit', 1)),
			array('config.add', array('wall_cmt_edit_time', '60')),
			array('config.add', array('wall_cmt_smilies', 1)),
			array('config.add', array('wall_cmt_bbcode', 0)),
			array('config.add', array('wall_cmt_url', 1)),
			array('config.add', array('wall_blocks_new_users', 1)),
			array('config.add', array('wall_blocks_new_users_max', '4')),
			array('config.add', array('wall_blocks_friends_req', 1)),
			array('config.add', array('wall_blocks_latest_topics', 1)),	
			array('config.add', array('wall_blocks_latest_topics_max', '10')),	
			
			array('config.add', array('wall_music_enabled', 0)),							

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_WALL_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_WALL_TITLE',
				array(
					'module_basename'	=> '\msaulohenrique\wall\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_WALL_TITLE',
				array(
					'module_basename'	=> '\msaulohenrique\wall\acp\main_module',
					'modes'				=> array('comments'),
				),
			)),
			array('module.add', array(
				'acp',
				'ACP_WALL_TITLE',
				array(
					'module_basename'	=> '\msaulohenrique\wall\acp\main_module',
					'modes'			    => array('blocks'),
				),
			)),
		);
	}
	
	
	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'wall_comments'		=> array(
					'COLUMNS'		=> array(
						'comment_id'	   => array('UINT', NULL, 'auto_increment'),
                        'comment'          => array('TEXT', ''),
                        'status_id'        => array('UINT', 0),
                        'wall_id'          => array('UINT', 0),
                        'poster_id'        => array('UINT', 0),
                        'time'             => array('UINT:11', 0),
                        'bbcode_bitfield'  => array('VCHAR:255', ''),
                        'bbcode_uid'	   => array('VCHAR:8', '')
					),
					'PRIMARY_KEY'    => 'comment_id',
				),
				$this->table_prefix . 'wall_likes'		=> array(
					'COLUMNS'		=> array(
						'like_id'    	   => array('UINT', NULL, 'auto_increment'),
                        'item_id'          => array('UINT', 0),
                        'item_type'        => array('UINT', 0),
                        'user_id'          => array('UINT', 0),
                        'like_date'        => array('UINT:11', 0)
					),
					'PRIMARY_KEY'    => 'like_id',
				),				
				$this->table_prefix . 'wall_music'		=> array(
					'COLUMNS'		=> array(
						'm_id'      	   => array('UINT', NULL, 'auto_increment'),
                        'm_poster'         => array('UINT', 0),
                        'm_time'           => array('UINT:11', 0),
                        'm_name'           => array('VCHAR:255', ''),
                        'm_path'           => array('VCHAR:255', '')
					),
					'PRIMARY_KEY'    => 'm_id',
				),
				$this->table_prefix . 'wall_photos'		=> array(
					'COLUMNS'		=> array(
						'photo_id'	       => array('UINT', NULL, 'auto_increment'),
                        'poster_id'        => array('UINT', 0),
                        'image_time'       => array('UINT:11', 0),
                        'wall_id'          => array('UINT', 0),
                        'status_id'        => array('UINT', 0),
                        'photo_path'       => array('TEXT', ''),
                        'cover'      	   => array('TEXT', NULL),
                        'cover_top'        => array('TEXT', NULL)
					),
					'PRIMARY_KEY'    => 'photo_id',
				),
				$this->table_prefix . 'wall_status'		=> array(
					'COLUMNS'		=> array(
						'status_id'	       => array('UINT', NULL, 'auto_increment'),
                        'status'           => array('TEXT', ''),
                        'wall_id'          => array('UINT', 0),
                        'poster_id'        => array('UINT', 0),
                        'time'             => array('UINT:11', 0),
                        'bbcode_bitfield'  => array('VCHAR:255', ''),
                        'bbcode_uid'	   => array('VCHAR:8', ''),
                        'status_type'      => array('VCHAR:255', ''),
                        'status_extra'	   => array('VCHAR:255', NULL)
					),
					'PRIMARY_KEY'    => 'status_id',
				),
			),
			'add_columns'	=> array(
				 $this->table_prefix . 'zebra'          => array(
                    'approval'				    => array('UINT', 0),
            ),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'zebra'			=> array(
					'approval',
				),
			),
			'drop_tables'		=> array(
				$this->table_prefix . 'wall_comments',
				$this->table_prefix . 'wall_likes',
				$this->table_prefix . 'wall_music',
				$this->table_prefix . 'wall_photos',
				$this->table_prefix . 'wall_status'
			),
		);
		
	}
	
	
		public function revert_data()
	{
		return array(
			array('module.remove', array(
				'acp',
				'ACP_WALL_TITLE',
				array(
					'module_basename'	=> '\msaulohenrique\wall\acp\main_module',
				),
			)),
			
            array('custom', array(array(&$this, 'remove_photos'))),
			
		);
		
	}
	
		public function remove_photos()
      {
        global $phpbb_root_path;
		$this->RemoveFolderContent($phpbb_root_path. 'ext/msaulohenrique/wall/images/gallery/');
      }
	  
	function RemoveFolderContent($folder)
	{
		foreach(glob("{$folder}/*") as $file)
		{
			if(is_dir($file))
			{
				$this->RemoveFolderContent($file);
				rmdir($file);
			}
			else {
				unlink($file);
			}
		}
	}	  
}
