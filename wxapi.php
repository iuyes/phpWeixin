<?php
/**
 * 微信服务的入口，即放在你网站根目录，此文件是在微信开发者平台中需要填写的访问接口文件
 * 如微信开发者中心中填入的url：http://www.***.com/wxapi.php。则此文件就是在www.***.com的根目录下。
 *
 * 你要使用，那么此文件不需要多大的修改，只需要修改load方法中的具体服务类名称即可。
 *
 * 微信公众平台 PHP SDK 示例文件
 *
 * @author NetPuter <netputer@gmail.com>     create
 *          chengn(https://github.com/chengn)    modify
 */
header("Content-Type: text/html;charset=utf-8");

require('wx/Wechat.php');//核心服务文件
require('wx/wx.php');    //业务服务文件




/**
 * 微信公众平台入口
 */
class MyWechat extends Wechat
{

    /**
     * 加载服务对象,具体公众号的服务对象
     */
    protected function load()
    {
        //TODO:此处需要修改为具体的公众号服务，比如这里是LeJian，替换成你自己的服务类即可,以服务号命名。
        //载入具体的服务类
        $this->service = new LeJian();
    }

    /**
     * 用户关注时触发，回复「欢迎关注」
     *
     * @return void
     */
    protected function onSubscribe()
    {
        $openid = $this->getRequest('fromusername');
        $text = $this->service->subscribe($openid);
        $this->responseText($text);
    }

    /**
     * 用户取消关注时触发
     *
     * @return void
     */
    protected function onUnsubscribe()
    {
    }

    /**
     * 收到文本消息时触发，回复收到的文本消息内容
     * 通过数据库来匹配指定的回复
     * @return void
     */
    protected function onText()
    {
        $openid = $this->getRequest('fromusername');
        $input = $this->getRequest('content');
        $text = $this->service->text($openid, $input);
        $this->responseText($text);
    }

    /**
     * 收到图片消息时触发，回复由收到的图片组成的图文消息
     *
     * @return void
     */
    protected function onImage()
    {
        $items = array(
            new NewsResponseItem('标题一', '描述一', $this->getRequest('picurl'), $this->getRequest('picurl')),
            new NewsResponseItem('标题二', '描述二', $this->getRequest('picurl'), $this->getRequest('picurl')),
        );

        $this->responseNews($items);
    }

    /**
     * 收到地理位置消息时触发，回复收到的地理位置
     *
     * @return void
     */
    protected function onLocation()
    {
        $num = 1 / 0;
        // 故意触发错误，用于演示调试功能

        $this->responseText('收到了位置消息：' . $this->getRequest('location_x') . ',' . $this->getRequest('location_y'));
    }

    /**
     * 收到链接消息时触发，回复收到的链接地址
     *
     * @return void
     */
    protected function onLink()
    {
        $this->responseText('收到了链接：' . $this->getRequest('url'));
    }

    /**
     * 收到未知类型消息时触发，回复收到的消息类型
     *
     * @return void
     */
    protected function onUnknown($type)
    {
        $this->responseText('收到未知类型 ' . $type);
    }

    /**
     *  click事件响应，自定义菜单中按钮的响应。
     * 这里有几个示例，在开发服务号的时候，可以定制菜单，菜单中的key值对应到这里来。
     *
     * 如下代码中的btn_tools_acc等就是作者在微信服务号自定义菜单中的key值。在这里针对具体的按钮座处理。
     */
    protected function onClick()
    {
        switch ($this->getRequest('eventkey')) {
            case 'btn_tools_acc':
                $this->onBtnToolsAcc();
                break;
            case 'btn_tools_fee':
                $this->onBtnToolsFee();
                break;
            case 'btn_act_query':
                $this->onBtnActQuery();
                break;
            default:
                $this->onUnknown('click');
                break;
        }
    }

    /**
     * 账户查询按钮
     *
     * @return void
     */
    protected function onBtnToolsAcc()
    {
        $openid = $this->getRequest('fromusername');
        $text = $this->service->accInfo($openid);
        $this->responseText($text);
    }

    /**
     * 费用明细按钮
     *
     * @return void
     */
    protected function onBtnToolsFee()
    {
        $openid = $this->getRequest('fromusername');
        $text = $this->service->accInfo($openid);
        $this->responseText($text);
    }

    /**
     * 活动查询 按钮
     * @return void
     */
    protected function onBtnActQuery()
    {
        $text = $this->service->actQuery();
        $this->responseText($text);
    }

}

$wechat = new MyWechat('lejian', TRUE);
$wechat->run();

