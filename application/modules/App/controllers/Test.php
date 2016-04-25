<?php

// namespace App;

use Core\Service;

use Bean\App\TestO;

final class TestController extends Service{
	 /**
     * 默认动作
     * @access public
     */
    public function indexAction()
    {
        // 空的，加载一下模板
        // session_start();
        // $_SESSION['hello']=time();
        
        $testO = new testO();
        $testO->testi="abc";
        $this->sendSuccessMsg($testO,"hello kuleyang");

    }
}
?>