<?php
namespace app\index\controller;

use app\common\controller\Th;
use think\facade\Request;
use think\facade\Session;
use think\facade\Validate;
use app\index\model\Tag;

class Tooltag extends Th
{

    /**
     * 获取分类下标签列表
     */
    public function getTags()
    {
        //设置数据的默认返回值
        $ret = array('code' => 20000, 'prompt' => '', 'value' => array());
        //查询当前用户登陆状态,没有用户选择创建者
        $uid = app()->user['uid'];
        $type = Request::param('type');
        //获取所有的工具项
        $tags = Tag::where(['uid'=>$uid, 'tid'=>$type] )->all();
        // 赋值
        $ret['value'] = $tags;
        //返回数据结果
        return Th::response($ret);
    }

    /**
     * 添加分类下标签的方法
     */
    public function addTag()
    {
        //设置数据的默认返回值
        $ret = array('status' => true, 'prompt' => '', 'value' => '');
        //获取提交的所有数据
        $tag = Request::param();
        //验证提交的数据
        $check = $this->checkAddTag($tag);
        if ($check['status']) {
            //添加数组到额外数据
            $tag['uid'] = app()->user['uid'];
            $tag['tid'] = $tag['type']; 
            //添加数据
            $addTag = Tag::create($tag);
            if (!$addTag) {
                $ret['status'] = false;
                $ret['prompt'] = '数据添加失败...';
            } else {
                $ret['value'] = $addTag;
            }
            //返回数据结果
            return Th::response($ret);
        } else {
            $ret['status'] = false;
            $ret['prompt'] = $check['message'];
            return Th::response($ret);
        }
    }

    /**
     * 获取所有工具项列表数据
     *
     * @return void
     * @Description
     * @example
     * @author user
     * @since
     */
    public function getToolList()
    {
        //设置数据的默认返回值
        $ret = array('status' => true, 'prompt' => '', 'value' => '');

        //获取当前登陆账户
        $uid = app()->user['uid'];
        // 使用查询构造器查询
        $list = Tools::where([
            'uid' => $uid,
            'status' => 1,
        ])->select();

        if ($list) {
            $ret['value'] = $list;
        } else {
            $ret['status'] = false;
            $ret['prompt'] = '请求数据异常...';
        }
        return Th::response($ret);
    }

    /**
     * 修改工具项数据
     *
     * @return void
     * @Description
     * @example
     * @author user
     * @since
     */
    public function editTool()
    {
        //设置数据的默认返回值
        $ret = array('status' => true, 'prompt' => '', 'value' => '');
        //获取提交数据
        $tool = Request::param();
        if ($tool['sid'] == '' || $tool['sid'] == null) {
            $ret['status'] = false;
            $ret['prompt'] = '找不到要更新的数据';
            return Th::response($ret);
        }

        //验证数据是否规范
        $check = $this->checkAddTools($tool);
        if ($check['status']) {
            $tools = Tools::get($tool['sid']);
            $tools->title = $tool['title'];
            if (!$tools->save()) {
                $ret['status'] = false;
                $ret['prompt'] = '数据更新失败,请稍候重试...';
            }
        }else {
            $ret['status'] = false;
            $ret['prompt'] = $check['message'];
        }
        return Th::response($ret);
    }

    /**
     * 删除工具项
     *
     * @return void
     * @Description
     * @example
     * @author user
     * @since
     */
    public function delTool()
    {
        //设置数据的默认返回
        $ret = array('status' => true, 'prompt' => '', 'value' => '');
        //获取提交数据
        $tool = Request::param();
        if ($tool['sid'] == '') {
            $ret['status'] = false;
            $ret['prompt'] = '找不到要删除的工具项';
            return json($ret);
        }

        $tools = Tools::get($tool['sid']);
        if ($tools->delete()) {
            return json($ret);
        } else {
            $ret['status'] = false;
            $ret['prompt'] = '删除数据失败...';
            return json($ret);
        }
    }

    //+-----------------------------------
    //|  工具项下子元素部分
    //+-----------------------------------

