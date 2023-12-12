<?php
class ControllerExtensionShippingCleverpoint extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/cleverpoint');

		$this->document->setTitle($this->language->get('heading_title'));
		$data['heading_title'] = $this->language->get('heading_title');

		$this->load->model('setting/setting');
		$this->load->model('extension/shipping/cleverpoint');
		
		$data['user_token'] = $this->session->data['user_token'];
		

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_cleverpoint', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/cleverpoint', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/cleverpoint', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		if (isset($this->request->post['shipping_cleverpoint_total'])) {
			$data['shipping_cleverpoint_total'] = $this->request->post['shipping_cleverpoint_total'];
		} else {
			$data['shipping_cleverpoint_total'] = $this->config->get('shipping_cleverpoint_total');
		}

		if (isset($this->request->post['shipping_cleverpoint_geo_zone_id'])) {
			$data['shipping_cleverpoint_geo_zone_id'] = $this->request->post['shipping_cleverpoint_geo_zone_id'];
		} else {
			$data['shipping_cleverpoint_geo_zone_id'] = $this->config->get('shipping_cleverpoint_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		

		if (isset($this->request->post['shipping_cleverpoint_status'])) {
			$data['shipping_cleverpoint_status'] = $this->request->post['shipping_cleverpoint_status'];
		} else {
			$data['shipping_cleverpoint_status'] = $this->config->get('shipping_cleverpoint_status');
		}

		if (isset($this->request->post['shipping_cleverpoint_sort_order'])) {
			$data['shipping_cleverpoint_sort_order'] = $this->request->post['shipping_cleverpoint_sort_order'];
		} else {
			$data['shipping_cleverpoint_sort_order'] = $this->config->get('shipping_cleverpoint_sort_order');
		}
		
		if (isset($this->request->post['shipping_cleverpoint_testkey'])) {
			$data['shipping_cleverpoint_testkey'] = $this->request->post['shipping_cleverpoint_testkey'];
		} else {
			$data['shipping_cleverpoint_testkey'] = $this->config->get('shipping_cleverpoint_testkey');
		}	

		if (isset($this->request->post['shipping_cleverpoint_fee'])) {
			$data['shipping_cleverpoint_fee'] = $this->request->post['shipping_cleverpoint_fee'];
		} else {
			$data['shipping_cleverpoint_fee'] = $this->config->get('shipping_cleverpoint_fee');
		}		
		
		if (isset($this->request->post['shipping_cleverpoint_mode'])) {
			$data['shipping_cleverpoint_mode'] = $this->request->post['shipping_cleverpoint_mode'];
		} else {
			$data['shipping_cleverpoint_mode'] = $this->config->get('shipping_cleverpoint_mode');
		}			
		
		if (isset($this->request->post['shipping_cleverpoint_layout'])) {
			$data['shipping_cleverpoint_layout'] = $this->request->post['shipping_cleverpoint_layout'];
		} else {
			$data['shipping_cleverpoint_layout'] = $this->config->get('shipping_cleverpoint_layout');
		}			
		
		if (isset($this->request->post['shipping_cleverpoint_livekey'])) {
			$data['shipping_cleverpoint_livekey'] = $this->request->post['shipping_cleverpoint_livekey'];
		} else {
			$data['shipping_cleverpoint_livekey'] = $this->config->get('shipping_cleverpoint_livekey');
		}			
		
		if (isset($this->request->post['shipping_cleverpoint_arcgiskey'])) {
			$data['shipping_cleverpoint_arcgiskey'] = $this->request->post['shipping_cleverpoint_arcgiskey'];
		} else {
			$data['shipping_cleverpoint_arcgiskey'] = $this->config->get('shipping_cleverpoint_arcgiskey');
		}			
		
		if (isset($this->request->post['shipping_cleverpoint_googlekey'])) {
			$data['shipping_cleverpoint_googlekey'] = $this->request->post['shipping_cleverpoint_googlekey'];
		} else {
			$data['shipping_cleverpoint_googlekey'] = $this->config->get('shipping_cleverpoint_googlekey');
		}		
		
		if (isset($this->request->post['shipping_cleverpoint_payment_modules'])) {
			$data['shipping_cleverpoint_payment_modules'] = $this->request->post['shipping_cleverpoint_payment_modules'];
		} else {
			$data['shipping_cleverpoint_payment_modules'] = $this->config->get('shipping_cleverpoint_payment_modules');
		}		
		
		if (isset($this->request->post['shipping_cleverpoint_shipping_modules'])) {
			$data['shipping_cleverpoint_shipping_modules'] = $this->request->post['shipping_cleverpoint_shipping_modules'];
		} else {
			$data['shipping_cleverpoint_shipping_modules'] = $this->config->get('shipping_cleverpoint_shipping_modules');
		}
		
		if (isset($this->request->post['shipping_cleverpoint_addressbar'])) {
			$data['shipping_cleverpoint_addressbar'] = $this->request->post['shipping_cleverpoint_addressbar'];
		} else {
			$data['shipping_cleverpoint_addressbar'] = $this->config->get('shipping_cleverpoint_addressbar');
		}		
		
		if (isset($this->request->post['shipping_cleverpoint_headerbar'])) {
			$data['shipping_cleverpoint_headerbar'] = $this->request->post['shipping_cleverpoint_headerbar'];
		} else {
			$data['shipping_cleverpoint_headerbar'] = $this->config->get('shipping_cleverpoint_headerbar');
		}

		if (isset($this->request->post['shipping_cleverpoint_pointlist'])) {
			$data['shipping_cleverpoint_pointlist'] = $this->request->post['shipping_cleverpoint_pointlist'];
		} else {
			$data['shipping_cleverpoint_pointlist'] = $this->config->get('shipping_cleverpoint_pointlist');
		}

		if (isset($this->request->post['shipping_cleverpoint_pointinfotype'])) {
			$data['shipping_cleverpoint_pointinfotype'] = $this->request->post['shipping_cleverpoint_pointinfotype'];
		} else {
			$data['shipping_cleverpoint_pointinfotype'] = $this->config->get('shipping_cleverpoint_pointinfotype');
		}		
		
		if (isset($this->request->post['shipping_cleverpoint_singleselect'])) {
			$data['shipping_cleverpoint_singleselect'] = $this->request->post['shipping_cleverpoint_singleselect'];
		} else {
			$data['shipping_cleverpoint_singleselect'] = $this->config->get('shipping_cleverpoint_singleselect');
		}

		// Categories
		$this->load->model('catalog/category');

		if (isset($this->request->post['shipping_cleverpoint_categories'])) {
			$categories = $this->request->post['shipping_cleverpoint_categories'];
		} elseif ($this->config->get('shipping_cleverpoint_categories')) {
			$categories = $this->config->get('shipping_cleverpoint_categories');
		} else {
			$categories = array();
		}

		$data['shipping_cleverpoint_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['shipping_cleverpoint_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			}
		}		
		
		// Payment
		$files = glob(DIR_APPLICATION . 'controller/extension/payment/*.php');
		
		$data['payment_modules'] = array();
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				if ($this->config->get('payment_' . $extension . '_status')) {
					$this->load->language('extension/payment/' . $extension);

					$data['payment_modules'][] = array(
						'name'		=> $this->language->get('heading_title'),
						'code'		=> $extension
					);
				}
			}
		}	
		
		// Shipping
		$files = glob(DIR_APPLICATION . 'controller/extension/shipping/*.php');
		
		$data['shipping_modules'] = array();
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				if ($this->config->get('shipping_' . $extension . '_status')) {
					$this->load->language('extension/shipping/' . $extension);
					
					if($extension == 'xshippingpro') {
						$xMethods = $this->model_extension_shipping_cleverpoint->getXshippingProMethods();
						foreach($xMethods as $xMethod) {
							$data['shipping_modules'][] = array(
								'name'		=> $xMethod['name'],
								'code'		=> $xMethod['code']
							);	
						}
					} else {
						$data['shipping_modules'][] = array(
							'name'		=> $this->language->get('heading_title'),
							'code'		=> $extension
						);					
					}
				}
			}
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/cleverpoint', $data));
	}
	
	public function adminMenu(string $route = '', array &$data = []): void {
		if ($this->config->get('shipping_cleverpoint_status')) {
			if ($this->user->hasPermission('access', 'extension/shipping/cleverpoint')) {
				$this->load->language('extension/shipping/cleverpoint');

				$data['menus'][] = [
					'id'       => 'menu-cleverpoint',
					'icon'	   => 'fa-envelope',
					'name'	   => 'Cleverpoint',
					'href'     => $this->url->link('extension/shipping/cleverpoint/report', 'user_token=' . $this->session->data['user_token'], true),
					'children' => []
				];
			}
		}
	}	
	
	public function injectOrder(&$route, &$args, &$output) {
		if (strpos($output, '<td>Cleverpoint:') !== false) {
			$output = str_replace('<td>Cleverpoint:', '<td><a href="'.$this->url->link('extension/shipping/cleverpoint/report', 'user_token=' . $this->session->data['user_token'], true).'" class="btn btn-primary">Cleverpoint Orders</a></br>Cleverpoint:', $output);
		}
	}		
	
	public function report() {
		
		$this->load->language('extension/shipping/cleverpoint');
		$this->load->language('sale/order');
		
		$this->load->model('extension/shipping/cleverpoint');
		$this->load->model('sale/order');

		$this->document->setTitle($this->language->get('heading_title_report'));

		$this->load->model('setting/setting');
		
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}		
		
		$data['orders'] = array();

		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_report'),
			'href' => $this->url->link('extension/shipping/cleverpoint/report', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['carriers'] = $this->getCarriers();
		
		$order_total = $this->model_extension_shipping_cleverpoint->getCleverpointTotalOrders($filter_data);
		$results = $this->model_extension_shipping_cleverpoint->getCleverpointOrders($filter_data);

		foreach ($results as $result) {
			
			$cleverpoint_info = $this->model_extension_shipping_cleverpoint->getCleverpointOrder($result['order_id']);
			
			$total_products = 0;
			$order_products = $this->model_sale_order->getOrderProducts($result['order_id']);
			foreach($order_products as $order_product) {
				$total_products += $order_product['quantity'];
			}
			
			$data['orders'][] = array(
				'order_id'      		=> $result['order_id'],
				'customer'      		=> $result['customer'],
				'total_products'      	=> $total_products,
				'order_status'  		=> $result['order_status'] ? $result['order_status'] : $this->language->get('text_missing'),
				'status'				=> $cleverpoint_info['status'],
				'total'         		=> $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    		=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' 		=> date($this->language->get('date_format_short'), strtotime($result['date_modified'])),			
				'shipment_id'			=> isset($cleverpoint_info['shipment_id']) ? $cleverpoint_info['shipment_id'] : '',
				'shipment_awb'			=> isset($cleverpoint_info['shipment_awb']) ? $cleverpoint_info['shipment_awb'] : '',
				'outbound_awb'			=> isset($cleverpoint_info['outbound_awb']) ? $cleverpoint_info['outbound_awb'] : '',
				'carrier'				=> isset($cleverpoint_info['carrier']) ? $cleverpoint_info['carrier'] : '',
				'station_id'			=> isset($cleverpoint_info['station_id']) ? $cleverpoint_info['station_id'] : '',
				'station_name'			=> isset($cleverpoint_info['station_name']) ? $cleverpoint_info['station_name'] : '',
				'station_address'		=> isset($cleverpoint_info['station_address']) ? $cleverpoint_info['station_address'] : '',
				'station_prefix'		=> isset($cleverpoint_info['station_prefix']) ? $cleverpoint_info['station_prefix'] : '',
				'station_city'			=> isset($cleverpoint_info['station_city']) ? $cleverpoint_info['station_city'] : '',
				'station_postcode'		=> isset($cleverpoint_info['station_postcode']) ? $cleverpoint_info['station_postcode'] : '',
				'is_cod'				=> $cleverpoint_info['is_cod'],
				'view'          		=> $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'].'&quantity=1'. $url, true)
			);
		}

		$url = '';
		
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/shipping/cleverpoint/report', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));

		$data['user_token'] = $this->session->data['user_token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->session->data['error'])) {
			$data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error'] = '';
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/shipping/cleverpoint_list', $data));
	}	
	
	public function createShipping() {
		
		if (isset($this->request->post['order_id'])) {
			$order_id = $this->request->post['order_id'];
		} else {
			return null;
		}		
		
		if (isset($this->request->post['tracking'])) {
			$tracking = $this->request->post['tracking'];
		} else {
			return null;
		}		

		if (isset($this->request->post['items'])) {
			$order_items = $this->request->post['items'];
		} else {
			return null;
		}		
		
		if (isset($this->request->post['amount'])) {
			$amount = $this->request->post['amount'];
		} else {
			$amount = 1;
		}			

		if($tracking) {
			$tracking = explode('_',$tracking);
		}

		$this->load->library('cleverpoint');
		$cleverpoint = new CleverPoint($this->registry);
		
		$this->load->model('extension/shipping/cleverpoint');
		$this->load->model('sale/order');
		$this->load->model('catalog/product');
		
		$shipment = array();
		$consignee = array();
		$CODs = array();
		$Items = array();
		$References = array();
		
		//get order info
		$order_info = $this->model_sale_order->getOrder($order_id);		
		
		//get order info
		$cleverpoint_info = $this->model_extension_shipping_cleverpoint->getCleverpointOrder($order_id);
		
		//get order products
		$order_products = $this->model_sale_order->getOrderProducts($order_id);
		
		//set recipient data
		$consignee = array(
			'ContactName' => $order_info['shipping_firstname'].' '.$order_info['shipping_lastname'],
			'Address' => $order_info['shipping_address_1'],
			'Area' => $order_info['shipping_city'],
			'City' => $order_info['shipping_zone'],
			'PostalCode' => $order_info['shipping_postcode'],
			'Country' => $order_info['shipping_iso_code_2'],
			'Phones' => $order_info['telephone'],
			'NotificationPhone' => $order_info['telephone'],
			'Emails' => $order_info['email'],
			'NotificationEmail' => $order_info['email'],
			'Reference' => $order_info['order_id']
		);
		
		foreach($order_items as $order_item) {
			$Items[] = array(
				'Code' => $order_item['voucher'],
				'Weight' => array(
					'UnitType' => 'kg',
					'Value' => $order_item['weight'] ? ($order_item['weight'] / 1000) : '0.1',
				)
			);	
		}
		
		//set consignee info
		$consignee = array(
			'ContactName' => $order_info['shipping_firstname'].' '.$order_info['shipping_lastname'],
			'Address' => $order_info['shipping_address_1'],
			'Area' => $order_info['shipping_city'],
			'City' => $order_info['shipping_zone'],
			'PostalCode' => $order_info['shipping_postcode'],
			'Country' => $order_info['shipping_iso_code_2'],
			'Phones' => $order_info['telephone'],
			'NotificationPhone' => $order_info['telephone'],
			'Emails' => $order_info['email'],
			'Reference' => $order_info['order_id']
		);
		
		//set COD if exists
		$cleverpoint_cods = $this->config->get('shipping_cleverpoint_payment_modules');
		if($cleverpoint_info['is_cod'] && in_array($order_info['payment_code'], $cleverpoint_cods)){
			$CODs[] = array(
				'Amount' => array(
						'CurrencyCode' => $order_info['currency_code'],
						'Value' => $order_info['total']
					)
			);
		}
		
		$shipment = array(
			'ExternalCarrierId' => $tracking[0],
			'ExternalCarrierName' => $tracking[1],
			'ShipmentAwb' => $order_items[0]['voucher'],
			'ItemsDescription' => '',
			'Consignee' => $consignee,
			'CODs' => $CODs,
			'Items' => $Items,
			"DeliveryStation" => $cleverpoint_info['station_id']
		);
		
		$result = $cleverpoint->createShipment($shipment);
		
		if($result['ResultType'] == 'Success') {
			$this->model_extension_shipping_cleverpoint->updateCleverpointOrder($order_info, $shipment, $result['Content']);
		} else {
			$this->model_extension_shipping_cleverpoint->updateCleverpointOrderStatus($order_info, $shipment, $result['Messages']);
			$result['error'] = $result['Messages'][0]['Code'];
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));		
		
	}
	
	public function getVouchers() {
		$this->load->library('cleverpoint');
		$cleverpoint = new CleverPoint($this->registry);

		if (isset($this->request->get['voucher'])) {
			$voucher = $this->request->get['voucher'];
			
			$result = $cleverpoint->getVouchers($voucher);
			
			if ($result['ResultType'] === 'Success' && $result['Content']['DocumentFormat'] === 'PDF') {
				$this->response->addHeader('Content-Type: application/pdf');
				$this->response->addHeader('Content-Disposition: inline; filename="'.$voucher.'.pdf"');
				$this->response->setOutput(base64_decode($result['Content']['Document']));
			} else {
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($result));
			}
		} else {
			return null;
		}
	}	
		
	public function getCarriers() {
		$this->load->library('cleverpoint');
		$cleverpoint = new CleverPoint($this->registry);

		$result = $cleverpoint->getCarriers();
		
		return $result['Content'];
	}	
	
	public function trackShipping() {
		$this->load->language('extension/shipping/cleverpoint');
		$this->load->library('cleverpoint');
		$cleverpoint = new CleverPoint($this->registry);
		$content = '';
		$data = array();
		
		if (isset($this->request->post['voucher'])) {
			$voucher = $this->request->post['voucher'];
			
			$result = $cleverpoint->trackShipping($voucher);
			
			if ($result['ResultType'] === 'Success') {
				$content = $result['Content'];
			}
			
			if($content) {
				if($content['TrackingData']) {
					$data['tracking_data'] = $content['TrackingData'];
				}
				if($content['PODData']) {
					$data['PODData'] = $content['PODData'];
				}
			}
			
			$this->response->setOutput($this->load->view('extension/shipping/cleverpoint_track', $data));
		} else {
			return null;
		}
	}	
	
	public function cancelShipment() {
		$this->load->library('cleverpoint');
		$cleverpoint = new CleverPoint($this->registry);
		$content = '';
		
		if (isset($this->request->post['voucher'])) {
			$voucher = $this->request->post['voucher'];
			$order_id = $this->request->post['order_id'];
			
			$result = $cleverpoint->CancelShipment($voucher);
			
			if ($result['ResultType'] === 'Success') {
				$content = $result['Content'];
				$this->clearOrder($order_id);
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($content));
		} else {
			return null;
		}
	}

	protected function clearOrder($order_id) {
		$this->load->model('extension/shipping/cleverpoint');
		$this->model_extension_shipping_cleverpoint->clearOrder($order_id);
	}	
	
	public function install() {
		$this->load->model('extension/shipping/cleverpoint');
		$this->load->model('setting/event');
        $this->model_setting_event->addEvent('cleverpoint', 'catalog/view/*/after', 'extension/shipping/cleverpoint/addClevermap');
        $this->model_setting_event->addEvent('cleverpoint', 'catalog/controller/common/header/after', 'extension/shipping/cleverpoint/addScript');
        $this->model_setting_event->addEvent('cleverpoint', 'admin/view/sale/order_info/after', 'extension/shipping/cleverpoint/injectOrder');
        $this->model_setting_event->addEvent('cleverpoint', 'catalog/view/*/before', 'extension/shipping/cleverpoint/paymentMethods');
        $this->model_setting_event->addEvent('cleverpoint', 'catalog/model/checkout/order/addOrderHistory/before', 'extension/shipping/cleverpoint/addOrder');	
        $this->model_setting_event->addEvent('cleverpoint', 'admin/view/common/column_left/before', 'extension/shipping/cleverpoint/adminMenu');	
		$this->model_extension_shipping_cleverpoint->install();
	}	
	
	public function uninstall() {
		$this->load->model('extension/shipping/cleverpoint');
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('cleverpoint');
		$this->model_extension_shipping_cleverpoint->uninstall();
	}		
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/cleverpoint')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}