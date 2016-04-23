<?php


namespace Core;

use Yaf\Application;
use Yaf\Dispatcher;
use Yaf\Controller_Abstract;
use PhpSecure\Crypt\AES;
use Exception as Exception;
use Rncryptor\RNDecryptor;

/**
 * Class ServiceApi
 *
 * 导出API的controller基类
 *
 * @package Core
 */
abstract class ServiceApi extends Controller_Abstract
{
    /**
     * @var string
     */
    protected $_output_format;

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
     * ServiceApi初始化
     */
    public function init()
    {
        $http_raw_content = file_get_contents("php://input");
        $request =  $this->getRequest();

        // if(!($request->getParam('f')&&$request->getParam('v')&&$request->getParam('d')){
        //     throw new \Exception("Error Processing Request", 1);
            
        // }
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
        Dispatcher::getInstance()->returnResponse(true);
        Dispatcher::getInstance()->disableView();
        $this->_output_format = 'json';
        var_dump($this->raw);
    }

    /**
     * 标准响应输出
     *
     * @access protected
     * @param mixed $response
     * @param string $format
     * @return void
     */
    protected function sendOutput($response, $format = '') {
        if (empty($format)) {
            if ($this->_output_format) {
                $format = $this->_output_format;
            } else {
                $format = 'json';
            }
        }
        if ($response !== null) {
            if (is_object($response) && method_exists($response, 'toArray')) {
                $response = $response->toArray();
            } elseif ($response instanceof \Traversable) {
                $temp = array();
                foreach ($response as $key => $val) {
                    $temp[$key] = $val;
                }
                $response = $temp;
            }
            switch ($format) {
                case 'serialize':
                    $response = "serialize\r\n\r\n".serialize($response);
                    break;
                case 'plain':
                    $response = "plain\r\n\r\n".print_r($response, true);
                    break;
                case 'json': default:
                    $response = "json\r\n\r\n".json_encode($response);
                    break;
            }
        }

        $this->getResponse()->setBody($response, 'content');
    }

    /**
     * 标准响应错误输出
     *
     * @access protected
     * @param mixed $error
     * @param string $format
     * @return mixed
     */
    protected function getStderr($error, $format = '') {
        if (empty($format)) {
            if ($this->_output_format) {
                $format = $this->_output_format;
            } else {
                $format = 'json';
            }
        }

        if ($error !== null) {
            if (is_object($error) && method_exists($error, 'toArray')) {
                $error = $error->toArray();
            } elseif ($error instanceof \Traversable) {
                $temp = array();
                foreach ($error as $key => $val) {
                    $temp[$key] = $val;
                }
                $error = $temp;
            }
            switch ($format) {
                case 'serialize':
                    $error = "serialize\r\n\r\n".serialize($error);
                    break;
                case 'plain':
                    $error = "plain\r\n\r\n".print_r($error, true);
                    break;
                case 'json': default:
                    $error = "json\r\n\r\n".json_encode($error);
                break;
            }
        }

        return $error;
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
}