    /**
     * 添加工具
     *
     * @return void
     * @Description
     * @example
     * @author user
     * @since
     */
    public function addItemTool()
    {
        //设置默认数据返回值
        $ret = array('status' => true, 'prompt' => '', 'value' => '');

        //获取数据
        $tool = Request::param();
        $check = $this->checkItemTool($tool);

        if (!$check['status']) {
            $ret['status'] = false;
            $ret['prompt'] = $check['message'];
        };

        $tool['uid'] = Session::get('user')['uid'];
        $addItem = ToolItems::create($tool);
        if ($addItem) {
            $ret['value'] = $addItem;
        } else {
            $ret['status'] = false;
            $ret['prompt'] = '数据添加失败...';
        }
        return json($ret);
    }

    /**
     * 获取工具项下所有列表数据
     *
     * @return void
     * @Description
     * @example
     * @author user
     * @since
     */
    public function getToolItem()
    {
        $ret = array('status' => true, 'prompt' => '', 'value' => '');

        $type = Request::param('type');
        if ($type == '' || $type == null) {
            $ret['status'] = false;
            $ret['prompt'] = '未找到您选择的数据类型';
            return Th::response($ret);
        }

        $toolInfo = Tools::get($type);
        ToolItems::get($type);

        $totalNum = 0; //总条目数
        //$perTotalNum = isset($data['perTotalNum']) ? $data['perTotalNum'] : 5; //每页显示多少条
        //$currentPage = isset($data['currentPage']) ? $data['currentPage'] : 1; //当前也码数
        //$currentNum = $currentPage == 1 ? $currentNum = 0 : ($currentPage - 1) * $perTotalNum; //当前起始条目数

        // 使用查询构造器查询
        //$totalNum = count(Article::all());
        $list = ToolItems::where([
            'uid' => $uid = app()->user['uid'],
            'type' => $type])
        //->limit($currentNum, $perTotalNum)
            ->order('create_time', 'desc')
            ->all();

        $ret['value'] = array('totalNum' => $totalNum, 'tools' => $list, 'info' => $toolInfo );

        return Th::response($ret);
    }

    /**
     * 删除工具
     *
     * @return void
     * @Description
     * @example
     * @author user
     * @since
     */
    public function delToolItem()
    {
        //设置数据默认返回值
        $ret = array('status' => true, 'prompt' => '', 'value' => '');
        $sid = Request::param('sid');
        if ($sid == '' || $sid == null) {
            $ret['status'] = false;
            $ret['prompt'] = '删除的数据不存在...';
            return json($ret);
        }

        //删除数据
        $tool = ToolItems::get($sid);
        if (!$tool->delete()) {
            $ret['status'] = false;
            $ret['prompt'] = '数据删除失败...';
        }
        return json($ret);
    }

    //+-------------------------------
    //|        独立验证部分.以及工具函数
    //+-------------------------------

    /**
     * 添加工具箱验证部分
     * @param $data
     * @return array
     */
    protected function checkAddTag($data)
    {
        //设置返回数组
        $ret = array('status' => true);

        //定义验证字段
        $rule = [
            'title|标签名称' => [
                'require' => 'require',
                'max' => '60',
            ],
        ];

        //初始化验证过则
        Validate::rule($rule);
        //验证数据
        if (!Validate::check($data)) {
            $ret['message'] = Validate::getError();
            $ret['status'] = false;
        }
        return $ret;
    }

    /**
     * 验证工具格式是否合法
     * @param $data
     * @return array
     */
    protected function checkItemTool($data)
    {
        //设置返回数组
        $ret = array('status' => true);

        //定义验证字段
        $rule = [
            'title|名称' => [
                'require' => 'require',
                'max' => '60',
            ],
            'type|工具项' => [
                'require' => 'require',
            ],
            'img|图片' => [
                'require' => 'require',
            ],
            'url|地址' => [
                'require' => 'require',
            ],
        ];

        //初始化验证过则
        Validate::rule($rule);
        //验证数据
        if (!Validate::check($data)) {
            $ret['message'] = Validate::getError();
            $ret['status'] = false;
        }
        return $ret;
    }
}
