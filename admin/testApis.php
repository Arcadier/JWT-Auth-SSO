<?php
require_once '../sdk/ApiSdk.php';

$sdk = new ApiSdk();
$contentBodyJson = file_get_contents('php://input');
$content = json_decode($contentBodyJson, true);

$apiType = $content['apiType'];

switch ($apiType) {
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
                "Name" => "Damask Roses",
            ],
            "015bf8c9-5332-4717-a26f-b09f15692e4d",
            "a3b37841-5011-44a8-ab92-9ad6bfe0acf4"
        );
        echo json_encode($response);
        break;
    case "tagitem":
        $response = $sdk->tagItem(
            [
                "Scented", "Fragrant", "Musky", "Invigorating"
            ],
            "015bf8c9-5332-4717-a26f-b09f15692e4d",
            "a3b37841-5011-44a8-ab92-9ad6bfe0acf4"
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
            "a3b37841-5011-44a8-ab92-9ad6bfe0acf4"
        );
        echo json_encode($response);
        break;
        //USERS TEST CASES
    case "filter":
        $response = $sdk->getAllItems("?maxPrice=45&pageSize=1&pageNumber=2");
        echo json_encode($response);
        break;
    default:
        echo "Something went wrong ";
}
