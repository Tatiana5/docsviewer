<?php
/**
 *
 * @package       docsviewer
 * @copyright (c) BB3.Mobi 2014 (c) Anvar [apwa.ru], 2022 Татьяна5
 * @license       http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace tatiana5\docsviewer\controller;

class docs
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var string phpbb_root_path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;

	public function __construct(\phpbb\user $user, \phpbb\auth\auth $auth,
								\phpbb\config\config $config, \phpbb\cache\service $cache,
								\phpbb\db\driver\driver_interface $db, \phpbb\request\request_interface $request,
								$phpbb_root_path, $php_ext)
	{
		$this->user = $user;
		$this->auth = $auth;
		$this->config = $config;
		$this->cache = $cache;
		$this->db = $db;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function documents($mode, $attach_id, $extension)
	{
		// Thank you sun.
		if ($this->request->server('CONTENT_TYPE', '') !== '')
		{
			if ($this->request->server('CONTENT_TYPE', '') === 'application/x-java-archive')
			{
				exit;
			}
		}
		else if (($this->request->server('HTTP_USER_AGENT', '') !== '') && strpos($this->request->server('HTTP_USER_AGENT', ''), 'Java') !== false)
		{
			exit;
		}

		if (!function_exists('send_file_to_browser'))
		{
			require($this->phpbb_root_path . 'includes/functions_download' . '.' . $this->php_ext);
		}

		$this->user->setup('viewtopic');

		if (!$this->config['allow_attachments'] && !$this->config['allow_pm_attach'])
		{
			send_status_line(404, 'Not Found');
			trigger_error('ATTACHMENT_FUNCTIONALITY_DISABLED');
		}

		if (!$attach_id)
		{
			send_status_line(404, 'Not Found');
			trigger_error('NO_ATTACHMENT_SELECTED');
		}

		$sql = 'SELECT attach_id, post_msg_id, topic_id, poster_id, is_orphan, physical_filename, real_filename, extension, mimetype, filesize, filetime
			FROM ' . ATTACHMENTS_TABLE . '
			WHERE attach_id = ' . $attach_id . '
				AND extension = "' . (string) $extension . '"
				AND in_message = 0';
		$result = $this->db->sql_query($sql);
		$attachment = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$attachment)
		{
			send_status_line(404, 'Not Found');
			trigger_error('ERROR_NO_ATTACHMENT');
		}
		else if (!download_allowed())
		{
			send_status_line(403, 'Forbidden');
			trigger_error($this->user->lang['LINKAGE_FORBIDDEN']);
		}
		else
		{
			$attachment['physical_filename'] = utf8_basename($attachment['physical_filename']);

			if (!$this->config['allow_attachments'])
			{
				send_status_line(404, 'Not Found');
				trigger_error('ATTACHMENT_FUNCTIONALITY_DISABLED');
			}

			if ($attachment['is_orphan'])
			{
				// We allow admins having attachment permissions to see orphan attachments...
				$own_attachment = ($this->auth->acl_get('a_attach') || $attachment['poster_id'] == $this->user->data['user_id']) ? true : false;

				if (!$own_attachment || !$this->auth->acl_get('u_download'))
				{
					send_status_line(404, 'Not Found');
					trigger_error('ERROR_NO_ATTACHMENT');
				}

				// Obtain all extensions...
				$extensions = $this->cache->obtain_attach_extensions(true);
			}
			else
			{
				phpbb_download_handle_forum_auth($this->db, $this->auth, $attachment['topic_id']);

				$sql = 'SELECT forum_id, post_visibility
					FROM ' . POSTS_TABLE . '
					WHERE post_id = ' . (int) $attachment['post_msg_id'];
				$result = $this->db->sql_query($sql);
				$post_row = $this->db->sql_fetchrow($result);
				$this->db->sql_freeresult($result);

				if (!$post_row || ($post_row['post_visibility'] != ITEM_APPROVED && !$this->auth->acl_get('m_approve', $post_row['forum_id'])))
				{
					// Attachment of a soft deleted post and the user is not allowed to see the post
					send_status_line(404, 'Not Found');
					trigger_error('ERROR_NO_ATTACHMENT');
				}
				else if (!$this->auth->acl_get('u_download') || (!$this->auth->acl_get('f_download_images', $post_row['forum_id']) && !$this->auth->acl_get('f_download', $post_row['forum_id'])))
				{
					send_status_line(403, 'Forbidden');
					if ($this->user->data['is_registered'])
					{
						trigger_error('RULES_DOWNLOAD_CANNOT');
					}
					trigger_error('SORRY_AUTH_VIEW_ATTACH');
				}

				$extensions = array();
				if (!extension_allowed($post_row['forum_id'], $attachment['extension'], $extensions))
				{
					send_status_line(403, 'Forbidden');
					trigger_error(sprintf($this->user->lang['EXTENSION_DISABLED_AFTER_POSTING'], $attachment['extension']));
				}
			}

			$download_mode = (int) $extensions[$attachment['extension']]['download_mode'];
			$display_cat = $extensions[$attachment['extension']]['display_cat'];

			/*if (($display_cat == ATTACHMENT_CATEGORY_IMAGE || $display_cat == ATTACHMENT_CATEGORY_THUMB) && !$this->user->optionget('viewimg'))
			{
				$display_cat = ATTACHMENT_CATEGORY_NONE;
			}

			if ($mode == 'thumb')
			{
				$attachment['physical_filename'] = 'thumb_' . $attachment['physical_filename'];
			}
			else if ($display_cat == ATTACHMENT_CATEGORY_NONE && !$attachment['is_orphan'] && !phpbb_http_byte_range($attachment['filesize']))
			{
				send_status_line(403, 'Forbidden');
				trigger_error($this->user->lang('FILE_NOT_FOUND', $attachment['attach_id'] . '.' . $extension));
			}

			if ($mode == 'pic')
			{
				$mode = 'img';
			}

			if ($display_cat == ATTACHMENT_CATEGORY_IMAGE && $mode === 'img' && (strpos($attachment['mimetype'], 'image') === 0) && (strpos(strtolower($this->user->browser), 'msie') !== false) && !phpbb_is_greater_ie_version($this->user->browser, 7))
			{
				wrap_img_in_html(append_sid($this->phpbb_root_path . 'download/file.' . $this->php_ext, 'id=' . $attachment['attach_id']), $attachment['real_filename']);
				file_gc();
			}
			else
			{*/
				send_file_to_browser($attachment, $this->config['upload_path'], $display_cat);
				file_gc();
			/*}*/
		}
	}
}
