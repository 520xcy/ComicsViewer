<?php


header('Content-Type:text/html;Charset=utf-8');
define('ROOT_PATH', dirname(__FILE__)); // 网站根目录
define('DB_DSN', 'sqlite:' . ROOT_PATH . '/../data/xiang');
define('DB_USER', '');
define('DB_PWD', '');
// 自动加载文件类

if (function_exists('spl_autoload_register')) {
	//php7.2后使用spl_autoload_register
	function autoload($className)
	{
		require_once ROOT_PATH . '/class/' . ucfirst($className) . '.class.php';
	}
	spl_autoload_register('autoload');
} else {
	function __autoload($className)
	{
		require_once ROOT_PATH . '/class/' . ucfirst($className) . '.class.php';
	}
}

require_once ROOT_PATH . '/fun.php';
