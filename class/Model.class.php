<?php
/**
* 数据库操作类库
* author Lee.
* Last modify $Date: 2012-1-19 13:59；04 $
*/
class Model {

    /**
     * 数据库添加操作
     * @param string $tName 表名
     * @param array $field 字段数组
     * @param array $val 值数组
     * @param bool $is_lastInsertId 是否返回添加ID
     * @return int 默认返回成功与否，$is_lastInsertId 为true，返回添加ID
     */
    public function add($tName, $field, $val, $is_lastInsertId=FALSE) {
        try {
            if (!is_array($field) || !is_array($val)) exit($this->getError(__FUNCTION__, __LINE__));
            $pdo = Db::getDB();
            $field = $this->formatArr($field);
            $val = $this->formatArr($val, false);
            if (!$is_lastInsertId) {
                $row = $pdo->exec("INSERT INTO {$tName} ({$field}) VALUES ({$val})");
                $pdo = null;
                return $row;
            } else {
                $pdo->exec("INSERT INTO {$tName} ({$field}) VALUES ({$val})");
                $lastId = $pdo->lastInsertId();
                $pdo = null;
                return $lastId;
            }
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 数据库修改操作
     * @param string $tName 表名
     * @param array $field 字段数组
     * @param array $val 值数组
     * @param string $condition 条件
     * @return int 受影响的行数
     */
    public function update($tName, $fieldVal, $condition) {
        try {
            if (!is_array($fieldVal) || !is_string($tName) || !is_string($condition)) exit($this->getError(__FUNCTION__, __LINE__));
            $pdo = Db::getDB();
            foreach ($fieldVal as $k=>$v) {
                $upStr .= $k . '=' . '\'' . $v . '\'' . ',';
            }
            $upStr = rtrim($upStr, ',');
            $row = $pdo->exec("UPDATE {$tName} SET {$upStr} WHERE {$condition}");
            $pdo = null;
            return $row;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 数据库删除操作（注：必须添加 where 条件）
     * @param string $tName 表名
     * @param string $condition 条件
     * @return int 受影响的行数
     */
    public function del($tName, $condition) {
        try {
            if (!is_string($tName) || !is_string($condition)) exit($this->getError(__FUNCTION__, __LINE__));
            $pdo = Db::getDB();
            $row = $pdo->exec("DELETE FROM {$tName} WHERE {$condition}");
            return $row;
            $pdo = null;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 返回表总个数
     * @param string $tName 表名
     * @param string $condition 条件
     * @return int
     */
    public function total($tName, $condition='') {
        try {
            if (!is_string($tName)) exit($this->getError(__FUNCTION__, __LINE__));
            $pdo = Db::getDB();
            $re = $pdo->query("SELECT COUNT(*) AS total FROM {$tName}" . ($condition=='' ? '' : ' WHERE ' . $condition));
            foreach ($re as $v) {
                $total = $v['total'];
            }
            return $total;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 数据库删除多条数据
     * @param string $tName 表名
     * @param string $field 依赖字段
     * @param array $ids 删除数组
     * @return int 受影响的行数
     */
    public function delMulti($tName, $field, $ids) {
        try {
            if (!is_string($tName) || !is_array($ids)) exit($this->getError(__FUNCTION__, __LINE__));
            $pdo = Db::getDB();
            foreach ($ids as $v) {
                $delStr .= $v . ',';
            }
            $delStr = rtrim($delStr, ',');
            $row = $pdo->exec("DELETE FROM {$tName} WHERE {$field} IN ({$delStr})");
            $pdo = null;
            return $row;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 获取表格的最后主键（注：针对 INT 类型）
     * @param string $tName 表名
     * @param string $primaryName 依赖字段
     * @return int
     */
    public function lastId($tName, $primaryName) {
        try {
            if (!is_string($tName) || !is_string($primaryName)) exit($this->getError(__FUNCTION__, __LINE__));
            $pdo = Db::getDB();
            $re = $pdo->query("SELECT {$primaryName} FROM {$tName} ORDER BY {$primaryName} DESC LIMIT 1");
            $lastId = null;
            foreach ($re as $v) {
                $lastId = $v[$primaryName];
            }
            $pdo = null;
            return $lastId;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 返回全部数据，返回 PDOStatement 对象
     * @param string $sql
     * @return PDOStatement
     */
    public function getAll($sql) {
        try {
            if (!is_string($sql)) exit($this->getError(__FUNCTION__, __LINE__));
            $pdo = Db::getDB();
            $result = $pdo->query($sql);
            $pdo = null;
            return $result;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
	 * 检查数据是否已经存在（依赖条件）
	 * @param string $tName 表名
	 * @param string $field 依赖的字段
	 * @return bool
	 */
	public function isExists($tName, $condition) {
		try {
			if (!is_string($tName) || !is_string($condition)) exit($this->getError(__FUNCTION__, __LINE__));
			$pdo = Db::getDB();
			$result = $pdo->query("SELECT COUNT(*) AS total FROM {$tName} WHERE {$condition}");
			foreach ($result as $v) {
				 $b = $v['total'];
			}
			$pdo = null;
			if ($b) {
				return true;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			exit($e->getMessage());
		}
	}

	/**
	 * 检查数据是否已经存在（依赖 INT 主键）
	 * @param string $tName 表名
	 * @param string $primary 主键
	 * @param int $id 主键值
	 * @return bool
	 */
	public function isExistsByIntPrimary($tName, $primary, $id) {
		try {
			if (!is_string($tName) || !is_string($primary) || !is_int($id)) exit($this->getError(__FUNCTION__, __LINE__));
			$pdo = Db::getDB();
			$result = $pdo->query("SELECT COUNT(*) AS total FROM {$tName} WHERE {$primary} = ". $id);
			foreach ($result as $v) {
				 $b = $v['total'];
			}
			$pdo = null;
			if ($b) {
				return true;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			exit($e->getMessage());
		}
	}

    /**
     * 预处理添加数据（推荐使用）
     * @param string $tableName  表格名
     * @param array $fieldArr  字段名数组
     * @param array $valArr  字段值数组
     * @param bool  $mult  是否添加多条数据，如果是那么 $valArr 为二维数组
     * @return int  返回添加的记录数，添加单条为1，多条为多
     */
    public function insert($tableName, $fieldArr, $valArr, $mult=FALSE) {
        try {
            if (!is_string($tableName) || !is_array($fieldArr) || !is_array($valArr) || !is_bool($mult)) exit($this->getError(__FUNCTION__, __LINE__));
            $pdo = Db::getDB();
            $fields = $this->formatArr($fieldArr);
            $arrCount = count($fieldArr);
            $mark = $this->formatMark($arrCount);
            $ss="INSERT INTO {$tableName} ({$fields}) VALUES ({$mark})";
            $stmt = $pdo->prepare("INSERT INTO {$tableName} ({$fields}) VALUES ({$mark})");
            if (!$mult) {
            	$row = $stmt->execute($valArr); // 添加单条数据
            } else {
            	$row = 0;
            	for ($i=0; $i<count($valArr); $i++) {
            		if ($stmt->execute($valArr[$i])) {
            			$row++;
            		}
            	}
            }
            $pdo = null;
            return $row;
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 预处理删除（注：针对主键为 INT 类型，推荐使用）
     * @param string $tName 表名
     * @param string $primary 主键字段
     * @param int or array or string $ids 如果是删除一条为 INT，多条为 array，删除一个范围为 string
     * @return int 返回受影响的行数
     */
    public function remove($tName, $primary, $ids, $mult=FALSE) {
    	try {
    		if (!is_string($tName) || !is_string($primary) || (!is_int($ids) && !is_array($ids) && !is_string($ids)) || !is_bool($mult)) exit($this->getError(__FUNCTION__, __LINE__));
	    	$pdo = Db::getDB();
	    	$stmt = $pdo->prepare("DELETE FROM {$tName} WHERE {$primary}=?");
	    	if (!$mult) {
	    		$stmt->bindParam(1, $ids);
	    		$row = $stmt->execute();
	    	} else {
	    		if (is_array($ids)) {
		    		$row = 0;
		    		foreach ($ids as $v) {
		    			$stmt->bindParam(1, $v);
		    			if ($stmt->execute()) {
		    				$row++;
		    			}
		    		}
	    		} elseif (is_string($ids)) {
	    			if (!strpos($ids, '-')) exit($this->getError(__FUNCTION__, __LINE__));
	    			$split = explode('-', $ids);
	    			if (count($split)!=2 || $split[0]>$split[1]) exit($this->getError(__FUNCTION__, __LINE__));
	    			$i = null;
	    			$count = $split[1]-$split[0]+1;
	    			for ($i=0; $i<$count; $i++) {
	    				$idArr[$i] = $split[0]++;
	    			}
	    			foreach ($idArr as $id) {
	    				$idStr .= $id . ',';
	    			}
	    			$idStr = rtrim($idStr, ',');
	    			$row = $pdo->exec("DELETE FROM {$tName} WHERE {$primary} in ({$idStr})");
	    		}
	    	}
	    	return $row;
	    	$pdo = null;
    	} catch (PDOException $e) {
    		exit($e->getMessage());
    	}
    }

    /**
     * 返回单条数据
     * @param string $tName 表名
     * @param string $condition 条件
     * @param string or array $fields 返回的字段，默认是*
     * @return PDOStatement
     */
    public function getOne($tName, $condition, $fields="*") {
    	try {
    		if (!is_string($tName) || !is_string($condition) || !is_string($fields)) exit($this->getError(__FUNCTION__, __LINE__));
    		$pdo = Db::getDB();
    		$data = $pdo->query("SELECT {$fields} FROM {$tName} WHERE {$condition} LIMIT 1");
    		$pdo = null;
    		return $data;
    	} catch (PDOException $e) {
    		exit($e->getMessage());
    	}
    }

    /**
     * 返回全部数据
     * @param string $tName 表名
     * @param string $fields 返回字段，默认为*
     * @param string $condition 条件
     * @param string $order 排序
     * @param string $limit 显示个数
     * @return PDOStatement
     */
    public function fetchAll($tName, $fields='*', $condition='', $order='', $limit='') {
    	try {
    		if (!is_string($tName) || !is_string($fields) || !is_string($condition) || !is_string($order) || !is_string($limit)) exit($this->getError(__FUNCTION__, __LINE__));
    		$fields = ($fields=='*' || $fields=='') ? '*' : $fields;
    		$condition = $condition=='' ? '' : " WHERE ". $condition ;
    		$order = $order=='' ? '' : " ORDER BY ". $order;
    		$limit = $limit=='' ? '' : " LIMIT ". $limit;
    		$pdo = Db::getDB();
    		$re = $pdo->query("SELECT {$fields} FROM {$tName} {$condition} {$order} {$limit}");
    		return $re;
    	} catch (PDOException $e) {
    		exit($e->getMessage());
    	}
    }

    /**
     * 格式化数组（表结构和值）
     * @param array $field
     * @param bool $isField
     * @return string
     */
    private function formatArr($field, $isField=TRUE) {
        if (!is_array($field)) exit($this->getError(__FUNCTION__, __LINE__));
        if ($isField) {
            foreach ($field as $v) {
                $fields .= $v.',';
            }
        } else {
            foreach ($field as $v) {
                $fields .= '\''.$v.'\''.',';
            }
        }
        $fields = rtrim($fields, ',');
        return $fields;
    }

    /**
     * 格式化问号
     * @param int $count 数量
     * @return string 返回格式化后的字符串
     */
    private function formatMark($count) {
    	if (!is_int($count)) exit($this->getError(__FUNCTION__, __LINE__));
    	if ($count==1) return '?';
    	for ($i=0; $i<$count; $i++) {
    		$str .= '?,';
    	}
    	return rtrim($str, ',');
    }

    /**
     * 错误提示
     * @param string $fun
     * @return string
     */
    private function getError($fun, $line) {
        return __CLASS__  . '->' . $fun . '() line<font color="red">'. $line .'</font> ERROR!';
    }
}
