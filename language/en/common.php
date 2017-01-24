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


if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(

	'WALL_PAGE'			=> 'Wall',
	'WALL_MAIN'			=> 'Main Page',	
	'WALL_NOSET'		=> 'Not set',	
	
	'WALL_ABOUT_BLOCK'	              => 'About me',	
	'WALL_FRIENDS_BLOCK'              => 'Friend request',	
	'WALL_NEW_USERS_BLOCK'            => 'New members',	
	'WALL_LATEST_TOPICS_BLOCK'        => 'Recent forum topics',	
	
		// WALL status
	
	'WALL_STATUS_PH_STATUS'		    	=> 'Share something...',	
	'WALL_STATUS_PH_IMG'	         	=> 'Add a description...',	
	'WALL_STATUS_POST'	             	=> 'Post',	
	'WALL_STATUS_POST_IMG'	         	=> 'Share an image',		
	'WALL_STATUS_POST_STA'	         	=> 'Post status',	
	'WALL_STATUS_DELETE_INFO'        	=> 'Are you sure you want to delete this status?',
	'WALL_COMMENTS_PH'	             	=> 'Add a comment...',			
	'WALL_COMMENTS_POST'             	=> 'Comment',			
	'WALL_COMMENTS_COUNT'             	=> '%1$s Comments',
	'WALL_COMMENTS_HIDE'             	=> 'Hide comments',
	'WALL_COMMENTS_EDITING'	         	=> 'Edit comment...',	
	'WALL_COMMENTS_DELETE_INFO'      	=> 'Are you sure you want to delete this comment?',
	
	
	'WALL_STATUS_SHARE_INFO'	     => '%1$s shared a %2$s to %3$s’s wall.',
	'WALL_STATUS_TYPE'			     => array(
		'1'	     => 'status',
		'2'	     => 'comment',
		'3'	     => 'photo',
		'4'  	 => 'song',
		),	
		
	'WALL_EDIT'	                     	=> 'Edit',		
	'WALL_UPDATE'                    	=> 'Update',		
	'WALL_DELETE'                    	=> 'Delete',
	'WALL_CANCEL'	                 	=> 'Cancel',		
	'WALL_MORE'                      	=> 'Load more',	
	
	// WALL ACP
	
	'ACP_WALL_TITLE'				 	=> 'phpBB Wall',
	'ACP_WALL_SETTINGS_EXPLAIN' 	 	=> 'This is the configuration page for the “phpBB Wall extension”.',
	'ACP_WALL_VERSION'          	 	=> 'Extension version',
	'ACP_WALL_GENERAL'		         	=> 'General settings',
	'ACP_WALL_ENABLED'				 	=> 'Enable phpBB Wall',
	'ACP_WALL_PROFILE_SETTINGS'    	 	=> 'Profile settings',
	'ACP_WALL_PROFILE'		         	=> 'Use the phpBB Wall profile',
	'ACP_WALL_PROFILE_EXPLAIN'  	 	=> 'Replace the core PhpBB profile system.',
	'ACP_WALL_MUSIC_ENABLED'		    => 'Allow users to upload and share music',
	'ACP_WALL_MUSIC_ENABLED_EXPLAIN'    => 'PHP post_max_size must be set higher than 8M, 20M will work great for music upload.',

	'ACP_WALL_STATUS'	             	=> 'Status settings',	 
	'ACP_WALL_STATUS_SMILIES' 	        => 'Allow smilies in status',  
	'ACP_WALL_STATUS_BBCODE' 	        => 'Allow BBcodes in status',  
	'ACP_WALL_STATUS_URL' 	            => 'Allow URLs in status', 	
	
	'ACP_WALL_COMMENTS'	             	=> 'Comments settings',
	'ACP_WALL_CMT_EDIT'              	=> 'Edit Comments', 
	'ACP_WALL_CMT_EDIT_EXPLAIN'      	=> 'Allow users to edit their comments.', 
	'ACP_WALL_CMT_EDIT_TIME'         	=> 'Edit comments time',  
	'ACP_WALL_CMT_EDIT_TIME_EXPLAIN' 	=> 'Maximum comments edit period (seconds).',  
	'ACP_WALL_CMT_SMILIES' 	            => 'Allow smilies in status comments',  
	'ACP_WALL_CMT_BBCODE' 	            => 'Allow BBcodes in status comments',  
	'ACP_WALL_CMT_URL' 	                => 'Allow URLs in status comments', 
	
	'ACP_WALL_BLOCKS'	             	=> 'Blocks settings', 
	'ACP_WALL_BLOCKS_NEW_USERS'      	=> 'Enable the new users block',  
	'ACP_WALL_BLOCKS_NEW_USERS_MAX'  	=> 'Maximum users to display in the new users block',  
	'ACP_WALL_BLOCKS_FRIENDS_REQ'    	=> 'Enable the friends request block',  
	'ACP_WALL_BLOCKS_LATEST_TOPICS'  	=> 'Enable the recent discussions block',  
	'ACP_WALL_BLOCKS_LATEST_TOPICS_MAX' => 'Maximum number of topics to display in the recent discussions block',  
	
	'ACP_WALL_SETTING_SAVED'		 	=> 'Settings have been saved successfully!',


	// WALL tabs
	'WALL_TAB_MAIN'		                => 'Activity',	
	'WALL_TAB_INFO'		              	=> 'Information',	
	'WALL_TAB_FRIENDS'		          	=> 'Friends',	
	'WALL_TAB_PHOTOS'		          	=> 'Photos',	
	'WALL_TAB_MUSIC'		          	=> 'Music',	

	// WALL Likes
	'WALL_LIKE'		               	  => 'Like',
	'WALL_UNLIKE'		              => 'Unlike',	
	'WALL_LIKE_U'	                  => 'You',	
	'WALL_LIKE_AND'		              => ' and ',
	'WALL_LIKE_AND_OTHERS'	          => ' and <strong>%1$s other</strong> ',	
	'WALL_LIKE_THIS'	              => 'like this',		
	'WALL_LIKE_THIS_S'	              => 'likes this',		
	'WALL_LIKE_LIST'	              => 'Likes list',			
	
	
	// WALL notifications
	'NOTIFICATION_TYPE_WALL_COMMENTS' => 'Wall comments',	 
    'NOTIFICATION_TYPE_WALL_STATUS'	  => 'Wall status',	 
    'NOTIFICATION_TYPE_WALL_LIKES'    => 'Wall likes',		
	'NOTIFICATION_WALL_ADD_STATUS'	  => '%1$s added something to your wall "%2$s"',		
	'NOTIFICATION_WALL_ADD_CMT'	      => '%1$s commented your status "%2$s"',
	'NOTIFICATION_WALL_LIKE_STATUS'	  => '%1$s liked your status "%2$s"',
	
	
	
	// WALL Friends system			
	'WALL_FRIENDS'	                  => 'Friends',	
	'WALL_FRIENDS_ADD'		          => 'Add as a friend',
	'WALL_FRIENDS_CANCEL_REQ'	      => 'Cancel friend request',
	'WALL_FRIENDS_CANCEL_REQ_SHORT'	  => 'Remove',	
	'WALL_FRIENDS_ACCEPT_REQ_SHORT'   => 'Accept',		
	'WALL_FRIENDS_ACCEPT_REQ'	      => 'Accept friend request',	
	'WALL_FRIENDS_REMOVE_BLOCK'	      => 'Unblock',	 
	 
	
	/**
	 * WALL TIME AGO
	 */
	'WALL_TIME_AGO'					  => '%1$u %2$s ago',
	'WALL_TIME_FROM_NOW'			  => '%1$u %2$s from now',
	'WALL_TIME_PERIODS'				  => array(
		'SECOND'	 => 'second',
		'SECONDS'	 => 'seconds',
		'MINUTE'	 => 'minute',
		'MINUTES'	 => 'minutes',
		'HOUR'		 => 'hour',
		'HOURS'		 => 'hours',
		'DAY'		 => 'day',
		'DAYS'		 => 'days',
		'WEEK'		 => 'week',
		'WEEKS'		 => 'weeks',
		'MONTH'		 => 'month',
		'MONTHS'	 => 'months',
		'YEAR'		 => 'year',
		'YEARS'		 => 'years',
		'DECADE'	 => 'decade',
		'DECADES'	 => 'decades',
		),
		
	'WALL_SEARCH_NR'			      => 'No results',			
	'UCP_PROFILE_AVATAR'	          => 'Edit my avatar',	
	'WALL_PROFILE_UPDATE_COVER'		  => 'Change cover',
	'WALL_PROFILE_PICK_COVER'         => 'Please select a photo to use it as a cover',	
	'WALL_PROFILE_COVER_DRAG'		  => 'Drag for reposition ...',
	'WALL_PROFILE_NO_COVER'           => 'We are sorry, but you don’t have any photo to use it as a cover. Please upload one first.',
	
	
	/**
	 * WALL Music
	 */	
	'WALL_MUSIC_TITLE'				  => 'Music',	
	'WALL_MUSIC_UPLOAD'		          => 'Upload music',
	'WALL_MUSIC_LIMIT'			      => 'Sorry but you are not allow to upload more than 15 songs!',	
	'WALL_MUSIC_DOWNLOAD'		      => 'Download',	
    'WALL_MUSIC_ALLOWED'			  => 'Allowed files',
    'WALL_MUSIC_ADDED'			      => 'added a new song ',
	'WALL_MUSIC_DELETE'			      => 'Delete',	
	'WALL_MUSIC_RETURN'			      => 'Go back',
	'WALL_MUSIC_PLAY'				  => 'Play',						
	'WALL_MUSIC_JUST'				  => 'Just now',	
	
	
// Music ACP

    'WALL_MUSIC_RECENT'			      => 'Recent music',
    'WALL_MUSIC_SONG'				  => '1 song',
	'WALL_MUSIC_SONGS'				  => 'songs',		
	'WALL_MUSIC_LMUSIC'			      => 'Last songs list on activity page',
	'WALL_MUSIC_STATS'				  => 'Total user song on  profile stats',
	
));


