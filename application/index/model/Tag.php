<?php

namespace app\index\model;

use think\Model;

class Tag extends Model
{

    protected $pk = 'sid'; //默认主键
    protected $table = 'so_tags'; //默认数据表

    protected $autoWriteTimestamp = 'datetime'; //开启自动时间戳，并且设置为datetime格式

    protected $createTime = 'create_time'; //创建时间字段
    protected $updateTime = 'update_time'; //更新时间字段
    protected $dateFormat = 'Y-m-d H:i:s'; //时间字段取出后的默认时间格式

}
