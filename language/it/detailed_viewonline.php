<?php
/**
*
* Detailed Viewonline extension for the phpBB Forum Software package.
*
* @copyright (c) 2013 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'READING_THE_TOPIC'			=> 'Sta leggendo l\'argomento <strong>%1$s</strong> in %2$s',
	'READING_THE_TOPIC_PAGE'	=> 'Sta leggendo la pagina %3$s dell\'argomento <strong>%1$s</strong> in %2$s',
	'READING_THE_NEW_POSTS'		=> 'Sta leggendo i nuovi messaggi dell\'argomento <strong>%1$s</strong> in %2$s',
	'READING_THE_POST'			=> 'Sta leggendo il messaggio nell\'argomento <strong>%1$s</strong> in %2$s',
));
