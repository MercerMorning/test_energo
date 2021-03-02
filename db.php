<?php
require_once("config.php");

class DB
{
    protected static $pdo;

    public static $maxAmount = 10;
    public static $maxPrice = 10000;
    public static $productNames = ["phone",
        "laptop",
        "printer",
        "digital book",
        "mouse",
        "router",
        "SSD",
        "HDD",
        "camera",
        "guitar",
        "vr-helmet"];
    public static $productPropertiesTypeValues = [
        "color" =>
            ["red", "blue", "green"],
        "material" =>
            ["wood", "metal", "plastic"],
        "weight" =>
            ["32kg", "1kg", "5kg"]
    ];




    public function __construct()
    {
        if (self::$pdo === null) {
            self::$pdo = new \PDO(
                DSN_DB, USERNAME_DB, PASSWORD_DB
            );
        }
    }

    public function generateProduct()
    {
        $sql = "INSERT INTO `product` (name, price, amount) VALUES (:name, :price, :amount)";
        $statement = self::$pdo->prepare($sql);
        $statement->execute(["name" => self::$productNames[array_rand(self::$productNames)],
            "price" => rand(0, self::$maxPrice),
            "amount" => rand(0, self::$maxAmount)
        ]);
        $productID = self::$pdo->lastInsertId();
        foreach (self::$productPropertiesTypeValues as $key => $typeValues) {
            $sql = "INSERT INTO `product_properties` (product_id, type, value) VALUES (:product_id, :type, :value)";
            $statement = self::$pdo->prepare($sql);
            $ret = $statement->execute(["product_id" => $productID,
                "type" => $key,
                "value" => $typeValues[array_rand($typeValues)]
            ]);
        }

        return $ret;
    }

//    public function createComment($request)
//    {
//        $sql = "INSERT INTO `comments` (id, postId, name, email, body) VALUES (:id, :postId, :name, :email, :body)";
//        $statement = self::$pdo->prepare($sql);
//        $ret = $statement->execute(["id" => $request["id"],
//            "postId" => $request["postId"],
//            "name" => $request["name"],
//            "email" => $request["email"],
//            "body" => $request["body"]
//        ]);
//        return $ret;
//    }

    public function searchProducts($request)
    {
        $sql = "SELECT product.name
                FROM `product` INNER JOIN product_properties ";
        $loop = 0;
        $executeVars = [];
        foreach ($request as $key) {
            $executeVars += $key;
        }

        foreach ($request as $table => $field) {
            foreach ($field as $fieldKey => $fieldValue) {
                if ($loop != 0) {
                    $sql .= "AND ";
                } else {
                    $sql .= "WHERE ";
                };
                if (is_array($typesArray = $fieldValue)) {
                    $innerLoop = 0;
                    foreach ($typesArray as $typeKey => $typeValue) {
                        $executeSql = $sql . $table . "." . $fieldKey . " = '" . $typeKey . "' AND " . $table . ".value = '" . $typeValue . "' ";

                        $executeSql .= " AND product_properties.product_id = product.id";
                        $statement = self::$pdo->prepare($executeSql);
                        $ret = $statement->execute([$executeVars]);
                        if ($ret == false) {
                            return 123;
                        }
                        $executeSql = '';
                        $innerLoop++;
                    }
                } else {
                    $sql .= $table . "." . $fieldKey . " = " . $fieldValue . " ";
                }
                $loop++;
            }
        }
        return $statement->queryString;
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

}