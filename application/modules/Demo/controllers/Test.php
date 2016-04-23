<?php

use Core\ServiceApi;

final class TestController extends ServiceApi{
	 /**
     * 默认动作
     * @access public
     */
    public function indexAction()
    {
        // 空的，加载一下模板
        // session_start();
        // $_SESSION['hello']=time();

    }
}
?>