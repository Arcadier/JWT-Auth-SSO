<?php
require_once '../admin/admin_token.php';
//apisdk
class ApiSdk
{
    private $adminToken = '';
    private $merchantToken = '';
    private $baseUrl    = '';
    public function __construct()
    {
        $this->baseUrl = $this->getMarketplaceBaseUrl();
    }

    public function callAPI($method, $access_token, $url, $data = false)
    {
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) {
                    $jsonDataEncoded = json_encode($data);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonDataEncoded);
                }
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    $jsonDataEncoded = json_encode($data);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonDataEncoded);
                }
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }
        $headers = ['Content-Type: application/json'];
        if ($access_token != null && $access_token != '') {
            array_push($headers, sprintf('Authorization: Bearer %s', $access_token));
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    public function getMarketplaceBaseUrl()
    {
        $marketplace = $_COOKIE["marketplace"];
        $protocol    = $_COOKIE["protocol"];

        $baseUrl = $protocol . '://' . $marketplace;
        return $baseUrl;
    }

    public function getPackageID()
    {
        $requestUri = "$_SERVER[REQUEST_URI]";
        preg_match('/([a-f0-9]{8}(?:-[a-f0-9]{4}){3}-[a-f0-9]{12})/', $requestUri, $matches, 0);
        return $matches[0];
    }

    public function getCustomFieldPrefix()
    {
        $requestUri = "$_SERVER[REQUEST_URI]";
        preg_match('/([a-f0-9]{8}(?:-[a-f0-9]{4}){3}-[a-f0-9]{12})/', $requestUri, $matches, 0);
        $customFieldPrefix = str_replace('-', '', $matches[0]);
        return $customFieldPrefix;
    }

    ///////////////////////////////////////////////////// BEGIN USER APIs /////////////////////////////////////////////////////

    public function getUserInfo($id, $include)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url = $this->baseUrl . '/api/v2/users/' . $id;
        if ($include != null) {
            $url .= "?includes=" . $include;
        }
        $userInfo = $this->callAPI("GET", $this->adminToken['access_token'], $url, null);
        return $userInfo;
    }

    public function updateUserInfo($id, $data)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url      = $this->baseUrl . '/api/v2/users/' . $id;
        $userInfo = $this->callAPI("PUT", $this->adminToken['access_token'], $url, $data);
        return $userInfo;
    }

    public function upgradeUserRole($id, $role)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }

        $url = $this->baseUrl . '/api/v2/admins/' . $this->adminToken['UserId'] . '/users/' . $id . '/roles/' . $role;
        $userRole = $this->callAPI("PUT", $this->adminToken['access_token'], $url, null);
        return $userRole;
    }

    public function deleteUser($id)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }

        $url = $this->baseUrl . '/api/v2/admins/' . $this->adminToken['UserId'] . '/users/' . $id;
        $deletedUser = $this->callAPI("DELETE", $this->adminToken['access_token'], $url, null);
        return $deletedUser;
    }

    ///////////////////////////////////////////////////// END USER APIs /////////////////////////////////////////////////////

    ///////////////////////////////////////////////////// BEGIN ADDRESS APIs /////////////////////////////////////////////////////
    public function getUserAddress($id,  $addressID)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }

        $url = $this->baseUrl . '/api/v2/users/' . $id . '/addresses/' . $addressID;
        $newAddress = $this->callAPI("GET", $this->adminToken['access_token'], $url, null);
        return $newAddress;
    }

    public function createUserAddress($id, $data)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }

        $url = $this->baseUrl . '/api/v2/users/' . $id . '/addresses/';
        $newAddress = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $newAddress;
    }

    public function updateUserAddress($id, $addressID, $data)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }

        $url = $this->baseUrl . '/api/v2/users/' . $id . '/addresses/' . $addressID;
        $updatedAddress = $this->callAPI("PUT", $this->adminToken['access_token'], $url, $data);
        return $updatedAddress;
    }

    public function deleteUserAddress($id, $addressID)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }

        $url = $this->baseUrl . '/api/v2/users/' . $id . '/addresses/' . $addressID;
        $deletedAddress = $this->callAPI("DELETE", $this->adminToken['access_token'], $url, null);
        return $deletedAddress;
    }


    ///////////////////////////////////////////////////// END ADDRESS APIs /////////////////////////////////////////////////////

    public function getAdminId()
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        return $this->adminToken['UserId'];
    }

    function getMerchantToken($username, $password)
    {
        $marketplace = $_COOKIE["marketplace"];
        $protocol = $_COOKIE["protocol"];
        $baseUrl = $protocol . '://' . $marketplace;
        $url = $baseUrl . '/token';
        $client_id = '{client_id}';
        $client_secret = '{client_secret}';
        $body = 'grant_type=client_credentials&client_id=' . $client_id . '&client_secret=' . $client_secret . '&scope=admin'
            . '&username:' . $username . '&password:' . $password;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
    }

    ///////////////////////////////////////////////////// BEGIN ITEM APIs /////////////////////////////////////////////////////

    public function getItemInfo($id)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url      = $this->baseUrl . '/api/v2/items/' . $id;
        $itemInfo = $this->callAPI("GET", null, $url, null);
        return $itemInfo;
    }

    public function getAllItems($sortParams)
    {
        $url       = $this->baseUrl . '/api/v2/items/?' . $sortParams;
        /* if ( isset($createdAscParam) ) {
            $url .= $createdAscParam . "&"
        };

        if ( isset($updatedAscParam) ) {
            $url .= $updatedAscParam . "&"
        };

        if ( isset($priceAscParam) ) {
            $url .= $priceAscParam . "&"
        };

        if ( isset($keywordsParam) ) {
            $url .= "keywords=" . $priceAscParam . "&"
        }; 

        if ( isset($nameParam) ) {
            $url .= $nameParam . "&"
        }; */

        $items = $this->callAPI("GET", $this->adminToken['access_token'], $url, null);
        return $items;
    }

    public function getAllItemsJsonFiltering($data)
    {
        $url       = $this->baseUrl . '/api/v2/items';
        $items = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $items;
    }

    // For Creating an item and creating a listing
    public function createItem($data, $merchantId)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url       = $this->baseUrl . '/api/v2/merchants/' . $merchantId . '/items';
        $createdItem = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $createdItem;
    }

    public function editItem($data, $merchantId, $itemId)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url       = $this->baseUrl . '/api/v2/merchants/' . $merchantId . '/items/' . $itemId;
        $editedItem = $this->callAPI("PUT", $this->adminToken['access_token'], $url, $data);
        return $editedItem;
    }

    public function deleteItem($merchantId, $itemId)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url       = $this->baseUrl . '/api/v2/merchants/' . $merchantId . '/items/' . $itemId;
        $deletedItem = $this->callAPI("DELETE", $this->adminToken['access_token'], $url, null);
        return $deletedItem;
    }

    public function getItemTags($filterParams)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url       = $this->baseUrl . '/api/v2/tags/?' . $filterParams;
        $tags = $this->callAPI("GET", $this->adminToken['access_token'], $url, null);
        return $tags;
    }

    public function tagItem($data, $merchantId, $itemId)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url       = $this->baseUrl . '/api/v2/merchants/' . $merchantId . '/items/' . $itemId . '/tags';
        $result = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $result;
    }

    public function deleteTags($data)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url       = $this->baseUrl . '/api/v2/tags';
        $result = $this->callAPI("DELETE", $this->adminToken['access_token'], $url, $data);
        return $result;
    }

    ///////////////////////////////////////////////////// END ITEM APIs /////////////////////////////////////////////////////

    public function getOrderInfo($id, $buyerId)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url       = $this->baseUrl . '/api/v2/users/' . $buyerId . '/orders/' . $id;
        $orderInfo = $this->callAPI("GET", $this->adminToken['access_token'], $url, null);
        return $orderInfo;
    }

    public function updateOrders($data)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url       = $this->baseUrl . '/api/v2/admins/' . $this->adminToken['UserId'] . '/orders?autoUpdatePayment=false';
        $orderInfo = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $orderInfo;
    }

    public function getTransactionInfo($invoiceNo)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url         = $this->baseUrl . '/api/v2/admins/' . $this->adminToken['UserId'] . '/transactions/' . $invoiceNo . '?includes=Transaction.Orders.PaymentDetails,Transaction.Orders.CartItemDetails.ItemDetail.Media';
        $invoiceInfo = $this->callAPI("GET", $this->adminToken['access_token'], $url, null);
        return $invoiceInfo;
    }

    public function updateTransactionInfo($invoiceNo, $data)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url         = $this->baseUrl . '/api/v2/admins/' . $this->adminToken['UserId'] . '/invoices/' . $invoiceNo;
        $invoiceInfo = $this->callAPI("PUT", $this->adminToken['access_token'], $url, $data);
        return $invoiceInfo;
    }

    public function getEventTriggers()
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url           = $this->baseUrl . '/api/v2/event-triggers/';
        $eventTriggers = $this->callAPI("GET", $this->adminToken['access_token'], $url, null);
        return $eventTriggers;
    }

    public function addEventTrigger($uri, $eventIds)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url  = $this->baseUrl . '/api/v2/event-triggers/';
        $data = [
            'Uri'     => $uri,
            'Filters' => $eventIds,
        ];
        $eventResult = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $eventResult;
    }

    public function removeEventTrigger($eventId)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url         = $this->baseUrl . '/api/v2/event-triggers/' . $eventId;
        $eventResult = $this->callAPI("DELETE", $this->adminToken['access_token'], $url, null);
        return $eventResult;
    }

    public function getMarketplaceInfo()
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url           = $this->baseUrl . '/api/v2/marketplaces/';
        $eventTriggers = $this->callAPI("GET", $this->adminToken['access_token'], $url, null);
        return $eventTriggers;
    }

    public function disableEdms()
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $data = [
            "Settings" => [
                "email-configuration" => [
                    "new-order"      => [
                        "enabled" => "False",
                    ],
                    "received-order" => [
                        "enabled" => "False",
                    ],
                ],
            ],
        ];
        $url           = $this->baseUrl . '/api/v2/marketplaces/';
        $eventTriggers = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $eventTriggers;
    }

    public function enabledEdms()
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $data = [
            "Settings" => [
                "email-configuration" => [
                    "new-order"      => [
                        "enabled" => "True",
                    ],
                    "received-order" => [
                        "enabled" => "True",
                    ],
                ],
            ],
        ];
        $url           = $this->baseUrl . '/api/v2/marketplaces/';
        $eventTriggers = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $eventTriggers;
    }

    public function sendEmail($to, $html, $subject)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url  = $this->baseUrl . '/api/v2/admins/' . $this->adminToken['UserId'] . '/emails/';
        $data = [
            'From'    => 'admin@arcadier.com',
            'To'      => $to,
            'Body'    => $html,
            'Subject' => $subject,
        ];
        $emailResult = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $emailResult;
    }

    public function ssoToken($exUserId, $userEmail)
    {
        if ($this->adminToken == null) {
            $this->adminToken = getAdminToken();
        }
        $url  = $this->baseUrl . '/api/v2/sso';
        $data = [
            'ExternalUserId' => $exUserId,
            'Email'          => $userEmail,
        ];
        $emailResult = $this->callAPI("POST", $this->adminToken['access_token'], $url, $data);
        return $emailResult;
    }
}
