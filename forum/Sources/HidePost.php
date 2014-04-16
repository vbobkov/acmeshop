<?php
/******************************************************************************
* HidePost.php                                                                *
*******************************************************************************
* SMF: Simple Machines Forum - MOD                                            *
*                                                                             *
* =========================================================================== *
* Software Version:           SMF 1.0.5 - MOD                                 *
* Software by:                Xiaoqing Zhou                                   *
* Copyright 2005 by:          Xiaoqing Zhou                                   *
* Contact:                    leaf88@gmail.com                                *
* URL:                        http://www.anetcity.com                         *
*******************************************************************************
* This mod is free software. You may redistribute it and/or modify it under   *
* the terms of GNU General Public License as published by Free Software       *
* Foundation; either version 2 of the License, or (at your option) any later  *
* version.                                                                    *
*                                                                             *
* This program is distributed in the hope that it is and will be useful,      *
* but WITHOUT ANY WARRANTIES; without even any implied warranty of            *
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                        *
*                                                                             *
* All SMF copyrights are still in effect. Anything not mine is theirs. Enjoy! * 
******************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');

// Check whether the user is in a special group.
function checkUserIsInSpecialGroup()
{
  global $user_info, $board_info, $context;
  
	// Find if a user is in special gorups.
	$context['user_special'] = 0;
	if ($user_info['is_admin'])
	    $context['user_special'] = 1;
	elseif (!$user_info['is_guest']) 
	{
		// Global moderators
		if (in_array(2, $user_info['groups'])) {
		      $context['user_special'] = 1;
		      return;
		}
    
		// Moderators
		foreach ($board_info['moderators'] as $mod)
 		  	if ($mod['id'] == $user_info['id']) {
			        $context['user_special'] = 1;
			        return;
			}
	}
	else $context['user_special'] = 0; //Anyway you are not a special group member!

	return $context['user_special'];
}

// Check whether the user has replied to the current topic.
function checkUserRepliedToTopic($topic)
{
  global $user_info, $board_info, $context, $db_prefix, $smcFunc;
  
	// Find if there a post from you in this thread!!!
	if ($user_info['is_admin'])
	    $context['user_post_avaible'] = 1;
	elseif (!$user_info['is_guest']) 
	{
		// Skip check if no topic, and allow global moderators.
		if (empty($topic) || in_array(2, $user_info['groups'])) {
		      $context['user_post_avaible'] = 1;
		      return $context['user_post_avaible'];
		}
   
		// Allow moderators to see the topic.
		foreach ($board_info['moderators'] as $mod)
 		  	if ($mod['id'] == $user_info['id']) {
			        $context['user_post_avaible'] = 1;
			        return $context['user_post_avaible'];
			}
		$request = $smcFunc['db_query']('',"
			SELECT id_msg
			FROM {db_prefix}messages
			WHERE id_topic = {int:current_topic} AND id_member = {int:current_member}
			LIMIT 1",
			array(
				'current_topic' => $topic,
				'current_member' => $user_info['id'],
			)			
		);

		if ($smcFunc['db_num_rows']($request)) $context['user_post_avaible'] = 1;
		else $context['user_post_avaible'] = 0;
		$smcFunc['db_free_result']($request);
	}
	else $context['user_post_avaible'] = 0; //Anyway no way you should see it!

	return $context['user_post_avaible'];
}

// Check for hidden posts. Show hidden message if needed.
function getHiddenMessage($disable_hidden_msg_color = 0)
{
	global $modSettings, $context, $user_info, $txt;	

	$message = $context['current_message'];

	// Did this user post this message?
	$user_own = isset($message['id_member']) && $user_info['id'] == $message['id_member'] && !$user_info['is_guest'];

	// Some contexts for hiding posts ... ;)
	$context['can_view_post'] = $user_own || allowedTo('view_hidden_post', array($message['id_board']));
	$message['hiddenOption'] = (int) $message['hiddenOption'];
	$message['hiddenValue'] = (int) $message['hiddenValue'];

	// Hiding posts ? ..... Haha....Check here.....
	if (!empty($modSettings['allow_hiddenPost']) && !empty($message['hiddenOption']) && isset($message['hiddenValue']))
	{	
		$hidden_message = '';
		// Check each hiding criteria..... ;)
		if ($message['hiddenOption'] == 1)
		{
			// Hide by login ....;;;;
			$hidden_message = $txt['hide_login_msg'];
			$context['can_view_post'] |= !$user_info['is_guest'];
		}
		elseif ($message['hiddenOption'] == 2)
		{
			 // Hide by reply ....... ;
			$hidden_message = $txt['hide_reply_msg'];
			$context['can_view_post'] |= checkUserRepliedToTopic($message['id_topic']); 
		}		
		elseif ($message['hiddenOption'] == 3) 
		{
			// Hide by Karma ..... ;;;
			$karma = isset($user_info['karma']) ? ($user_info['karma']['good'] - $user_info['karma']['bad']) : 0;
			$hidden_message = sprintf($txt['hide_karma_msg'], $message['hiddenValue'], $karma);
			checkUserIsInSpecialGroup();
			$context['can_view_post'] |= (checkUserIsInSpecialGroup()) || ($karma >= $message['hiddenValue']);
		}		
		elseif ($message['hiddenOption'] == 4)
		{
			// Hide by post number .....;;
			$posts = isset($user_info['posts']) ? $user_info['posts'] : 0;
			$hidden_message = sprintf($txt['hide_posts_msg'], $message['hiddenValue'], $posts);
			$context['can_view_post'] |= (checkUserIsInSpecialGroup()) || ($posts >= $message['hiddenValue']);
		}
		elseif ($message['hiddenOption'] == 5)
		{
			// Hide by moderator .....;;
			$hidden_message = $txt['hide_mod_msg'];
			$context['can_view_post'] |= checkUserIsInSpecialGroup();
		}
		else
		{
			// Normal posts. Anyone can see it. ;)
			$context['can_view_post'] = 1;
		}

		if ($hidden_message != '') {
			// Get final hiding message here.... ;;;;
			if (isset($message['hiddenInfo']) && !empty($message['hiddenInfo']))
				$hidden_message .= " (" . $txt['hide_info'] . ": " . $message['hiddenInfo'] . ")";
			$show_hidden_msg = !empty($modSettings['show_hiddenMessage']) || allowedTo('view_hidden_msg');
			$show_hidden_msg_in_color = empty($disable_hidden_msg_color) && !empty($modSettings['show_hiddenColor']);
			$hidden_message = $show_hidden_msg_in_color ? ('[color=' . $modSettings['show_hiddenColor'] . ']' . $hidden_message . '[/color]') : $hidden_message;
			$message['body'] = !empty($context['can_view_post']) ? (empty($show_hidden_msg) ? $message['body'] : $hidden_message . '<br /><br />' . $message['body']) : $hidden_message;
		}
		else {
			// Normal posts!!!.Anyone can see it. ;)
			$context['can_view_post'] = 1;
		}
	}
	else
	{
		// Normal posts!!! Of course, anyone can see it.
		$context['can_view_post'] = 1;
	}

	/* Vic B.
	* 1337 h4x
	*/
	if(!$context['can_view_post']) {
		if(strpos($context['current_action'], 'search') !== false) {
			return '';
		}
		else {
			header('Location: /forum');
		}
	}

	return $message['body'];
}

?>