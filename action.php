<?php
require_once './config.inc.php';
session_start();
ini_set("display_errors", "On");
error_reporting(E_ALL);

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
                $m = new Model();
                $result = $m->getOne('files', 'id=' . $id);
                $data = $result->fetch(PDO::FETCH_ASSOC);
                try {
                    removeDir($data['path']);

                    $m->del('files', 'id=' . $id);
                    echo "<script>alert('删除成功');window.opener=null;window.open('','_self');window.close();</script>";
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }

            break;
        }
    }
}
?>