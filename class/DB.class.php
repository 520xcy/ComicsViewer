<?php
// 连接数据库
class Db {
	static public function getDB() {
		try {
			$pdo = new PDO(DB_DSN, DB_USER, DB_PWD);
			$pdo->setAttribute(PDO::ATTR_PERSISTENT, true);	// 设置数据库连接为持久连接
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // 设置抛出错误
			$pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, true);  // 设置当字符串为空转换为 SQL 的 NULL
			$pdo->query('SET NAMES utf8');  // 设置数据库编码
		} catch (PDOException $e) {
			exit('数据库连接错误，错误信息：'. $e->getMessage());
		}
		return $pdo;
	}
}