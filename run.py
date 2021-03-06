# -*- coding: utf-8 -*-
# python 3.7.0

import os
import time
import shelve
import urllib.parse
from PIL import Image
from fun import re_sort

# 默认templete模板生成目录与h资源目录相差2层
BASE_TEMP_DEEPTH = 1

BASE_PATH = os.getcwd()
# CONTENTS_PATH = BASE_PATH + "/contents"
CONTENTS_PATH = "contents"

DATA_PATH = "data/data"
INDEX_HTML = "/index.html"
# CONTENT_HTML = "/29f459a44fee58c7.html"
CONTENT_HTML = "/detail.html"

TEMPLETE_HTML = "/h/new_templete.html"
INDEX_TEMPLETE_HTML = "/h/index_templete.html"

IMG_SUFFIX = [".jpg", ".png", ".jpeg", ".gif"]

'''把时间戳转化为时间: 1479264792 to 2016-11-16 10:53:12'''


def TimeStampToTime(timestamp):
    timeStruct = time.localtime(timestamp)
    return time.strftime('%Y-%m-%d %H:%M:%S', timeStruct)


'''
:图片处理部分
'''


def get_size(file):
    # 获取文件大小:KB
    size = os.path.getsize(file)
    return size / 1024


def get_outfile(infile, outfile):
    if outfile:
        return outfile
    dir, suffix = os.path.splitext(infile)
    outfile = '{}-out{}'.format(dir, suffix)
    return outfile


def compress_image(infile, outfile='', mb=150, step=10, quality=80):
    """不改变图片尺寸压缩到指定大小
    :param infile: 压缩源文件
    :param outfile: 压缩文件保存地址
    :param mb: 压缩目标，KB
    :param step: 每次调整的压缩比率
    :param quality: 初始压缩比率
    :return: 压缩文件地址，压缩文件大小
    """
    o_size = get_size(infile)
    if o_size <= mb:
        return infile
    outfile = get_outfile(infile, outfile)
    while o_size > mb:
        im = Image.open(infile)
        im.save(outfile, quality=quality)
        if quality - step < 0:
            break
        quality -= step
        o_size = get_size(outfile)
    return outfile, get_size(outfile)


def resize_image(infile, outfile='', x_s=800):
    """修改图片尺寸
    :param infile: 图片源文件
    :param outfile: 重设尺寸文件保存地址
    :param x_s: 设置的宽度
    :return:
    """
    im = Image.open(infile)
    x, y = im.size
    y_s = int(y * x_s / x)
    out = im.resize((x_s, y_s), Image.ANTIALIAS)
    outfile = get_outfile(infile, outfile)
    out.save(outfile)


def createComicItems(title, content_path, first_img, count):
    templete = r'<li><a href="{url}" target="_blank" title="{title}"><h2>{title}</h2><div class="image"><img class="lazy" src="h/img/loading.gif" data-original="{first_img}"><table class="data"><tr><th scope="row">枚数</th><td>{count}枚</td></tr><tr><td class="tag" colspan="2"><span>{title}</span></td></tr></table></div><p class="date">{date}</p></a></li><!--{comic_contents}-->'
    templete = templete.replace(r"{url}", urllib.parse.quote(content_path) + CONTENT_HTML)
    templete = templete.replace(r"{title}", title)
    templete = templete.replace(r"{count}", str(count))
    templete = templete.replace(r"{first_img}", urllib.parse.quote(content_path)+"/"+first_img)
    date = time.localtime(os.stat(content_path).st_ctime)
    templete = templete.replace(
        r"{date}", ("%d-%d-%d" % (date.tm_year, date.tm_mon, date.tm_mday)))
    return templete


def getTempleteHtml(templeteURL):
    templete = open(BASE_PATH + templeteURL, "r", encoding="UTF-8")
    htmlStr = templete.read()
    templete.close()
    return htmlStr


def output2Html(htmlContent, file):
    output = open(file, "w", encoding="UTF-8")
    output.write(htmlContent)
    output.flush()
    output.close()


def createOptions(imgData):
    options = ""
    _i = 0
    for _img in imgData:
        options += ('<option value="%d" file="%s">第%d页</option>' %
                    (_i, _img, _i+1))
        _i += 1
    return options


