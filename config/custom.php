<?php

// +----------------------------------------------------------------------
// | 用户自定义设置
// +----------------------------------------------------------------------

return [
    // 用户上传图片地址
    'file_upload_path' => Env::get('root_path') . 'public/upload/user',
    //媒体资源库用于上传markdown文件
    'markdown_file_upload' => Env::get('root_path') . 'public/upload/media',
    //markdown图片访问url地址
    'http_markdown_image' => '/so/service/public/upload/media',

];
