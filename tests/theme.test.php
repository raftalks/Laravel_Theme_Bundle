<?php

class TestTheme extends PHPUnit_Framework_TestCase
{
	public $theme;

	public function setup()
	{
		Bundle::start('theme');
	}

	public function testThemeInstance()
	{
		
		$config = array(
            'theme_path'=>'themes',  
        );
    	
    	$this->theme = new Theme("admin",$config);

		return $this->assertInstanceOf('Theme', $this->theme);
	}


	public function testThemeAttributes()
	{

	}


}