def createImgList(content_path):
    imgs = []
    for _dir in os.listdir(content_path):
        if os.path.splitext(_dir)[1].lower() in IMG_SUFFIX:
            imgs.append(_dir)
    try:
        # {True: imgs.sort(key=lambda x: int(x[:-4])), False: imgs.sort()}[imgs[3][:-4].isdigit()]
        # imgs.sort(key=lambda x: x[:-4])
        # imgs = sort_filename.sort_insert_filename(imgs)
        imgs.sort(key=re_sort.sort_key)
    except:
        imgs.sort()
        pass
    return imgs


def createContentHtml(contentPath):
    imgData = createImgList(contentPath)
    if len(imgData) == 0 or imgData == None:
        return
    count = len(imgData)
    options = createOptions(imgData)
    htmlStr = getTempleteHtml(TEMPLETE_HTML)
    title = contentPath.replace(CONTENTS_PATH+"/", "")
    deepth = "../"
    d_i = 0
    while d_i < BASE_TEMP_DEEPTH:
        deepth += "../"
        d_i += 1
    htmlStr = htmlStr.replace(r"{deepth}", deepth)
    htmlStr = htmlStr.replace(r"{imgData}", "var imgData="+str(imgData))
    htmlStr = htmlStr.replace(r"{title}", title).replace(r"{options}", options)
    htmlStr = htmlStr.replace(r"{count}", str(
        count)).replace(r"{first_img}", imgData[0])
    try:
        htmlStr = htmlStr.replace(r"{next_img}", imgData[1])
    except IndexError:
        htmlStr = htmlStr.replace(r"{next_img}", imgData[0])
    output2Html(htmlStr, contentPath + CONTENT_HTML)
    return [title, contentPath, imgData[0], count]


def pushData(data):
    try:
        dir, suffix = os.path.splitext(data[2])
        if not dir[-4:] == '-out':
            print('生成封面缩略图...')
            compress_image(data[1]+'/'+data[2])
            resize_image(data[1]+'/'+data[2])
            data[2] = get_outfile(data[2])
    except:
        print('生成封面缩略图失败')
        pass
    with shelve.open(DATA_PATH) as write:
        write[data[0]] = data


def checkData():
    with shelve.open(DATA_PATH) as read:
        keys = list(read.keys())
        for key in keys:
            if not checkFileExist(read[key][1]+CONTENT_HTML):
                print("移除： ", read[key])
                del(read[key])


def getData():
    data = []
    with shelve.open(DATA_PATH) as read:
        keys = list(read.keys())
        try:
            keys.sort()
        except:
            pass
        for key in keys:
            data.append(read[key])
    return data


def createIndexHtml():
    checkData()
    datas = getData()
    indexStr = getTempleteHtml(INDEX_TEMPLETE_HTML)
    for data in datas:
        _s = createComicItems(data[0], data[1], data[2], data[3])
        indexStr = indexStr.replace(r"<!--{comic_contents}-->", _s)
    output2Html(indexStr, BASE_PATH + INDEX_HTML)


def checkFileExist(fileURI):
    if os.path.isfile(fileURI):
        return True
    return False


def getContentPaths(path):
    for _path in os.listdir(path):
        _dir = path + "/" + _path
        if os.path.isdir(_dir):
            contentPaths.append(_dir)
        else:
            return contentPaths


def gci(filepath):
    # 遍历filepath下所有文件，包括子目录
    files = os.listdir(filepath)
    for fi in files:
        fi_d = os.path.join(filepath, fi)
        if os.path.isdir(fi_d):
            contentPaths.append(fi_d)
            gci(fi_d)


if __name__ == '__main__':
    contentPaths = []
    gci(CONTENTS_PATH)
    for contentPath in contentPaths:
        BASE_TEMP_DEEPTH = contentPath.count('/', 0)
        if checkFileExist(contentPath + CONTENT_HTML):
            continue
        data = createContentHtml(contentPath)
        if data is not None:
            print("新增： ", data)
            pushData(data)
    createIndexHtml()
