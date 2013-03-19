<?php

	class card_model extends CI_Model
	{
		public function __construct()
		{
			$this->load->database();
			$this->load->helper('date');
		}
		
		public function get_card($card){
			$query = $this->db->get_where('cards',array('card' => $card));
			$results = $query->row_array();
			if(!empty($results))
				return $results;
			else
				return 0;
		}
		
		public function add_card($newcard, $mastercard){
			//Check if the card exists, and if it's already a maintainer
			$datestring = "Year: %Y Month: %m Day: %d - %h:%i %a";
			$this->db->insert('cards',array('card' => $newcard, 'added_by_card' => $mastercard, "added_on" => 'NOW()'));
			return $this->db->insert_id();
		}
		
		public function get_permissions($node, $card){
			$query = $this->db->get_where('permissions',array('node' => $node, 'card' => $card));
			$results = $query->row_array();
			if(!empty($results['permission']))
				return $results['permission'];
			else
				return 0;
		}
	}
?>