<?php

class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('Acnode_model', 'Card_model', 'Tool_model', 'User_model'));
    }

    /*
        TITLE: Sync with membership json file

        DESCRIPTION:
            Note that this is a slow process, as it involves lots of DB access!

            Retrieves the contents of the carddb (using carddb.php on the
            London Hackspace site) and updates the internal db structure.

            Note that this code expects a valid and working carddb.php
            output file to be stored at /var/run/carddb.json
            
            The carddb.json file should be downloaded periodically
            externally from this script, and validated before it's
            written to the above location
        
        URL STRUCTURE:
            Can't be called from the web interface as it can lead to timeouts
            
        DESCRIPTION URL:
            Not described in original Solexious Proposal. Added so that we can process
            updates from the command line
            
        EXAMPLES:
            ...@babbage:~$ cd /var/www/acserver/
            ...@babbage:~$ sudo curl -o /var/run/carddb.json https://london.hackspace.org.uk/carddb.php
            ...@babbage:~$ php index.php update_from_carddb


    */
    public function update_from_carddb() {
        $carddb_str = file_get_contents("/var/run/carddb.json");
        $users = json_decode($carddb_str, true);
        
        foreach ($users as $user) {
            $user_id = $user['id'];    # The carddb uses 'id' while we use 'user_id'

            $user_result = $this->User_model->add_or_update_user($user_id, $user['nick']);

            # If the user previously had a card, but now that card no longer exists, we need
            # to revoke access.
            #
            # Similarly, we could have a situation where card A belonged to Alice, but Alice
            # gave the card to Bob, after removing it from their account.
            #
            # To do this in a sane way, we delete all cards from the cards table for the user,
            # then re-add any cards that should be there.
            $this->Card_model->delete_all_cards_for_user($user_id);

            if ($user['subscribed'] == 1) {
                foreach ($user['cards'] as $card_unique_identifier) {
                    $card_result = $this->Card_model->add_card_to_user($user_id, $card_unique_identifier);
                }
            }
        }
        $this->response(RESPONSE_SUCCESS);
    }


    /*
        TITLE: Get Card Permission

        DESCRIPTION:
            Determine what access (if any) the given card has.

        URL STRUCTURE:
            GET /[nodeID]/card/[cardID]
            
        DESCRIPTION URL:
            http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Get_card_permissions
            
        EXAMPLES:
            (Using test data set)

            Returns 2, as this card is set up as an administrative card:
                curl http://babbage:1234/1/card/00000001
            
            Returns 1, as this is a NORMAL user card:
                curl http://babbage:1234/1/card/AAAAAAAA
            
            Returns 0, as this is an un-authorised card (assuming you haven't authorised it in testing)
                curl http://babbage:1234/1/card/BABABABA

            Returns 0, as this is an unknown card
                curl http://babbage:1234/1/card/JKLMNOP
    */
    public function card() {
        $acnode_id = (int) $this->uri->segment(1);
        $card_unique_identifier = $this->uri->segment(3);
        $this->uri->segment(3) . "\n";
        $result = NODE_ACCESS_DENIED;           // Default is to deny access
        
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
        TITLE: Add Card

        DESCRIPTION:
            Given a Node ID, Card to be Added, and a Card with admin powers for that node's associated tool,
            grant permissions to the user.

        URL STRUCTURE:
            POST /1/grant-to-card/123456/by-card/00000001

            Notes:
                * 00000001 needs to be an administrator card.
                * 123456 needs to be associated with an existing user in
                    the card database, which can be done via the standard doorbot
                    access structure.
                * 123456 needs to associated with a currently subscribed member
            

        DESCRIPTION URL:
            http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Add_card
            
        EXAMPLES:
            (Using test data set)
            
                
            Curl Examples:
                This returns 0 as the user doesn't have permission by default in the test data:
                     curl http://babbage:1234/1/card/BABABABA
                Add the card, using the 00000001 card's admin permissions
                     curl --data '' http://babbage:1234/1/grant-to-card/BABABABA/by-card/00000001
                And now this returns 1
                     curl http://babbage:1234/1/card/BABABABA
                     
                Note you should possibly reset the db after testing
        
    */
    public function grant_to_card_by_card() {
        
        $acnode_id = (int) $this->uri->segment(1);
        $card_to_add_unique_id   = $this->uri->segment(3);
        $added_by_unique_card_id = $this->uri->segment(5);

        // If the card_to_add_unique_id already has permission, just return OK
        error_log("Checking if $card_to_add_unique_id already has permission?");
        if  (
                   ($this->Card_model->get_permission($acnode_id, $card_to_add_unique_id) == NODE_ACCESS_STANDARD)
                || ($this->Card_model->get_permission($acnode_id, $card_to_add_unique_id) == NODE_ACCESS_ADMIN)
            ) {
                error_log("Card $card_to_add_unique_id already has permission - leaving as-is $card_to_add_unique_id");
                $this->response(RESPONSE_SUCCESS);
        }


        // Check that the supplied card_added_by has admin permission
        error_log("Checking if grantor card $added_by_unique_card_id has admin permissions");
        if ($this->Card_model->get_permission($acnode_id, $added_by_unique_card_id) == NODE_ACCESS_ADMIN) {
            error_log("Grantor is good - about to add permissions");
            $add_card_response_status = $this->Card_model->add_permissions_with_card($acnode_id, $card_to_add_unique_id, $added_by_unique_card_id);
            if ($add_card_response_status) {
                $this->response(RESPONSE_SUCCESS);
            } else {
                $this->response(RESPONSE_FAILURE);
            }
        
        } else {
            error_log("Supposed grantor card $added_by_unique_card_id does not have admin permissions to acnode $acnode_id");
            $this->output->set_status_header('401', "'Added By' card $added_by_unique_card_id does not have admin permission");
            $this->response(RESPONSE_FAILURE);
        }

    }
    
    
    /*
        TITLE: Check DB sync

        DESCRIPTION:
            Retrieves cards associated with a particular node one-by-one, so as to ensure the permission set is
            fully synchronised
            
            This method is called in two modes.
                1) Without a 'last card ID', where we return the first card ID
                2) With a 'last card ID', where we return the next card in the list.
                
            When all cards are exhausted, we return END

        URL STRUCTURE:
            GET /[nodeID]/sync
            GET /[nodeID]/sync/[previouslyRetrievedCardID]

        DESCRIPTION URL:
            http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Check_DB_sync

        EXAMPLES:
            (Using test data set)
            Returns 00000001, the first card:
                curl http://babbage:1234/1/sync

            Returns AAAAAAAA, the second card, by passing in the 00000001 referred to above
                curl http://babbage:1234/1/sync/00000001
                
             Returns BBBBBBBB, the third card, by passing in the AAAAAAAA referred to above. If it returns
                BABABABA it means that you haven't reset the card database after testing granting permission above
                
                curl http://babbage:1234/1/sync/AAAAAAAA

            Continue retrieving cards until you reach 'END'

    */
    public function sync() {
        $acnode_id = (int) $this->uri->segment(1);

        $this->db->select('card_unique_identifier');
        $this->db->from('cards');
        $this->db->join('users', 'users.user_id = cards.user_id');
        $this->db->join('permissions', 'permissions.user_id = users.user_id');
        $this->db->join('tools', 'tools.tool_id = permissions.tool_id');
        $this->db->join('acnodes', 'acnodes.tool_id = tools.tool_id');

        $this->db->where('acnodes.acnode_id', $acnode_id);
        $this->db->where('permissions.permission > ', 0);

        /*
        * If we're passed a previous card number, find the next card above that
        */

        if ($this->uri->total_segments() == 3) {     # 1/sync/000000
            # If we're supplied with a 'last card id', we retrieve items > than that
            $last_card_unique_identifier = $this->uri->segment(3);
            $this->db->where('card_unique_identifier > ', $last_card_unique_identifier);
        }

        $this->db->limit(1);

        // The sort order is NB - we sort by card unique identifier, then by actual
        // underlying db ID, so that the results are always deterministic (even if they
        // are wrong, due to some problem like a non-unique card ID)
        $this->db->order_by("card_unique_identifier asc, card_id asc");
        
        $query = $this->db->get();
        if ( $query->num_rows() > 0 ) {
            $row = $query->row();
            $this->response($row->card_unique_identifier);            
        } else {
            $this->response('END');            
        }
    }



    /*
        TITLE: Report Tool Status

        DESCRIPTION:
            Indicates whether a card is in our out of service.

            Any user with a card can take a device out of service (even if they haven't got permission
            for the device itself!) and we log who took the device out of service. This way if someone
            notices a problem they can immediately mark the device as problematic, without having to
            get an admin user involved.
            
            Only an administrative user can, however, mark a device back 'in service'.
            
        URL STRUCTURE:
            POST /[nodeID]/status/[new_status]/by/[cardID]

        DESCRIPTION URL:
            http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Report_tool_status
            
        EXAMPLES:
            (Using test data set - run these commands in the presented order for testing)

            Shows the status as '1' by default in the test data:
                curl http://babbage:1234/1/status
            
            Sets the status to 0, using admin card 00000001. Returns 1 to indicate the save was ok
                curl --data '' http://babbage:1234/1/status/0/by/00000001

            The status is now showing as 0
                curl http://babbage:1234/1/status

            Sets the status back to 1, using admin card 00000001. Returns 1 to indicate the save was ok
                curl --data '' http://babbage:1234/1/status/1/by/00000001

            The status is showing as 1
                curl http://babbage:1234/1/status

            Tries to set status to 0, using unknown card DOESNOTEXIST. Returns 0 to indicate the save failed
                curl --data '' http://babbage:1234/1/status/0/by/DOESNOTEXIST

            Now the status is still 1, as the attempt by DOESNOTEXIST has not worked
                curl http://babbage:1234/1/status

            Sets the status to 0, using non-admin (but known) card AAAAAAAA. Returns 1 to indicate the save was ok
                (non-admin cards can take devices out of service)
                curl --data '' http://babbage:1234/1/status/0/by/AAAAAAAA

            Now the status is 0 as the tool has been taken out of service
                curl http://babbage:1234/1/status

            Tries to set status to 1, using non-admin (but knonw) card AAAAAAAA. Returns 0 to indicate the save failed
                (non-admin cards can not bring devices back into service)
                curl --data '' http://babbage:1234/1/status/1/by/AAAAAAAA

            Tries to set status to 1, using unknown card DOESNOTEXIST. Returns 0 to indicate the save failed
                curl --data '' http://babbage:1234/1/status/1/by/DOESNOTEXIST

            The status is now still 0 as the tool has been taken out of service above, and the attempts
                by AAAAAAAA and DOESNOTEXIST have failed
                curl http://babbage:1234/1/status

            Returns the status to 1, using admin card 00000001. Returns 1 to indicate the save was ok
                curl --data '' http://babbage:1234/1/status/1/by/00000001
            
            Now the status is back to 1:
                curl http://babbage:1234/1/status

        
    */
    public function change_status() {
        $acnode_id     = (int) $this->uri->segment(1);
        $new_status    = (int) $this->uri->segment(3);
        $card_unique_identifier = $this->uri->segment(5);

        /* First, find the user associated with the card for logging purposes */
        $user_id = $this->Card_model->get_user_id_for_card_unique_identifier($card_unique_identifier);

        /* If the user is bringing the device back into service, check that they are an administrator */
        if ($new_status == 1) {
            if ($this->Card_model->get_permission($acnode_id, $card_unique_identifier) == NODE_ACCESS_ADMIN) {
                $this->Tool_model->set_tool_status_for_acnode_id($acnode_id, $new_status);
                $this->Tool_model->log_usage($acnode_id, $user_id, $card_unique_identifier, 'Tool brought back into service', 0);
                $this->response(RESPONSE_SUCCESS);
            } else {
                $this->Tool_model->log_usage($acnode_id, $user_id, $card_unique_identifier, 'Non-admin / unknown user tried to bring tool back into service', 0);
                $this->response(RESPONSE_FAILURE);
            }
        } else {
            /*
                Someone is taking the device out of service. If it's a known user, change the status (even if they don't have any
                permissions for the tool). We log a message so we know whom took it out of service.
                
                If the card isn't for a known user, deny the request and log a message.
            */
            if ($user_id) {
                $this->Tool_model->set_tool_status_for_acnode_id($acnode_id, $new_status);
                $this->Tool_model->log_usage($acnode_id, $user_id, $card_unique_identifier, 'Tool taken out of service', 0);
                $this->response(RESPONSE_SUCCESS);
            } else {
                $this->Tool_model->log_usage($acnode_id, NULL, $card_unique_identifier, 'Unknown card attempted to mark tool as out-of-service', 0);
                $this->response(RESPONSE_FAILURE);
            }
        }
    }


    /*
        TITLE: Check Tool Status

        DESCRIPTION:
            Returns a binary status of the tool - indicating whether it's available for use or not
            
        URL STRUCTURE:
            GET /[nodeID]/status/

        DESCRIPTION URL:
            http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Check_tool_status
            
        EXAMPLES:
            (Using test data set)
            
            Returns 1, indicating the tool status is OK for the Laser
                curl http://babbage:1234/1/status

            Returns 0, indicating the tool status is out out of service for the Rage
                curl http://babbage:1234/2/status
        
    */
    public function status() {
        $node = (int) $this->uri->segment(1);

        $data = $this->Acnode_model->get_status($node);
        $this->response($data);
    }


    /*
        TITLE: Tool Usage (live)

        DESCRIPTION:
            Details when a user starts or stops using a tool.
            
        URL STRUCTURE:
            POST /[nodeId]/tooluse/[status]/[cardID]

        DESCRIPTION URL:
            http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Tool_usage_.28live.29
            
        EXAMPLES:
            (Using test data set)
            
            Inserts into the log that user 100 (with card 00000001) that tool access started. Returns 1 for save ok.
                curl --data '' http://babbage:1234/1/tooluse/1/00000001
                
            Inserts into the log that user 100 (with card 00000001) that tool access finished. Returns 1 for save ok.
                curl --data '' http://babbage:1234/1/tooluse/0/00000001
        
        
    */
    public function tooluselive() {
        $acnode_id = (int) $this->uri->segment(1);
        $status    = (int) $this->uri->segment(3);
        $card_unique_identifier = $this->uri->segment(4);
        
        $user_id = $this->Card_model->get_user_id_for_card_unique_identifier($card_unique_identifier);
        
        if ($status == 1) {
            $this->Tool_model->log_usage($acnode_id, $user_id, $card_unique_identifier, 'Access Started', 0);
        } else {
            $this->Tool_model->log_usage($acnode_id, $user_id, $card_unique_identifier, 'Access Finished', 0);
        }
        $this->response(RESPONSE_SUCCESS);
    }
     
    /*
        TITLE: Tool usage (usage time)

        DESCRIPTION:
            Logs tool usage time
            
        URL STRUCTURE:
            POST /[nodeID]/tooluse/time/for/[cardID]/[timeUsed]

        DESCRIPTION URL:
            http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Tool_usage_.28usage_time.29
            
        EXAMPLES:
            (Using test data set)
            
            Inserts into the log that user 100 (with card 00000001) used the tool for 30000 ms. Returns 1 for save ok.
                curl --data '' http://babbage:1234/1/tooluse/time/for/00000001/30000
        
    */
    public function toolusetime() {
        $acnode_id = (int) $this->uri->segment(1);
        $card_unique_identifier = $this->uri->segment(5);
        $time_used  = $this->uri->segment(6);

        $user_id = $this->Card_model->get_user_id_for_card_unique_identifier($card_unique_identifier);
        
        $this->Tool_model->log_usage($acnode_id, $user_id, $card_unique_identifier, 'Time Used', $time_used);
        $this->response(RESPONSE_SUCCESS);
    }
    

    /*
        TITLE: Case alert

        DESCRIPTION:
            Lots an alert whenever the acnode or it's associated tool's case is opened / closed
            
        URL STRUCTURE:
            /[nodeID]/case/change/[newStatus]

        DESCRIPTION URL:
            http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Case_alert
            
        EXAMPLES:
            (Using test data set)
            
            Marks the case as closed (0)
                curl --data '' http://babbage:1234/1/case/change/0

            Marks the case as opened (1)
                curl --data '' http://babbage:1234/1/case/change/1
            
        
    */
    public function case_change() {
        $acnode_id = (int) $this->uri->segment(1);
        $status    = (int) $this->uri->segment(4);

        $tool_id = $this->Card_model->get_tool_id_for_acnode_id($acnode_id);
        
        $this->Tool_model->log_case_status_change($acnode_id, $status);
        $this->response(RESPONSE_SUCCESS);
    }


    
    protected function response($data) {
        //Content-Length required for the ACnode
        $this->output
            ->set_header("Content-Length: ". strlen($data));
        echo $data;
    }

}