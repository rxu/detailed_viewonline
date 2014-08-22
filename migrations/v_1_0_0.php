<?php
/**
*
* @package DetailedViewonline
* @copyright (c) 2014 rxu
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace rxu\DetailedViewonline\migrations;

class v_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['detailed_viewonline_version']) && version_compare($this->config['detailed_viewonline_version'], '1.0.0', '>=');
	}

	static public function depends_on()
	{
			return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return 	array();
	}

	public function revert_schema()
	{
		return 	array();
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('detailed_viewonline_version', '1.0.0')),
		);
	}
}
