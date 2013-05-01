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
		
		public function add_card_to_user($user_id, $card_unique_identifier) {
		    $insert_status = $this->db->insert('cards',
                array(
                    'user_id' => $user_id,
                    'card_unique_identifier' => $card_unique_identifier,
                )
            );
		}
		
		public function add_permissions_with_card($acnode_id, $card_to_add_unique_id, $added_by_unique_card_id) {

            // Gather the required fields
            $user_id = $this->get_user_id_for_card_unique_identifier($card_to_add_unique_id);
            if (!isset($user_id)) {
                return FALSE;
            }
            $added_by_user_id = $this->get_user_id_for_card_unique_identifier($added_by_unique_card_id);
            if (!isset($added_by_user_id)) {
                error_log("Card to add ($added_by_unique_card_id) is not associated with any user?");
                return FALSE;
            }
            
            $tool_id = $this->get_tool_id_for_acnode_id($acnode_id);
            if (!isset($tool_id)) {
                error_log("Acnode ID ($acnode_id) does not have an associated tool id?");
                return FALSE;
            }
            
            // Insert the data into the permissions table
            $insert_status = $this->db->insert('permissions',
                array(
                    'tool_id' => $tool_id,
                    'user_id' => $user_id,
                    'added_by_user_id' => $added_by_user_id,
                    'added_on' => date('Y-m-d H:i:s'),
                    'permission' => 1
                )
            );
            if ($insert_status) {
                error_log("Insert into permissions table success");
                return TRUE;
            } else {
                error_log("Insert into permissions table failed");
		        return FALSE;
            }
		}
		
		/*
		 * Returns the permission value (0, 1, 2) for a given card unique ID for a given ACNode ID
		 */
		public function get_permission($acnode_id, $card_unique_identifier){

		    $this->db->select('permissions.permission');
		    $this->db->from('permissions, cards, tools, acnodes');

    	    $this->db->where('acnodes.acnode_id', $acnode_id);
    	    $this->db->where('cards.card_unique_identifier', $card_unique_identifier);

            // Ensure the tool status is 'in service' (status = 1)
    	    $this->db->where('tools.status', 1);

            // Relationships
    	    $this->db->where('tools.tool_id', 'acnodes.tool_id', FALSE);
    	    $this->db->where('permissions.tool_id', 'tools.tool_id', FALSE);
    	    $this->db->where('permissions.user_id', 'cards.user_id', FALSE);

            $query = $this->db->get();

            if ( $query->num_rows() == 1 ) {
                $row = $query->row();
                return (int) $row->permission;            // Return the permission value stored - which may be 0, 1, or 2 (No permissions, user, or maintainer)
            } else {
                return 0;                           // If they aren't in the system, or they haven't captured a card, return 0 for 'No Permissions'
            }

		}
		
		/*
		 * Given a user ID, remove all the cards for that user, if any
		 */
		public function delete_all_cards_for_user($user_id) {
		    $this->db->delete('cards', array('user_id', $user_id));
		}
		
		
		
		/* PRIVATE FUNCTIONS */
		private function get_user_id_for_card_unique_identifier($card_unique_identifier) {
		    $this->db->select('user_id');
		    $this->db->from('cards');
    	    $this->db->where('cards.card_unique_identifier', $card_unique_identifier);
            $query = $this->db->get();

            if ( $query->num_rows() == 1 ) {
                $row = $query->row();
                return (int) $row->user_id;
            } else {
                return NULL;
            }
		}
		
		
		private function get_tool_id_for_acnode_id($acnode_id) {
		    $this->db->select('acnodes.tool_id');
		    $this->db->from('acnodes');
    	    $this->db->where('acnode_id', $acnode_id);
            $query = $this->db->get();

            if ( $query->num_rows() == 1 ) {
                $row = $query->row();
                return (int) $row->tool_id;
            } else {
                return NULL;
            }
		}

	}
?>