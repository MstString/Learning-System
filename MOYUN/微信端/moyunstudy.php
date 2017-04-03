<?php
/**
  * wechat php test
  */

//define your token
//定义token密钥参数
define("TOKEN", "mystudy");

//实例化一个微信对象
$wechatObj = new wechatCallbackapiTest();

if (isset($_GET['echostr'])) {
    //验证，调用valid方法
    $wechatObj->valid();
}else{
    //调用responseMsg方法开启自动回复功能
    $wechatObj->responseMsg();
}

//定义wechatCallbackapiTest类
class wechatCallbackapiTest
{
    //验证方法，对接微信公众平台
	public function valid()
    { 
        //得到随机字符串
        $echoStr = $_GET["echostr"];

        //valid signature , option
        //进行用户签名验证，如果成功，返回接受的随机字符串
        if($this->checkSignature()){
        	echo $echoStr;
            //强制退出 
        	exit;
        }
    }
  
    //定义自动回复功能函数
    public function responseMsg()
    {
		//get post data, May be due to the different environments
        //接受用户发送的XML数据
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
        //判断是否为空
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);

                //通过simplexml解析XML数据
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                //微信手机端
                $fromUsername = $postObj->FromUserName;
                //微信公众平台
                $toUsername = $postObj->ToUserName;
                //用户发送的关键词
                $keyword = trim($postObj->Content);
                //接受用户的消息类型
                $msgType = $postObj->MsgType;
                //时间戳
                $time = time();

                //XML数据组装的文本消息发送模板
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";  
                $musiTpl = "<xml>
                            <ToUserName><![CDATA[toUser]]></ToUserName>
                            <FromUserName><![CDATA[fromUser]]></FromUserName>
                            <CreateTime>12345678</CreateTime>
                            <MsgType><![CDATA[music]]></MsgType>
                            <Music>
                            <Title><![CDATA[TITLE]]></Title>
                            <Description><![CDATA[DESCRIPTION]]></Description>
                            <MusicUrl><![CDATA[MUSIC_Url]]></MusicUrl>
                            <HQMusicUrl><![CDATA[HQ_MUSIC_Url]]></HQMusicUrl>
                            <ThumbMediaId><![CDATA[media_id]]></ThumbMediaId>
                            </Music>
                            </xml>";
                
                $photoTpl ="<xml>
                            <ToUserName><![CDATA[toUser]]></ToUserName>
                            <FromUserName><![CDATA[fromUser]]></FromUserName>
                            <CreateTime>1348831860</CreateTime>
                            <MsgType><![CDATA[image]]></MsgType>
                            <PicUrl><![CDATA[this is a url]]></PicUrl>
                            <MediaId><![CDATA[media_id]]></MediaId>
                            <MsgId>1234567890123456</MsgId>
                            </xml>";

                $vedioTpl ="<xml>
                            <ToUserName><![CDATA[toUser]]></ToUserName>
                            <FromUserName><![CDATA[fromUser]]></FromUserName>
                            <CreateTime>1357290913</CreateTime>
                            <MsgType><![CDATA[video]]></MsgType>
                            <MediaId><![CDATA[media_id]]></MediaId>
                            <ThumbMediaId><![CDATA[thumb_media_id]]></ThumbMediaId>
                            <MsgId>1234567890123456</MsgId>
                            </xml>";

               if ($msgType=='text') {
                    //判断用户发送的关键词是否为空                   
                    if(!empty($keyword))
                    {
                        //定义回复消息类型，当前回复文本类型（text）
                        $msgType = "text";
                        //定义回复内容，
                        $contentStr = "您发送的是文本消息";
                        //格式化字符串
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        //返回XML数据给手机端 
                        echo $resultStr;
                    } 
                    else
                {
                echo "Input something...";
                }
                                
            }
        }
        else 
        {
        	echo "";
        	exit;
        }
    }
		
    //定义checkSignnature方法    
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        //判断密钥参数是否定义
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        //接受微信加密签名
        $signature = $_GET["signature"];
        //接受时间戳
        $timestamp = $_GET["timestamp"];
        //接受随机数 
        $nonce = $_GET["nonce"];
        		
        //把TOKEN常量赋值给￥token变量
		$token = TOKEN;

        //把3个相关参数组装成数组array
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        //进行字典法排序
		sort($tmpArr, SORT_STRING);
        //把排序后的数组转化为字符串
		$tmpStr = implode( $tmpArr );
        //通过哈希算法对参数进行加密
		$tmpStr = sha1( $tmpStr );
		
        //与加密签名进行对比
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>