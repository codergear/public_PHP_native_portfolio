<?php




namespace gtc_core\internals {
    class url
    {
        public function filename()
        {
            return basename($_SERVER['PHP_SELF']);
        }

        public function url_file()
        {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $protocol = 'https://';
            } else {
                $protocol = 'http://';
            }

            return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        public function url_folder()
        {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $protocol = 'https://';
            } else {
                $protocol = 'http://';
            }
            return $protocol . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['PHP_SELF']), "", $_SERVER['REQUEST_URI']);
        }

        public function url_sub_folder()
        {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $protocol = 'https://';
            } else {
                $protocol = 'http://';
            }
            $url_folder = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            return dirname($url_folder, 2) . "/";
        }
    }
}


namespace gtc_core {
    class Functions
    {
        public static function get_enviroment()
        {
            $enviroment = "production";

            if (strpos(Functions::url()->url_folder(), "sandbox") !== false) {
                $enviroment = "sandbox";
            }

            if ($_SERVER['SERVER_NAME'] == 'localhost') {
                $enviroment = "local";
            }

            return $enviroment;
        }

        public static function url()
        {
            return new \gtc_core\internals\url();
        }

        public static function equalize($origin, $container)
        {
            foreach ($origin as $clave => $valor) {
                $container->$clave = $valor;
            }
            return $container;
        }

        public static function data64Mask($str)
        {
            return base64_encode(uniqid() . $str);
        }

        public static function data64UnMask($str)
        {
            return substr(base64_decode($str), 13);
        }

        public static function random_password($length = 8)
        {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $password .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
            return $password;
        }

        public static function jwt_decode($jwt)
        {
            try {
                require_once('../integration/JWT/JWT.php');
                require_once('../integration/JWT/Key.php');
                $key = 'gtc_core';
                $result = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($key, 'HS256'));
            } catch (\Throwable | \Exception | \DomainException $e) {
                //silent exception
            }

            return $result;
        }

        public static function jwt_encode($json)
        {
            require_once('../integration/JWT/JWT.php');
            $jwt = new \Firebase\JWT\JWT;
            $key = 'gtc_core';
            $token = array(
                "iss" => "GTK",
                "aud" => "data",
                "rtti" => $json
            );
            $jwt = $jwt::encode($token, $key, 'HS256');
            return $jwt;
        }

        public static function security_access_log($id_user, $request_module, $request_action, $request_record_id)
        {

            if (strpos($request_module, "handler_security_access_log") !== false) {
                return false;
            }

            $ipaddress = 'UNTRACEABLE';
            try {
                if (getenv('HTTP_CLIENT_IP'))
                    $ipaddress = getenv('HTTP_CLIENT_IP');
                else if (getenv('HTTP_X_FORWARDED_FOR'))
                    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
                else if (getenv('HTTP_X_FORWARDED'))
                    $ipaddress = getenv('HTTP_X_FORWARDED');
                else if (getenv('HTTP_FORWARDED_FOR'))
                    $ipaddress = getenv('HTTP_FORWARDED_FOR');
                else if (getenv('HTTP_FORWARDED'))
                    $ipaddress = getenv('HTTP_FORWARDED');
                else if (getenv('REMOTE_ADDR'))
                    $ipaddress = getenv('REMOTE_ADDR');
                else
                    $ipaddress = 'UNKNOWN';
            } catch (\Throwable | \Exception $e) {
                // // silent exception
            }

            try {
                $security_access_log = new classsecurity_access_log;
                $security_access_log->user_id = $id_user;
                $security_access_log->https = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'off';
                $security_access_log->user_agent = $_SERVER['HTTP_USER_AGENT'];
                $security_access_log->accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
                $security_access_log->remote_addr = $ipaddress;
                $security_access_log->request_time = date('m/d/Y H:i:s');
                $security_access_log->request_module = $request_module;
                $security_access_log->request_action = $request_action;
                $security_access_log->request_record_id = $request_record_id;
                $security_access_log->insert($security_access_log, $id_user);
            } catch (\Throwable | \Exception $e) {
                // // silent exception
            }
        }


        public static function sum_by($list, $filter, $data)
        {
            if ($list) {
                $result = array();
                foreach ($list as $item) {
                    if (self::count_items_by($result, $filter, $item->$filter) == 0) {
                        $item->$data = str_replace("$", "", $item->$data);
                        array_push($result, $item);
                    } else {
                        foreach ($result as $item_result) {
                            if ($item->$filter == $item_result->$filter) {
                                $item->$data = str_replace("$", "", $item->$data);
                                $item_result->$data = $item_result->$data + $item->$data;
                            }
                        }
                    }
                }
                return $result;
            } else {
                return null;
            }
        }

        public static function multi_sort_items_by($list, $property1, $reverse1 = "", $property2 = NULL, $reverse2 = "", $property3 = NULL, $reverse3 = "")
        {
            if (!is_array($list)) {
                return (False);
            }
            $result = $list;
            if (!is_null($property3)) {
                $result = self::sort_items_by($result, $property3, $reverse3);
            }
            if (!is_null($property2)) {
                $result = self::sort_items_by($result, $property2, $reverse2);
            }
            $result = self::sort_items_by($result, $property1, $reverse1);
            return ($result);
        }

        public static function sort_items_by($list, $property, $reverse = "")
        {
            if (strtoupper($reverse) == "ASC") {
                $reverse = false;
            } else {
                $reverse = true;
            }
            if (!is_array($list))
                return (False);
            uasort($list, function ($itemA, $itemB) use ($property, $reverse) {
                if ($itemA->$property < $itemB->$property)
                    return (($reverse) ? 1 : -1);
                if ($itemA->$property > $itemB->$property)
                    return (($reverse) ? -1 : 1);
                return (0);
            });
            array_splice($list, 0, 0);
            return ($list);
        }

