<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once("application/libraries/REST_Controller.php");

class Api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('User_model','Tool_model', 'Card_model'));
    }

    /*
     *  Get card permissions
     *  GET /[nodeID]/card/
     *  i.e.
     *  GET /1/card/04FF7922E40080
     *  returns
     *  0 - no permissions
     *  1 - user
     *  2 - maintainer
     */

    public function card_get() {
        $acnode_id = (int) $this->uri->segment(1);
        $card_unique_identifier = $this->uri->segment(3);
        $this->uri->segment(3) . "\n";
        $result = 0;        // default to 0 for permission denied
        
        // If we've correctly been presented with a node and a card, then check to see what
        // permission is associated with it
        if ($acnode_id && $card_unique_identifier) {
            $result = $this->Card_model->get_permission($acnode_id, $card_unique_identifier);
        } else {
            error_log("Cannot parse query string to determine the Node ($acnode_id) and Card ($card_unique_identifier) values to check");
        }
        $this->response($result);
    }

    public function card_post() {

        $this->load->model('Card_model');

        foreach ($this->post() as $card_to_add => $card_added_by) {
            if (($card_to_add != "format") && ($card_to_add != "node")) {
                if (get_permission($this->post("node"), $card_added_by) == 2) {
                    //TODO add user to the model and check in maintainer is adding a card that is not a maintainer on this node already
                    $this->response(1);
                } else {
                    $this->response(0);
                }
            }
        }



        // $data = $this->Card_model->get_permission($this->get('node'), $this->get('card'));
        // echo $data;
        //$this->response($data);
    }

    public function permissions_get() {

        $this->load->model('Node_model');
        $data = $this->Node_model->get_permission($this->get('node'), $this->get('card'));
        echo $data;
        //$this->response($data);
    }

    /*
     * Check tool status
      GET /[nodeID]/status/
      Check if the ACNode has been remotely taken out of service, or put back in service
      returns
      0 - out of service
      1 - in service

     */

    public function status_get() {
        $node = (int) $this->uri->segment(1);
        print "node: " . $node;
        $data = $this->Tool_model->get_satus($node);
        
        $this->response($data);
    }

    /*
     *    Report tool status
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

        $this->load->model('Node_model');
        $data = $this->Node_model->get_permission($this->get('node'), $this->get('card'));
        echo $data;
        //$this->response($data);
    }

    public function toolusetime_get() {
        
    }

    public function toolusetime_put() {
        
    }

    public function case_get() {
        
    }

    public function case_put() {
        
    }

    public function sync_get() {
        $this->response(1);
    }
    
    public function init_get() {
        
    }
    public function sync_db_get() {
        $carddb_str = file_get_contents("/var/www/acserver/carddb.json");
        $users = json_decode($carddb_str, true);

        foreach ($users as $user) {
            $user_exists = $this->User_model->get_user($user['id']);
            if (empty($user_exists)) {
                $user_result = $this->User_model->add_user($user['id'], $user['nick']);
                
                if (isset($user['cards'])) {
                    if (empty($cards_exists))
                        foreach ($user['cards'] as $card) {
                            $card_exists = $this->Card_model->get_card($card);
                            if (empty($card_exists))
                                $card_result = $this->Card_model->add_card($user['id'], $card);
                        }
                }
            }
        }
        //$this->response($json_a);
    }

    public function page_missing_get() {
        $this->response("0");
    }

}