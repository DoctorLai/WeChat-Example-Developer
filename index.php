<?php
define("TOKEN", "需要替换掉");

$wechatObj = new WeChatJustYY();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg(); //执行WeChatJustYY类下的responseMsg()方法
} else {
    $wechatObj->valid(); //执行WeChatJustYY类下的valid()方法
}

class WeChatJustYY {
    public function valid() {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }
    
    public function getWelcomeMessage($user) {
      return "您好! 感谢关注 【小赖子的英国生活和资讯】                                      
公众号：justyyuk
微信号: ACM-er 
博客: https://justyy.com  
畅购英伦: https://happyukgo.com
  
- 24点扑克算术求解: 请输入4个数字 按空格隔开 比如 10 5 10 5
- 名人名言(随机, 英文): 请输入 fortune 或 名言
- 英镑兑人民币汇率: 请输入 gbp 或 rmb 或 英镑 或 人民币
- 查看IP地址: 请输入 ip
- 随机地址: 请输入 address 或者 地址
- 帮助：请输入 help 或 帮助 
- 打赏一下我 用您的几P钱狠狠的砸我吧！ 请输入 打赏 或 money

更多功能正在开发中 敬请期待 多谢多谢!   
      ";
    } 
    
    public function responseMsg() {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //extract post data
        if (!empty($postStr)) {
            $postObj      = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername   = $postObj->ToUserName;
            $keyword      = trim($postObj->Content);
            $cmd          = strtolower($keyword);
            $msgType      = $postObj->MsgType;
            $picUrl       = $postObj->PicUrl;
            $mediaId      = $postObj->MediaId;            
            $textTpl      = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
             $picTpl = "<xml>
                       <ToUserName><![CDATA[%s]]></ToUserName>
                       <FromUserName><![CDATA[%s]]></FromUserName>
                       <CreateTime>%s</CreateTime>
                       <MsgType><![CDATA[%s]]></MsgType>
                       <ArticleCount>1</ArticleCount>
                       <Articles>
                       <item>
                       <Title><![CDATA[%s]]></Title> 
                       <Description><![CDATA[%s]]></Description>
                       <PicUrl><![CDATA[%s]]></PicUrl>
                       <Url><![CDATA[%s]]></Url>
                       </item>
                       </Articles>
                       <FuncFlag>1</FuncFlag>
                  </xml> ";              
            switch ($msgType) {
                case "event";
                    $event = $postObj->Event;
                    $contentStr = $this->getWelcomeMessage($fromUsername);
                    if ($event == "subscribe") {
                    } elseif ($event == "unsubscribe") {
                    }
                    echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    break;
                case "image";                
                    $contentStr = "你的图片很棒！";
                    echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    break;
                case "voice":
                    $contentStr = "我不知道你在说什么！还是发送文字吧！\n";
                    echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    break;
                case "video";
                    $contentStr = "你的视频很棒！";
                    echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    break;
                case "location";
                    break;
                case "link";
                    $contentStr = "你的链接有病毒吧！";
                    echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    break;
                case "text":                    
                    if (($cmd == 'help') || ($keyword == '帮助')) {
                      $contentStr = $this->getWelcomeMessage($username);
                      echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    } elseif (($cmd == 'fortune') || ($keyword == '名言')) {
                      $contentStr = shell_exec('/usr/games/fortune');
                      echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    } elseif (($cmd == 'address') || ($keyword == '地址')) {
                      $contentStr = shell_exec('rig');
                      echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    } elseif ($cmd == 'ip') {
                      require_once('/var/www/ip.php');
                      $contentStr = "您的IP地址是: " . get_ip_address();
                      echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    } elseif (($cmd == 'rmb') || ($keyword == '人民币') || ($cmd == 'gbp') || ($keyword == '英镑')) {
                      $contentStr = "1英镑可换 ".file_get_contents('/var/www/rates/gbp-rmb.txt'). " 人民币";
                      echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);                      
                    } elseif (($cmd == 'beauty') || ($keyword == '美女')) {
                      $title = '雨天';
                      $description = '雨天';
                      $image = 'https://uploadbeta.com/_s/upload/2016/05/21/8fe2e20a0afc58ddbb67d5b06fd93cc6.jpg';
                      $turl = $image;
                      echo sprintf($picTpl, $fromUsername, $toUsername, $time, "news", $title, $desription, $image, $turl);
                    } elseif (($cmd == 'money') || ($keyword == '打赏')) {
                      $title = '打赏一下我 用您的几P钱狠狠的砸我吧！';
                      $description = '打赏一下我 用您的几P钱狠狠的砸我吧！';
                      $image = 'https://justyy.com/jpg/happyukgo-payment.jpg';
                      $turl = $image;
                      echo sprintf($picTpl, $fromUsername, $toUsername, $time, "news", $title, $desription, $image, $turl);
                    } else {
                      $flag = true;
                      $arr = explode(' ', $keyword);
                      if (count($arr) == 4) {
                        $a = (integer)trim($arr[0]);
                        $b = (integer)trim($arr[1]);
                        $c = (integer)trim($arr[2]);
                        $d = (integer)trim($arr[3]);
                        if (($a > 0) && ($a <= 15) &&  
                            ($b > 0) && ($b <= 15) &&
                            ($c > 0) && ($c <= 15) &&
                            ($d > 0) && ($d <= 15)) {
                          $url = "https://helloacm.com/api/24/?a=$a&b=$b&c=$c&d=$d";
                          $data = json_decode(trim(file_get_contents($url)), true);
                          if ($data) {
                            $cnt = $data['cnt'];
                            var_dump($data);
                            if ($cnt == 0) {
                              echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', "24点扑克无解");
                            } else {
                              $contentStr =  "共" . $cnt . '种解' . "\n"; 
                              $i = 1; 
                              foreach ($data['result'] as $v) {
                                $contentStr .= "第 $i 种解: ". $v . "\n";
                                $i ++;
                              }
                              echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);                              
                            }
                            $flag = false;  
                          }    
                        }
                      }
                      if ($flag) {
                        $contentStr = $this->getWelcomeMessage($username) . "\n" . shell_exec('figlet ' . escapeshellarg($keyword));
                        echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                      }
                    }
                    break;
                default;
                    $contentStr = "此项功能尚未开发";
                    echo sprintf($textTpl, $fromUsername, $toUsername, time(), 'text', $contentStr);
                    break;
            }
        } else {
            echo "https://justyy.com";
            exit;
        }
    }

    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];
        $token     = TOKEN;
        $tmpArr    = array(
            $token,
            $timestamp,
            $nonce
        );
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        return $tmpStr == $signature;
    }
}
?>
