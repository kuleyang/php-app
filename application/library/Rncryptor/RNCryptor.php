<?php

namespace Rncryptor;

abstract class RNCryptor {

	protected $_settings;
	protected $_options;

	function RNCryptor($op=1){
		$this->_options = $op;
	}
	protected function _configureSettings($version,$options=0) {
		if($options == 0){
			$options = $this->_options;
		}

		$settings = new \stdClass();
		
		$settings->algorithm = MCRYPT_RIJNDAEL_128;
		$settings->saltLength = 8;
		$settings->ivLength = 16;

		$settings->pbkdf2 = new \stdClass();
		$settings->pbkdf2->prf = 'sha1';
		$settings->pbkdf2->iterations = 10000;
		$settings->pbkdf2->keyLength = 32;
		
		$settings->hmac = new \stdClass();
		$settings->hmac->length = 32;

		switch ($version) {
			case 1:
				$settings->mode = 'cbc';
				$settings->options = 1;
				$settings->hmac->includesHeader = false;
				$settings->hmac->algorithm = 'sha256';
				$settings->hmac->includesPadding = false;
				break;

			case 2:
					switch ($options) {
						case 1://ios
							$settings->mode = 'cbc';
							$settings->options = 1;
							$settings->hmac->includesHeader = true;
							$settings->hmac->algorithm = 'sha256';
							$settings->hmac->includesPadding = false;
							break;
						case 2://android
							$settings->mode = 'cbc';
							$settings->options = 2;
							$settings->pbkdf2->keyLength = 16;
							$settings->hmac->includesHeader = true;
							$settings->hmac->algorithm = 'sha1';
							$settings->hmac->length = 20;
							$settings->hmac->includesPadding = false;
							break;
						default:
							throw new \Exception('Unsupported schema options ' . $options);
							break;
					}
					break;


			default:
				throw new \Exception('Unsupported schema version ' . $version);
		}
		
		$this->_settings = $settings;
	}
	
	/**
	 * Encrypt or decrypt using AES CTR Little Endian mode
	 */
	protected function _aesCtrLittleEndianCrypt($payload, $key, $iv) {

		$numOfBlocks = ceil(strlen($payload) / strlen($iv));
		$counter = '';
		for ($i = 0; $i < $numOfBlocks; ++$i) {
			$counter .= $iv;

			// Yes, the next line only ever increments the first character
			// of the counter string, ignoring overflow conditions.  This
			// matches CommonCrypto's behavior!
			$iv[0] = chr(ord(substr($iv, 0, 1)) + 1);
		}

		return $payload ^ mcrypt_encrypt($this->_settings->algorithm, $key, $counter, 'ecb');
	}

	protected function _generateHmac(\stdClass $components, $password) {

		$hmacMessage = '';
		if ($this->_settings->hmac->includesHeader) {
			$hmacMessage .= $components->headers->version
							. $components->headers->options
							. $components->headers->salt
							. $components->headers->hmacSalt
							. $components->headers->iv;
		}

		$hmacMessage .= $components->ciphertext;

		$hmacKey = $this->_generateKey($components->headers->hmacSalt, $password);
	
		$hmac = hash_hmac($this->_settings->hmac->algorithm, $hmacMessage, $hmacKey, true);

		if ($this->_settings->hmac->includesPadding) {
			$hmac = str_pad($hmac, $this->_settings->hmac->length, chr(0));
		}
	
		return $hmac;
	}

	protected function _generateKey($salt, $password) {
		return hash_pbkdf2($this->_settings->pbkdf2->prf, $password, $salt, $this->_settings->pbkdf2->iterations, $this->_settings->pbkdf2->keyLength, true);
	}

}


