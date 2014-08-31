<?php
class AutoLoader
{
	public static function adminLoader($class)
	{
		$path = PATH_CONTROLLERS . "admin/{$class}.php" ;
		if (is_readable($path)) require_once $path ;
	}
	public static function generalLoader($class)
	{
		$path = PATH_CONTROLLERS . "general/{$class}.php" ;
		if (is_readable($path)) require_once $path ;
	}
	public static function payrollLoader($class)
	{
		$path = PATH_CONTROLLERS . "payroll/{$class}.php" ;
		if (is_readable($path)) require_once $path ;
	}
	public static function baseLoader($class)
	{
		$path = PATH_CONTROLLERS . "base/{$class}.php" ;
		if (is_readable($path)) require_once $path ;
	}
	public static function hrLoader($class) 
	{
		$path = PATH_CONTROLLERS . "hr/{$class}.php" ;
		if (is_readable($path)) require_once $path ;
	}
	public static function leaveLoader($class)
	{
		$path = PATH_CONTROLLERS . "leave/{$class}.php" ;
		if (is_readable($path)) require_once $path ;
	}
	public static function claimsLoader($class)
	{
		$path = PATH_CONTROLLERS . "claims/{$class}.php" ;
		if (is_readable($path)) require_once $path ;
	}
	public static function attendanceLoader($class)
	{
		$path = PATH_CONTROLLERS . "attendance/{$class}.php" ;
		if (is_readable($path)) require_once $path ;
	}
	public static function portalLoader($class)
	{
		$path = PATH_CONTROLLERS . "portal/{$class}.php" ;
		if (is_readable($path)) require_once $path ;
	}
}
spl_autoload_register('AutoLoader::adminLoader') ;
spl_autoload_register('AutoLoader::generalLoader') ;
spl_autoload_register('AutoLoader::payrollLoader') ;
spl_autoload_register('AutoLoader::baseLoader') ;
spl_autoload_register('AutoLoader::hrLoader') ;
spl_autoload_register('AutoLoader::leaveLoader') ;
spl_autoload_register('AutoLoader::claimsLoader');
spl_autoload_register('AutoLoader::attendanceLoader');
spl_autoload_register('AutoLoader::portalLoader');
?>