<?php
/**
 *
 * Detailed Viewonline.
 * Provide more detailed information about a place on the board on viewonline page.
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2013-2025, rxu, https://www.phpbbguru.net
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace rxu\detailedviewonline\event;

/**
 * Event listener
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var string phpbb_root_path */
	protected $phpbb_root_path;

	/** @var string php_ext */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth                     $auth             User object
	 * @param \phpbb\config\config                 $config           Config object
	 * @param \phpbb\db\driver\driver_interface    $db               DBAL object
	 * @param \phpbb\language\language             $language         Language object
	 * @param string                               $phpbb_root_path  phpbb_root_path
	 * @param string                               $php_ext          php_ext
	 * @access public
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, $phpbb_root_path, $php_ext)
	{
		$this->config = $config;
		$this->language = $language;
		$this->db = $db;
		$this->auth = $auth;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.viewonline_overwrite_location' => 'detailed_viewonline',
		];
	}

	public function detailed_viewonline($event)
	{
		$this->language->add_lang('detailed_viewonline', 'rxu/detailedviewonline');

		$forum_data = $event['forum_data'];
		$on_page = $event['on_page'];
		$location = $event['location'];
		$location_url = $event['location_url'];
		$row = $event['row'];

		switch ($on_page[1])
		{
			case 'viewtopic':
				preg_match('#[\?&]t=([0-9]+)#i', $row['session_page'], $topic_id);
				$topic_id = (count($topic_id)) ? (int) $topic_id[1] : 0;

				preg_match('#[\?&]p=([0-9]+)#i', $row['session_page'], $post_id);
				$post_id = (count($post_id)) ? (int) $post_id[1] : 0;

				preg_match('#[\?&]start=([0-9]+)#i', $row['session_page'], $start);
				$start = (count($start)) ? (int) $start[1] : 0;
				$page = ($start) ? (floor($start / $this->config['posts_per_page']) + 1) : 0;

				$view = (preg_match('#[\?&]view=unread#i', $row['session_page'])) ? true : false;

				if ($post_id)
				{
					$sql_ary = [
						'SELECT'	=> 't.topic_id, t.topic_title, t.forum_id',
						'FROM'		=> [
							POSTS_TABLE		=> 'p',
							TOPICS_TABLE	=> 't',
						],
						'WHERE'		=> 't.topic_id = p.topic_id
							AND p.post_id = ' . $post_id,
					];
				}
				else
				{
					$sql_ary = [
						'SELECT'	=> 't.topic_title, t.forum_id',
						'FROM'		=> [
							TOPICS_TABLE	=> 't',
						],
						'WHERE'		=> 't.topic_id = ' . $topic_id,
					];
				}

				$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));
				if ($topicdata = $this->db->sql_fetchrow($result))
				{
					$topic_id = ($topic_id) ?: (int) $topicdata['topic_id'];
					$forum_id = (int) $topicdata['forum_id'];
					if ($forum_id && $this->auth->acl_get('f_list', $forum_id))
					{
						$topic_title = $topicdata['topic_title'];
						if ($post_id)
						{
							$location = $this->language->lang('READING_THE_POST', $topic_title, $forum_data[$forum_id]['forum_name']);
							$location_url = append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", "p=$post_id#p$post_id");
						}
						else if ($start)
						{
							$location = $this->language->lang('READING_THE_TOPIC_PAGE', $topic_title, $forum_data[$forum_id]['forum_name'], $page);
							$location_url = append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", 'f=' . $forum_id . '&amp;t=' . $topic_id . '&amp;start=' . $start);
						}
						else if ($view)
						{
							$location = $this->language->lang('READING_THE_NEW_POSTS', $topic_title, $forum_data[$forum_id]['forum_name']);
							$location_url = append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", 'f=' . $forum_id . '&amp;t=' . $topic_id . '&amp;view=unread#unread');
						}
						else
						{
							$location = $this->language->lang('READING_THE_TOPIC', $topic_title, $forum_data[$forum_id]['forum_name']);
							$location_url = append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", 'f=' . $forum_id . '&amp;t=' . $topic_id);
						}
					}
				}
				$this->db->sql_freeresult($result);
			break;

			case 'search';
				preg_match('#search_id=([a-z_]+)#i', $row['session_page'], $search_id);
				$search_id = $search_id[1] ?? '';
				$search_mode = [
					'egosearch'		=> 'SEARCH_SELF',
					'unanswered'	=> 'SEARCH_UNANSWERED',
					'unreadposts'	=> 'SEARCH_UNREAD',
					'newposts'		=> 'SEARCH_NEW',
					'active_topics'	=> 'SEARCH_ACTIVE_TOPICS',
				];
				$location = $this->language->lang('SEARCHING_FORUMS') . (($search_id && isset($search_mode[$search_id])) ? ': <strong>' . $this->language->lang($search_mode[$search_id]) . '</strong>' : '');
				$location_url = append_sid("{$this->phpbb_root_path}search.{$this->php_ext}", ($search_id && isset($search_mode[$search_id])) ? "search_id=$search_id" : '');
			break;

			case 'memberlist';
				preg_match('#[\?&]u=([0-9]+)#i', $row['session_page'], $user_id);
				$user_id = (count($user_id)) ? (int) $user_id[1] : 0;
				if ($user_id)
				{
					$sql = 'SELECT username, user_colour FROM ' . USERS_TABLE . '
						WHERE user_id = ' . $user_id;

					$result = $this->db->sql_query($sql);
					if ($userdata = $this->db->sql_fetchrow($result))
					{
						$username = get_username_string('no_profile', $user_id, $userdata['username'], $userdata['user_colour'], $userdata['username']);
						$location = $this->language->lang('VIEWING_MEMBER_PROFILE') . ' <strong>' . $username . '</strong>';
						$location_url = append_sid("{$this->phpbb_root_path}memberlist.{$this->php_ext}", "mode=viewprofile&amp;u=$user_id");
					}
					$this->db->sql_freeresult($result);
				}
			break;

			case 'download/file':
				preg_match('#[\?&]id=([0-9]+)#i', $row['session_page'], $file_id);
				$file_id = (count($file_id)) ? (int) $file_id[1] : 0;
				if ($file_id)
				{
					$sql = 'SELECT real_filename, post_msg_id FROM ' . ATTACHMENTS_TABLE . '
						WHERE attach_id = ' . $file_id;

					$result = $this->db->sql_query($sql);
					if ($filedata = $this->db->sql_fetchrow($result))
					{
						$location = $this->language->lang('DOWNLOADING_FILE') . ' <strong>' . $filedata['real_filename'] . '</strong>';
						$location_url = append_sid("{$this->phpbb_root_path}viewtopic.{$this->php_ext}", "p={$filedata['post_msg_id']}#p{$filedata['post_msg_id']}");
					}
					$this->db->sql_freeresult($result);
				}
			break;

			case 'feed';
				$location = $this->language->lang('FEED');
				$location_url = append_sid("{$this->phpbb_root_path}feed.{$this->php_ext}");
			break;

			default:
			break;
		}

		$event['location']= $location;
		$event['location_url'] = $location_url;
	}
}
