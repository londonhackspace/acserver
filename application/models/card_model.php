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
//                      TODO: Tie into synced database
//			$query = $this->db->get_where('permissions',array('node' => $node, 'card' => $card));
//			$results = $query->row_array();
//			if(!empty($results['permission']))
//				return $results['permission'];
//			else
//				return 0;
                    $sols_cards = array("04307922E42280","51DBC4CD","C7B7B20B","26515764","042D343A4F2380");
                    $sols_nodes = array("1");
                    $permission = 0;
                    if($node ==1){
                        //TestNode
                        foreach ($sols_cards as $is_sols_card) {
                            if ($card == $is_sols_card){
                                $permission = 2;
                            }
                        } 
                        
                    }
                    
                    return $permission;
		}
	}
?>