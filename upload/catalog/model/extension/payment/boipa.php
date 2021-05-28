<?php
class ModelExtensionPaymentBoipa extends Model {
	

	public function getMethod($address, $total) {
	    $this->load->language('extension/payment/boipa');
	    
        $status = true;
	    
	    $method_data = array();
	    if ($status) {
	        $method_data = array(
	            'code'       => 'boipa',
	            'title'      => $this->language->get('text_title'),
	            'terms'      => '',
	            'sort_order' => $this->config->get('payment_boipa_sort_order')
	        );
	    }
	    
	    return $method_data;
	}
	public function addOrder($order_data) {
	    
	    $pay_action = $this->config->get('payment_boipa_pay_action'); //1 refer to auth, 0 refer to purchase
	    $cap = '';
	    if($pay_action == 0 ){
	        $cap = ",`capture_status` = '1'";//the transaction is for Purchase: Auth + Capture
	    }
	    $this->db->query("INSERT INTO `" . DB_PREFIX . "boipa_order` SET `order_id` = '" . (int)$order_data['order_id'] . "', `created` = NOW(), `modified` = NOW(), "  . "`total` = '" . $this->currency->format($order_data['total'], $order_data['currency_code'], false, false) . "', `currency_code` = '" . $this->db->escape($order_data['currency_code']) . "', `merchant_tx_id` = '" . $this->db->escape($order_data['merchant_tx_id']) . "'{$cap}");
	    
	    return $this->db->getLastId();
	}
	
	public function addTransaction($boipa_order_id, $type, $order_info) {
	    
	    $this->db->query("INSERT INTO `" . DB_PREFIX . "boipa_transaction` SET `boipa_order_id` = '" . (int)$boipa_order_id . "', `created` = NOW(), "  . " `type` = '" . $this->db->escape($type) . "', `amount` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], false, false) . "'");
	    
	    return $this->db->getLastId();
	}
	public function hasOrder($order_id){
	    $query = $this->db->query("SELECT `boipa_order_id` FROM `" . DB_PREFIX . "boipa_order` WHERE `order_id` = '" . (int)$order_id . "'");
	    if($query->num_rows){
	        return true;
	    }else{
	        return false;
	    }
	}
	//to get the total order history numbers from OpenCart system
	public function getTotalOrderHistories($order_id) {
	    $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
	    
	    return $query->row['total'];
	}
	
}
