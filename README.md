# dcat-admin 表单媒体资源选择器

<p align="center">
    <a href="https://packagist.org/packages/de-memory/dcat-media-selector">
        <img src="https://img.shields.io/packagist/dt/de-memory/dcat-media-selector.svg?color=" />
    </a> 
    <a><img src="https://img.shields.io/badge/php-7.1+-59a9f8.svg?style=flat" /></a> 
    <a><img src="https://img.shields.io/badge/laravel-7.29+-59a9f8.svg?style=flat" ></a>
</p>


![](https://raw.githubusercontent.com/de-memory/dcat-media-selector/master/1.png)
![](https://raw.githubusercontent.com/de-memory/dcat-media-selector/master/2.png)

## 依赖

- php | >= 7.1.0
- dcat/laravel-admin | >= ~2.0
- intervention/image | >= ^2.5

## 安装

### composer 安装

```
composer require de-memory/dcat-media-selector
```

### 后台发布

```
开发工具=》扩展=》de-memory.dcat-media-selector=》启用
```

### 执行该扩展迁移文件

```
php artisan migrate --path=vendor/de-memory/dcat-media-selector/updates
```

## 更新

```
composer update

// 强制发布静态资源文件
php artisan vendor:publish --tag=dcat-media-selector-assets --force

// 清理视图缓存
php artisan view:clear
```

## 方法使用

```
用法一
$form->mediaSelector('avatar', '头像')->area()->limit()->sortable()->move();
用法二
$form->mediaSelector('avatar1', '头像2')
    ->options([
        'area'     => ['60%', '98%'], // 弹框大小
        'limit'    => 2, // 媒体数量
        'sortable' => true, // 排序
        'move'     => json_encode(['dir' => 'media', 'fileNameIsEncrypt' => true]),// 第一个参数，媒体上传路径。默认media。第二个参数，媒体名称是否加密。默认true
    ]);
```

## 参数说明

```
/*
|--------------------------------------------------------------------------
| 弹框大小。默认宽['60%', '98%']
|--------------------------------------------------------------------------
*/
limit(array)

/*
|--------------------------------------------------------------------------
| 媒体选择数量。默认1
|--------------------------------------------------------------------------
*/
limit(int)

/*
|--------------------------------------------------------------------------
| 媒体上传路径，媒体名称是否加密
|--------------------------------------------------------------------------
| 第一个参数，媒体上传路径。默认upload_files
| 第二个参数，媒体名称是否加密。默认true
|
| 注意：第二个参数如果是false，上传文件时，跟已上传的文件名称相同，会覆盖已上传的文件
| 
*/
move(string, boolean)

/*
|--------------------------------------------------------------------------
| 媒体选择类型。默认所有
|--------------------------------------------------------------------------
| image            图片选择
| video            视频选择
| audio            音频选择
| powerpoint       文稿选择
| code             代码文件选择
| zip              压缩包选择
| text             文本选择
| other            其他选择
*/
types(string)

/*
|--------------------------------------------------------------------------
| 推动排序。默认true
|--------------------------------------------------------------------------
| true             启用排序
| false            禁用排序
*/
sortable(boolean)
```

## 说明

```

在JSON表单可能无法使用。（这个还没有测试）

数据保存处理
1、可以用官网文档中的，模型表单回调
https://learnku.com/docs/dcat-admin/2.x/event/8113

2、可以用laravel模型处理（模型修改器）
https://learnku.com/docs/laravel/7.x/eloquent-mutators/7502#81e641
```

## 回滚该扩展迁移文件。(谨慎操作)

```
php artisan migrate:rollback --path=vendor/de-memory/dcat-media-selector/updates
```

## 优化说明

```
新增分组右键

移除 dcat-media-selector:install 命令

媒体选择类型移除blend选项

type修改为types。参数由string修改为array

媒体弹框由modal修改为layer.open

移除英文翻译文件

代码优化
```
