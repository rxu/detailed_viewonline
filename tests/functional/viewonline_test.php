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

namespace rxu\detailedviewonline\tests\functional;

/**
 * @group functional
 */
class viewonline_test extends \phpbb_functional_test_case
{
	protected function get_forum_name_by_topic_id($topic_id)
	{
		$db = $this->get_db();

		// Forum info
		$sql =  'SELECT f.forum_name
			FROM ' . FORUMS_TABLE . ' f,' . TOPICS_TABLE . ' t
			WHERE t.forum_id = f.forum_id
				AND t.topic_id = ' . (int) $topic_id;
		$result = $db->sql_query($sql);
		$forum_name = $db->sql_fetchfield('forum_name');
		$db->sql_freeresult($result, 1800); // cache for 30 minutes

		return $forum_name;
	}

	protected function get_forum_name_by_forum_id($forum_id)
	{
		$db = $this->get_db();

		// Forum info
		$sql =  'SELECT forum_name
			FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . (int) $forum_id;
		$result = $db->sql_query($sql);
		$forum_name = $db->sql_fetchfield('forum_name');
		$db->sql_freeresult($result, 1800); // cache for 30 minutes

		return $forum_name;
	}

	protected function get_topic_title_by_topic_id($topic_id)
	{
		$db = $this->get_db();

		// Forum info
		$sql =  'SELECT topic_title
			FROM ' . TOPICS_TABLE . '
			WHERE topic_id = ' . (int) $topic_id;
		$result = $db->sql_query($sql);
		$topic_title = $db->sql_fetchfield('topic_title');
		$db->sql_freeresult($result, 1800); // cache for 30 minutes

		return $topic_title;
	}

	protected function setUp(): void
	{
		parent::setUp();

		// Create users and send them to the Viewonline
		foreach (['user1', 'user2', 'user3', 'user4', 'user5', 'user6'] as $username)
		{
			if (!$this->user_exists($username))
			{
				$this->create_user($username);
			}
		}

		$this->add_lang_ext('rxu/detailedviewonline', 'detailed_viewonline');
	}

	static protected function setup_extensions()
	{
		return ['rxu/detailedviewonline'];
	}

	public function test_viewonline_page_viewtopic()
	{
		$topic_title = $this->get_topic_title_by_topic_id(1);
		$forum_name = $this->get_forum_name_by_topic_id(1);

		self::$client->restart();
		$this->login('user1');
		$crawler = self::request('GET', "viewtopic.php?t=1");

		self::$client->restart();
		$this->login('user2');
		$crawler = self::request('GET', "viewtopic.php?t=1&view=unread#unread");

		self::$client->restart();
		$this->login('user3');
		$crawler = self::request('GET', "viewtopic.php?p=1#p1");

		self::$client->restart();
		$this->login();
		$crawler = self::request('GET', "viewonline.php?sid={$this->sid}");
		$this->assertStringContainsString('user1', $crawler->text());
		$this->assertStringContainsString(strip_tags($this->lang('READING_THE_TOPIC', $topic_title, $forum_name)), $crawler->text());
		$this->assertStringContainsString('user2', $crawler->text());
		$this->assertStringContainsString(strip_tags($this->lang('READING_THE_NEW_POSTS', $topic_title, $forum_name)), $crawler->text());
		$this->assertStringContainsString('user3', $crawler->text());
		$this->assertStringContainsString(strip_tags($this->lang('READING_THE_POST', $topic_title, $forum_name)), $crawler->text());
	}

	public function test_viewonline_page_search()
	{
		self::$client->restart();
		$this->login('user1');
		$crawler = self::request('GET', "search.php?search_id=egosearch");

		self::$client->restart();
		$this->login('user2');
		$crawler = self::request('GET', "search.php?search_id=unanswered");

		self::$client->restart();
		$this->login('user3');
		$crawler = self::request('GET', "search.php?search_id=unreadposts");

		self::$client->restart();
		$this->login('user4');
		$crawler = self::request('GET', "search.php?search_id=newposts");

		self::$client->restart();
		$this->login('user5');
		$crawler = self::request('GET', "search.php?search_id=active_topics");

		self::$client->restart();
		$this->login();
		$crawler = self::request('GET', "viewonline.php?sid={$this->sid}");
		$this->assertStringContainsString('user1', $crawler->text());
		$this->assertStringContainsString($this->lang('SEARCHING_FORUMS') . ': ' . $this->lang('SEARCH_SELF'), $crawler->text());
		$this->assertStringContainsString('user2', $crawler->text());
		$this->assertStringContainsString($this->lang('SEARCHING_FORUMS') . ': ' . $this->lang('SEARCH_UNANSWERED'), $crawler->text());
		$this->assertStringContainsString('user3', $crawler->text());
		$this->assertStringContainsString($this->lang('SEARCHING_FORUMS') . ': ' . $this->lang('SEARCH_UNREAD'), $crawler->text());
		$this->assertStringContainsString('user4', $crawler->text());
		$this->assertStringContainsString($this->lang('SEARCHING_FORUMS') . ': ' . $this->lang('SEARCH_NEW'), $crawler->text());
		$this->assertStringContainsString('user5', $crawler->text());
		$this->assertStringContainsString($this->lang('SEARCHING_FORUMS') . ': ' . $this->lang('SEARCH_ACTIVE_TOPICS'), $crawler->text());
	}

	public function test_viewonline_page_nonexistant_search()
	{
		self::$client->restart();
		$this->login('user6');
		$crawler = self::request('GET', "search.php?search_id=nonexistantsearch");

		self::$client->restart();
		$this->login();
		$crawler = self::request('GET', "viewonline.php?sid={$this->sid}");

		$this->assertStringContainsString('user6', $crawler->text());
		$this->assertStringContainsString($this->lang('SEARCHING_FORUMS'), $crawler->text());
	}

	public function test_viewonline_page_memberlist()
	{
		self::$client->restart();
		$this->login('user1');
		$crawler = self::request('GET', "memberlist.php?u=2");

		self::$client->restart();
		$this->login();
		$crawler = self::request('GET', "viewonline.php?sid={$this->sid}");
		$this->assertStringContainsString('user1', $crawler->text());
		$this->assertStringContainsString($this->lang('VIEWING_MEMBER_PROFILE') . ' admin', $crawler->text());
	}


}
