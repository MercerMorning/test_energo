<?php
require_once("config.php");
require_once("functions.php");

class DB
{
    protected static $pdo;

    public static $maxAmount = 10;
    public static $maxPrice = 10000;
    public static $peopleNames = ["Vasya",
        "Petya",
        "Tolia",
        "Kolia",
        "Masha",
        "Pasha",
        "Sasha",
        "Glashs",
        "Viktor",
        "Sema",
        "Borya"];

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
        "size" =>
            ["32", "1", "5"]
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

    private function getAllUsers()
    {
        $sql = "SELECT * FROM `user`";
        $statement = self::$pdo->prepare($sql);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }


    private function updateAgeUser($user)
    {
        $sql = "UPDATE user SET age = :age WHERE id = :id";
        $statement = self::$pdo->prepare($sql);

        $dob = strtotime($user['dob']);
        $now = strtotime("now");

        $diff = $now - $dob;
        $diff = $diff/(60*60*24*365);
        $age = floor($diff);
        $statement->execute(['age' => $age,
            'id' => $user['id']
        ]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateAgeAllUsers()
    {
        foreach ($this->getAllUsers() as $user) {
            $this->updateAgeUser($user);
        }
    }

    public function generateUser()
    {
        $sql = "INSERT INTO `user` (name, registration_date, dob, age) VALUES (:name, :registration_date, :dob, :age)";
        $statement = self::$pdo->prepare($sql);

        $now = strtotime("now");
        $reg_unix = mt_rand(strtotime("-20 year"), strtotime("-1 year"));
        $reg_date = date("Y-m-d H:i:s",$reg_unix);

        $maxBirthUnix = strtotime("-1 year", $reg_unix);
        $minBirthUnix = strtotime("-100 year", $reg_unix);

        $birthRange = mt_rand($minBirthUnix,$maxBirthUnix);
        $birthDate = date("Y-m-d H:i:s",$birthRange);

        $diff = $now - date("U", $birthRange);
        $diff = $diff/(60*60*24*365);
        $age = floor($diff);

        $statement->execute(["name" => self::$peopleNames[array_rand(self::$peopleNames)],
            "registration_date" => $reg_date,
            "dob" => $birthDate,
            "age" => $age,
        ]);
    }

    public function apiSearchProducts($request)
    {
        return json_encode($this->searchProducts($request));
    }

    public function searchProducts($request)
    {
        $sql = "SELECT product.name, product.id
                FROM `product`";

        $loop = 0;
        $executeVars = [];
        $afterSql = '';

        foreach ($request as $key) {
            $executeVars += $key;
        }

        $request = array_reverse($request);

        foreach ($request as $table => $field) {
            foreach ($field as $fieldKey => $fieldValue) {
                if (is_array($typesArray = $fieldValue)) {
                    foreach ($typesArray as $typeKey => $typeValue) {
                        $sql .= " INNER JOIN product_properties "
                            . "as " . $typeKey . "p ";
                        $loop++;
                    }
                    $innerLoop = 0;
                    foreach ($typesArray as $typeKey => $typeValue) {
                        if ($innerLoop != 0) {
                            $sql .= "AND ";
                        } else {
                            $sql .= " WHERE ";
                        };
                        $sql .= $typeKey . "p." . $fieldKey . " = '" . $typeKey . "' AND " . $typeKey . "p." . "value" . " = '"  . $typeValue . "' "
                            . " AND " . $typeKey . "p." . "product_id = product.id ";
                        $innerLoop++;
                    }
                }
                elseif($fieldValue != '' || $request['product']['amount'] == null) {

                    if ($loop != 0) {
                        $afterSql .= "AND ";
                    } elseif ($loop == 0) {
                        $afterSql .= " WHERE ";
                    }
                    if ($request['product']['amount'] == null) {
                        $afterSql .= " product.amount != 0 ";
                        if ($fieldValue != '') {
                            $afterSql .= "AND ";
                        }
                    }
                    if ($fieldValue != '') {
                        $afterSql .= $table . "." . $fieldKey . " = " . $fieldValue . " ";
                    }
                }

                $loop++;
            }

        }

        if ($afterSql) {
            $sql .= $afterSql;
        }

        $statement = self::$pdo->prepare($sql);
        $statement->execute([$executeVars]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Пользователи, которые зарегестрировались год назад
    public function showYearAgoRegUsers()
    {
        $sql = "SELECT * FROM `user` WHERE registration_date = :last_year";
        $yearAgoDate = date("Y-m-d", strtotime("-1 year"));
        $statement = self::$pdo->prepare($sql);
        $statement->execute(["last_year" => $yearAgoDate,
        ]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);;
    }

    //Пользователи старше 45
    public function showOldUsers()
    {
        $sql = "SELECT * FROM `user` WHERE age >= :age";
        $statement = self::$pdo->prepare($sql);
        $statement->execute(["age" => 45,
        ]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);;
    }

    //Пользователи у которых день рождения
    public function showBirthDayUsers()
    {
        $sql = "SELECT * FROM `user` WHERE DAYOFYEAR(user.dob) = DAYOFYEAR(:now)";
        $statement = self::$pdo->prepare($sql);
        $now = new DateTime();
        $statement->execute(["now" => $now->format("Y-m-d")]);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);;
    }

}