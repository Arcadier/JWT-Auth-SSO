<?php
require_once '../sdk/ApiSdk.php';

$sdk = new ApiSdk();
$contentBodyJson = file_get_contents('php://input');
$content = json_decode($contentBodyJson, true);

$packageId = getPackageID();
$buyerId = "c33cfb0f-b665-42e9-bb04-84c723e7e65a";
$merchantId = $content['merchantId'];
//"d7b4a56c-93c6-4c24-b2d0-f730061f7744";
//Test User auth
$buyerUsername = "testuser20@gmail.com";
$buyerPassword = "testuser20pw";
//Real User auth
/* $buyerUsername = "bryanchee@arcadier.com";
$buyerPassword = "bryanchee"; */
//Upgrade user role not working
/* $merchantUsername = "testmerchant20@gmail.com";
$merchantPassword = "testmerchant20pw"; */
$testInputs = [
    //User test inputs
    "registeruser:$buyerUsername:$buyerPassword",
    //"registeruser:$merchantUsername:$merchantPassword",
    "getusers",
    "getmerchants",
    "resetpassword",
    "password:$buyerPassword",
    //Category test inputs
    "createcategory:first",
    "getcategories",
    "getcategoriesfiltered",
    "getcategorieshierarchy",
    "createcategory:second",
    "sortcategories",
    "updatecategory",
    "deletecategory:first",
    //Item test inputs
    "createitem",
    "itemfilter",
    "itemjsonfilter",
    "edititem",
    "tagitem",
    "gettags",
    "deletetags",
    "deleteitem",
    // Custom Table test inputs
    "newrowentry",
    "getcustomtable",
    "editrowentry",
    "searchcustomtable",
    "deleterowentry",
    // Shipping Method test inputs
    "createshippingmethod",
    "getshippingmethods",
    "getdeliveryrates",
    "updateshippingmethod",
    "deleteshippingmethod",
    //Payment test inputs
    "createpaymentgateway",
    "linkpaymentgateway",
    "getpaymentgateways",
    "getpaymentmethods",
    "updatepaymentmethod",
    "deletepaymentgateway",
    "deletepaymentmethod",
    //Cart test inputs
    //"createcategory:first",
    "createitem",
    "createshippingmethod",
    "addtocart",
    "getcart",
    "updatecart",
    "deletecartitem",
    //Checkout and Email test inputs
    "addtocart",
    "editbuyercart",
    "generateinvoice",
    "sendemailinvoice",
    "updatetransactionpayment",
    "deleteitem",
    "deleteshippingmethod",
    //Order test inputs
    "getallorders",
    "getallordersfiltered",
    "getorderbyinvoiceno",
    "editorderstatus",
    //Transaction test inputs
    "gettransactions",
    "getfilteredtransactions",
    "getbuyertransactions",
    //Custom Field and Marketplace test inputs
    "createcustomfield",
    "getcustomfields",
    "updatemarketplaceinformation",
    "updatecustomfield",
    "getcustomfieldplugin",
    "deletecustomfield",
    //Static test inputs
    "getfulfilmentstatuses",
    "getcurrencies",
    "getcountries",
    "getorderstatuses",
    "getpaymentstatuses",
    "gettimezones",
    //Content Page test inputs
    "createcontentpage",
    "getcontentpages",
    "getpagecontent",
    "updatecontentpage",
    "deletecontentpage",
    //Panel test inputs
    "getpanels",
    "getpanelbyid",
    "deletecategory:second",
    "deleteuser",
    //"customiseURL",
];
$testCases = [
    [
        "Total number of tests" => count($testInputs),
        "Number of passed tests" => 0,
        "Number of failed tests" => 0,
    ]
];
foreach ($testInputs as $tc) {
    $testResult = testOneTestCase($tc);
    $testCases[] = [
        "Test" => $testResult["TestName"],
        "Result" => $testResult['testSuccessStatus'],
        "Message" => $testResult["Message"]
    ];
    if ($testResult['testSuccessStatus'] == "Passed") {
        $testCases[0]["Number of passed tests"] = $testCases[0]["Number of passed tests"] + 1;
    } else {
        $testCases[0]["Number of failed tests"] = $testCases[0]["Number of failed tests"] + 1;
    }
}

