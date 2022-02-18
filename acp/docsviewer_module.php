<?php
/**
 *
 * @package       docsviewer
 * @copyright (c) 2022 Татьяна5
 * @license       http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

namespace tatiana5\docsviewer\acp;

use tatiana5\docsviewer\functions\acp_module_helper;

class docsviewer_module extends acp_module_helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	public function main($id, $mode)
	{
		global $db;

		$this->db = $db;

		$this->ext_name ='tatiana5/docsviewer';
		//$this->ext_langname = 'docsviewer';
		$this->tpl_name = 'acp_docsviewer';
		$this->form_key = 'config_docsviewer';
		add_form_key($this->form_key);

		parent::main($id, $mode);
	}

	/**
	 * Generates the array of display_vars
	 */
	protected function generate_display_vars()
	{
		$this->display_vars = [
			'lang'  => ['acp/board'],
			'title' => 'ACP_DOCSVIEWER_TITLE',
			'vars'  => [
				'legend1'					=> '',
				'docsviewer_gdocs'			=> ['lang' => 'ACP_DOCSVIEWER_GDOSC', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true],
				'docsviewer_msoffice'		=> ['lang' => 'ACP_DOCSVIEWER_MSOFFICE', 'validate' => 'string', 'type' => 'text:40:255', 'explain' => true],
				//
				'legend2'                 => 'ACP_SUBMIT_CHANGES',
			],
		];
	}
}
