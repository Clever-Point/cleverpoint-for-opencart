<?php
class CleverPoint {
	private $server = array(
		'test' => 'https://test.cleverpoint.gr/api',
		'production' => 'https://platform.cleverpoint.gr/api'
	);
	
	private $environment;
	private $ApiKey;
	private $loader,$registry,$config;
	
    public function __construct($registry) {		
		
		$this->registry = new Registry();	
		$this->loader = new Loader($registry);
		$this->registry->set('load', $this->loader);
		$this->config = new Config();
		$this->registry->set('config', $this->config);
		
		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
		$registry->set('db', $db);		
		
		$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting");

		$clever_config = [];
		
		foreach ($query->rows as $setting) {
			if (!$setting['serialized']) {
				$clever_config[$setting['key']] = $setting['value'];
			} else {
				$clever_config[$setting['key']] = json_decode($setting['value'], true);
			}
		}
	
        $mode = $clever_config['shipping_cleverpoint_mode'];
        $this->ApiKey = ($mode == 'production') ? $clever_config['shipping_cleverpoint_livekey'] : $clever_config['shipping_cleverpoint_testkey'];
		
		$this->environment = $clever_config['shipping_cleverpoint_mode'];
    }
	
	public function getShipment() {
		$command = '/v1/Shipping/Sample';
		
		$params = array();
		
		$result = $this->execute('GET', $command, $params);
	}	
	
	public function createShipment($json) {
		
		$command = '/v1/Shipping';
		
		$params = array();
		
		$result = $this->execute('POST', $command, $params, $json);
		
		return $result;
	}	
	
	public function getVouchers($vouchers = NULL) {
		
		$command = '/v1/Vouchers/';
		
		$params = array();
		
		$params['awbs'] = $vouchers;
		
		$result = $this->execute('GET', $command, $params);
		
		return $result;
	}	
	
	public function GetPrices($vouchers = NULL) {
		
		$command = '/v1/Shipping/GetPrices';
		
		$params = array();
		
		$result = $this->execute('GET', $command, $params);
		
		return $result;
	}	
		
	public function GetCarriers() {
		
		$command = '/v1/Shipping/GetCarriers';
		
		$params = array();
		
		$result = $this->execute('GET', $command, $params);
		
		return $result;
	}	
	
	public function CancelShipment($voucher = NULL) {
		
		$command = '/v1/Shipping/'.$voucher.'/Cancel';
		
		$params = array();
		
		$result = $this->execute('POST', $command, $params);
		
		return $result;
	}	
	
	public function trackShipping($vouchers = NULL) {
		
		$command = '/v1/ShipmentTracking/'.$vouchers;
		
		$params = array();
		
		$result = $this->execute('GET', $command, $params);
		
		return $result;
	}
	
	public function getApiKey() {
		return $this->ApiKey;
	}
	
	public function config($key) {
		return $this->registry->get('config')->get($key);
	}
	
	private function execute($method, $command, $params = array(), $json = false) {
		
		$this->errors = array();

		if ($method && $command) {
			$curl_options = array(
				CURLOPT_URL => $this->server[$this->environment] . $command,
				CURLOPT_HEADER => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_INFILESIZE => Null,
				CURLOPT_HTTPHEADER => array(),
				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT => 10
			);
			
			$curl_options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
			
			if ($this->ApiKey) {
				$curl_options[CURLOPT_HTTPHEADER][] = 'Authorization: ApiKey ' . $this->ApiKey;
			} else {
				return false;
			}

			switch (strtolower(trim($method))) {
				case 'get':
					$curl_options[CURLOPT_HTTPGET] = true;
					$curl_options[CURLOPT_URL] .= '?' . $this->buildQuery($params, $json);
										
					break;
				case 'post':
					$curl_options[CURLOPT_POST] = true;
					$curl_options[CURLOPT_POSTFIELDS] = $this->buildQuery($params, $json);
					$curl_options[CURLOPT_HTTPHEADER][] = 'Content-Length: '. strlen($this->buildQuery($params, $json));
										
					break;
				case 'patch':
					$curl_options[CURLOPT_POST] = true;
					$curl_options[CURLOPT_POSTFIELDS] = $this->buildQuery($params, $json);
					$curl_options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
										
					break;
				case 'delete':
					$curl_options[CURLOPT_POST] = true;
					$curl_options[CURLOPT_POSTFIELDS] = $this->buildQuery($params, $json);
					$curl_options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
										
					break;
				case 'put':
					$curl_options[CURLOPT_PUT] = true;
					
					if ($params) {
						if ($buffer = fopen('php://memory', 'w+')) {
							$params_string = $this->buildQuery($params, $json);
							fwrite($buffer, $params_string);
							fseek($buffer, 0);
							$curl_options[CURLOPT_INFILE] = $buffer;
							$curl_options[CURLOPT_INFILESIZE] = strlen($params_string);
						} else {
							$this->errors[] = array('name' => 'FAILED_OPEN_TEMP_FILE', 'message' => 'Unable to open a temporary file');
						}
					}
					
					break;
				case 'HEAD':
					$curl_options[CURLOPT_NOBODY] = true;
					
					break;
				default:
					$curl_options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
			}
			
			$ch = curl_init();
			
			curl_setopt_array($ch, $curl_options);
			$response = curl_exec($ch);
			
			if (curl_errno($ch)) {
				$curl_code = curl_errno($ch);
				
				$constant = get_defined_constants(true);
				$curl_constant = preg_grep('/^CURLE_/', array_flip($constant['curl']));
				
				$this->errors[] = array('name' => $curl_constant[$curl_code], 'message' => curl_strerror($curl_code));
			}
			
			$head = '';
			$body = '';
			
			$parts = explode("\r\n\r\n", $response, 3);
			
			if (isset($parts[0]) && isset($parts[1])) {
				if (($parts[0] == 'HTTP/1.1 100 Continue') && isset($parts[2])) {
					list($head, $body) = array($parts[1], $parts[2]);
				} else {
					list($head, $body) = array($parts[0], $parts[1]);
				}
            }
			
            $response_headers = array();
            $header_lines = explode("\r\n", $head);
            array_shift($header_lines);
			
            foreach ($header_lines as $line) {
                list($key, $value) = explode(':', $line, 2);
                $response_headers[$key] = $value;
            }
			
			curl_close($ch);
			
			if (isset($buffer) && is_resource($buffer)) {
                fclose($buffer);
            }

			$this->last_response = json_decode($body, true);
			
			return $this->last_response;		
		}
	}
	
	private function buildQuery($params, $json) {
		if (is_string($params)) {
            return $params;
        }
		
		if ($json) {
			return json_encode($json);
		} else {
			return http_build_query($params);
		}
    }
}