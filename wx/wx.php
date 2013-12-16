<?php
/*
 * 微信操作的业务服务
 *
 * @Author:chengn(https://github.com/chengn)
 * @Date:  2013-12-12
 *
*/


/**
 * Class LeJian
 *  LeJian 公众号的业务处理类
 *
 */
class LeJian
{
    //用户菜单，文字模式，针对订阅号，服务号可以使用自定义菜单
    private $USER_MENU = "请根据如下菜单输入\n0 注册(回复“0 姓名”)\n1 活动查询\n2 账户查询\n3 费用明细\n4 活动签到\n5 签到查询\n* 系统管理";
    //管理员菜单，文字模式
    private $ADMIN_MENU = "管理员菜单(*):\n1 增加活动\n2 用户充值\n3 活动结算\n4 用户查询\n5 用户维护\n6 用户删除 ";

    /**
     * 用户关注服务
     * @param $openid
     * @return string
     */
    public function subscribe($openid)
    {

        $text = '欢迎关注';
        return $text;
    }

    /**
     * 取消关注
     */
    public function unSubscribe()
    {
    }

    /**
     * 对文本事件的响应逻辑
     *
     * @param $openid 用户微信id
     * @param $input 用户输入文字
     * @return string   系统输出文字
     */
    public function text($openid, $input)
    {
        $text = '';
        switch (substr($input, 0, 1)) {
            case '0':
            { // 注册
                $name = trim(substr($input, 1));
                if (strlen($name) > 0) {
                    $result = $this->RegUser($openid, $name);
                    if ($result == 0) {
                        $text = "注册成功，你注册账户为：" . $name;
                    } else if ($result == 1) {
                        $text = "亲，您已经注册过了,如果修改，请联系管理员";
                    }
                } else {
                    $text = "请在0之后输入用户名称";
                }
                break;
            }
            case '1':
            { // 活动查询
                //$text = 'ok';
                $text = $this->actQuery();
                break;
            }
            case '2':
            { // 账户查询
                $text = $this->accInfo($openid);
                break;
            }
            case '3':
            { // 费用明细
                $text = $this->accDetail($openid);
                break;
            }
            case '4':
            { // 活动签到
                $text = $this->joinInActivity($openid);
                break;
            }
            case '5':
            { // 签到查询
                $text = $this->queryJoinIn();
                break;
            }
            case '*':
            { //管理员模式
                $cmd = substr($input, 1);
                $text = $this->Admin($cmd,$openid);
                break;
            }
            case '#':
            {
                $text = $openid;
                break;
            }
            default:
            {
                $text = $this->USER_MENU;
                break;
            }
        }

        return $text;
    }

    /**
     *进入管理员模式
     * 需要根据fromuser校验是否管理员
     */
    private function Admin($cmd,$openid)
    {
        $result = 'input error';
        if (!$this->isAdmin($openid)) {
            return '你不是管理员，没有权限操作';
        }
        $CMD_START = 1; //具体命令的开始点
        $content = trim(substr($cmd, $CMD_START));
        $cmd = substr($cmd, 0, 1);
        switch ($cmd) {
            case '1':
            {
                if ($content == 'h') {
                    $result = "*1 日期 开始时间 结束时间 名称 地点 花费\neg:\n*1 2013-01-01 09:00:00 11:00:00 羽毛球 北京语言大学体育馆 100";
                } else {
                    $result = $this->cmdAddActivity($content);
                    //$result = $content;
                }
                break;
            }
            case '2':
            {
                if ($content == 'h') {
                    $result = "*2 微信昵称 金额\neg:\n*2 name 100";
                } else {
                    $result = $this->cmdAddMoney($content);
                }
                break;
            }
            case '3'://活动结算
            {
                $result = $this->balanceActivity();
                //给所有结算用户发送结算信息
                break;
            }
            case '4'://用户查询
            {
                $result = $this->queryUser($content);
                break;
            }
            case '5'://用户维护
            {
                $result = '正在建设';
                break;
            }
            case '6':
            {
                $result = '正在建设';
                break;
            }
            default:
                {
                $result = $this->ADMIN_MENU;
                break;
                }
        }
        return $result;
    }

