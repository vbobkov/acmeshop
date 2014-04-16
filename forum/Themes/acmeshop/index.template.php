<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = false;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// <a href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? $context['forum_name'] : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name'] . '" />', '</a>
	// <a href="', $scripturl, '"><img id="smflogo" src="' . $settings['images_url'] . '/acmelectronics-forum-logo.png" alt="Simple Machines Forum" title="Simple Machines Forum" /></a>
	echo !empty($settings['forum_width']) ? '
<script type="text/javascript" src="/forum/Themes/acmeshop/scripts/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery.noConflict();
		jQuery(document).ready(function(){var b={combatWindow:\'#top_section .forumtitle\',zIndexPlayer:1337,lightning:function(j,d,c,l,h,g,i){var m="electrodes";var e="#"+m;var k=16;if(i!=-1){var f=this.getAngle(j,d,c,l);if(this.getDistance(j,d,c,l)>(this.screenWidth+this.screenHeight)*0.25){c=j+(Math.cos(f)*i);l=d+(Math.sin(f)*i);c=c-(jQuery(e).outerWidth()*0.5);l=l-(jQuery(e).outerHeight()*0.5)}}this.createFlareCircle(m+"-origin",1,1,1,j,d,this.zIndexPlayer+10,6,6,1000,50,1,0,"255,255,255","255,255,255",1,0,"255,255,255","255,255,255");this.createFlareCircle(m+"-destination",1,1,1,c,l,this.zIndexPlayer+10,6,6,1000,50,1,0,"255,255,255","255,255,255",1,0,"255,255,255","255,255,255");this.createLineXY(m+"-lightning-corona",16,j-16,d,c+16,l,this.zIndexPlayer+10,16,16,500,25,0.2,0.1);this.createLightning(m+"-lightning",3,10,1,j,d,c,l,this.zIndexPlayer+10,0,0,250,25,1,0.5,"255,255,255","128,128,255","255,255,255","128,128,255")},addOrShowExistingDiv:function(e,f,d,c){div="#"+e;if(jQuery(div).length<1){jQuery(this.combatWindow).append("<div id=\'"+e+"\' class=\'"+f+"\' style=\'"+d+"\' "+c+"></div>")}else{jQuery(div).css("visibility","visible")}},createLightning:function(q,s,p,n,v,f,t,e,d,r,y,c,h,j,o,g,i){var x;var u;var m;var k;for(var w=0;w<s;w++){x=v;u=f;for(var l=0;l<p;l++){if(l<p-1){m=x+((t-v)/p);k=u+((e-f)/p);m+=this.rand(0,m*0.1*((Math.round(Math.random())*4)-2));k+=this.rand(0,k*0.1*((Math.round(Math.random())*4)-2))}else{m=t;k=e}this.createLineXY(q+w+l,n,x,u,m,k,d,r,y,c,h,j,o,g,i,g,i);x=m;u=k}}},createFlareCircle:function(z,i,w,t,f,c,e,A,C,d,n,s,x,k,q,B,u,y,v,l,o,p,m,r,h,D,g){var j="#"+z;var e=this.parseUndefined(e,1);this.addOrShowExistingDiv(z,"flare-circle","position:absolute;z-index:"+e);jQuery(j).css("width",w+"px");jQuery(j).css("height",t+"px");jQuery(j).css("left",f+"px");jQuery(j).css("top",c+"px");jQuery(j).css("background-color","rgba("+k+","+s+")");this.setCSS3Property("border-radius",j,"50%");this.setCSS3Property("box-shadow",j,"0px 0px "+A+"px "+C+"px rgba("+y+","+B+")");var d=this.parseUndefined(d,-1);if(d!=-1){this.animateTransitionRGBA(j,A,C,d,n,s,x,k,q,B,u,y,v)}var l=this.parseUndefined(l,-1);if(l!=-1){this.animateScale(j,l,o,p,m,r,h,D,g)}},createLineXY:function(m,f,g,q,e,p,r,i,n,j,k,d,o,h,l,c,s){this.createLine(m,f,g,q,this.getDistance(g,q,e,p),180*(this.getAngle(g,q,e,p)/Math.PI),r,i,n,j,k,d,o,h,l)},createLine:function(n,d,e,q,f,i,r,h,o,j,k,c,p,g,l){var j=this.parseUndefined(j,-1);var m="#"+n;var r=this.parseUndefined(r,1);this.addOrShowExistingDiv(n,"line","position:absolute;");jQuery(m).css("z-index",r);jQuery(m).css("height",d+"px");jQuery(m).css("width",f+"px");jQuery(m).css("left",e+"px");jQuery(m).css("top",(q-(d*0.5))+"px");jQuery(m).css("background-color","rgba("+g+","+c+")");this.setCSS3Property("transform-origin",m,"0% 50%");this.setRotation(m,i);if(j!=-1){this.animateTransitionRGBA(m,h,o,j,k,c,p,g,l,c,p,g,l)}},animateTransitionRGBA:function(n,u,C,c,k,o,r,h,m,x,p,t,q){var u=this.parseUndefined(u,0);var C=this.parseUndefined(C,0);var k=this.parseUndefined(k,50);var o=this.parseUndefined(o,1);var r=this.parseUndefined(r,0);var h=this.parseUndefined(h,"128,128,255");var m=this.parseUndefined(m,"128,128,255");var x=this.parseUndefined(x,1);var p=this.parseUndefined(p,0);var t=this.parseUndefined(t,"128,128,255");var q=this.parseUndefined(q,"128,128,255");var j=c/k;var z=o;var s=(o-r)/j;var i=x;var l=(x-p)/j;var w=false;if(h!=m){var w=true;var v=h.split(",");var y=m.split(",");var f=new Array();jQuery.each(v,function(D,E){f.push((v[D]-y[D])/j)})}var g=false;if(t!=q&&(u>0||C>0)){var g=true;var B=t.split(",");var d=q.split(",");var A=new Array();jQuery.each(B,function(D,E){A.push((B[D]-d[D])/j)})}var e=setInterval(function(){if(w){jQuery(n).css("background-color","rgba("+v.join(",")+","+z+")");jQuery.each(v,function(D,E){v[D]=parseInt(v[D]-f[D])})}if(g){jQuery(n).css("box-shadow","0px 0px "+u+"px "+C+"px rgba("+B.join(",")+","+i+")");jQuery.each(v,function(D,E){B[D]=parseInt(B[D]-A[D])})}else{jQuery(n).css("background-color","rgba("+h+","+z+")");if(u>0||C>0){jQuery(n).css("box-shadow","0px 0px "+u+"px "+C+"px rgba("+t+","+i+")")}}z-=s;i-=l;if(z<r||i<p){jQuery(n).css("visibility","hidden");clearInterval(e)}},k)},animateScale:function(d,e,r,k,f,s,i,q,n){var m=e/r;var c=k;var o=f;var j=s;var p=(k-i)/m;var l=(f-q)/m;var h=(s-n)/m;var g=setInterval(function(){c-=p;o-=l;j-=h;jQuery(d).css("width",o);jQuery(d).css("height",j);jQuery(d).css("left",parseFloat(jQuery(d).css("left"))+(l*0.5));jQuery(d).css("top",parseFloat(jQuery(d).css("top"))+(h*0.5));if((k>i&&c<i)||(k<=i&&c>i)){jQuery(d).css("visibility","hidden");clearInterval(g)}},r)},rand:function(d,c){return Math.random()*(c-d)+d},getAngle:function(d,f,c,e){return Math.atan2(e-f,c-d)},polarDegsToXY:function(d,c){return this.polarRadsToXY(d*this.deg2rad,c)},polarRadsToXY:function(d,c){return[c*Math.cos(d),c*Math.sin(d)]},getDistance:function(d,f,c,e){return Math.sqrt(Math.pow(c-d,2)+Math.pow(e-f,2))},setCSS3Property:function(c,e,d){jQuery(e).css("-webkit-"+c,d);jQuery(e).css("-moz-"+c,d);jQuery(e).css("-ms-"+c,d);jQuery(e).css(c,d)},setRotation:function(d,c){this.setCSS3Property("transform",d,"rotate("+c+"deg)")},parseUndefined:function(d,c){if(typeof(d)==="undefined"){return c}return d}};var a=setInterval(function(){b.lightning(jQuery("#electrode1").position().left+8,jQuery("#electrode1").position().top+8,jQuery("#electrode2").position().left+4,jQuery("#electrode2").position().top-36,"rgb(0,0,255)",0.5,10)},5000)});
	});
</script>
<div id="wrapper" style="width: ' . $settings['forum_width'] . '">' : '', '
	<div id="header"><div class="frame">
		<div id="top_section">
			<h1 class="forumtitle">
				<div id="electrode1"></div>
				<a href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? $context['forum_name'] : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name'] . '" />', '</a>
				<div id="electrode2"></div>
			</h1>';

	// the upshrink image, right-floated
	echo '
			<img id="upshrink" src="', $settings['images_url'], '/upshrink.png" alt="*" title="', $txt['upshrink_description'], '" style="display: none;" />';
	echo '
			',
			//empty($settings['site_slogan']) ? '<img id="smflogo" src="' . $settings['images_url'] . '/smflogo.png" alt="Simple Machines Forum" title="Simple Machines Forum" />' : '<div id="siteslogan" class="floatright">' . $settings['site_slogan'] . '</div>',
			'
		<h1 class="forumtitle" style="float:right;margin-left:0em 0em 0em 2em">
			<a target="_blank" href="/forum/index.php?topic=10.0">Rules</a>
		</h1>
		</div>
		<div id="upper_section" class="middletext"', empty($options['collapse_header']) ? '' : ' style="display: none;"', '>
			<div class="user">';

	// If the user is logged in, display stuff like their name, new messages, etc.
	if ($context['user']['is_logged'])
	{
		if (!empty($context['user']['avatar']))
			echo '
				<p class="avatar">', $context['user']['avatar']['image'], '</p>';
		echo '
				<ul class="reset">
					<li class="greeting">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></li>
					<li><a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a></li>
					<li><a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a></li>';

		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
					<li class="notice">', $txt['maintain_mode_on'], '</li>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
					<li>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
					<li><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></li>';

		echo '
					<li>', $context['current_time'], '</li>
				</ul>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	elseif (!empty($context['show_login_bar']))
	{
		echo '
				<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
				<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<div class="info">', sprintf($txt['welcome_guest'], $txt['guest_title']), '</div>
					<input type="text" name="user" size="10" class="input_text" />
					<input type="password" name="passwrd" size="10" class="input_password" />
					<select name="cookielength">
						<option value="60">', $txt['one_hour'], '</option>
						<option value="1440">', $txt['one_day'], '</option>
						<option value="10080">', $txt['one_week'], '</option>
						<option value="43200">', $txt['one_month'], '</option>
						<option value="-1" selected="selected">', $txt['forever'], '</option>
					</select>
					<input type="submit" value="', $txt['login'], '" class="button_submit" /><br />
					<div class="info">', $txt['quick_login_dec'], '</div>';

		if (!empty($modSettings['enableOpenID']))
			echo '
					<br /><input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />';

		echo '
					<input type="hidden" name="hash_passwrd" value="" />
				</form>';
	}

	echo '
			</div>
			<div class="news normaltext">
				<form id="search_form" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
					<input type="text" name="search" value="" class="input_text" />&nbsp;
					<input type="submit" name="submit" value="', $txt['search'], '" class="button_submit" />
					<input type="hidden" name="advanced" value="0" />';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
	// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
					<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

	echo '</form>';

	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '
				<h2>', $txt['news'], ': </h2>
				<p>', $context['random_news_line'], '</p>';

	echo '
			</div>
		</div>
		<br class="clear" />';

	// Define the upper_section toggle in JavaScript.
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			var oMainHeaderToggle = new smc_Toggle({
				bToggleEnabled: true,
				bCurrentlyCollapsed: ', empty($options['collapse_header']) ? 'false' : 'true', ',
				aSwappableContainers: [
					\'upper_section\'
				],
				aSwapImages: [
					{
						sId: \'upshrink\',
						srcExpanded: smf_images_url + \'/upshrink.png\',
						altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
						srcCollapsed: smf_images_url + \'/upshrink2.png\',
						altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
					}
				],
				oThemeOptions: {
					bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
					sOptionName: \'collapse_header\',
					sSessionVar: ', JavaScriptEscape($context['session_var']), ',
					sSessionId: ', JavaScriptEscape($context['session_id']), '
				},
				oCookieOptions: {
					bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
					sCookieName: \'upshrink\'
				}
			});
		// ]]></script>';

	// Show the menu here, according to the menu sub template.
	template_menu();

	echo '
		<br class="clear" />
	</div></div>';

	// The main content should go here.
	echo '
	<div id="content_section"><div class="frame">
		<div id="main_content_section">';

	// Custom banners and shoutboxes should be placed here, before the linktree.

	// Show the navigation tree.
	theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
		</div>
	</div></div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<div id="footer_section"><div class="frame">
		<ul class="reset">
			<li class="copyright">', theme_copyright(), '</li>
			<li><a id="button_xhtml" href="http://validator.w3.org/check?uri=referer" target="_blank" class="new_win" title="', $txt['valid_xhtml'], '"><span>', $txt['xhtml'], '</span></a></li>
			', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? '<li><a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss" class="new_win"><span>' . $txt['rss'] . '</span></a></li>' : '', '
			<li class="last"><a id="button_wap2" href="', $scripturl , '?wap2" class="new_win"><span>', $txt['wap2'], '</span></a></li>
		</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

	echo '
	</div></div>', !empty($settings['forum_width']) ? '
</div>' : '';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' &#187;';

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<div id="main_menu">
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
									</a>
								</li>';

					echo '
							</ul>';
				}

				echo '
						</li>';
			}
				echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>
		</div>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>