<?php

namespace Bean\App;

class TestO implements \JsonSerializable {
	public $testi;

	public function jsonSerialize() {
		return [
			"testi"=>$this->testi,
		];
	}
}