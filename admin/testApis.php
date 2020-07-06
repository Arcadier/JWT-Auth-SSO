<?php
require_once '../sdk/ApiSdk.php';

$sdk = new ApiSdk();
$contentBodyJson = file_get_contents('php://input');
$content = json_decode($contentBodyJson, true);

$apiType = $content['apiType'];
$keywords = explode(":", $apiType);
switch ($keywords[0]) {
        //ITEMS TEST CASES
    case "filter":
        $response = $sdk->getAllItems("?maxPrice=45&pageSize=1&pageNumber=2");
        echo json_encode($response);
        break;
    case "jsonfilter":
        $response = $sdk->getAllItemsJsonFiltering(
            [
                "maxPrice" => 45,
                "pageSize" => 1,
                "pageNumber" => 2
            ]
        );
        echo json_encode($response);
        break;
    case "createitem":
        $response = $sdk->createItem(
            [
                "Categories" => array(
                    [
                        "ID" => "c68c6c51-9e9d-4b22-93b4-d9c0c7d64d24"
                    ]
                ),
                "Name" => "Roses",
                "Price" => 65,
                "CurrencyCode" => "EUR",
                "SellerDescription" => "red roses",
                "SKU" => "676545",
                "StockQuantity" => 75,
                "Media" => array(
                    [
                        "MediaUrl" => "https://d2q2f0pfv13tpb.cloudfront.net/wp-content/uploads/2019/05/mothers-day-flowers-768x549.jpg"
                    ]
                ),
            ],
            "015bf8c9-5332-4717-a26f-b09f15692e4d"
        );
        echo json_encode($response);
        break;
    case "edititem":
        $response = $sdk->editItem(
            [
                "Name" => "abibas" . strval(rand()),
            ],
            "015bf8c9-5332-4717-a26f-b09f15692e4d",
            "8dfa6111-68eb-4aba-ae09-41a966996006"
        );
        echo json_encode($response);
        break;
    case "tagitem":
        $response = $sdk->tagItem(
            [
                "Scented", "Fragrant", "Musky", "Invigorating"
            ],
            "015bf8c9-5332-4717-a26f-b09f15692e4d",
            "8dfa6111-68eb-4aba-ae09-41a966996006"
        );
        echo json_encode($response);
        break;
    case "gettags":
        $response = $sdk->getItemTags("?pageSize=3");
        echo json_encode($response);
        break;
    case "deletetags":
        $response = $sdk->deleteTags(
            [
                "Scented", "Musky"
            ]
        );
        echo json_encode($response);
        break;
    case "deleteitem":
        $response = $sdk->deleteItem(
            "015bf8c9-5332-4717-a26f-b09f15692e4d",
            $keywords[1]
        );
        echo json_encode($response);
        break;
        //USERS TEST CASES
    case "getusers":
        $response = $sdk->getAllUsers(null);
        echo json_encode($response);
        break;
    case "getmerchants":
        $response = $sdk->getAllUsers("?role=merchant");
        echo json_encode($response);
        break;
    case "registeruser":
        $response = $sdk->registerUser(
            [
                "Email" => "testuser1@gmail.com",
                "Password" => "testuser1pw",
                "ConfirmPassword" => "testuser1pw"
            ]
        );
        echo json_encode($response);
        break;
    case "resetpassword":
        $response = $sdk->resetPassword(
            [
                "UserId" => "2b4ea0dc-583d-4bb5-9c9a-0d2b2c7ab5e4",
                "Action" => "token"
            ]
        );
        echo json_encode($response);
        break;
    case "password":
        $response = $sdk->updatePassword(
            [
                "Password" => "testuser1pw",
                "ConfirmPassword" => "testuser1pw",
                "ResetPasswordToken" => $keywords[1],
            ],
            "2b4ea0dc-583d-4bb5-9c9a-0d2b2c7ab5e4"
        );
        echo json_encode($response);
        break;
        //CART TEST CASES
    case "addtocart":
        $response = $sdk->addToCart(
            [
                "ItemDetail" => [
                    "ID" => "0751e830-b6fe-49d2-9f1b-89a90cafd858"
                ],
                "Quantity" => 5,
                "CartItemType" => "delivery",
                "ShippingMethod" => [
                    "ID" => "e35cb2b3-c09a-448c-99a4-98999c83bf32"
                ]
            ],
            "c33cfb0f-b665-42e9-bb04-84c723e7e65a",
            "bryanchee@arcadier.com",
            "bryanchee"
        );
        echo json_encode($response);
        break;
    case "getcart":
        $response = $sdk->getCart(
            "c33cfb0f-b665-42e9-bb04-84c723e7e65a"
        );
        echo json_encode($response);
        break;
    case "updatecartput":
        $response = $sdk->updateCartItem(
            [
                "Quantity" => 5
            ],
            "c33cfb0f-b665-42e9-bb04-84c723e7e65a",
            "1ef9d2d7-2106-4e68-894b-e6bfde96c13c",
            "bryanchee@arcadier.com",
            "bryanchee",
            true
        );
        echo json_encode($response);
        break;
    case "updatecartpost":
        $response = $sdk->updateCartItem(
            [
                "Quantity" => 5
            ],
            "c33cfb0f-b665-42e9-bb04-84c723e7e65a",
            "1ef9d2d7-2106-4e68-894b-e6bfde96c13c",
            "bryanchee@arcadier.com",
            "bryanchee",
            false
        );
        echo json_encode($response);
        break;
        //Transaction test cases
    case "gettransactions":
        $response = $sdk->getAllTransactions();
        echo json_encode($response);
        break;
    case "getfilteredtransactions":
        $response = $sdk->getAllFilteredTransactions(3, 1, $keywords[1], $keywords[2]);
        echo json_encode($response);
        break;
    case "getbuyertransactions":
        $response = $sdk->getBuyerTransactions($keywords[1]);
        echo json_encode($response);
        break;
        //Custom Table test cases
    case "getcustomtable":
        $response = $sdk->getCustomTable($keywords[1], "TestTable");
        echo json_encode($response);
        break;
    case "newrowentry":
        $response = $sdk->createRowEntry(
            $keywords[1],
            "TestTable",
            [
                "Age" => 34,
                "Gender" => "undecided",
                "Name" => "TestUser"
            ]
        );
        echo json_encode($response);
        break;
    case "editrowentry":
        $response = $sdk->editRowEntry(
            $keywords[1],
            "TestTable",
            $keywords[2],
            [
                "Age" => 42
            ]
        );
        echo json_encode($response);
        break;
    case "searchcustomtable":
        $response = $sdk->searchTable(
            $keywords[1],
            "TestTable",
            array(
                [
                    "Name" => "Age",
                    "Operator" => "equal",
                    "Value" => "42"
                ]
            )
        );
        echo json_encode($response);
        break;
    case "deleterowentry":
        $response = $sdk->deleteRowEntry(
            $keywords[1],
            "TestTable",
            $keywords[2]
        );
        echo json_encode($response);
        break;
    default:
        echo "Something went wrong ";
}
