<?php

    class User_model extends CI_Model
    {
        public function __construct()
        {
            $this->load->database();
        }
        
        public function get_user($card){
            $query = $this->db->get_where('cards',array('card' => $card));
                        $results = $query->row_array();
            if(!empty($results))
                return $results;
            else
                return null;
        }
        
        public function add_or_update_user($user_id, $user_nick){
            // If the user id is new, we add them, otherwise we force the details
            // to match the authorative source
            $query = $this->db->where(array('user_id' =>$user_id))->get('users');
            if ($query->num_rows() == 0){
                // Create a new entry in the users table
                $this->db->insert('users',array('user_id' => $user_id, 'nick' => $user_nick));
                return $this->db->insert_id();
            } else {
                // Update the nick to match the website nick
                $this->db->where('user_id', $user_id);
                $this->db->update('users',array('nick' => $user_nick));
                $row = $query->row_array();
                return $row["user_id"];
            }
        }
        
        public function normalise_users(&$local_denormalised_users){
            $normalised_users = array();
            foreach($local_denormalised_users as $user_and_card){
                   if(isset($normalised_users[$user_and_card['user_id']]))
                   {
                       $normalised_users[$user_and_card['user_id']]['cards'][]=
                              $user_and_card['card_unique_identifier']; 
                   }else{
                       $normalised_users[$user_and_card['user_id']] = 
                               array('nick'=> $user_and_card['nick'],
                                   'id'=> $user_and_card['user_id'],
                                   'cards'=>array($user_and_card['card_unique_identifier']));
                       
                   }
                }
            
            return $normalised_users;
        }
        
        public function batch_sync(&$user_card_data){
            // Get the users and cards tables
            //$users_cards_join_query = $this->db->select("users.user_id,nick,card_unique_identifier")
            //        ->join('cards','users.user_id = cards.user_id')->order_by('users.user_id','asc')->get('users');
            //$local_denormalised_users = $users_cards_join_query->result_array();
            //$local_users = $this->normalise_users($local_denormalised_users); 
            $users =array();
            $cards =array();
            foreach($user_card_data as $user){
                if($user['subscribed'] ==1){
                    $users[] = array("user_id" => $user['id'], 
                        "nick" => $user['nick']);
                    if(!empty($user['cards'])){
                        foreach($user['cards'] as $card){
                            $cards[] = array("user_id" => $user['id'],
                                "card_unique_identifier" => $card );
                        }
                    }
                }
                    
            }
            
            //Lets purge the current data in the table
            $this->db->empty_table('cards');
            $this->db->empty_table('users');
            
            //And reinsert the updated data
            $user_result = $this->db->insert_batch('users', $users);
            $card_result = $this->db->insert_batch('cards', $cards);
            
            //print_r($cards);
            return $user_result && $card_result?true:false;
        }
        
        
    }
?>