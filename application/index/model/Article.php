<?php

namespace app\index\model;

use think\Model;

class Article extends Model
{

    protected $pk = 'sid'; //默认主键
    protected $table = 'so_article'; //默认数据表

    protected $autoWriteTimestamp = 'datetime'; //开启自动时间戳，并且设置为datetime格式

    protected $createTime = 'create_time'; //创建时间字段
    protected $updateTime = 'update_time'; //更新时间字段
    protected $dateFormat = 'Y-m-d H:i:s'; //时间字段取出后的默认时间格式

    //设置存储标签格式
    public function setTagAttr($tags)
    {
        if ($tags == '') {
            return '';
        }

        $tagStr = '';
        $tags = json_decode($tags, true);
        if (!empty($tags)) {
            foreach ($tags as $key => $tag) {
                $tagStr = $tagStr == '' ? '[' . $tag['title'] . ']' : $tagStr . ',' . '[' . $tag['title'] . ']';
            }
        }
        return $tagStr;
    }

    //设置获取存储标签格式
    public function getTagAttr($tags)
    {
        $ret_tags = array();
        if ($tags == '') {
            $ret_tags;
        }

        $tagData = explode(',', $tags);
        if (count($tagData) != 0) {
            foreach ($tagData as $index => $item) {
                $tag = array();
                $tag['id'] = $index;
                $tag['title'] = str_replace(']', '', str_replace('[', '', $item));
                array_push($ret_tags, $tag);
            }
        }
        return $ret_tags;
    }

}
