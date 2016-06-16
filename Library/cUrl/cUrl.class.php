<?php
/**
 * cUrl扩展实现http Get、Post方法的封装
 * @authors Jea杨 (JJonline@JJonline.cn)
 * @date    2016-06-03 10:57:51
 * @version $Id$
 */
namespace Library\cUrl;
class cUrl {
    protected $_useragent          = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';                          //UserAgent 头信息  这里默认提供的是win7_x86下的chrome50
    protected $_url;                                 //cUrl请求的Url
    protected $_timeout            = 30;             //cUrl请求的超时时间 默认30秒超时
    protected $_isPost             = false;          //标记这个cUrl请求的方式为Post 默认为Get
    protected $_postFields;                          //若$_isPost为真则设置Post的数据体
    protected $_referer            = "";             //设置cUrl请求的http头referer来源

    protected $_hasHeader          = false;          //输出结果是否包含header头信息
    protected $_hasBody            = false;          //输出结果不包含body部分，若为true请求方式将变成HEAD
    protected $_status;
    public    $authentication      = false;          //是否需要进行请求认证
    public    $auth_name           = '';             //authentication设置为true时有效 http认证用户名
    public    $auth_pass           = '';             //authentication设置为true时有效 http认证密码

    /**
     * 设置请求的url
     * @param string $url 需要请求的url
     */
    public function setUrl($url) {
        $this->_url      = $url;
    }

    /**
     * 设置请求的超时时间
     * @param int $time 请求超时时间 单位：秒
     */
    public function setTimeOut($time) {
        $this->_timeout  = $time;
    }

    /**
     * 设置Post方法的Post数据体
     * @param mixed $postFields Post数据体
     */
    public function setPostData($postFields) {
        $this->_isPost     = true;
        $this->_postFields = $postFields;
    }
}