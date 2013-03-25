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
		
		public function get_permissions($node, $card){
                    $this->load->model(array('Card_model'));
                    $user_results = $this->User_model->get_user($card);
                    //$query = $this->db->get_where('permissions',array('node' => $node, 'card' => $card));
//                    $results = $query->row_array();
//                    if(!empty($results['permission']))
//                            return $results['permission'];
//                    else
//                            return 0;
//                    
//                    return $permission;
		}
	}
?>