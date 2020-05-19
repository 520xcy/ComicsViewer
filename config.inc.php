<?php
header('Content-Type:text/html;Charset=utf-8');
define('ROOT_PATH', dirname(__FILE__)); // 网站根目录
define('DB_DSN', 'sqlite:'.ROOT_PATH.'/data/db.db');
define('DB_USER', 'OA');
define('DB_PWD', '8444973');
// 自动加载文件类
function __autoload($className) {
	require_once ROOT_PATH . '/class/'. ucfirst($className) .'.class.php';
}
