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
		
		public function add_card($user_id,$newcard, $mastercard = null){
			//Check if the card exists, and if it's already a maintainer
			$query = $this->db->where(array('card' =>$newcard))->get('cards');
                        if($query->num_rows() == 0){
                            $datestring = "Year: %Y Month: %m Day: %d - %h:%i %a";
                            $this->db->insert('cards',array('card' => $newcard,
                                'user_id' => $user_id, 
                                'added_by_card' => $mastercard, 
                                'added_on' => date('Y-m-d H:i:s')));
			return $this->db->insert_id();
                        }else{
                            return null;
                            
                        }
                        

		}
		
		public function has_permission($node, $card){
		    
		    $this->db->select('permissions.permission');
    	    $this->db->where('cards.user_id', 'permissions.user_id', FALSE);
    	    $this->db->where('tools.tool_id', 'permissions.tool_id', FALSE);
    	    $this->db->where('permissions.permission', 1, FALSE);
    	    $this->db->where('tools.status', 1, FALSE);
    	    $this->db->where('tools.node', $node_id);
    	    $this->db->where('cards.card', $card_id);
    	    
		    $this->db->from('permissions, cards, tools');
            $query = $this->db->get();
            
            if ( $query->num_rows() > 0 ) {
                return TRUE;
            } else {
                return FALSE;
            }
            
		}
	}
?>