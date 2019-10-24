<?php

function emailcheckGuest($email)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from users where email = ?)as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function nicknamecheckGuest($nickname)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from users where nickname = ?)as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$nickname]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function signUp($email, $name, $birth, $password, $nickname)
{
    $no = (int)0;
    $email = (string)$email;
    $name = (string)$name;
    $birth = (int)$birth;
    $password = (string)$password;
    $auth = (string)"N";
    $is_deleted = (int)0;
    $nickname = (string)$nickname;
    $timestamp = date("Y-m-d H:i:s");


    $pdo = pdoSqlConnect();
    $query = "INSERT INTO users (no, email, name, birth, password, Authorization, registered_timestamp, is_deleted, nickname) VALUES (?,?,?,?,?,?,?,?,?);";


    $st = $pdo->prepare($query);
    $st->bindParam(1, $no, PDO::PARAM_INT);
    $st->bindParam(2, $email, PDO::PARAM_STR);
    $st->bindParam(3, $name, PDO::PARAM_STR);
    $st->bindParam(4, $birth, PDO::PARAM_INT);
    $st->bindParam(5, $password, PDO::PARAM_STR);
    $st->bindParam(6, $auth, PDO::PARAM_STR);
    $st->bindParam(7, $timestamp, PDO::PARAM_STR);
    $st->bindParam(8, $is_deleted, PDO::PARAM_STR);
    $st->bindParam(9, $nickname, PDO::PARAM_STR);
    $st -> execute();

   $st = null;
   $pdo = null;
}

function insertImage($url)
{

    $url = (string)$url;
    $no = (int)0;
    $is_deleted = (string)'N';

    $pdo = pdoSqlConnect();
    $query = "INSERT INTO images (no, is_deleted, url) VALUES (?,?,?);";
    $st = $pdo->prepare($query);
    $st->bindParam(1, $no, PDO::PARAM_INT);
    $st->bindParam(2, $is_deleted, PDO::PARAM_STR);
    $st->bindParam(3, $url, PDO::PARAM_STR);
    $st -> execute();

    $st = null;
    $pdo = null;
}

function getArea()
{
    $pdo = pdoSqlConnect();
    $query = "select * from locations";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function postArea($userNo, $result)
{
    $no = 0;
    $int = 0;
    $count = count($result);
    $question_marks = str_repeat(",(?,?,?)", $count-1);

    $pdo = pdoSqlConnect();
    $query = " INSERT INTO interesting_relations (no , user_no, location_no) VALUES (?,?,?)$question_marks;";

    $st = $pdo->prepare($query);

    foreach ($result as $row => $value)
    {
        $productId = $value->nationalNo;
        $st->bindValue($int + 1, $no);
        $st->bindValue($int + 2, $userNo);
        $st->bindValue($int + 3, $productId);
        $int = $int + 3;
    }
    $st -> execute();

    $st = null;
    $pdo = null;

}

function login($email, $password)
{
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from users where email = ? and password = ?)as exist;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email, $password]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

//READ
function test()
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM users;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

//READ
function testDetail($testNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT * FROM TEST_TB WHERE no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$testNo]);
    //    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0];
}


function testPost($name)
{
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO TEST_TB (name) VALUES (?);";

    $st = $pdo->prepare($query);
    $st->execute([$name]);

    $st = null;
    $pdo = null;

}

// CREATE
//    function addMaintenance($message){
//        $pdo = pdoSqlConnect();
//        $query = "INSERT INTO MAINTENANCE (MESSAGE) VALUES (?);";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message]);
//
//        $st = null;
//        $pdo = null;
//
//    }


// UPDATE
//    function updateMaintenanceStatus($message, $status, $no){
//        $pdo = pdoSqlConnect();
//        $query = "UPDATE MAINTENANCE
//                        SET MESSAGE = ?,
//                            STATUS  = ?
//                        WHERE NO = ?";
//
//        $st = $pdo->prepare($query);
//        $st->execute([$message, $status, $no]);
//        $st = null;
//        $pdo = null;
//    }

// RETURN BOOLEAN
//    function isRedundantEmail($email){
//        $pdo = pdoSqlConnect();
//        $query = "SELECT EXISTS(SELECT * FROM USER_TB WHERE EMAIL= ?) AS exist;";
//
//
//        $st = $pdo->prepare($query);
//        //    $st->execute([$param,$param]);
//        $st->execute([$email]);
//        $st->setFetchMode(PDO::FETCH_ASSOC);
//        $res = $st->fetchAll();
//
//        $st=null;$pdo = null;
//
//        return intval($res[0]["exist"]);
//
//    }

function convert_to_userNo($email)
{
    $pdo = pdoSqlConnect();
    $query = "select no from users where email = ?;";
//        echo $query;
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;
    $pdo = null;

   return $res[0]['no'];
}

function isValidJWToken($email, $password)
{

    $pdo = pdoSqlConnect();
//        echo "현재 로그인한 유저 아이디: $userid";
//        echo "pw : $userpw";
    $query = "SELECT EXISTS(SELECT * FROM users WHERE email = ? and password = ?) AS exist";
//        echo $query;
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$email, $password]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;
    $pdo = null;

    return array("intval"=>intval($res[0]["exist"]), "email"=>$email);
}
