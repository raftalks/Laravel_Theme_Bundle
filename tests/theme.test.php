<?php

class TestTheme extends PHPUnit_Framework_TestCase
{

	public function testThemeInstance(){
		Bundle::start('theme');
		return $this->assertInstanceOf('Theme', new Theme);
	}
}