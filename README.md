# 欢迎使用 ComicsViewer
ComicsViewer是一个基于php+sqlite的图片阅读器，旨在将本地的漫画图片进行整理生成HMTL文件，以便于在浏览器中阅读，项目使用python 3.7.0进行开发。

## 使用方法
### Step1
将漫画文件夹放在contents目录下，即```contents/C(94).../xxxx.jpg```
注意，如```contents/favorite/C(94).../xxxx.jpg```等多级目录也会被解析

### Step2
当前目录运行```python run.py```或是Windows用户双击run.bat
```python run.py date``` 生成首页目录按添加日期排序，默认按文件夹名排序

### Step3
index.php为动态生成目录页面，性能有限
