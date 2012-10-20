<?php

class TestTheme extends PHPUnit_Framework_TestCase
{
	public $theme;
	public $themeName = "DefaultTheme";

	public function setup()
	{
		Bundle::start('theme');
		$config = array(
            'theme_path'=>'themes',  
        );
    	
    	$this->theme = new Theme($this->themeName,$config);
	}

	public function testThemeInstance()
	{
		return $this->assertInstanceOf('Theme', $this->theme);
	}


	public function testThemeAttributeThemeName()
	{
		return $this->assertObjectHasAttribute('_theme_name', $this->theme);
	}

	public function testThemeAttributeThemeNameReturnString()
	{
		$theme_name = $this->theme->_theme_name;
		return $this->asserttrue(is_string($theme_name));
	}

	public function testThemeSetTheme()
	{
		$this->theme->set_theme('NewTheme');
		return $this->assertTrue(($this->theme->_theme_name == 'NewTheme'), 'set_theme method not passing the new theme');
	}

	


}