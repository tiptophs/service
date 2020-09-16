<?php
namespace app\index\controller;

use app\common\controller\Th;
use app\index\model\ToolItems;
use app\index\model\Tools;
use think\facade\Request;
use think\facade\Session;
use think\facade\Validate;

class Tool extends Th
{

    /**
     * 获取首页资源工具数据
     *
     * @return void
     * @Description
     * @example
     * @author tiptop
     * @since
     */
    public function getTools()
    {
        //设置数据的默认返回值
        $ret = array('code' => 20000, 'prompt' => '', 'data' => array());

        //查询当前用户登陆状态,没有用户选择创建者
        $uid = app()->user['uid'];

        //获取所有的工具项
        $tools = Tools::where('uid', $uid)->all();
        // 对数据集进行遍历操作
        foreach ($tools as $key => $tool) {
            $item = array();
            //存储工具项名称
            $item['title'] = $tool['title'];
            $item['item'] = array();

            $toolItems = ToolItems::where(['type' => $tool['sid'], 'uid' => '8AC4E8DG918B1'])->all();
            //循环数据
            foreach ($toolItems as $index => $ti) {
                $tool_item = array();
                $tool_item['img'] = $ti['img'];
                $tool_item['desc'] = $ti['title'];
                $tool_item['href'] = $ti['url'];
                array_push($item['item'], $tool_item);
            }

            array_push($ret['data'], $item);
        }
        return json($ret);
    }

    /**
     * 获取我的技能分类数据
     *
     * @return void
     * @Description
     * @example
     * @author tiptop
     * @since
     */
    public function getSkills()
    {
        $ret = array('code' => 20000, 'prompt' => '', 'data'=>'');
        $tools = array(
            ['title' => 'Git/SVN/集中式、分布式版本控制系统', 'desc' => '版本控制系统，当然最先学习的就是git啦，毋庸置疑的好！', 'sid' => '1'],
            ['title' => 'Php/ThinkPhp/CI/后台服务', 'desc' => 'php语言，php框架都是开发后台服务不可缺少的部分！当然php是世界上最好的语言。滑稽！', 'sid' => '2'],
            ['title' => 'Bootstrap/响应式布局框架', 'desc' => '前台最流行的模版框架，其他框架都或多或少的借鉴与他！好好学习他没错！', 'sid' => '3'],
            ['title' => 'Vue.js/React/Angular前台编程框架', 'desc' => '不用细说,react/vue/angular前段程序员必学，时代在发展。当然jquery库也不能放弃！', 'sid' => '4'],
            ['title' => 'Python/scrapy相关爬虫技术', 'desc' => 'python爬虫有多火，不用多解释了吧！骚年，任务繁重啊！', 'sid' => '5'],
            ['title' => 'Mysql/Redis/MongoDb/数据库相关', 'desc' => '数据库优化,非关系型数据库，数据并发，海量数据读取...', 'sid' => '6'],
            ['title' => 'NodeJs/ES6/javascript/Jquery相关', 'desc' => '前端也应该学习下nodejs，有了它后充当后台服务，前端也可以开发完成项目了。', 'sid' => '7'],
        );
        $ret['data'] = $tools;
        return json($ret);
    }

    /**
     * 添加工具项方法
     *
     * @return void
     * @Description
     * @example
     * @author user
     * @since
     * @return $string json
     */
    public function addTools()
    {
        //设置数据的默认返回值
        $ret = array('status' => true, 'prompt' => '', 'value' => '');
        //获取提交的所有数据
        $tool = Request::param();

        //验证提交的数据
        $check = $this->checkAddTools($tool);
        if ($check['status']) {
            //添加数组到额外数据
            $tool['uid'] = app()->user['uid'];
            $tool['delivery'] = $tool['delivery']? 1 : 0; //启用
            $tool['type'] = json_encode($tool['type']);
            $tool['mode'] = json_encode($tool['mode']); 
            $tool['status'] = 1; //启用
            //添加数据
            $addTool = Tools::create($tool);
            if (!$addTool) {
                $ret['status'] = false;
                $ret['prompt'] = '数据添加失败...';
            } else {
                $ret['value'] = $addTool;
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
    protected function checkAddTools($data)
    {
        //设置返回数组
        $ret = array('status' => true);

        //定义验证字段
        $rule = [
            'title|工具项名称' => [
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