    /**
     * 是否管理员
     * @return bool
     */
    private function isAdmin($openid)
    {
        //判断usertype = 0
        //TODO:管理员微信openid配置在数据库
        return $openid == 'ovHbRjkWUrl_tjXhjKJACD1NbJG4';
    }

    /**
     * 用户查询
     * @param $content 查询条件，为空查询所有，负责会模糊查询名称和昵称
     * @return string
     */
    private function queryUser($content){
        $result = '无符合条件的用户';
        //如下示例代码是从数据中查询得到结果
//        $mysql = new MySQL();
//        $sql = 'select * from user';
//        if($content != ''){
//            $sql = "select * from user where name like '%" .$content ."%' or nickname like '%" .$content ."%'";
//        }
//        $re = $mysql->ExecuteSQL($sql);
//        if(is_array($re) && count($re) > 0){
//            $index = 1;
//            $result = "查询结果：";
//            foreach($re as $user){
//                $result .= "\n" . $index++ . " " .$user['name'] . " " . $user['nickname'] . " " . $user['acc'];
//            }
//        }
        return $result;
    }

    /**
     * 活动结算
     * 根据签到人数，平摊活动花费，需要更新个人账户余额以及账户轨迹
     * @return string
     */
    private function balanceActivity(){
        $result = '今日无待结算费用';
        //TODO:具体的业务处理
        return $result;
    }
    /**
     * 增加一条活动
     * /*1 日期 开始时间 结束时间 名称 地点/
     *
     * @param $content 用户输入内容
     * @return string 用于输出的结果
     */
    private function cmdAddActivity($content)
    {
        $result = "添加成功" . $content;
        $item = explode(" ", $content);
        if (count($item) != 6) {
            return '输入参数个数错误';
        }
        $activity = array('date' => $item[0],
            'begintime' => $item[1],
            'endtime' => $item[2],
            'name' => $item[3],
            'content' => $item[3],
            'address' => $item[4],
            'fee' => $item[5]);
        //TODO:操作数据库添加入库
        return $result;

    }

    /*
     * 充值支出
     * 微信昵称 金额
     */
    private function cmdAddMoney($content)
    {
        $result = "添加成功" . $content;
        $item = explode(" ", $content);
        if (count($item) != 2) {
            return '输入参数个数错误';
        }
        //TODO：业务处理.......
        return $result;
    }

    /**
     * 活动签到，记录活动的人员到账户费用
     * @param $openid 用户openid
     * @return string  输出给用户的消息
     */
    private function joinInActivity($openid)
    {
        $result = '签到成功';
        //TODO:进行业务处理
        return $result;
    }

    /**
     * 查询当天活动的签到人员
     * @return string  返回给用户的信息
     */
    private function queryJoinIn()
    {
        $result = '无人签到';
        //TODO:业务处理
        return $result;
    }

    /**
     * 账户操作
     * @param $fee 账户金额
     * @param $feetype 费用类型
     * @param $user 指定用户
     * @return string    返回信息
     */
    private function addAccFee($fee, $feetype, $user)
    {
        $result = '操作成功';
        //TODO:业务处理....
        return $result;
    }

    /**
     * 用户注册
     * @param $openid 用户微信id，自动获取
     * @param $nickname 用户昵称，需要用户输入（如果是服务号，可以通过微信接口自动获取，订阅号无此接口使用权）
     * @return int   注册结果 0：成功，其他 ：失败
     * 错误码：1：用户已经注册
     */
    private function RegUser($openid, $nickname)
    {
        //TODO:业务处理........
        return 0;
    }

    /**
     * 账户费用明细
     * @param $openid
     * @return string
     */
    private function accDetail($openid)
    {
        $text = '无你的账户明细信息';
        //TODO:do something
        return $text;
    }

    /**
     * 用户账户信息
     * @param $openid
     * @return string
     */
    private function accInfo($openid)
    {
        $text = '账户信息查询失败';
        //TODO:do sth.
        return $text;
    }

    /**
     * 活动查询，当前时间及以后的活动，下一个活动
     *
     * @return string
     */
    private function actQuery()
    {
        $text = '没有最近的活动信息';
        //TODO:do sth.
        return  $text;
    }

}


?>