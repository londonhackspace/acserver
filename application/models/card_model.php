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
		
		public function get_permission($acnode_id, $card_unique_identifier){
		    
		    $this->db->select('permissions.permission');
		    
    	    $this->db->where('acnodes.acnode_id', $acnode_id);
    	    $this->db->where('cards.card_unique_identifier', $card_unique_identifier);

            // Ensure the tool status is 'in service' (status = 1)
    	    $this->db->where('tools.status', 1);

            // Relationships
    	    $this->db->where('tools.tool_id', 'acnodes.tool_id', FALSE);
    	    $this->db->where('permissions.tool_id', 'tools.tool_id', FALSE);
    	    $this->db->where('permissions.user_id', 'cards.user_id', FALSE);
    	    
		    $this->db->from('permissions, cards, tools, acnodes');
            $query = $this->db->get();
            
            if ( $query->num_rows() == 1 ) {
                $row = $query->row();
                return (int) $row->permission;            // Return the permission value stored - which may be 0, 1, or 2 (No permissions, user, or maintainer)
            } else {
                return 0;                           // If they aren't in the system, or they haven't captured a card, return 0 for 'No Permissions'
            }
            
		}
	}
?>