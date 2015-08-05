<?php
# The homepage URL for this framework is:
#
#	http://www.openwall.com/phpass/
#
# @date 2015-8-5 Change For PHP5.3+ By JJonline@JJonline.Cn
# @use  实例化加密类
# @use  $hashPass = new PasswordHash();
# @use  加密（或者说获得存储在数据库中的hash值，用于下次对比）
# @use  $passHash = $hashPass->HashPassword('password String');
# @use  对比密码(一般是先实例化PasswordHash)
# @use  $isCheck  = $hashPass->CheckPassword('password String',$passHash);
class PasswordHash {
	private $itoa64;
	private $iteration_count_log2;
	private $portable_hashes;
	private $random_state;

	/**
	 * 构造函数 不再使用与类同名的方法名
	 * @param int $iteration_count_log2 加密深度，取值5-30
	 * @param boolean $portable_hashes 是否支持hash对比在不同平台迁移
	 */
	public function __construct($iteration_count_log2=8, $portable_hashes=true) {
		$this->itoa64 				= './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		if($iteration_count_log2 < 4 || $iteration_count_log2 > 31) {
			$iteration_count_log2 = 8;
		}			
		$this->iteration_count_log2 = $iteration_count_log2;
		$this->portable_hashes 		= $portable_hashes;
		$this->random_state 		= microtime();
		if(function_exists('getmypid')) {
			$this->random_state    .= getmypid();
		}			
	}

	/**
	 * 产生随机字符串
	 * @param int $count 随机串长度
	 */
	public function get_random_bytes($count) {
		$output = '';
		if(is_readable('/dev/urandom') && ($fh = @fopen('/dev/urandom', 'rb'))) {
			$output = fread($fh, $count);
			fclose($fh);
		}
		if(strlen($output) < $count) {
			$output = '';
			for($i = 0; $i < $count; $i += 16) {
				$this->random_state =
				    md5(microtime() . $this->random_state);
				$output .=
				    pack('H*', md5($this->random_state));
			}
			$output = substr($output, 0, $count);
		}
		return $output;
	}

	public function encode64($input, $count) {
		$output = '';
		$i = 0;
		do {
			$value = ord($input[$i++]);
			$output .= $this->itoa64[$value & 0x3f];
			if ($i < $count)
				$value |= ord($input[$i]) << 8;
			$output .= $this->itoa64[($value >> 6) & 0x3f];
			if ($i++ >= $count)
				break;
			if ($i < $count)
				$value |= ord($input[$i]) << 16;
			$output .= $this->itoa64[($value >> 12) & 0x3f];
			if ($i++ >= $count)
				break;
			$output .= $this->itoa64[($value >> 18) & 0x3f];
		} while ($i < $count);
		return $output;
	}

	private function gensalt_private($input) {
		$output  = '$P$';
		$output .= $this->itoa64[min($this->iteration_count_log2 + ((PHP_VERSION >= '5') ? 5 : 3), 30)];
		$output .= $this->encode64($input, 6);
		return $output;
	}

	private function crypt_private($password, $setting)	{
		$output = '*0';
		if (substr($setting, 0, 2) == $output) {
			$output = '*1';
		}
		$id 		= substr($setting, 0, 3);
		if ($id != '$P$' && $id != '$H$') {
			return $output;
		}
		$count_log2 = strpos($this->itoa64, $setting[3]);
		if ($count_log2 < 7 || $count_log2 > 30) {
			return $output;
		}
		$count = 1 << $count_log2;
		$salt  = substr($setting, 4, 8);
		if (strlen($salt) != 8) {
			return $output;
		}
		if (PHP_VERSION >= '5') {
			$hash = md5($salt . $password, true);
			do {
				$hash = md5($hash . $password, true);
			} while (--$count);
		} else {
			$hash = pack('H*', md5($salt . $password));
			do {
				$hash = pack('H*', md5($hash . $password));
			} while (--$count);
		}
		$output  = substr($setting, 0, 12);
		$output .= $this->encode64($hash, 16);
		return $output;
	}

	private function gensalt_extended($input) {
		$count_log2 = min($this->iteration_count_log2 + 8, 24);
		$count      = (1 << $count_log2) - 1;
		$output     = '_';
		$output    .= $this->itoa64[$count & 0x3f];
		$output    .= $this->itoa64[($count >> 6) & 0x3f];
		$output    .= $this->itoa64[($count >> 12) & 0x3f];
		$output    .= $this->itoa64[($count >> 18) & 0x3f];
		$output    .= $this->encode64($input, 3);
		return $output;
	}

	private function gensalt_blowfish($input) {
		$itoa64   = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$output   = '$2a$';
		$output  .= chr(ord('0') + $this->iteration_count_log2 / 10);
		$output  .= chr(ord('0') + $this->iteration_count_log2 % 10);
		$output  .= '$';

		$i = 0;
		do {
			$c1 = ord($input[$i++]);
			$output .= $itoa64[$c1 >> 2];
			$c1 = ($c1 & 0x03) << 4;
			if ($i >= 16) {
				$output .= $itoa64[$c1];
				break;
			}

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 4;
			$output .= $itoa64[$c1];
			$c1 = ($c2 & 0x0f) << 2;

			$c2 = ord($input[$i++]);
			$c1 |= $c2 >> 6;
			$output .= $itoa64[$c1];
			$output .= $itoa64[$c2 & 0x3f];
		} while (1);
		return $output;
	}

	/**
	 * 产生字符串的hash串::对字符串加密；一个字符串对应多个hash
	 * @param string $password
	 * @return string
	 */
	public function HashPassword($password) {
		$random = '';
		if(CRYPT_BLOWFISH == 1 && !$this->portable_hashes) {
			$random = $this->get_random_bytes(16);
			$hash   = crypt($password, $this->gensalt_blowfish($random));
			if(strlen($hash) == 60) {
				return $hash;
			}				
		}
		if(CRYPT_EXT_DES == 1 && !$this->portable_hashes) {
			if(strlen($random) < 3) {
				$random = $this->get_random_bytes(3);
			}				
			$hash 		= crypt($password, $this->gensalt_extended($random));
			if(strlen($hash) == 20) {
				return $hash;
			}				
		}
		if(strlen($random) < 6) {
			$random 	= $this->get_random_bytes(6);
		}
		$hash 			= $this->crypt_private($password,$this->gensalt_private($random));
		if(strlen($hash) == 34) {
			return $hash;
		}
		return '*';
	}

	/**
	 * 对比字符与该密码字符串与hash值是否一致
	 * @param string $password     密码明文
	 * @param string $stored_hash  保存的hash值
	 * @return boolean
	 */
	public function CheckPassword($password, $stored_hash)	{
		$hash = $this->crypt_private($password, $stored_hash);
		if ($hash[0] == '*') {
			$hash = crypt($password, $stored_hash);
		}
		return $hash == $stored_hash;
	}
}