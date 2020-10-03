<?php
require_once './config.inc.php';
session_start();
$_token             = md5(crypt(date('Y-m-d H:m:s') . session_id()));
$_SESSION['_token'] = $_token;
$_ORDER = $_GET['order'];

?>
<!DOCTYPE html>
<html>

<head>
    <!--==============手机端适应============-->
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <!--===================================-->
    <meta name="description" content="">
    <meta name="author" content="">
    <!--==============强制双核浏览器使用谷歌内核============-->
    <meta name="renderer" content="webkit" />
    <meta name="force-rendering" content="webkit" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="format-detection" content="telephone=no">
    <meta name="referrer" content="never">
    <title>ComicViewer v1.0.0-SNAPSHOT</title>
    <link rel="icon" href="h/img/favicon.ico">
    <link href="h/css/black-t.css" rel="stylesheet">
    <script type="text/javascript" src="h/js/jquery.min.js"></script>
    <script type="text/javascript" src="h/js/jquery.lazyload.min.js"></script>
    <style>
        a {
            color: #fff;
            text-decoration: none;
            outline: 0
        }

        .index-nav {
            display: none;
            position: fixed;
            width: 60px;
            z-index: 10010;
            text-align: center;
            background: #111111;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;

        }

        .name {
            color: #fff;
        }


        .index-nav .pointer-block {
            position: absolute;
            width: 60px;
            height: 30px;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
            transition: .1s all linear;
            display: none;
        }


        .index-nav.customizing .tip {
            top: 0;
            opacity: 1;
        }

        .index-nav .nav-list {
            position: relative;
            z-index: 233;
            width: 60px;
        }

        .index-nav .n-i {
            border-bottom: 1px solid #414141;
            cursor: pointer;
        }



        .index-nav .n-i .name {
            line-height: 30px;
            width: 60px;
            font-size: 12px;
            text-align: center;
            height: 30px;
            transition: .1s background linear;
        }

        .file_list li .date {
            position: relative;
            padding: 1px 0 0;
            color: #fff;
            font-size: 12px;
            text-align: right
        }
    </style>
</head>

<body class="page page-id-15 page-template page-template-list_tag-php">

    <div id="container">
        <div id="contents">
            <section class="leftbox l1">

                <ul class="file_list">
                    <?php
                    $m      = new Model();
                    switch ($_ORDER){
                        case 'asc':
                            $result = $m->fetchAll('files', '*', '', 'created_at');
                        break;

                        case 'desc':
                            $result = $m->fetchAll('files', '*', '', 'created_at desc');
                        break;

                        default:
                            $result = $m->fetchAll('files', '*', '', 'title');
                    }

                    foreach ($result as $row) {
                    ?>
                        <li><a href="<?php echo $row['path'] ?>/detail.html" target="_blank" title="1">
                                <h2><?php echo $row['title'] ?></h2>
                                <div class="image"><img class="lazy" src="h/img/loading.gif" data-original="<?php echo $row['path'] . '/' . $row['pic'] ?>">
                                    <table class="data">
                                        <tr>
                                            <th scope="row">枚数</th>
                                            <td><?php echo $row['count'] ?>枚</td>
                                        </tr>
                                        <tr>
                                            <td class="tag" colspan="2"><span>1</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </a>
                            <p class="date"><?php echo $row['created_at'] ?>&nbsp;<a class='del' href="javascript:;" data-id="<?php echo $row['id'] ?>">删</a></p>

                        </li>
                    <?php } ?>
                </ul>
            </section>
        </div>
    </div>
    <footer>
        <div class="box">
            <p id="copyright">
                <span class="copyright">
                    <a href="https://github.com/masazumi-github/ComicsViewer" target="_blank">See at Github</a>
                </span>
        </div>
    </footer>

    <script type="text/javascript" charset="utf-8">
        $(function() {
            $("img").lazyload({
                placeholder: "h/img/loading.gif",
                effect: "fadeIn"
            });
        });
    </script>
    <script>
        var Ajax = {
            get: function(url, fn) {
                // XMLHttpRequest对象用于在后台与服务器交换数据
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url, false);
                xhr.onreadystatechange = function() {
                    // readyState == 4说明请求已完成
                    if (xhr.readyState == 4) {
                        if (xhr.status == 200 || xhr.status == 304) {
                            console.log(xhr.responseText);
                            fn.call(xhr.responseText);
                        }
                    }
                }
                xhr.send();
            },

            // data应为'a=a1&b=b1'这种字符串格式，在jq里如果data为对象会自动将对象转成这种字符串格式
            post: function(url, data, fn) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', url, false);
                // 添加http头，发送信息至服务器时内容编码类型
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4) {
                        if (xhr.status == 200 || xhr.status == 304) {
                            console.log(xhr.responseText);
                            fn.call(xhr.responseText);
                        }
                    }
                }
                xhr.send(data);
            }
        }
    </script>
    <script>
        $(document).on('click', '.del', function(event) {
            if (confirm('确实要删除吗?')) {
                let that = $(this);
                let path = that.data('id');
                let data = 'action=remove&id=' + path + '&_token=<?php echo $_token ?>';
                Ajax.post('action.php', data, function() {
                    alert(this);
                    that.parents('li').remove();
                })
            }
        });
    </script>
</body>

</html>