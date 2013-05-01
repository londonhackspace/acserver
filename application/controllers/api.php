<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("application/libraries/REST_Controller.php");

class Api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('User_model','Tool_model', 'Card_model', 'Acnode_model'));
    }

    /*
     * GET CARD ACCESS LEVEL
     * http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Get_card_permissions
     *
     * Determine what access (if any) the given card has for the given Node's associated tool
     *
     *      GET /[nodeID]/card/[cardID]
     *      i.e.
     *      GET /1/card/04FF7922E40080
     *      returns
     *      0 - no permissions
     *      1 - user
     *      2 - maintainer
     */

    public function card_get() {
        $acnode_id = (int) $this->uri->segment(1);
        $card_unique_identifier = $this->uri->segment(3);
        $this->uri->segment(3) . "\n";
        $result = 0;        // default to 0 for permission denied
        
        // If we've correctly been presented with a node and a card, then check to see what
        // permission is associated with it
        if (isset($acnode_id) && isset($card_unique_identifier)) {
            $result = $this->Card_model->get_permission($acnode_id, $card_unique_identifier);
        } else {
            error_log("Cannot parse query string to determine the Node ($acnode_id) and Card ($card_unique_identifier) values to check");
        }
        $this->response($result);
    }


    /*
     *
     * ADD CARD (NOTE NEW POST STRUCTURE)
     * http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Add_card
     *
     *
     * Given a Node ID, Card to be Added, and a Card with admin powers for that node's associated tool,
     * grant permissions to the user.
     * 
     * If the user has the permission already, success is returned
     * If the 'granting' user does not have admin powers, failure is returned
     *
     *
     *
     * The original syntax isn't really ideal, in that it passed data in the Post block, and without
     * form-like encoding.
     *
     * Original Structure:
     *      POST /[nodeID]/card/
     *      [card to be added],[card added by]
     *      04FF7922E40080,04FF1234540080               # Note: in the content of the post, which is "weird"
     *      returns
     *          0 - card not added
     *          1 - card added
     * 
     * Changed to
     *        POST /[nodeID]/grant-to-card/[card_being_granted_to]/by/[card_with_admin_permissions]
     */
    public function grant_to_card_by_card_post() {
        
        $acnode_id = (int) $this->uri->segment(1);
        $card_to_add_unique_id   = $this->uri->segment(3);
        $added_by_unique_card_id = $this->uri->segment(5);

        $this->load->model('Card_model');
    
        // If the card_to_add_unique_id already has permission, just return OK
        error_log("Checking if $card_to_add_unique_id already has permission?");
        if  (
                ($this->Card_model->get_permission($acnode_id, $card_to_add_unique_id) == NODE_ACCESS_STANDARD)
                || ($this->Card_model->get_permission($acnode_id, $card_to_add_unique_id) == NODE_ACCESS_ADMIN)
            ) {
                error_log("Card already has permission - leaving as-is $card_to_add_unique_id");
                $this->response(RESPONSE_SUCCESS);
        }


        // Check that the supplied card_added_by has admin permission
        error_log("Checking if $added_by_unique_card_id already has permission");
        if ($this->Card_model->get_permission($acnode_id, $added_by_unique_card_id) == NODE_ACCESS_ADMIN) {
            error_log("Does not already have permission - adding it");
            $add_card_response_status = $this->Card_model->add_permissions_with_card($acnode_id, $card_to_add_unique_id, $added_by_unique_card_id);
            if ($add_card_response_status) {
                error_log("Permission added successfully");
                $this->response(RESPONSE_SUCCESS);
            } else {
                error_log("Problem adding card");
                $this->response(RESPONSE_FAILURE);
            }
    		
        } else {
            error_log("Granting card does not actually have permission - $added_by_unique_card_id tried to grant access to $card_to_add_unique_id");
            $this->output->set_status_header('401', "'Added By' card $added_by_unique_card_id does not have admin permission");
            $this->response(RESPONSE_FAILURE);
        }

    }

    // Not in the defined API
    // public function permissions_get() {
    // 
    //     $this->load->model('Node_model');
    //     $data = $this->Node_model->get_permission($this->get('node'), $this->get('card'));
    //     echo $data;
    //     //$this->response($data);
    // }

    /*
     * Check tool status
     * http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Check_tool_status
     *
     *      GET /[nodeID]/status/
     *      Check if the ACNode has been remotely taken out of service, or put back in service
     *      returns
     *      0 - out of service
     *      1 - in service
     *
     */

    public function status_get() {
        $node = (int) $this->uri->segment(1);
        // print "node: " . $node;
        $data = $this->Acnode_model->get_status($node);
        $this->response($data);
    }

    /*
     * Report tool status
     * http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Report_tool_status
     *
     *    PUT /[nodeID]/status/
     *    i.e.
     *    PUT /1/status/
     *    1
     *    0 - out of service
     *    1 - in service
     *    returns
     *    0 - not saved
     *    1 - saved

     */

    public function status_put() {

        $this->load->model('Acnode_model');
        $data = $this->Acnode_model->get_permission($this->get('node'), $this->get('card'));
        echo $data;
        //$this->response($data);
    }

    public function toolusetime_get() {
        
    }

    /*
     * Tool Usage (live)
     * http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Tool_usage_.28live.29
     * PUT /[nodeID]/tooluse/
     *    PUT /1/tooluse/
     *    1,04FF7922E40080
     *    0 - tool use stopped
     *    1 - tool in use
     */
    public function tooluselive_put() {
        
    }
     
    /* 
     * Tool usage (usage time)
     * http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Tool_usage_.28usage_time.29
     *
     *      POST /[nodeID]/tooluse/time/
     *      i.e.
     *      POST /1/tooluse/time/
     *      34000,04FF7922E40080
     *      returns
     *      0 - not saved
     *      1 - saved
     */
    public function toolusetime_put() {
        
    }


    /*
     * Case alert (Alert if the ACNode case is opened)
     * 
     *      PUT /[nodeID]/case/
     *      i.e.
     *      PUT /1/case/
     *      1
     *
     *   0 - case closed
     *   1 - case opened
     *   returns
     *   0 - not saved
     *   1 - saved
     */
    public function case_put() {
        
    }

    /*
     * Check DB sync
     *
     * http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Check_DB_sync
     *
     */

    public function sync_get() {
        
	    $this->db->select('card_id');
        $this->db->limit(1);
	    $this->db->from('cards');

        if ($this->uri->total_segments() == 3) {     # 1/sync/000000
            # If we're supplied with a 'last card id', we retrieve items > than that
            $last_card_unique_identifier = $this->uri->segment(3);
            $this->db->where('card_unique_identifier > ', $card_unique_identifier);
        }

        $this->db->order_by("card_unique_identifier asc");
        $query = $this->db->get();
        if ( $query->num_rows() > 0 ) {
            $row = $query->row();
            $this->response($row->card_unique_identifier);            
        } else {
            $this->response(1);            
        }
    }
    
    public function init_get() {
        
    }
    public function update_from_carddb_get() {
        $carddb_str = file_get_contents("/var/run/carddb.json");
        $users = json_decode($carddb_str, true);

        foreach ($users as $user) {
            $user_result = $this->User_model->add_or_update_user($user['user_id'], $user['nick']);
            
            # If the user previously had a card, but now that card no longer exists, we need
            # to revoke access.
            #
            # Similarly, we could have a situation where card A belonged to Alice, but Alice
            # gave the card to Bob, after removing it from their account.
            #
            # To do this in a sane way, we delete all cards from the cards table for the user,
            # then re-add any cards that should be there.
            $this->Card_model->delete_all_cards_for_user($user['user_id']);

            if (isset($user['cards']) && (!empty($cards_exists))) {
                foreach ($user['cards'] as $card) {
                    $card_result = $this->Card_model->add_card_to_user($user['user_id'], $card);
                }
            }
        }
        $this->response();
    }

    public function page_missing_get() {
        $this->response("0");
    }

}