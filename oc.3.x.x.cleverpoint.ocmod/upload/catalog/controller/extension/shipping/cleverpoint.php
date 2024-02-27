<?php
class ControllerExtensionShippingCleverpoint extends Controller {
	public function index() {
	}
	
	public function addScript($route, $args, &$output)
	{
		$this->load->language('extension/shipping/cleverpoint');
		if($this->config->get('shipping_cleverpoint_mode') == 'production') {
			$data = '<script type="text/javascript" src="https://platform.cleverpoint.gr/portal/content/clevermap_v2/script/cleverpoint-map.js"></script></head>';
		} else {
			$data = '<script type="text/javascript" src="https://test.cleverpoint.gr/portal/content/clevermap_v2/script/cleverpoint-map.js"></script></head>';
		};
		$output = str_replace('</head>', $data, $output);
	}	
	
	public function addClevermap($route, &$args, &$output)
	{
		$this->load->language('extension/shipping/cleverpoint');
		$data['layout'] = $this->config->get('shipping_cleverpoint_layout');
		if (strpos($route, 'footer') !== false) {
			$output = str_replace('</body>', $this->load->view('extension/shipping/cleverpoint', $data).'</body>', $output);
		}
	}
	
	public function renderClevermap()
	{	
		$this->load->language('extension/shipping/cleverpoint');
		$data = array();
		
		//get Map Details
		$data['arcgiskey'] = $this->config->get('shipping_cleverpoint_arcgiskey');	
		$data['googlekey'] = $this->config->get('shipping_cleverpoint_googlekey');	
		$data['cleverpointkey'] = $this->config->get('shipping_cleverpoint_mode') == 'production' ? $this->config->get('shipping_cleverpoint_livekey') : $this->config->get('shipping_cleverpoint_testkey');
		
		$data['cleverpoint_shipping_method'] = '';
		if(isset($this->session->data['cleverpoint_station']['shipping_method'])) {
			$data['cleverpoint_shipping_method'] = $this->session->data['cleverpoint_station']['shipping_method']['code'];
		}
		
		$data['defaultLockerAddress'] = null;
		$data['defaultLockerId'] = null;
		
		$data['layout'] = $this->config->get('shipping_cleverpoint_layout');
		$data['addressBar'] = (bool) $this->config->get('shipping_cleverpoint_addressbar');
		$data['headerbar'] = (bool) $this->config->get('shipping_cleverpoint_headerbar');
		$data['pointList'] = (bool) $this->config->get('shipping_cleverpoint_pointlist');
		$data['singleSelect'] = (bool) $this->config->get('shipping_cleverpoint_singleselect');
		$data['pointInfoType'] = $this->config->get('shipping_cleverpoint_pointinfotype') ?? 'docked';
		
		if(isset($this->session->data['cleverpoint_station']['station_address'])) {
			$data['defaultLockerAddress'] = $this->session->data['cleverpoint_station']['station_name'].' ['.$this->session->data['cleverpoint_station']['station_address'].']';
			$data['defaultLockerId'] = $this->session->data['cleverpoint_station']['station_id'];
		}
		
		$data['defaultAddress'] = null;
		if(isset($this->session->data['shipping_address'])) {
			$data['defaultAddress'] = $this->session->data['shipping_address']['address_1'].', '.$this->session->data['shipping_address']['postcode'];
		}
		
		$data['cleverpoint_shipping_methods'] = $this->getShippingMethods();
		
		$this->response->setOutput($this->load->view('extension/shipping/cleverpoint_map', $data));
	}
	
	public function setStation() {
		$this->session->data['cleverpoint_station']['station_name'] = $this->request->post['station_name'];
		$this->session->data['cleverpoint_station']['station_address'] = $this->request->post['station_address'];
		$this->session->data['cleverpoint_station']['station_city'] = $this->request->post['station_city'];
		$this->session->data['cleverpoint_station']['station_postcode'] = $this->request->post['station_postcode'];
		$this->session->data['cleverpoint_station']['station_zone'] = $this->request->post['station_zone'];
		$this->session->data['cleverpoint_station']['station_id'] = $this->request->post['station_id'];
		$this->session->data['cleverpoint_station']['station_prefix'] = $this->request->post['station_prefix'];
		$this->session->data['cleverpoint_station']['is_cod'] = $this->request->post['is_cod'];
	}	
	
	public function getCosts() {
		if(isset($this->session->data['cleverpoint_station']['costs'])) {
			$data['cleverpoint_costs'] = $this->session->data['cleverpoint_station']['costs'];
			$this->response->setOutput($this->load->view('extension/shipping/cleverpoint_costs', $data));
		}
	}
	
	public function setShippingMethod() {
		$json = [];
		if(isset($this->request->post['shipping_method']) && $this->request->post['shipping_method']) {
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			}
		}
		
