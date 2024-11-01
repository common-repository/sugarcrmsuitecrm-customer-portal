<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( !class_exists( 'SugarRestApiCall' ) ) {
    Class SugarRestApiCall {

        var $base_url;
        var $username;
        var $password;
        var $access_token;
        var $user_id;

        function __construct($base_url, $username, $password) {
            $this->base_url = $base_url;
            $this->username = $username;
            $this->password = $password;
            $login_response = $this->login();
            if (!empty($login_response->access_token)) {
                $this->access_token = $login_response->access_token;
            }
        }

        /**
         * Generic function to make cURL request.
         * @param $url - The URL route to use.
         * @param string $oauthtoken - The oauth token.
         * @param string $type - GET, POST, PUT, DELETE. Defaults to GET.
         * @param array $arguments - Endpoint arguments.
         * @param array $encodeData - Whether or not to JSON encode the data.
         * @param array $returnHeaders - Whether or not to return the headers.
         * @return mixed
         */
        function call($url, $oauthtoken = '', $type = 'GET', $arguments = array(), $encodeData = true, $returnHeaders = false) {
            $type = strtoupper($type);

            if ($type == 'GET') {
                $url .= "?" . http_build_query($arguments);
            }

            $curl_request = curl_init($url);

            if ($type == 'POST') {
                curl_setopt($curl_request, CURLOPT_POST, 1);
            } elseif ($type == 'PUT') {
                curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "PUT");
            } elseif ($type == 'DELETE') {
                curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "DELETE");
            }

            curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($curl_request, CURLOPT_HEADER, $returnHeaders);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

            if (!empty($oauthtoken)) {
                $token = array("oauth-token: {$oauthtoken}");
                curl_setopt($curl_request, CURLOPT_HTTPHEADER, $token);
            }

            if (!empty($arguments) && $type !== 'GET') {
                if ($encodeData) {
                    //encode the arguments as JSON
                    $arguments = json_encode($arguments);
                }
                curl_setopt($curl_request, CURLOPT_POSTFIELDS, $arguments);
            }

            $result = curl_exec($curl_request);

            if ($returnHeaders) {
                //set headers from response
                list($headers, $content) = explode("\r\n\r\n", $result, 2);
                foreach (explode("\r\n", $headers) as $header) {
                    header($header);
                }

                //return the nonheader data
                return trim($content);
            }

            curl_close($curl_request);

            //decode the response from JSON
            $response = json_decode($result);

            return $response;
        }

        function login() {
            //Login - POST /oauth2/token

            $url = $this->base_url . "/oauth2/token";

            $oauth2_token_arguments = array(
                "grant_type" => "password",
                //client id/secret you created in Admin > OAuth Keys
                "client_id" => "sugar",
                "client_secret" => "",
                "username" => $this->username,
                "password" => $this->password,
                "platform" => "base"
            );

            $oauth2_token_response = $this->call($url, '', 'POST', $oauth2_token_arguments);            
            return $oauth2_token_response;
        }

        //get relationship between two modules
        function getRelationship($module_name, $module_id, $relationship_name, $fields = '', $where_condition = array(), $limit = '', $offset = '', $order_by = '') {
            if ($this->access_token != '') {
                $filter_arguments = array(
                    "filter" => array(
                        $where_condition,
                    ),
                    "offset" => $offset,
                    "order_by" => $order_by,
                    "max_num" => $limit,
                    "fields" => $fields
                );
                $url = "$this->base_url/{$module_name}/{$module_id}/link/{$relationship_name}";
                $link_response = $this->call($url, $this->access_token, 'GET', $filter_arguments);

                return $link_response;
            }
        }

        //Set entry
        function set_entry($module_name, $set_entry_dataArray) {
            $url = $this->base_url;
            if (isset($set_entry_dataArray['id']) && $set_entry_dataArray['id'] != '') {//Added by BC on 28-oct-2015 $set_entry_dataArray['id'] != ''
                $isUpdate = true;
            } else {
                $isUpdate = false;
            }
            if ($this->access_token != '') {
                if ($isUpdate == true) {
                    $url = $url . "/{$module_name}/{$set_entry_dataArray['id']}";
                    unset($set_entry_dataArray['id']);
                    $response = $this->call($url, $this->access_token, 'PUT', $set_entry_dataArray);
                    $return_id = (isset($response->id)) ? $response->id : NULL;
                    return $return_id;
                } else {
                    $url = $url . "/{$module_name}";
                    $response = $this->call($url, $this->access_token, 'POST', $set_entry_dataArray);
                    $return_id = (isset($response->id)) ? $response->id : NULL;
                    return $return_id;
                }
            }
        }

        //Set Relationship
        function set_relationship($module, $module_id, $relate_module, $relate_id) {
            $url = $this->base_url;
            if ($this->access_token != '') {
                $nameValueList = array(
                    'link_name' => $relate_module,
                    'ids' => array($relate_id), // second module id.
                    'sugar_id' => $module_id // first module id
                );
                $url = $url . "/{$module}/{$nameValueList['sugar_id']}/link";
                unset($nameValueList['sugar_id']);
                $response = $this->call($url, $this->access_token, 'POST', $nameValueList);
                return $response;
            }
        }

        //get Record Detail by record id
        function getRecordDetail($module, $record_id) {
            if ($this->access_token != '') {
                $url = $this->base_url . "/{$module}/{$record_id}";
                $response = $this->call($url, $this->access_token, 'GET');
                return $response;
            }
        }

        //get Module Record Detail by passing query
        function getModuleRecords($module_name, $fields = "", $limit = "", $offset = "") {
            if ($this->access_token != '') {
                $filter_arguments = array(
                    "filter" => array(
                    ),
                    "offset" => $offset,
                    "max_num" => $limit,
                    "fields" => $fields,
                );
                $module = $module_name;
                $url = $this->base_url;
                $url = $url . "/{$module}/filter";
                $response = $this->call($url, $this->access_token, 'POST', $filter_arguments);
                return $response;
            }
        }

        // Portal Login by username and password
        function PortalLogin($username, $password, $login_by_email = 0) {
            if ($this->access_token != '') {
                        $filter_arguments = array(
                            "filter" => array(
                                array(
                                    '$and' => array(
                                        array(
                                            "username_c" => array(
                                                '$equals' => "{$username}",
                                            )
                                        ),
                                        array(
                                            "password_c" => array(
                                                '$equals' => "{$password}",
                                            ),
                                        )
                                    ),
                                ),
                            ),
                            "offset" => 0,
                            "fields" => "id,username_c,password_c,salutation,first_name,last_name,email1,account_id,title,phone_work,phone_mobile','phone_fax",
                        );
                    }
                    $module = "Contacts";
                    $url = $this->base_url;
                    $url = $url . "/{$module}/filter";
                    $response = $this->call($url, $this->access_token, 'POST', $filter_arguments);
                    return $response;
        }

        //Check user information by username
        function getUserInformationByUsername($username) {
            if ($this->access_token != '') {
                $filter_arguments = array(
                    "filter" => array(
                        array(
                            "username_c" => array(
                                '$equals' => "{$username}",
                            ),
                        ),
                    ),
                    "offset" => 0,
                    "fields" => "id,username_c,password_c,email1",
                );
                $url = $this->base_url . "/Contacts/filter";
                $response = $this->call($url, $this->access_token, 'GET', $filter_arguments);
            }
            $isUser = $response->records[0]->username_c;
            if ($isUser == $username) {
                return $response;
            } else {
                return false;
            }
        }

        //Check email id exists
        function getPortalEmailExists($email1, $portal_username = '') {
            if ($this->access_token != '') {
                if ($portal_username != '') {
                    $filter_arguments = array(
                        "filter" => array(
                            array(
                                '$and' => array(
                                    array(
                                        "username_c" => array(
                                            '$not_equals' => "{$portal_username}",
                                        )
                                    ),
                                    array(
                                        "email1" => array(
                                            '$equals' => "{$email1}",
                                        )
                                    )
                                )
                            )
                        ),
                        "offset" => 0,
                        "fields" => "id,username_c,password_c,email1",
                    );
                } else {
                    $filter_arguments = array(
                        "filter" => array(
                            array(
                                "email1" => array(
                                    '$equals' => "{$email1}",
                                ),
                            ),
                        ),
                        "offset" => 0,
                        "fields" => "id,username_c,password_c,email1",
                    );
                }
                $url = $this->base_url . "/Contacts/filter";
                $response = $this->call($url, $this->access_token, 'GET', $filter_arguments);
            }
            $isEmail = $response->records[0]->email1;
            if ($isEmail == $email1) {
                //return $response;
                return true;
            } else {
                return false;
            }
        }

        //get User Information by user id
        function getPortalUserInformation($contact_id) {
            if ($this->access_token != '') {
                $url = $this->base_url . "/Contacts/" . $contact_id;
                $user_response = $this->call($url, $this->access_token, 'GET');
                return $user_response;
            }
        }

    }
}