echo json_encode($testCases);

function testOneTestCase($inputString)
{
    global $sdk, $buyerId, $merchantId, $resetPasswordToken, $itemId, $cartItemId,
        $invoiceNo, $orderId, $packageId, $rowId, $shippingMethodId, $firstCategoryId, $secondCategoryId,
        $gatewayCode, $paymentMethodId, $pageId, $panelId, $buyerUsername, $buyerPassword, $customFieldCode;

    $keywords = explode(":", $inputString);
    switch ($keywords[0]) {
            //ITEMS TEST CASES
        case "itemfilter":
            $response = $sdk->getAllItems("?maxPrice=75&pageSize=1&pageNumber=1");
            return getRecords("Get All Filtered Items", "Item", $response, 1);
        case "itemjsonfilter":
            $response = $sdk->getAllItemsJsonFiltering(
                [
                    "maxPrice" => 75,
                    "pageSize" => 1,
                    "pageNumber" => 1
                ]
            );
            return getRecords("Get All Filtered Items", "Item", $response, 1);
        case "createitem":
            $response = $sdk->createItem(
                [
                    "Categories" => array(
                        [
                            "ID" => $firstCategoryId
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
                $merchantId
            );
            if (array_key_exists('ID', $response)) {
                $itemId = $response['ID'];
            }
            return createRecord("Create Item", $response, "Item successfully created", 'ID');
        case "edititem":
            $response = $sdk->editItem(
                [
                    "Name" => "abibas",
                ],
                $merchantId,
                $itemId
            );
            return editRecord("Edit Item", "Item", $response, "Name", "abibas");
        case "tagitem":
            $response = $sdk->tagItem(
                [
                    "TestTag1000", "TestTag2000", "TestTag3000", "TestTag4000"
                ],
                $merchantId,
                $itemId
            );
            return checkResult("Tag Item", $response, "Item successfully tagged");
        case "gettags":
            $response = $sdk->getItemTags("?pageSize=3");
            return getRecords("Get Filtered Tags", "Item", $response, 3);
        case "deletetags":
            $response = $sdk->deleteTags(
                [
                    "TestTag1000", "TestTag2000"
                ]
            );
            return checkResult("Delete Tags", $response, "Tags successfully deleted");
        case "deleteitem":
            $response = $sdk->deleteItem(
                $merchantId,
                $itemId
            );
            return deleteRecord("Delete Item", "Item", $response, $itemId, "ID");
            //USERS TEST CASES
        case "getusers":
            $response = $sdk->getAllUsers(null);
            return getRecords("Get All Users", "Users", $response, 0);
        case "getmerchants":
            $response = $sdk->getAllUsers("?role=merchant");
            return getRecords("Get All Merchants", "Merchants", $response, 0);
        case "registeruser":
            $response = $sdk->registerUser(
                [
                    "Email" => $keywords[1],
                    "Password" => $keywords[2],
                    "ConfirmPassword" => $keywords[2]
                ]
            );
            if ($keywords[1] == "$buyerUsername") {
                $buyerId = $response['UserId'];
                return createRecord("Register User", $response, "Buyer successfully created", "UserId");
            } else {
                $sdk->upgradeUserRole($response['UserId'], "merchant");
                $merchantId = $response['UserId'];
                return createRecord("Register User", $response, "Merchant successfully created", "UserId");
            }

        case "resetpassword":
            $response = $sdk->resetPassword(
                [
                    "UserId" => $buyerId,
                    "Action" => "token"
                ]
            );
            if (array_key_exists('Token', $response)) {
                $resetPasswordToken = $response['Token'];
            }
            return createRecord("Reset Password", $response, " Password successfully resetted", "Token");
        case "password":
            $response = $sdk->updatePassword(
                [
                    "Password" => $keywords[1],
                    "ConfirmPassword" => $keywords[1],
                    "ResetPasswordToken" => $resetPasswordToken,
                ],
                $buyerId
            );
            return checkResult("Update Password", $response, "Password successfully updated");
        case "deleteuser":
            $response = $sdk->deleteUser(
                $buyerId
            );
            return checkResult("Delete User", $response, "User deleted");
            //CART TEST CASES
        case "addtocart":
            $response = $sdk->addToCart(
                [
                    "ItemDetail" => [
                        "ID" => $itemId
                    ],
                    "Quantity" => 2,
                    "CartItemType" => "delivery",
                    "ShippingMethod" => [
                        "ID" => $shippingMethodId
                    ]
                ],
                $buyerId,
                $buyerUsername,
                $buyerPassword
            );
            if (array_key_exists('ID', $response)) {
                $cartItemId = $response['ID'];
            }
            return createRecord("Add Item to Cart", $response, "Item added to cart", "ID");
        case "getcart":
            $response = $sdk->getCart(
                $buyerId
            );
            return getRecords("Get Cart Details", "Cart", $response, 0);
        case "updatecart":
            $response = $sdk->updateCartItem(
                [
                    "Quantity" => 5
                ],
                $buyerId,
                $cartItemId,
                $buyerUsername,
                $buyerPassword
            );
            return editRecord("Update Cart Item", "Cart Item", $response, "Quantity", 5);
        case "deletecartitem":
            $response = $sdk->deleteCartItem(
                $buyerId,
                $cartItemId,
                $buyerUsername,
                $buyerPassword
            );
            return deleteRecord("Delete Cart Item", "Cart Item", $response, $cartItemId, "ID");
            //Transaction test cases
        case "gettransactions":
            $response = $sdk->getAllTransactions();
            return getRecords("Get All Transactions", "Transaction", $response, 0);
        case "getfilteredtransactions":
            $currentUnixTime = time();
            $response = $sdk->getAllFilteredTransactions(1, 1, $currentUnixTime - 60, $currentUnixTime);
            return getRecords("Get All Filtered Transactions", "Transaction", $response, 1);
        case "getbuyertransactions":
            $response = $sdk->getBuyerTransactions($buyerId, $buyerUsername, $buyerPassword);
            return getRecords("Get Buyer Transactions", "Transaction", $response, 0);
            //Custom Table test cases
        case "getcustomtable":
            $response = $sdk->getCustomTable($packageId, "TestTable");
            return getRecords("Get Custom Table", "Custom Table", $response, 0);
        case "newrowentry":
            $response = $sdk->createRowEntry(
                $packageId,
                "TestTable",
                [
                    "Age" => 34,
                    "Gender" => "undecided",
                    "Name" => "TestUser"
                ]
            );
            if (array_key_exists('Id', $response)) {
                $rowId = $response['Id'];
            }
            return createRecord("Create Row Entry", $response, "Row entry successfully created", "Id");
        case "editrowentry":
            $response = $sdk->editRowEntry(
                $packageId,
                "TestTable",
                $rowId,
                [
                    "Age" => 42
                ]
            );
            return editRecord("Edit Row Entry", "Row Entry", $response, "Age", 42);
        case "searchcustomtable":
            $response = $sdk->searchTable(
                $packageId,
                "TestTable",
                array(
                    [
                        "Name" => "Age",
                        "Operator" => "equal",
                        "Value" => "42"
                    ]
                )
            );
            return getRecords("Search Custom Table", "Row Entry", $response, 1);
        case "deleterowentry":
            $response = $sdk->deleteRowEntry(
                $packageId,
                "TestTable",
                $rowId
            );
            return deleteRecord("Delete Row Entry", "Row Entry", $response, $rowId, "Id");
            //Checkout test cases
        case "editbuyercart":
            $response = $sdk->editBuyerCart(
                $merchantId,
                $cartItemId,
                [
                    "Quantity" => 2,
                    "SubTotal" => 1,
                    "DiscountAmount" => 0.3
                ]
            );
            return editRecord("Edit Buyer's cart", "Cart", $response, "Quantity", 2);
        case "updatetransactionpayment":
            $response = $sdk->updateMarketplaceTransaction(
                $invoiceNo,
                [
                    [
                        "Payee" => [
                            "ID" => $buyerId
                        ],
                        "Order" => [
                            "ID" => $orderId
                        ],
                        "Refunded" => false,
                        "Status" => "Processing"
                    ]
                ]
            );
            return editRecord("Update Transaction Details", "Transaction Details", $response[0], "Status", "Processing");
        case "generateinvoice":
            $response = $sdk->generateInvoice(
                $buyerId,
                [
                    $cartItemId
                ],
                $buyerUsername,
                $buyerPassword
            );
            if (array_key_exists('InvoiceNo', $response)) {
                $invoiceNo = $response['InvoiceNo'];
                $orderId = $response['Orders'][0]['ID'];
            }
            return createRecord("Generate Invoice", $response, "Invoice successfully generated", "InvoiceNo");
            //Shipping test cases
        case "getshippingmethods":
            $response = $sdk->getMerchantShippingMethods($merchantId);
            return checkArray("Get Shipping Methods", $response, "Shipping Method");
        case "getdeliveryrates":
            $response = $sdk->getDeliveryRates();
            return checkArray("Get Delivery Rates", $response, "Delivery Rates");
        case "createshippingmethod":
            $response = $sdk->createShippingMethod(
                $merchantId,
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
            if (array_key_exists('ID', $response)) {
                $shippingMethodId = $response['ID'];
            }
            return createRecord("Create Shipping Method", $response, "Shipping method successfully created", "ID");
        case "updateshippingmethod":
            $response = $sdk->updateShippingMethod(
                $merchantId,
                $shippingMethodId,
                [
                    "Price" => 6,
                    "CombinedPrice" => 8
                ]
            );
            return editRecord("Update Shipping Method", "Shipping Method", $response, "Price", 6);
        case "deleteshippingmethod":
            $response = $sdk->deleteShippingMethod(
                $merchantId,
                $shippingMethodId
            );
            return deleteRecord("Delete Shipping Method", "Shipping Method", $response, $shippingMethodId, "ID");
            //ORDER test cases
        case "getallorders":
            $response = $sdk->getOrderHistory($merchantId);
            return getRecords("Get All Orders", "Order", $response, 0);
        case "getallordersfiltered":
            $response = $sdk->getFilteredOrderHistory($merchantId, 1, 1);
            return getRecords("Get All Filtered Orders", "Order", $response, 1);
        case "getorderbyinvoiceno":
            $response = $sdk->getOrderInfoByInvoiceId($merchantId, $invoiceNo);
            return createRecord("Get Order By Invoice", $response, "Order successfully retrieved", "InvoiceNo");
        case "editorderstatus":
            $response = $sdk->editOrder(
                $merchantId,
                $orderId,
                [
                    "FulfilmentStatus" => "Acknowledged",
                    "PaymentStatus" => "Paid"
                ]
            );
            return editRecord("Edit Order Status", "Order", $response, "PaymentStatus", "Paid");
            //Category test cases
        case "getcategories":
            $response = $sdk->getCategories();
            return getRecords("Get All Categories", "Category", $response, 0);
        case "getcategoriesfiltered":
            $response = $sdk->getFilteredCategories(1, 1);
            return getRecords("Get All Filtered Categories", "Category", $response, 1);
        case "getcategorieshierarchy":
            $response = $sdk->getCategoriesWithHierarchy();
            return checkArray("Get Hierarchy of Categories", $response, "Total Records: " . count($response));
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
                    "ParentfirstCategoryId" => null,
                    "Level" => 1
                ]
            );
            if (array_key_exists('ID', $response)) {
                if ($keywords[1] == "first") {
                    $firstCategoryId = $response['ID'];
                } else {
                    $secondCategoryId = $response['ID'];
                }
            }
            return createRecord("Create Category", $response, "Category successfully created", "ID");
        case "sortcategories":
            $response = $sdk->sortCategories(
                [
                    $secondCategoryId,
                    $firstCategoryId
                ]
            );
            return checkArray("Sort Categories", $response, "Categories successfully sorted");
        case "updatecategory":
            $response = $sdk->updateCategory(
                $firstCategoryId,
                [
                    "Name" => "Drums & Equipment"
                ]
            );
            return editRecord("Update Category", "Category", $response, "Name", "Drums & Equipment");
        case "deletecategory":
            if ($keywords[1] == "first") {
                $deleteCategoryId = $firstCategoryId;
            } else {
                $deleteCategoryId = $secondCategoryId;
            }
            $response = $sdk->deleteCategory(
                $deleteCategoryId
            );

            return deleteRecord("Delete Category", "Category", $response[0], $deleteCategoryId, "ID");
            //Marketplace test cases
            /* case "customiseURL":
            $sdk->customiseURL(
                [
                    "Key" => "/customiseUrlTest",
                    "Value" => "/user/plugins/$packageId/customiseUrlTest.php",
                ]
            );
            $response = $sdk->callAPI("GET", null, getMarketplaceBaseUrl() . "/api/v2/customiseUrlTest", null);
            $result = [];
            if ($response == "Successfully called customiseUrlTest") {
                $result["testSuccessStatus"] = "Passed";
                $result["Message"] = "Successfully called customiseUrlTest";
            } else {
                $result["testSuccessStatus"] = "Failed";
            }
            $result["TestName"] = "Customise Url";
            return $result; */
        case "updatemarketplaceinformation":
            $marketplaceId = ($sdk->getMarketplaceInfo())['ID'];
            $response = $sdk->updateMarketplaceInfo(
                [
                    "ID" => $marketplaceId,
                    "CustomFields" => [
                        [
                            "Code" => $customFieldCode,
                            "Values" => [
                                "3.0 kg"
                            ]
                        ]
                    ]
                ]
            );
            $item = null;
            foreach ($response['CustomFields'] as $field) {
                if ($customFieldCode == $field['Code']) {
                    $item = $field;
                    break;
                }
            }
            return editRecord("Update Marketplace Information", "Marketplace Information", $item, "Values", ["3.0 kg"]);
            //Email test cases
        case "sendemailinvoice":
            $response = $sdk->sendEmailAfterGeneratingInvoice($invoiceNo);
            return checkResult("Send Email Invoice", $response, "Email successfully sent");
            //Custom Field test cases
        case "createcustomfield":
            $response = $sdk->createCustomField(
                [
                    "Name" => "Creating an CFD through API",
                    "IsMandatory" => true,
                    "SortOrder" => 5,
                    "DataInputType" => "textfield",
                    "ReferenceTable" => "Implementations",
                    "DataFieldType" => "string",
                    "IsSearchable" => true,
                    "IsSensitive" => true,
                    "Active" => true
                ]
            );
            if (array_key_exists('Code', $response)) {
                $customFieldCode = $response['Code'];
            }
            return createRecord("Create Custom Field", $response, "Custom Field successfully created", "Code");
        case "getcustomfields":
            $response = $sdk->getCustomFields();
            return getRecords("Get Custom Fields", "Custom Field", $response, 0);
        case "updatecustomfield":
            $response = $sdk->updateCustomField(
                $customFieldCode,
                [
                    "Code" => $customFieldCode,
                    "Name" => "Previously owned",
                    "IsMandatory" => false,
                ]
            );
            return editRecord("Update Custom Field", "Custom Field", $response, "Name", "Previously owned");
        case "getcustomfieldplugin":
            $response = $sdk->getPluginCustomFields($packageId);
            return createRecord("Get Custom Fields of Plugin", $response[0], "Custom Fields successfully retrieved", "Code");
        case "deletecustomfield":
            $response = $sdk->deleteCustomField($customFieldCode);
            return deleteRecord("Delete Custom Field", "Custom Field", $response, $customFieldCode, "Code");
            //Payment test cases
        case "createpaymentgateway":
            $response = $sdk->createPaymentGateway(
                [
                    "Description" => "Test Payment Gateway 2",
                    "Gateway" => "Test",
                    "Logo" => [
                        "ID" => "f9e001f5-53bf-4744-b73d-6e8d8734beed",
                        "MediaUrl" => "https://d1aeri3ty3izns.cloudfront.net/media/6/67664/1200/preview.jpg"
                    ]
                ]
            );
            if (array_key_exists('Code', $response)) {
                $gatewayCode = $response['Code'];
            }
            return createRecord("Create Payment Gateway", $response, "Payment Gateway successfully created", "Code");
        case "linkpaymentgateway":
            $response = $sdk->linkPaymentGateway(
                $merchantId,
                [
                    "PaymentGateway" => [
                        "Code" => $gatewayCode
                    ],
                    "Verified" => true,
                    "Account" => "testaccount",
                    "ClientID" => "some hash",
                    "Active" => true,
                    "BankAccountNumber" => "4242424242424242"
                ]
            );
            if (array_key_exists('ID', $response)) {
                $paymentMethodId = $response['ID'];
            }
            return createRecord("Link Payment Gateway", $response, "Payment gateway successfully linked", "ID");
        case "getpaymentgateways":
            $response = $sdk->getPaymentGateways();
            return getRecords("Get Payment Gateways", "Payment Gateways", $response, 0);
        case "getpaymentmethods":
            $response = $sdk->showPaymentAcceptanceMethods($merchantId);
            return getRecords("Get Payment Methods", "Payment Methods", $response, 0);
        case "updatepaymentmethod":
            $response = $sdk->updatePaymentMethod(
                $gatewayCode,
                [
                    "Description" => "Updated Description",
                    "Gateway" => "Updated Gateway",
                    "Logo" => [
                        "ID" => "40abc3db-241f-45a2-868d-dd798f6feb88",
                        "MediaUrl" => "https://theme.zdassets.com/theme_assets/2008942/9566e69f67b1ee67fdfbcd79b1e580bdbbc98874.svg"
                    ]
                ]
            );
            return editRecord("Update Payment Gateway", "Payment Gateway", $response, "Description", "Updated Description");
        case "deletepaymentmethod":
            $response = $sdk->deletePaymentAcceptanceMethod($merchantId, $paymentMethodId);
            return deleteRecord("Delete Payment Method", "Payment Method", $response, $paymentMethodId, "ID");
        case "deletepaymentgateway":
            $response = $sdk->deletePaymentGateway($gatewayCode);
            return deleteRecord("Delete Payment Gateway", "Payment Gateway", $response, $gatewayCode, "Code");
            //Static test cases
        case "getfulfilmentstatuses":
            $response = $sdk->getFulfilmentStatuses();
            return getRecords("Get Fulfilment Statuses", "Fulfilment Statuses", $response, 0);
        case "getcurrencies":
            $response = $sdk->getCurrencies();
            return getRecords("Get Currencies", "Currencies", $response, 0);
        case "getcountries":
            $response = $sdk->getCountries();
            return getRecords("Get Countries", "Countries", $response, 0);
        case "getorderstatuses":
            $response = $sdk->getOrderStatuses();
            return getRecords("Get Order Statuses", "Order Statuses", $response, 0);
        case "getpaymentstatuses":
            $response = $sdk->getPaymentStatuses();
            return getRecords("Get Payment Statuses", "Payment Statuses", $response, 0);
        case "gettimezones":
            $response = $sdk->getTimezones();
            return getRecords("Get Timezones", "Timezones", $response, 0);
            //Page test cases
        case "getcontentpages":
            $response = $sdk->getContentPages();
            return getRecords("Get Content Pages", "Content Pages", $response, 0);
        case "getpagecontent":
            $response = $sdk->getPageContent($pageId);
            return createRecord("Get Content of Page", $response, "Content successfully retrieved", "ID");
        case "createcontentpage":
            $response = $sdk->createContentPage(
                [
                    "Title" => "Footer Link",
                    "Content" => "<div class=\"contact-main\">\r\n    <div class=\"contact-title\">   \r\n        <h1>strAdmin_Contact_ContactUs</h1>\r\n    </div>\r\n    <p>strAdmin_Contact_ContactDescription</p>\r\n    <p><img src=\"/Assets/img/contact_icon.svg\" alt=\"\" style=\"margin-bottom: 0.25em; vertical-align: middle;\" data-pin-nopin=\"true\">dbContactNo</p>\r\n    <p><img src=\"/Assets/img/email_icon.svg\" alt=\"\" style=\"margin-bottom: 0.25em; vertical-align: middle;\" data-pin-nopin=\"true\">\r\n    <a href=\"mailto:dbContactEmail\">dbContactEmail</a>\r\n    </p>\r\n</div>",
                    "ExternalURL" => "string",
                    "CreatedDateTime" => "2019-05-31T07:10:24.834Z",
                    "ModifiedDateTime" => "2019-05-31T07:10:24.834Z",
                    "Active" => true,
                    "Available" => 0,
                    "VisibleTo" => 1,
                    "Meta" => "This"
                ]
            );
            if (array_key_exists('ID', $response)) {
                $pageId = $response['ID'];
            }
            return createRecord("Create Page", $response, "Page successfully created", "ID");
        case "updatecontentpage":
            $response = $sdk->editContentPage(
                $pageId,
                [
                    "Available" => 1,
                ]
            );
            return editRecord("Update Page", "Page", $response, "Available", "Hide");
        case "deletecontentpage":
            $response = $sdk->deleteContentPage($pageId);
            return deleteRecord("Delete Page", "Page", $response, $pageId, "ID");
            //Panel test cases
        case "getpanels":
            $response = $sdk->getAllPanels();
            if (array_key_exists('ID', $response['Records'][0])) {
                $panelId = $response['Records'][0]['ID'];
            }
            return getRecords("Get Panels", "Panels", $response, 0);
        case "getpanelbyid":
            $response = $sdk->getPanelById($panelId);
            return getRecords("Get Panel by Id", "Panel", $response, 1);
        default:
            $jsonresponse = "Something went wrong ";
            return $jsonresponse;
    }
}

function getRecords($testName, $element, $response, $filterNumber)
{
    if (array_key_exists('Records', $response)) {
        if ($filterNumber == 0) {
            $response["testSuccessStatus"] = "Passed";
            $response["Message"] = "Total Records: " . $response['TotalRecords'];
        } else if (count($response['Records'] == $filterNumber)) {
            $response["testSuccessStatus"] = "Passed";
            $response["Message"] = "$filterNumber $element retrieved";
        } else {
            $response["testSuccessStatus"] = "Failed";
            $response["Message"] = "More than $filterNumber $element retrieved";
        }
    } else {
        $response["testSuccessStatus"] = "Failed";
    }
    $response["TestName"] = $testName;
    return $response;
}

function createRecord($testName, $response, $message, $key)
{
    if (array_key_exists($key, $response)) {
        $response["testSuccessStatus"] = "Passed";
        $response["Message"] = $message;
    } else {
        $response["testSuccessStatus"] = "Failed";
    }
    $response["TestName"] = $testName;
    return $response;
}

function editRecord($testName, $element, $response, $updatedAttribute, $updatedValue)
{
    if (array_key_exists($updatedAttribute, $response)) {
        if ($response[$updatedAttribute] == $updatedValue) {
            $response["testSuccessStatus"] = "Passed";
            $response["Message"] = "$element $updatedAttribute successfully updated";
        } else {
            $response["testSuccessStatus"] = "Failed";
            $response["Message"] = "$element $updatedAttribute differs from $updatedValue";
        }
    } else {
        $response["testSuccessStatus"] = "Failed";
    }
    $response["TestName"] = $testName;
    return $response;
}

function deleteRecord($testName, $element, $response, $deletedId, $key)
{
    if (array_key_exists($key, $response)) {
        if ($response[$key] == $deletedId) {
            $response["testSuccessStatus"] = "Passed";
            $response["Message"] = "$element successfully deleted";
        } else {
            $response["testSuccessStatus"] = "Failed";
            $response["Message"] = "Wrong $element deleted";
        }
    } else {
        $response["testSuccessStatus"] = "Failed";
    }
    $response["TestName"] = $testName;
    return $response;
}

// For Responses returning Result: true
function checkResult($testName, $response, $message)
{
    if (array_key_exists('Result', $response)) {
        $response["testSuccessStatus"] = "Passed";
        $response["Message"] = $message;
    } else {
        $response["testSuccessStatus"] = "Failed";
    }
    $response["TestName"] = $testName;
    return $response;
}

// For Responses returning array of objects
function checkArray($testName, $response, $message)
{
    if (array_key_exists('ID', $response[0])) {
        $response["testSuccessStatus"] = "Passed";
        $response["Message"] = $message;
    } else {
        $response["testSuccessStatus"] = "Failed";
    }
    $response["TestName"] = $testName;
    return $response;
}

function getMarketplaceBaseUrl()
{
    $marketplace = $_COOKIE["marketplace"];
    $protocol    = $_COOKIE["protocol"];

    $baseUrl = $protocol . '://' . $marketplace;
    return $baseUrl;
}

function getPackageID()
{
    $requestUri = "$_SERVER[REQUEST_URI]";
    preg_match('/([a-f0-9]{8}(?:-[a-f0-9]{4}){3}-[a-f0-9]{12})/', $requestUri, $matches, 0);
    return $matches[0];
}
