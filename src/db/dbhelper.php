<?php

declare(strict_types=1);

class DBHelper
{
    public PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function fetchUserData()
    {
        $query = $this -> pdo->prepare(
            "SELECT * FROM users as u 
                JOIN user_stats as us ON u.id = us.user_id 
                JOIN user_maps as um ON u.id = um.user_id 
                WHERE u.id = :userId"
        );
        $query->execute([":userId" => $_SESSION["user"]["id"]]);
        $response = $query -> fetch();
        array_push($_SESSION["user"], $response);
    }

    public function updateUserMap()
    {
        $query = $this -> pdo->prepare(
            "UPDATE map_json FROM user_maps VALUES( :mapjson) 
            WHERE user_id = :userid"
        );

        $response = $query->execute([
            ":userid" => $_SESSION["user"]["id"],
            ":mapjson" => json_encode($_SESSION["map"])
        ]); 
    }
}
