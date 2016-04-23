<?php
namespace Demo;

use Core\Factory;
use Yaf\Registry;
/**
 * for test
 */
class DemoModel
{
	private $table_name = "demo";


	public function addOne($data){
		$table = Factory::table($this->table_name);
		// $table->sql=;
		$table->insert($data);
		$lastInsertValue = $table->getLastInsertValue();
        if ($lastInsertValue) {
            return $lastInsertValue;
        } else {
            return false;
        }
	}
}