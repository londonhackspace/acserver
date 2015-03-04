<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "api";
//$route['404_override'] = 'api/page_missing';


// Sync with membership file
$route['update_from_carddb'] = "api/update_from_carddb";
$route['batch_update_from_carddb'] = "api/batch_update_from_carddb";


// Get Card Permission
// Returns the status for a given node and given card.
// http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Get_card_permissions
$route[':num/card/:any'] = "api/card";


// Add Card
// A supervisor card grants access to a device.
// http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Add_card
$route[':num/grant-to-card/:any/by-card/:any'] = "api/grant_to_card_by_card";


// Check DB Sync
// Returns the first card in the db for a given acnode entry, and when re-called with that node
// as an argument, returns the next card in the DB. Returns END on completion
// http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Check_DB_sync
$route[':num/sync'] = "api/sync";
$route[':num/sync/:any'] = "api/sync";


// Returns or sets tool status.
// 1 means tool is ok for use, 0 means it is not
// http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Report_tool_status
// http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Check_tool_status
$route[':num/status'] = "api/status";
$route[':num/status/:num/by/:any'] = "api/change_status";


// Tool usage - Live
// Notes when a starts being used by a user, or when it stops being used by a user
// http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Tool_usage_.28live.29
$route[':num/tooluse/:num/:any'] = "api/tooluselive";


// Tool usage (usage time)
// Notes how much time a user spent on a tool
// http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Tool_usage_.28usage_time.29
$route[':num/tooluse/time/for/:any/:num'] = "api/toolusetime";



// Case Alter
// Notes when the tool's case is opened / closed
// http://wiki.london.hackspace.org.uk/view/Project:Tool_Access_Control/Solexious_Proposal#Case_alert
$route[':num/case/:any'] = "api/case_change";

// Is tool in use
// Reports wether the tool is being used
// https://github.com/londonhackspace/acserver/issues/8
$route[':num/is_tool_in_use'] = "api/is_tool_in_use";


/* End of file routes.php */
/* Location: ./application/config/routes.php */