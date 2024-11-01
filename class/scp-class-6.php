<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( !class_exists( 'SugarRestApiCall' ) ) {
    Class SugarRestApiCall {

        var $username;
        var $password;
        var $url;
        var $session_id;
        var $user_id;

        function __construct($url, $username, $password) {
            $this->url = $url;
            $this->username = $username;
            $this->password = $password;
            $login_response = $this->login();
            $this->session_id = $login_response->id;
            $this->user_id = $login_response->name_value_list->user_id->value;
        }

        public function call($method, $parameters, $url) {
            ob_start();
            $curl_request = curl_init();

            curl_setopt($curl_request, CURLOPT_URL, $url);
            curl_setopt($curl_request, CURLOPT_POST, 1);
            curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($curl_request, CURLOPT_HEADER, 1);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

            $jsonEncodedData = json_encode($parameters);

            $post = array(
                "method" => $method,
                "input_type" => "JSON",
                "response_type" => "JSON",
                "rest_data" => $jsonEncodedData
            );

            curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
            $result = curl_exec($curl_request);
            curl_close($curl_request);

            $result = explode("\r\n\r\n", $result, 2);
            $response = json_decode($result[1]);
            ob_end_flush();

            return $response;
        }

        //Added by BC on 05-aug-2015 for login with admin user
        public function login() {
            $login_parameters = array(
                "user_auth" => array(
                    "user_name" => $this->username,
                    "password" => md5($this->password),
                ),
            );
            $login_response = $this->call('login', $login_parameters, $this->url);
            return $login_response;
        }

        // login into Portal (login call in Users module, it give all information about contact)
        public function PortalLogin($username, $password, $login_by_email = 0) {
            /* $username and $password are passed from login page */
                $get_entry_list = array(
                    'session' => $this->session_id,
                    'module_name' => 'Contacts',
                    'query' => "username_c = '{$username}' AND  password_c = '{$password}'",
                    'order_by' => '',
                    'offset' => 0,
                    'select_fields' => array('id', 'username_c', 'password_c', 'salutation', 'first_name', 'last_name', 'email1', 'account_id', 'title', 'phone_work', 'phone_mobile', 'phone_fax'),
                    'max_results' => 0,
                );
                $get_entry_list_result = $this->call("get_entry_list", $get_entry_list, $this->url);
                return $get_entry_list_result;
        }

        public function set_entry($module_name, $set_entry_dataArray) { // create a new record
            if ($this->session_id) {
                $nameValueListArray = array();
                $i = 0;
                foreach ($set_entry_dataArray as $field => $value) {
                    $nameValueListArray[$i]['name'] = $field;
                    $nameValueListArray[$i]['value'] = $value;
                    $i++;
                }
                $set_entry_parameters = array(
                    "session" => $this->session_id,
                    "module_name" => $module_name,
                    "name_value_list" => $nameValueListArray
                );
                $set_entry_result = $this->call("set_entry", $set_entry_parameters, $this->url);
                $recordID = $set_entry_result->id;
                return $recordID;
            }
        }

        public function get_entry_list($module_name, $where_condition = '', $select_fields_array = array(), $order_by = '', $deleted = 0, $limit = '', $offset = '') {
            if ($this->session_id) {
                $get_entry_list_parameters = array(
                    'session' => $this->session_id,
                    'module_name' => $module_name,
                    'query' => $where_condition,
                    'order_by' => $order_by,
                    "offset" => $offset,
                    'select_fields' => $select_fields_array,
                    'link_name_to_fields_array' => array(),
                    'max_results' => $limit,
                    'deleted' => $deleted,
                );
                $get_entry_list_result = $this->call("get_entry_list", $get_entry_list_parameters, $this->url);
                return $get_entry_list_result;
            }
        }

        public function set_relationship($module_name, $module_id, $relationship_name, $related_ids_array = array(), $deleted = 0) {// set reletion between task and contact
            if ($this->session_id) {
                $set_relationships_parameters = array(
                    'session' => $this->session_id,
                    'module_name' => $module_name,
                    'module_id' => $module_id,
                    'link_field_name' => $relationship_name,
                    'related_ids' => $related_ids_array,
                    'delete' => $deleted,
                );
                $set_relationships_result = $this->call("set_relationship", $set_relationships_parameters, $this->url);
                return $set_relationships_result;
            }
        }

        public function get_relationships($module_name, $module_id, $relationship_name, $related_fields_array = array(), $where_condition = '', $order_By = '', $deleted = 0, $offset = 0, $limit = '') {
            if ($this->session_id) {
                $get_relationships_parameters = array(
                    'session' => $this->session_id,
                    'module_name' => $module_name,
                    'module_id' => $module_id,
                    'link_field_name' => $relationship_name, // relationship name
                    'related_module_query' => $where_condition, // where condition
                    'related_fields' => $related_fields_array,
                    'related_module_link_name_to_fields_array' => array(),
                    'deleted' => $deleted,
                    'order_by' => $order_By,
                    'offset' => $offset,
                    'limit' => $limit
                );

                $get_relationships_result = $this->call("get_relationships", $get_relationships_parameters, $this->url);
                return $get_relationships_result;
            }
        }

        // Check user exists or not
        public function getPortalUserExists($username) {//Updated by BC on 11-aug-2015
            $get_entry_list = array(
                'session' => $this->session_id,
                'module_name' => 'Contacts',
                'query' => "contacts_cstm.username_c = '{$username}'",
                'order_by' => '',
                'offset' => 0,
                'select_fields' => array('id', 'username_c'),
                'max_results' => 0,
            );
            $get_entry_list_result = $this->call("get_entry_list", $get_entry_list, $this->url);
            $isUser = $get_entry_list_result->entry_list[0]->name_value_list->username_c->value;
            if ($isUser == $username) {
                return true;
            } else {
                return false;
            }
        }

        // Get user information by email address //Added by BC on 11-sep-2015
        public function getPortalEmailExists($email1, $portal_username = '') {//Updated by BC on 11-aug-2015
            if ($portal_username != '') {
                $query_pass = "(contacts_cstm.username_c != '{$portal_username}' OR contacts_cstm.username_c IS NULL )AND contacts.id in (
                SELECT eabr.bean_id
                    FROM email_addr_bean_rel eabr JOIN email_addresses ea
                        ON (ea.id = eabr.email_address_id)
                    WHERE eabr.deleted=0 AND ea.email_address ='{$email1}')";
            } else {
                $query_pass = "contacts.id in (
                    SELECT eabr.bean_id
                    FROM email_addr_bean_rel eabr JOIN email_addresses ea
                    ON (ea.id = eabr.email_address_id)
                    WHERE eabr.deleted=0 AND ea.email_address = '{$email1}')";
            }

            $get_entry_list = array(
                'session' => $this->session_id,
                'module_name' => 'Contacts',
                'query' => $query_pass,
                'order_by' => '',
                'offset' => 0,
                'select_fields' => array('id', 'email1'),
                //'max_results' => 0,
                //A list of link names and the fields to be returned for each link name.
                'link_name_to_fields_array' => array(
                ),
                //The maximum number of results to return.
                'max_results' => '',
                //If deleted records should be included in results.
                'deleted' => 0,
                //If only records marked as favorites should be returned.
                'favorites' => false,
            );

            $get_entry_list_result = $this->call("get_entry_list", $get_entry_list, $this->url);

            $isEmailExist = $get_entry_list_result->entry_list[0]->name_value_list->email1->value;
            if ($isEmailExist) {
                return true;
            } else {
                return false;
            }
        }

        // Check user information by username
        public function getPortalUserInformationByUsername($username) {
            $get_entry_list = array(
                'session' => $this->session_id,
                'module_name' => 'Contacts',
                'query' => "contacts_cstm.username_c = '{$username}'",
                'order_by' => '',
                'offset' => 0,
                'select_fields' => array('id', 'username_c', 'password_c', 'email1'),
                'max_results' => 0,
            );
            $get_entry_list_result = $this->call("get_entry_list", $get_entry_list, $this->url);
            $isUser = $get_entry_list_result->entry_list[0]->name_value_list->username_c->value;
            if ($isUser == $username) {
                return $get_entry_list_result;
            } else {
                return false;
            }
        }

        // Get User Information
        public function getPortalUserInformation($user_id) {
            $get_entry_parameters = array(
                'session' => $this->session_id,
                'module_name' => 'Contacts',
                'id' => $_SESSION['scp_user_id'],
            );
            $get_entry_result = $this->call("get_entry", $get_entry_parameters, $this->url);
            return $get_entry_result;
        }

    }
}