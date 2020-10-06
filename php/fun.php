<?php
function inject_check($Sql_Str) {//自动过滤Sql的注入语句。
    $check=preg_match('/select|insert|update|delete|\'|\\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i',$Sql_Str);
    if ($check) {
        echo '<script language="JavaScript">alert("系统警告：\n\n请不要尝试在参数中包含非法字符尝试注入！");</script>';
        exit();
    }else{
		$Sql_Str=test_input($Sql_Str);
        return $Sql_Str;
    }
}

function test_input($data) {//输入转义
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  
function pswmd5($pass){//MD5算法
	$pass=md5(crypt($pass,substr($pass,0,2)));
	return $pass;
}


   