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
	'ACP_DOCSVIEWER_TITLE'					=> 'Просмотр документов из вложений',
	'ACP_DOCSVIEWER_TITLE_EXPLAIN'			=> 'Настройки',
	//
	'ACP_DOCSVIEWER_GDOSC'					=> 'Форматы документов для Google Docs',
	'ACP_DOCSVIEWER_GDOSC_EXPLAIN'			=> 'Перечислить через запятую, без пробелов.',
	'ACP_DOCSVIEWER_MSOFFICE'				=> 'Форматы документов для MS Office 365',
	'ACP_DOCSVIEWER_MSOFFICE_EXPLAIN'		=> 'Перечислить через запятую, без пробелов.',
]);
