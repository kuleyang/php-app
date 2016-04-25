<?php

namespace Bean;

class ReturnObj implements \JsonSerializable {
	public $code;
	public $msg;
	public $data;



	function __construct($data,$code,$msg=''){
		$this->data = $data;
		$this->code = $code;
		$this->msg = $msg;
	}

	public function jsonSerialize() {
		return [
			"code"=>$this->code,
			"msg"=>$this->msg,
			"data"=>$this->data
		];
	}
}