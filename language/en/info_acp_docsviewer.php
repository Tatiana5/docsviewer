<?php
/**
 *
 * @package       docsviewer
 * @copyright (c) 2022 Татьяна5
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'ACP_DOCSVIEWER_TITLE'					=> 'Attachment document viewer',
	'ACP_DOCSVIEWER_TITLE_EXPLAIN'			=> 'Settings',
	//
	'ACP_DOCSVIEWER_GDOSC'					=> 'Document formats for Google Docs',
	'ACP_DOCSVIEWER_GDOSC_EXPLAIN'			=> 'List separated by commas, no spaces.',
	'ACP_DOCSVIEWER_MSOFFICE'				=> 'Document formats for MS Office 365',
	'ACP_DOCSVIEWER_MSOFFICE_EXPLAIN'		=> 'List separated by commas, no spaces.',
]);
