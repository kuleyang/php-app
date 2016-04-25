<?php


namespace Core;

use Yaf\Application;
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Request\Http as Request_Http;
use Sender\Http as SenderHttp;
use Output\JsonOutput;
use Output\MsgpackOutput;
use Output\FormatOutput;
use PhpSecure\Crypt\AES;
use Console\Console;

use Rncryptor\RNDecryptor;
use Bean\ReturnObj;

/**
 * Class Service
 *
 * 普通服务的Controller基类
 *
 * @package Core
 */
abstract class Service extends Controller_Abstract
{
    /**
     * @var string
     */
    public $output_format;



    /**
     * 各平台的密钥
     * @var array
     */
    private static $dataKey = array(1=>'nlDF2WVkaRFo1SKSwYmTSQXqqBtt3JO',2=>'yMv5zoSx4waLDj4rdfgf6LSTTGd8exS');
    /**
     * 请求参数
     * @var array
     */
    protected $raw;
    /**
     * 客户端平台
     * @var int
     */
    protected $osf;
    /**
     * 客户端接口版本
     * @var int
     */
    protected $ver;

    /**
     * 是否加密
     * @var int 0加密 1不加密
     */
    protected $rnc;


    /**
     * 业务成功
     * @var int
     */
    const ERR_CODE_SUCCESS    = 0;
    /**
     * 业务失败
     * @var int
     */
    const ERR_CODE_FAILD      = 1;

    /**
     * Service初始化
     */
    public function init() {
        $this->output_format = 'json';
        $http_raw_content = file_get_contents("php://input");
        $request =  $this->getRequest();

        $this->osf = $request->getParam('f');
        $this->ver = $request->getParam('v');
        $this->rnc = $request->getParam('d');
        
        if ($this->rnc==0&&$this->osf>0) {
            $cryptor = new RNDecryptor();
            $raw_data = $cryptor->decrypt($http_raw_content, self::$dataKey[$this->osf]);
        } else {
            $raw_data = $http_raw_content;
        }
        $this->raw = json_decode($raw_data,TRUE);
        Dispatcher::getInstance()->disableView();
    }

    /**
     * 返回当前模块名
     *
     * @access protected
     * @return string
     */
    protected function getModule()
    {
        return $this->getRequest()->module;
    }

    /**
     * 返回当前控制器名
     *
     * @access protected
     * @return string
     */
    protected function getController()
    {
        return $this->getRequest()->controller;
    }

    /**
     * 返回当前动作名
     *
     * @access protected
     * @return string
     */
    protected function getAction()
    {
        return $this->getRequest()->action;
    }

    /**
     * 标准响应输出
     *
     * @access protected
     * @param $response string 响应正文
     * @param string $format 响应输出数据格式
     * @param int $code 返回的http状态码
     * @return void
     */
    protected function sendHttpOutput($response, $format = 'json', $code = 200) {
        if (is_array($format)) {
            $output_format = $format[1];
            $format = $format[0];
        }
        switch($format) {
            case 'json':
                $content = new JsonOutput($response);
                break;
            case 'msgpack':
                $content = new MsgpackOutput($response);
                break;
            case 'format':
                $content = new FormatOutput($response);
                $content->setFormat($output_format);
                break;
            default:
                $content = new JsonOutput($response);
        }
        $sender = new SenderHttp();
        if ($extra_headers = Console::serializeHeaders()) {
            $sender->getHeaders()->addHeaderLine('HTTP-CCS-FIREPHP', $extra_headers);
        }
        $sender->setStatus($code);
        $content($sender);
    }

    /**
     * 设置标准响应http状态码
     *
     * @access protected
     * @param int $code 返回的http状态码
     * @return void
     */
    protected function sendHttpCode($code = 200)
    {
        $sender = new SenderHttp();
        if ($extra_headers = Console::serializeHeaders()) {
            $sender->getHeaders()->addHeaderLine('HTTP-CCS-FIREPHP', $extra_headers);
        }
        $sender->setStatus($code);
        $sender->send();
    }


    protected function sendSuccess($data){
        $this->sendData($data,self::ERR_CODE_SUCCESS);
    }

    protected function sendSuccessMsg($data,$msg){
        $this->sendData($data,self::ERR_CODE_SUCCESS,$msg);
    }
    protected function sendData($data,$code,$msg=''){
        $returnObj = new ReturnObj($data,$code,$msg);
        // var_dump(json_encode($returnObj));
        $this->sendHttpOutput($returnObj);
    }

    protected function sendError($msg,$code=self::ERR_CODE_FAILD){
        $this->sendData(array(),self::ERR_CODE_FAILD,$msg);
    }
}