		if (!$json) {
			$this->session->data['cleverpoint_station']['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
		}
		
		//set shippingMethod
		if ($this->config->get('shipping_cleverpoint_status')) {
			$this->load->model('extension/shipping/cleverpoint');

			$quote = $this->{'model_extension_shipping_cleverpoint'}->getQuote($this->session->data['shipping_address']);

			if ($quote) {
				$this->session->data['shipping_methods']['cleverpoint'] = array(
					'title'      => $quote['title'],
					'quote'      => $quote['quote'],
					'sort_order' => $quote['sort_order'],
					'error'      => $quote['error']
				);
			}
		}
		
		//set Costs
		$this->session->data['cleverpoint_station']['costs'] = '';
		$cleverpoint_costs = array();
		$cp_cost_data = array();
		
		if($this->session->data['cleverpoint_station']['shipping_method']) {
			$cleverpoint_costs[] = array(
				'text' =>  $this->language->get('cleverpoint_shipping_cost'),
				'value' => $this->session->data['cleverpoint_station']['shipping_method']['text']
			);
		}
		
		if(isset($this->session->data['cleverpoint_station']['fee'])) {
			$cleverpoint_costs[] = array(
				'text' => $this->language->get('cleverpoint_fee_cost'),
				'value' => $this->currency->format($this->session->data['cleverpoint_station']['fee'], $this->session->data['currency'])
			);			
		}
		
		
		$this->session->data['cleverpoint_station']['costs'] = $cleverpoint_costs;
	}
	
	public function addOrder(&$route, &$args) {
		
		$this->load->model('extension/shipping/cleverpoint');
		
		if (isset($args[0])) {
			$order_id = $args[0];
		} else {
			$order_id = 0;
		}

		if (isset($args[1])) {
			$order_status_id = $args[1];
		} else {
			$order_status_id = 0;
		}	

		if (isset($args[2])) {
			$comment = $args[2];
		} else {
			$comment = '';
		}
		
		if (isset($args[3])) {
			$notify = $args[3];
		} else {
			$notify = '';
		}
						
		// We need to grab the old order status ID
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			// New Order
			if (
				isset($this->session->data['shipping_method']['code']) &&
				$this->session->data['shipping_method']['code'] == 'cleverpoint.cleverpoint' &&
				isset($this->session->data['cleverpoint_station'])
			) {
				$this->model_extension_shipping_cleverpoint->addCleverpointOrder($order_id, $this->session->data['cleverpoint_station']);
				unset($this->session->data['cleverpoint_station']);
			}
		}
	}
	
	public function paymentMethods(&$route, &$args, &$output) {
		$cleverpoint_cods = $this->config->get('shipping_cleverpoint_payment_modules');
		if (strpos($route, 'payment_method') !== false) {
			if(isset($this->session->data['shipping_method']) && $this->session->data['shipping_method']['code'] == 'cleverpoint.cleverpoint') {
				if(isset($this->session->data['cleverpoint_station']) && isset($this->session->data['cleverpoint_station']['is_cod']) && $this->session->data['cleverpoint_station']['is_cod'] != true) {
					//unset($args['payment_methods']['cod']);
					if(isset($args['payment_methods']) && $args['payment_methods']) {
						foreach($args['payment_methods'] as $code => $payment_method) {
							if(in_array($payment_method['code'],$cleverpoint_cods )) {
								unset($args['payment_methods'][$code]);
							}
						}
					}
				}
			}		
		}
	}	
	
	private function getShippingMethods() {
		$this->load->language('checkout/checkout');
		$this->load->language('extension/shipping/cleverpoint');

		if (isset($this->session->data['shipping_address'])) {
			// Shipping Methods
			$method_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');
			$activeMethods = $this->config->get('shipping_cleverpoint_shipping_modules');

			foreach ($results as $result) {
				
				if($result['code'] == 'cleverpoint') {
					continue;
				}
				
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);

					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($this->session->data['shipping_address']);
					
					//unset xShippingPro Methods Specifically
					if($result['code'] != 'xshippingpro') {
						if (is_array($activeMethods) && !in_array($result['code'], $activeMethods)) {
							continue;
						}						
					} else {
						if(isset($quote['quote']) && $quote['quote']) {
							foreach($quote['quote'] as $code => $quote_data) {
								if (is_array($activeMethods) && !in_array($quote_data['code'], $activeMethods)) {
									unset($quote['quote'][$code]);
								}	
							}
						}
					}
					
					if ($quote) {
						$method_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}

			$sort_order = array();

			foreach ($method_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $method_data);

			$data['cleverpoint_shipping_methods'] = $method_data;
		}

		if (empty($data['cleverpoint_shipping_methods'])) {
			$data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		} else {
			$data['error_warning'] = '';
		}
		
		return $data['cleverpoint_shipping_methods'];
	}	
}