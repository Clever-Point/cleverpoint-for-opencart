<?php
class ModelExtensionShippingCleverpoint extends Model {
	function getQuote($address) {
		
		$this->load->language('extension/shipping/cleverpoint');
		
		$status = true;

		$method_data = array();
		
		$this->load->library('cleverpoint');
		$cleverpoint = new CleverPoint($this->registry);
		$cleverpoint->getShipment();
		
		//filter excluded categories
		$exclude_categories = $this->config->get('shipping_cleverpoint_categories');
		$cart_products = $this->cart->getProducts();
		
		$this->load->model('catalog/product');
		foreach($cart_products as $cart_product) {
			$product_categories = $this->model_catalog_product->getCategories($cart_product['product_id']);
			if($exclude_categories) {
				foreach ($product_categories as $category) {
					if (in_array($category['category_id'], $exclude_categories)) {
						$status = false;
						break;
					}			  
				}			
			}			
		}

		if ($status) { 
			$quote_data = array();
			
			$cost = 0;
			$cleverpoint_cost = 0;
			$tax_class_id = 0;
			
			//get Cleverpoint Rate
			if($this->config->get('shipping_cleverpoint_fee')) {
				$rates = $cleverpoint->GetPrices();
				if($rates['ResultType'] == 'Success') {	
					foreach($rates['Content'] as $rate) {
						if($rate['Type'] == 'Pickup') {
							$cost = $rate['Price']['Value'];
							$cleverpoint_cost = $rate['Price']['Value'];
							break;
						}
					}
				}
			}
			//set Cleverpoint Cost in session
			$this->session->data['cleverpoint_station']['fee'] = $cleverpoint_cost;		
			
			//setDefault Settings
			if(isset($this->session->data['cleverpoint_station']['shipping_method'])) {
				$cost = $this->session->data['cleverpoint_station']['shipping_method']['cost'] + ($cleverpoint_cost);
			}
			
			$text = $this->currency->format($cost,$this->session->data['currency']);

			$quote_data['cleverpoint'] = array(
				'code'         => 'cleverpoint.cleverpoint',
				'title'        => $this->language->get('text_description'),
				'cost'         => $cost,
				'tax_class_id' => 0,
				'text'         => $text
			);
			
			$method_data = array(
				'code'       => 'cleverpoint',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_cleverpoint_sort_order'),
				'error'      => false
			);
		}
		
		return $method_data;
	}
	
	public function addCleverpointOrder($order_id = NULL, $cleverpoint_station = []) {
		if($order_id) {
			$sql = "
				REPLACE INTO `" . DB_PREFIX . "cleverpoint_requests`
				
				SET
					order_id = '".(int)$order_id."',
					station_id = '".$cleverpoint_station['station_id']."',
					station_prefix = '".$cleverpoint_station['station_prefix']."',
					station_name = '".$this->db->escape($cleverpoint_station['station_name'])."',
					station_address = '".$this->db->escape($cleverpoint_station['station_address'])."',
					station_city = '".$cleverpoint_station['station_city']."',
					station_postcode = '".$cleverpoint_station['station_postcode']."',
					station_zone = '".$cleverpoint_station['station_zone']."',
					is_cod = '".$cleverpoint_station['is_cod']."',
					status = '1'
			";
			
			$query = $this->db->query($sql);
			
			$this->db->query("
				UPDATE `" . DB_PREFIX . "order`
				
				SET
					shipping_address_1 = '" . $this->db->escape($cleverpoint_station['station_address']) . "',
					shipping_city = '" . $cleverpoint_station['station_city'] . "',
					shipping_postcode = '". $cleverpoint_station['station_postcode'] ."',
					shipping_zone = '" . $cleverpoint_station['station_zone'] . "',
					shipping_zone_id = '0',
					shipping_method = 'Cleverpoint: " . $this->db->escape($cleverpoint_station['station_name']) . "',
					shipping_company = 'Cleverpoint: " . $this->db->escape($cleverpoint_station['station_name'] . ", " . $cleverpoint_station['station_address'] . ", " . $cleverpoint_station['station_city']) . " [" . $cleverpoint_station['station_prefix'] . "]'
					
				WHERE order_id = '" . (int)$order_id . "'
			");			
		}		
	}
}