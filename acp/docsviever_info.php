<?php
/**
*
* @package docsviewer
* @copyright (c) 2022 Татьяна5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace tatiana5\docsviewer\acp;

class imgsliders_info
{
	public function module()
	{
		return [
			'filename'	=> '\tatiana5\docsviewer\acp\docsviewer_module',
			'title'		=> 'ACP_DOCSVIEWER_TITLE',
			'version'	=> '0.0.1',
			'modes'		=> [
				'config_docsviewer'		=> ['title' => 'ACP_DOCSVIEWER_TITLE_EXPLAIN', 'auth' => 'ext_tatiana5/docsviewer && acl_a_extensions', 'cat' => ['ACP_DOCSVIEWER_TITLE_EXPLAIN']],
			],
		];
	}
}
