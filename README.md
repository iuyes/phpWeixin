phpWeixin
=========

使用php开发微信公众号的框架，使用此框架只需做少许修改即可开发自己的微信公众号应用。

##使用步骤
根据如下的步骤，你可以快速的让你的微信公众号开发模式运行起来，然后你可以参照本文档中的【开发说明】来针对你的功能进行开发。

1. clone本项目或者下载本项目的zip文件到本地。

2. 一共有4个文件，wxapi.php,wx/wx.php,wx/Wechat.php,class.Service.php(暂时不用)

3. 打开wxapi.php,找到倒数第二行,将lejian修改为你自己的公众号token,此token在你进入微信公众平台的开发模式之后，会要你设置一个。代码如下：

```
  $wechat = new MyWechat('lejian', TRUE);
```

4. 将这几个文件放在你的网站根目录下，假如你的网站是http://www.***.com,那么http://www.***.com/wxapi.php必须是可以访问的。

5. 进入微信公众平台，登录你的公众号，然后打开开发者模式，在开发者模式的URL中输入http://www.***.com/wxapi.php，这个url即使你的微信开发服务的入口。

6. 使用手机登录你的微信，在公众号中输入任意的字母，如果得到自动的回复，那么表示运行正常。得到的是一个菜单。

##开发说明
*  如果你要开发自己的公众号服务，那么你基本不用修改wxapi.php和Wechat.php这两个文件。
*  修改wx.php，在wx.php中标有todo的地方填充你自己的逻辑即可。
*  如果你是服务号，那么你可以自定义菜单，这时候你可以不用文字模式，使用菜单模式，修改wxapi.php中的onclick方法，根据你菜单的key值来编写具体的业务。
*  class.Service.php是为wx.php服务的，你在这里定义你的业务实体类，或者你也可以使用多个单独的实体类。


  有问题可以email:truecn@gmail.com(truecn@gmail.com)
