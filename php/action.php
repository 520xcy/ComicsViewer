<?php
require_once './config.inc.php';
session_start();
// ini_set("display_errors", "On");
// error_reporting(E_ALL);

function removeDir($dirName)
{
    if (!is_dir($dirName)) {
        return false;
    }

    $handle = opendir($dirName);
    while (($file = readdir($handle)) !== false) {
        //判断是不是文件 .表示当前文件夹 ..表示上级文件夹 =2
        if ($file != '.' && $file != '..') {
            $dir = $dirName . '/' . $file;
            if(is_dir($dir)){
                removeDir($dir);
            }elseif(is_writable($dir)){
                
                unlink($dir);
            }else{
                throw new Exception('删除'.$dir.'出错');
            }
        }
    }
    closedir($handle);
    if(is_writable($dirName)){
        rmdir($dirName);
    }else{
        throw new Exception('删除'.$dirName.'出错');
    }
    
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_token = inject_check($_POST['_token']);
    $id = inject_check($_POST['id']);
    $action = inject_check($_POST['action']);
    if ($_SESSION['_token'] == $_token) {
        switch ($action) {
            case 'remove':
                try {
                    $m = new Model();
                    $res = $m->getOne('files', 'id=' . $id);
                    foreach($res as $data){}
                    if(!empty($data)){
                            removeDir($data['path']);
                            $res = $m->del('files', 'id='. $id);
                            echo '删除记录成功';
                        
                    }else{
                        echo '文件不存在';
                    }
                } catch (\Exception $e) {
                    die($e->getMessage());

                }
            break;
        }
    }
}
