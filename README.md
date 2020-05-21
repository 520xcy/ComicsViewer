# 欢迎使用 ComicsViewer
ComicsViewer是一个基于php+sqlite的图片阅读器，旨在将本地的漫画图片进行整理生成HMTL文件，以便于在浏览器中阅读，项目使用python 3.7.0进行开发。

## 目前版本
ComicsViewer1.0.0-SNAPSHOT

## 目录结构
| 名称        | 说明                |  类型   |
| --------    | :-----             | :----:  |
| contents    | 漫画文件存放目录     |  目录   |
| data        | shelve数据目录      |  目录   |
| h           | HTML模块文件目录     |  目录   |
| README.md   | 项目说明文件         |  文件   |
| run.bat     | 运行python run.py   |  文件   |
| run.py      | 主要代码文件         |  文件   |
| index.html  | 程序运行后生成       |  文件   |

## 使用方法
### Step1
将漫画文件夹放在contents目录下，即```contents/C(94).../xxxx.jpg```
注意，如```contents/favorite/C(94).../xxxx.jpg```等多级目录也会被解析

### Step2
当前目录运行```python run.py```或是Windows用户双击run.bat
```python run.py date``` 生成首页目录按添加日期排序，默认按文件夹名排序

### Step3
indexphp.php为动态生成目录页面，性能有限
index.php为静态生成页面，推荐使用
