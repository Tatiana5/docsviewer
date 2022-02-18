<?php
/**
 *
 * @package       docsviewer
 * @copyright (c) 2022 Татьяна5
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace tatiana5\docsviewer\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config              $config
	 * @param \phpbb\request\request            $request
	 * @param \phpbb\controller\helper          $helper
	 */
	public function __construct(\phpbb\config\config $config,
								\phpbb\request\request $request,
								\phpbb\controller\helper $helper)
	{
		$this->config = $config;
		$this->request = $request;
		$this->helper = $helper;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.parse_attachments_modify_template_data'	=> 'docs_parse_attachment',
		];
	}

	public function docs_parse_attachment($event)
	{
		$attachment = $event['attachment'];

		if ($this->check($attachment, 'gdocs'))
		{
			$event['block_array'] = array_merge($event['block_array'], [
				//'S_FILE'		=> false,
				'S_DOCSVIEWER_GDOCS'	=> true,
				'SRC'					=> $this->get_gilename($attachment),
			]);
		}

		if ($this->check($attachment, 'msoffice'))
		{
			$event['block_array'] = array_merge($event['block_array'], [
				//'S_FILE'		=> false,
				'S_DOCSVIEWER_MSOFFICE'	=> true,
				'SRC'					=> $this->get_gilename($attachment),
			]);
		}
	}

	private function check($attachment, $mode)
	{
		return !empty($this->config['docsviewer_' . $mode])
			&& in_array($attachment['extension'], explode(',', $this->config['docsviewer_' . $mode]));
	}

	private function get_gilename($attachment)
	{
		$seoname = strip_tags(htmlspecialchars_decode($attachment['real_filename']));
		$seoname = substr($seoname, 0, strrpos($seoname,'.'));
		$u_docs_url = generate_board_url(true) . $this->helper->route('tatiana5_docsviewer', array(
				'mode'		=> 'documents',
				'seoname'	=> $seoname,
				'attach_id'	=> $attachment['attach_id'],
				'extension'	=> $attachment['extension']),
				false, '', true
			);

		return (string) $u_docs_url;
	}
}
