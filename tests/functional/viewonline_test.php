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
	static protected function setup_extensions()
	{
		return ['rxu/detailedviewonline'];
	}

	public function test_viewonline_page()
	{
		$this->login();

		self::request('GET', "viewonline.php?sg=1&sk=b&sd=d&start=0");
	}
}