        public static function get_items_by($list, $property, $value)
        {
            if (!is_array($list))
                return (False);
            return (array_values(array_filter($list, function ($item) use ($property, $value) {
                if (!property_exists($item, $property))
                    return (False);
                return ($item->$property == $value);
            })));
        }

        public static function group_items_by($list, $property_list, $filter_fn = null)
        {
            $result = [];
            $sub_result = [];

            if (!is_array($list)) {
                return false;
            }

            foreach ($property_list as $property) {
                if (!property_exists($list[0], $property)) {
                    return false;
                }
            }

            foreach ($list as $item) {
                $sub_result = [];

                foreach ($list as $sub_item) {
                    $flag_total = 0;
                    $flag_mask = 0;
                    foreach ($property_list as $sub_property) {
                        $flag_total++;
                        if ($sub_item->$sub_property == $item->$sub_property) {
                            $flag_mask++;
                        }
                    }
                    if ($flag_mask == $flag_total) {
                        array_push($sub_result, $sub_item);
                    }
                }

                $found = false;
                foreach ($result as $result_item) {
                    $match = true;
                    foreach ($property_list as $property) {
                        if ($result_item->$property != $item->$property) {
                            $match = false;
                            break;
                        }
                    }
                    if ($match) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    if (property_exists($sub_result[0], '_date_umo')) {
                        $sub_result = self::sort_items_by($sub_result, '_date_umo', 'DESC');
                    }

                    if ($filter_fn) {
                        $result_item = $filter_fn($sub_result);
                    } else {
                        $result_item = $sub_result[0];
                    }
                    array_push($result, $result_item);
                }
            }

            return $result;
        }

        public static function get_items_list_by($listA, $listB, $propertyA, $propertyB)
        {
            $result = [];
            if (!is_array($listA)) {
                return $result;
            }
            if (!is_array($listB)) {
                return $result;
            }

            foreach ($listA as $itemA) {
                foreach ($listB as $itemB) {
                    if ($itemA->$propertyA == $itemB->$propertyB) {
                        array_push($result, $itemB);
                    }
                }
            }
            return ($result);
        }

        public static function list_to_array($list, $property)
        {
            $result = [];
            if (!is_array($list)) {
                return $result;
            }
            foreach ($list as $item) {
                array_push($result, $item->$property);
            }
            return ($result);
        }

        public static function count_items_by($list, $property, $value)
        {
            if (!is_array($list)) {
                return 0;
            }

            return (count(self::get_items_by($list, $property, $value)));
        }


        public static function filter_items_by_interval($list, $property, $filterDateBegin, $filterDateEnd)
        {
            if (strtotime($filterDateBegin) > strtotime($filterDateEnd)) {
                $temp = $filterDateBegin;
                $filterDateBegin = $filterDateEnd;
                $filterDateEnd = $temp;
            }

            if (!is_array($list))
                return (False);
            $result = array_filter($list, function ($item) use ($property, $filterDateBegin, $filterDateEnd) {
                $localDate = date('m/d/Y', strtotime($item->$property));
                if (($filterDateBegin == '') && ($filterDateEnd == '')) {
                    $filterDateBegin = date('m/d/Y', strtotime('first day of this month', time()));
                    $filterDateEnd = date('m/d/Y', strtotime('last day of this month', time()));
                } else {
                    $filterDateBegin = date('m/d/Y', strtotime($filterDateBegin));
                    $filterDateEnd = date('m/d/Y', strtotime($filterDateEnd));
                }
                return ((strtotime($localDate) >= strtotime($filterDateBegin)) && (strtotime($localDate) <= strtotime($filterDateEnd)));
            });
            array_splice($result, 0, 0);
            return ($result);
        }

        public static function filter_items_by_datetime($list, $property, $filterDateBegin, $filterDateEnd)
        {
            if (!is_array($list))
                return (false);
            $result = array_filter($list, function ($item) use ($property, $filterDateBegin, $filterDateEnd) {
                $localDate = strtotime($item->$property);
                if ($filterDateBegin == '') {
                    $filterDateBegin = strtotime('first day of this month', time());
                } else {
                    $filterDateBegin = strtotime($filterDateBegin);
                }

                if ($filterDateEnd == '') {
                    $filterDateEnd = strtotime('last day of this month', time());
                } else {
                    $filterDateEnd = strtotime($filterDateEnd);
                }
                return (($localDate >= $filterDateBegin) && ($localDate <= $filterDateEnd));
            });
            array_splice($result, 0, 0);
            if (count($result) == 0) {
                return false;
            }
            return ($result);
        }

        public static function filter_items_by_datetonly($list, $property, $filterDateBegin, $filterDateEnd)
        {
            if (!is_array($list))
                return (false);
            $result = array_filter($list, function ($item) use ($property, $filterDateBegin, $filterDateEnd) {
                $localDate = strtotime(date('m/d/Y', strtotime($item->$property)));
                if ($filterDateBegin == '') {
                    $filterDateBegin = strtotime(date('m/d/Y', strtotime('first day of this month', time())));
                } else {
                    $filterDateBegin = strtotime(date('m/d/Y', strtotime($filterDateBegin)));
                }

                if ($filterDateEnd == '') {
                    $filterDateEnd = strtotime(date('m/d/Y', strtotime('last day of this month', time())));
                } else {
                    $filterDateEnd = strtotime(date('m/d/Y', strtotime($filterDateEnd)));
                }
                return (($localDate >= $filterDateBegin) && ($localDate <= $filterDateEnd));
            });
            array_splice($result, 0, 0);
            if (count($result) == 0) {
                return false;
            }
            return ($result);
        }



    }
}
?>