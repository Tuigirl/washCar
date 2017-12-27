<?php
namespace Wechat\Model;

class WechatModel extends CommonModel {

    public $openid;
    public $developerID;

    /**
     * 推送图文消息
     */

    public function sendPicMsg($arr){
        $template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>" . count($arr) . "</ArticleCount>
						<Articles>";
        foreach ($arr as $k => $v) {
            $template .= "<item>
							<Title><![CDATA[" . $v['title'] . "]]></Title>
							<Description><![CDATA[" . $v['description'] . "]]></Description>
							<PicUrl><![CDATA[" . $v['picUrl'] . "]]></PicUrl>
							<Url><![CDATA[" . $v['url'] . "]]></Url>
							</item>";
        }

        $template .= "</Articles>
						</xml> ";
        echo sprintf($template, $this->openid, $this->developerID, SYS_TIME, 'news');
    }


}