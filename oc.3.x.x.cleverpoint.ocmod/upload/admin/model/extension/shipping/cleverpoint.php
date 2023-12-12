<?php
class ModelExtensionShippingCleverpoint extends Model {
	
	public function install() {
		$this->db->query("			
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cleverpoint_requests` (
			  `order_id` int(11) NOT NULL,
			  `shipment_id` varchar(255) DEFAULT NULL,
			  `shipment_awb` varchar(255) DEFAULT NULL,
			  `outbound_awb` varchar(255) DEFAULT NULL,
			  `carrier` varchar(255) DEFAULT NULL,
			  `station_id` varchar(255) DEFAULT NULL,
			  `is_cod` int(11) NOT NULL DEFAULT 0,
			  `station_name` varchar(255) DEFAULT NULL,
			  `station_prefix` varchar(255) DEFAULT NULL,
			  `station_address` text DEFAULT NULL,
			  `station_city` varchar(255) DEFAULT NULL,
			  `station_postcode` varchar(255) DEFAULT NULL,
			  `station_zone` varchar(255) DEFAULT NULL,
			  `status_message` text NOT NULL,
			  `status` int(11) DEFAULT 0
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;			
		");

		$this->db->query("
			ALTER TABLE `" . DB_PREFIX . "cleverpoint_requests`
			  ADD PRIMARY KEY (`order_id`),
			  ADD KEY `status` (`status`),
			  ADD KEY `station_id` (`station_id`),
			  ADD KEY `order_id` (`order_id`),
			  ADD KEY `shipment_id` (`shipment_id`);
		");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "cleverpoint_requests`;");
	}
	
	public function getCleverpointOrders($data = array()) {
		
		$sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') AS order_status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM `" . DB_PREFIX . "order` o";

		$sql .= " WHERE o.order_status_id > '0'";
		
		$sql .= " AND o.shipping_code = 'cleverpoint.cleverpoint'";

		$sql .= " ORDER BY o.order_id DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getCleverpointOrder($order_id) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "cleverpoint_requests`";

		$sql .= " WHERE order_id = '".(int)$order_id."' ";

		$query = $this->db->query($sql);

		return $query->row;
	}
	
	public function getCleverpointTotalOrders($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`";

		$sql .= " WHERE order_status_id > '0'";
		
		$sql .= " AND shipping_code = 'cleverpoint.cleverpoint'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}	
	
	public function updateCleverpointOrder($order = array(), $shipment = array(), $result = array()) {

		if(isset($order['order_id'])) {
			$sql = "
				UPDATE " . DB_PREFIX . "cleverpoint_requests
				
				SET
					shipment_id = '".$result['ShipmentMasterId']."',
					shipment_awb = '".$result['ShipmentAwb']."',
					outbound_awb = '".$result['OutboundAwb']."',
					carrier = '".$shipment['ExternalCarrierName']."',
					station_id = '".$shipment['DeliveryStation']."',
					status = '2'
				WHERE order_id = '".$order['order_id']."'
			";
			
			$query = $this->db->query($sql);
		}
	}	
	
	public function updateCleverpointOrderStatus($order = array(), $shipment = array(), $result = array()) {
		if(isset($order['order_id'])) {
			$sql = "
				UPDATE " . DB_PREFIX . "cleverpoint_requests
				
				SET
					status_message = '".$result[0]['Code']."'
				WHERE order_id = '".$order['order_id']."'
			";
			
			$query = $this->db->query($sql);
		}
	}	
	
	public function clearOrder($order_id) {
		$sql = "
			UPDATE " . DB_PREFIX . "cleverpoint_requests
			
			SET
				shipment_id = NULL,
				shipment_awb = NULL,
				outbounD_awb = NULL,
				carrier = NULL,
				status = 1
				
			WHERE order_id = '".(int)$order_id."'
		";
		
		$query = $this->db->query($sql);
	}	
	
	public function getXshippingProMethods() {
		
		$shipping_methods = array();
		
		$xspMethods = $this->db->query("
			SELECT 
			*
			FROM " . DB_PREFIX . "xshippingpro
		")->rows;
		
		foreach($xspMethods as $xspMethod) {
			$method_data = json_decode($xspMethod['method_data'],TRUE);
			$shipping_methods[$xspMethod['tab_id']]['code'] = 'xshippingpro.xshippingpro'.$xspMethod['tab_id'];
			$shipping_methods[$xspMethod['tab_id']]['name'] = $method_data['name'][(int)$this->config->get('config_language_id')];
		}
		
		return $shipping_methods;
	}
	
	public function getCleverpointStatus($order_id) {

		return array();
	}
}
