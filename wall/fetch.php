<?php
/**
*
* @phpBB Wall extension for the phpBB Forum Software package.
* @author Saulo Henrique
* @version 1.0.0
* @copyright (c) 2017 http://www.suportephpbb.com.br
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';

include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);
		
	
        $mode   = request_var('mode', '');
		$fetch_url   = request_var('url', '');
   
 if ($mode) {		 
		 global $db, $user, $config, $phpbb_root_path, $phpEx;	
		  
		 include_once($phpbb_root_path . 'wall/simple_html_dom.inc.' . $phpEx);			
		//get URL content
		$get_content = file_get_html($fetch_url); 
		
		//Get Page Title 
		foreach($get_content->find('title') as $element) 
		{
			$page_title = $element->plaintext;
		}
		
		//Get Body Text
		foreach($get_content->find('body') as $element) 
		{
			$page_body = trim($element->plaintext);
			$pos=strpos($page_body, ' ', 200); //Find the numeric position to substract
			$page_body = substr($page_body,0,$pos ); //shorten text to 200 chars
		}
	
		$image_urls = array();
		
		//get all images URLs in the content
		foreach($get_content->find('img') as $element) 
		{
				/* check image URL is valid and name isn't blank.gif/blank.png etc..
				you can also use other methods to check if image really exist */
				if(!preg_match('/blank.(.*)/i', $element->src) && filter_var($element->src, FILTER_VALIDATE_URL))
				{
					$image_urls[] =  $element->src;
				}
		}
		
		//prepare for JSON 
		$return = array(
		'title'   => $page_title, 
		'images'  => $image_urls, 
		'content' => $page_body
		);
		
			header('Content-type: application/json');
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			die(json_encode($return));
   
       }
		
		
		

   
?>