<?php
/**
*
* @package Docsviewer
* @copyright (c) 2022 Татьяна5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace tatiana5\docsviewer\migrations;

class v_0_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['docsviewer_version']) && version_compare($this->config['docsviewer_version'], '0.0.1', '>=');
	}

	public static function depends_on()
	{
			return ['\phpbb\db\migration\data\v310\dev'];
	}

	public function update_data()
	{
		return [
			// Add configs
			['config.add', ['docsviewer_gdocs', 'pdf,doc,docx,ppt,pptx']],
			['config.add', ['docsviewer_msoffice', 'xls,xlsx']],

			// Current version
			['config.add', ['docsviewer_version', '0.0.1']],

			// Add ACP modules
			['module.add', ['acp', 'ACP_CAT_DOT_MODS', 'ACP_DOCSVIEWER_TITLE']],
			['module.add', ['acp', 'ACP_DOCSVIEWER_TITLE', [
					'module_basename'	=> '\tatiana5\docsviewer\acp\docsviewer_module',
					'module_langname'	=> 'ACP_DOCSVIEWER_TITLE_EXPLAIN',
					'module_mode'		=> 'config_docsviewer',
					'module_auth'		=> 'ext_tatiana5/docsviewer && acl_a_extensions',
			]]],
		];
	}
}
