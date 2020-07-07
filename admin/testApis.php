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
                    "ID" => $keywords[1]
                ],
                "Quantity" => 5,
                "CartItemType" => "delivery",
                "ShippingMethod" => [
                    "ID" => $keywords[2]
                ]
            ],
            $keywords[3],
            "bryanchee@arcadier.com",
            "bryanchee"
        );
        echo json_encode($response);
        break;
    case "getcart":
        $response = $sdk->getCart(
            $keywords[1]
        );
        echo json_encode($response);
        break;
    case "updatecartput":
        $response = $sdk->updateCartItem(
            [
                "Quantity" => 5
            ],
            $keywords[1],
            $keywords[2],
            "bryanchee@arcadier.com",
            "bryanchee"
        );
        echo json_encode($response);
        break;
    case "deletecartitem":
        $response = $sdk->deleteCartItem(
            $keywords[1],
            $keywords[2],
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
        $response = $sdk->getBuyerTransactions($keywords[1], "bryanchee@arcadier.com", "bryanchee");
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
        //Checkout test cases
    case "editbuyercart":
        $response = $sdk->editBuyerCart(
            $keywords[1],
            $keywords[2],
            [
                "Quantity" => 2,
                "SubTotal" => 1,
                "DiscountAmount" => 0.3
            ]
        );
        echo json_encode($response);
        break;
    case "updatetransactionpayment":
        $response = $sdk->updateMarketplaceTransaction(
            $keywords[1],
            [
                [
                    "Payee" => [
                        "ID" => $keywords[2]
                    ],
                    "Order" => [
                        "ID" => $keywords[3]
                    ],
                    "Refunded" => false,
                    "Status" => "Processing"
                ]
            ]
        );
        echo json_encode($response);
        break;
    case "generateinvoice":
        $response = $sdk->generateInvoice(
            $keywords[1],
            [
                $keywords[2]
            ],
            "bryanchee@arcadier.com",
            "bryanchee"
        );
        echo json_encode($response);
        break;
        //Shipping test cases
    case "getshippingmethods":
        $response = $sdk->getMerchantShippingMethods($keywords[1]);
        echo json_encode($response);
        break;
    case "getdeliveryrates":
        $response = $sdk->getDeliveryRates();
        echo json_encode($response);
        break;
    case "createshippingmethod":
        $response = $sdk->createShippingMethod(
            $keywords[1],
            [
                "Courier" => "Snail Mail",
                "Method" => "delivery",
                "Price" => 5,
                "CombinedPrice" => 3,
                "CurrencyCode" => "SGD",
                "Description" => "Snail Trail",
                "CustomFields" => []
            ]
        );
        echo json_encode($response);
        break;
    case "updateshippingmethod":
        $response = $sdk->updateShippingMethod(
            $keywords[1],
            $keywords[2],
            [
                "Price" => 6,
                "CombinedPrice" => 8
            ]
        );
        echo json_encode($response);
        break;
    case "deleteshippingmethod":
        $response = $sdk->deleteShippingMethod(
            $keywords[1],
            $keywords[2]
        );
        echo json_encode($response);
        break;
        //ORDER test cases
    case "getallorders":
        $response = $sdk->getOrderHistory($keywords[1]);
        echo json_encode($response);
        break;
    case "getallordersfiltered":
        $response = $sdk->getFilteredOrderHistory($keywords[1], 4, 2);
        echo json_encode($response);
        break;
    case "getorder":
        $response = $sdk->getOrderInfoByInvoiceId($keywords[1], $keywords[2]);
        echo json_encode($response);
        break;
    case "editorderstatus":
        $response = $sdk->editOrder(
            $keywords[1],
            $keywords[2],
            [
                "FulfilmentStatus" => "Acknowledged",
                "PaymentStatus" => "Paid"
            ]
        );
        echo json_encode($response);
        break;
        //Category test cases
    case "getcategories":
        $response = $sdk->getCategories();
        echo json_encode($response);
        break;
    case "getcategoriesfiltered":
        $response = $sdk->getFilteredCategories(3, 1);
        echo json_encode($response);
        break;
    case "getcategorieshierarchy":
        $response = $sdk->getCategoriesWithHierarchy();
        echo json_encode($response);
        break;
    case "createcategory":
        $response = $sdk->createCategory(
            [
                "Name" => "Synths",
                "Description" => "Create your own music from your own room",
                "SortOrder" => 0,
                "Media" => [
                    [
                        "ID" => null,
                        "MediaUrl" => "https =>//cdn.pixabay.com/photo/2016/12/14/12/09/violin-1906127_960_720.jpg"
                    ]
                ],
                "ParentCategoryID" => $keywords[1],
                "Level" => 1
            ]
        );
        echo json_encode($response);
        break;
    case "sortcategories":
        $response = $sdk->sortCategories(
            [
                $keywords[1],
                $keywords[2]
            ]
        );
        echo json_encode($response);
        break;
    case "updatecategory":
        $response = $sdk->updateCategory(
            $keywords[1],
            [
                "Name" => "Drums & Equipment"
            ]
        );
        echo json_encode($response);
        break;
    case "deletecategory":
        $response = $sdk->deleteCategory(
            $keywords[1]
        );
        echo json_encode($response);
        break;
    default:
        echo "Something went wrong ";
}
