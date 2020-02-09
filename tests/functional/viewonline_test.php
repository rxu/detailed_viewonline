<?php
/**
*
* Detailed Viewonline extension for the phpBB Forum Software package.
*
* @copyright (c) 2013 phpBB Limited <https://www.phpbb.com>
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
		return array('rxu/detailedviewonline');
	}

	public function test_viewonline_page()
	{
		$this->login();

		self::request('GET', "viewonline.php?sg=1&sk=b&sd=d&start=0");
	}
}
