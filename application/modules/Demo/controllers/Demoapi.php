<?php
/**
 *
 * Demoapi.php
 *
 * 导出API示例
 *
 * @package Demo
 */
use Core\ServiceApi;

/**
 * Class DemoApiController
 * @package Demo
 */
final class DemoApiController extends ServiceApi
{
    /**
     * 初始化由yaf自动调用
     * @access public
     */
    public function init()
    {
        parent::init();
    }

    /**
     * 导出的getData方法
     * @access public
     */
    public function getDataAction()
    {
        $this->sendOutput("这是通过远程调用返回的数据(Yar)传递的参数: args => " . $this->getRequest()->getParam('args'));
    }
}