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
    $is_deleted = (string)'N';

    $pdo = pdoSqlConnect();
    $query = "INSERT INTO images (no, is_deleted) VALUES (?,?);";
    $st = $pdo->prepare($query);
    $st->bindParam(1, $no, PDO::PARAM_INT);
    $st->bindParam(2, $is_deleted, PDO::PARAM_STR);
    $st->execute();

    $st = null;
    $pdo = null;

    $pdo = pdoSqlConnect();
    $query = "select no from images order by is_registered desc;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    $image_no = $res[0]['no'];

    $no = (int)0;
//    $image_no = (int)1; //1이 NULL
    $email = (string)$email;
    $name = (string)$name;
    $birth = (string)$birth;
    $password = (string)$password;
    $auth = (string)"N";
    $is_deleted = (int)0;
    $nickname = (string)$nickname;
    $timestamp = date("Y-m-d H:i:s");


    $pdo = pdoSqlConnect();
    $query = "INSERT INTO users (no, email, name, birth, password, Authorization, registered_timestamp, is_deleted, profile_image_no, nickname) VALUES (?,?,?,?,?,?,?,?,?,?);";


    $st = $pdo->prepare($query);
    $st->bindParam(1, $no, PDO::PARAM_INT);
    $st->bindParam(2, $email, PDO::PARAM_STR);
    $st->bindParam(3, $name, PDO::PARAM_STR);
    $st->bindParam(4, $birth, PDO::PARAM_INT);
    $st->bindParam(5, $password, PDO::PARAM_STR);
    $st->bindParam(6, $auth, PDO::PARAM_STR);
    $st->bindParam(7, $timestamp, PDO::PARAM_STR);
    $st->bindParam(8, $is_deleted, PDO::PARAM_STR);
    $st->bindParam(9, $image_no, PDO::PARAM_INT);
    $st->bindParam(10, $nickname, PDO::PARAM_STR);
    $st->execute();

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
    $st->execute();

    $st = null;
    $pdo = null;
}

function getArea()
{
    $pdo = pdoSqlConnect();
    $query = "select * from locations ";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function postArea($userNo, $national)
{
    $no = 0;
    $int = 0;
    $count = count($national);
    $question_marks = str_repeat(",(?,?,?)", $count - 1);

    $pdo = pdoSqlConnect();
    $query = " INSERT INTO interesting_relations (no , user_no, location_no) VALUES (?,?,?)$question_marks;";

    $st = $pdo->prepare($query);

    foreach ($national as $row => $value) {
        $st->bindValue($int + 1, $no);
        $st->bindValue($int + 2, $userNo);
        $st->bindValue($int + 3, $value);
        $int = $int + 3;
    }
    $st->execute();

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

function getUser($userNo)
{
    $is_deleted = 'N';
    $pdo = pdoSqlConnect();
    $query = "select url, nickname, email, introduce from users inner join images on  users.profile_image_no = images.no where users.No = ? and images.is_deleted = ?;";
//    echo "$query";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userNo, $is_deleted]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getMyarea($userNo)
{
    $pdo = pdoSqlConnect();
    $query = "select nationalNo, name from
(select * from locations) selectNational inner join
(select distinct location_no from users inner join interesting_relations on users.no = interesting_relations.user_no where users.no = ?) selectUser
on selectNational.nationalNo = selectUser.location_no order by nationalNo;";
//    echo "$query";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function viewdetailUser($userNo)
{
    $pdo = pdoSqlConnect();
    $query = "select url, nickname, email, introduce, Authorization from users inner join images on users.profile_image_no = images.no where users.no = ? group by users.no;";

    $st = $pdo->prepare($query);
    $st->execute([$userNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;

    $is_deleted = 'N';

    $pdo = pdoSqlConnect();
    $query = "select selectNational.nationalNo, name from
(select * from locations) selectNational inner join
(select distinct location_no from users inner join interesting_relations on users.no = interesting_relations.user_no where users.no = ? and interesting_relations.is_deleted = ?) selectUser
on selectNational.nationalNo = selectUser.location_no;";

    $st = $pdo->prepare($query);
    $st->execute([$userNo, $is_deleted]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res2 = $st->fetchAll();
    $st = null;
    $pdo = null;

//    return $res;
//    return $res2;

    return array('user' => $res[0], 'national' => $res2);
}


function patchUser($url, $introduce, $userNo) //관심지역 추가 요망
{
    $pdo = pdoSqlConnect();
    $query = "UPDATE images
                        SET url = ?
                        WHERE NO = (select profile_image_no from users where no = ?);";

    $st = $pdo->prepare($query);
    $st->execute([$url, $userNo]);
    $st = null;
    $pdo = null;

    $pdo = pdoSqlConnect();
    $query = "UPDATE users
                        SET introduce = ?
                        WHERE NO = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$introduce, $userNo]);
    $st = null;
    $pdo = null;
}

function without_postPost($userNo, $nationalNo, $text)
{
    $no = (int)0;
    $text = (string)$text;
    $nationalNo = (int)$nationalNo;
    $is_registered = (string)date("Y-m-d H:i:s");
    $is_deleted = (string)'N';

    $pdo = pdoSqlConnect();
    $query = "insert into posts (no, contents, user_no, location_no, registered_timestamp, is_deleted) values (?,?,?,?,?,?);";
//    echo "$query";
    $st = $pdo->prepare($query);
    $st->execute([$no, $text, $userNo, $nationalNo, $is_registered, $is_deleted]);
    $st = null;
    $pdo = null; //insert post
}


function postPost($userNo, $nationalNo, $text, $photoResult)
{
    $no = (int)0;
    $text = (string)$text;
    $nationalNo = (int)$nationalNo;
    $is_registered = (string)date("Y-m-d H:i:s");
    $is_deleted = (string)'N';

    $pdo = pdoSqlConnect();
    $query = "insert into posts (no, contents, user_no, location_no, registered_timestamp, is_deleted) values (?,?,?,?,?,?);";
//    echo "$query";
    $st = $pdo->prepare($query);
    $st->execute([$no, $text, $userNo, $nationalNo, $is_registered, $is_deleted]);
    $st = null;
    $pdo = null; //insert post


    $int = 0;
    $squence = 1;
    $count = count($photoResult);
    $question_marks = str_repeat(",(?,?,?,?,?)", $count - 1);
    $pdo = pdoSqlConnect();
    $query = "insert into images (no, is_deleted, url, is_registered, image_sqeunce) values (?,?,?,?,?)$question_marks;";
//    echo "$query";
    $st = $pdo->prepare($query);
    foreach ($photoResult as $row => $value) //이미지
    {
        $st->bindValue($int + 1, $no);
        $st->bindValue($int + 2, $is_deleted);
        $st->bindValue($int + 3, $value);
        $st->bindValue($int + 4, $is_registered);
        $st->bindValue($int + 5, $squence);
        $int = $int + 5;
        $squence = $squence + 1;
    }
    $st->execute();
    $st = null;
    $pdo = null; //inset image url


    $pdo = pdoSqlConnect();
    $query = "select posts.no as post_no, images.no as image_no from images inner join posts on images.is_registered = posts.registered_timestamp where registered_timestamp = (select registered_timestamp from posts order by registered_timestamp desc limit 1);";
//    echo "$query";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null; //insert post

//    echo json_encode($res);

    $int2 = 0;
    $post_no = $res[0]['post_no'];
    $image_no = $res[0]['image_no'];
    $count = count($res);
    $question_marks = str_repeat(",(?,?,?)", $count - 1);
    $pdo = pdoSqlConnect();
    $query = "insert post_image_relations (no, post_image_no, post_no) VALUES (?,?,?)$question_marks;";
//    echo "$query";
    $st = $pdo->prepare($query);
    for ($int = 0; $int < $count; $int++) // 0,1
    {
        $result_image_no = $image_no + $int;
//        echo " post : $post_no";
//        echo " image : $result_image_no";
        $st->bindValue($int2 + 1, $no);
        $st->bindValue($int2 + 2, $result_image_no);
        $st->bindValue($int2 + 3, $post_no);
        $int2 = $int2 + 3;
    }
    $st->execute();
    $st = null;
    $pdo = null; //insert post
}

function getPost()
{
    $pdo = pdoSqlConnect();
    $query = "";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;

}

function postLike($postNo, $userNo)
{
    $no = (int)0;
    $is_deleted = (string)'N';

    $pdo = pdoSqlConnect();
    $query = "insert into likes (no, user_no, post_no, is_deleted) values (?,?,?,?);";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$no, $userNo, $postNo, $is_deleted]);

    $st = null;
    $pdo = null;
}

function deleteLike($userNo, $postNo)
{
    $is_deleted = (string)'Y';
    $pdo = pdoSqlConnect();
    $query = "UPDATE likes
                      SET is_deleted = ?
                     WHERE user_no = ? and post_no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$is_deleted, $userNo, $postNo]);
    $st = null;
    $pdo = null;
}

function postScrap($photoResult, $scrapName, $closure, $userNo)
{
    $int = 0;
    $no = (int)0;
    $is_deleted = (string)'N';
    $is_registered = (string)date("Y-m-d H:i:s");
    $count = count($photoResult);
    $question_marks = str_repeat(",(?,?,?,?)", $count - 1);

    $pdo = pdoSqlConnect();
    $query = "insert into images (no, is_deleted, url, is_registered) values (?,?,?,?)$question_marks;";
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    foreach ($photoResult as $row => $value) //이미지
    {
        $st->bindValue($int + 1, $no);
        $st->bindValue($int + 2, $is_deleted);
        $st->bindValue($int + 3, $value);
        $st->bindValue($int + 4, $is_registered);
        $int = $int + 4;
    }
    $st->execute();

    $st = null;
    $pdo = null;

    $pdo = pdoSqlConnect();
    $query = "insert into scraps (no, title, title_image_no, registered_timestamp, is_deleted, user_no, is_closure) values (?,?,(select no from images order by is_registered desc limit 1),?,?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$no, $scrapName, $is_registered, $is_deleted, $userNo, $closure]);

    $st = null;
    $pdo = null;
}

function getScrap($userNo)
{
    $is_deleted = 'N';
    $pdo = pdoSqlConnect();
    $query = "SELECT scraps.no, url,  title, is_closure FROM scraps inner join images on scraps.title_image_no = images.no where user_no = ? and scraps.is_deleted = ?;";

    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userNo, $is_deleted]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function doScrap($scrapNo, $postNo)
{
    $no = (int)0;
    $is_deleted = (string)'N';
    $is_registered = (string)date("Y-m-d H:i:s");

    $pdo = pdoSqlConnect();
    $query = "insert into scrap_relations (no, scrap_no, post_no, registered_timestamp, is_deleted)  values (?,?,?,?,?);";

    $st = $pdo->prepare($query);
    $st->execute([$no, $scrapNo, $postNo, $is_registered, $is_deleted]);

    $st = null;
    $pdo = null;
}

function dontScap($scrapNo, $postNo)
{
    $is_deleted = 'Y';
    $pdo = pdoSqlConnect();
    $query = "UPDATE scrap_relations
                 SET is_deleted = ?
                WHERE scrap_no = ? and post_no = ?;";

    $st = $pdo->prepare($query);
    $st->execute([$is_deleted, $scrapNo, $postNo]);

    $st = null;
    $pdo = null;
}

function getHome($nationalArr, $userNo, $page, $size)
{
    try {

        $post_result = array();

//    foreach ($nationalArr as $nationalNo) {
//    $nationalNo = (int)$nationalNo;
        $userNo = (int)$userNo;
        $page = (int)$page;
        $size = (int)$size;
        $is_deleted = (string)'N';
        $int = 0;
        $pdo = pdoSqlConnect();
        $query = "select getlike.postNo, nickname, getlike.user_no as userNo,profileUrl, getlike.registered_timestamp, contents, likedCount, count(scrap_relations.no) as scrapCount from(
select getUser.postNo, nickname, getUser.user_no,profileUrl, registered_timestamp, contents, count(likes.no) as likedCount
from (select postinUSER.no as postNo, nickname, postinUSER.user_no, images.url as profileUrl, postinUSER.name, postinUSER.registered_timestamp,contents
from users inner join images inner join (select * from posts inner join locations on posts.location_no = locations.nationalNo 
where ";
        $whereString = "(";
        if($nationalArr[0] == 0){
            $whereString = "1 = 1 ";
        }
        else{
            for ($i = 0; $i < count($nationalArr); $i++) {
                $nationalNo = $nationalArr[$i];
                if ($i == count($nationalArr) - 1) {
                    $whereString = $whereString . "location_no = " . $nationalNo . ") ";

                } else {
                    $whereString = $whereString . "location_no = " . $nationalNo . " or ";
                }
            }
        }
        $query = $query . $whereString;
        $queryBack = "and is_deleted = :deleted1) postinUSER
    on users.no = postinUSER.user_no and users.profile_image_no = images.no and postinUSER.user_no = users.no) getUser
left outer join likes
    on getUser.postNo = likes.post_no and likes.is_deleted = :deleted2 group by getUser.postNo, nickname, profileUrl, registered_timestamp, contents) getlike
left outer join scrap_relations on getlike.postNo = scrap_relations.post_no and scrap_relations.is_deleted = :deleted3
group by getlike.postNo, nickname, profileUrl, registered_timestamp, contents, likedCount order by registered_timestamp limit :page, :siz;";
        $query = $query.$queryBack;
        $st = $pdo->prepare($query);
//        echo $query;
        $st->bindParam(':deleted1', $is_deleted, PDO::PARAM_STR);
        $st->bindParam(':deleted2', $is_deleted, PDO::PARAM_STR);
        $st->bindParam(':deleted3', $is_deleted, PDO::PARAM_STR);
        $st->bindParam(':page', $page, PDO::PARAM_INT);
        $st->bindParam(':siz', $size, PDO::PARAM_INT);

        $st->execute();
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

//    where location_no = 1 OR location_no = 2 OR location_no = 3

        foreach ($res as $row) {
            $postNo = $row['postNo'];

            $pdo2 = pdoSqlConnect();
            $querymyLike = "select exists(select * from posts inner join likes on posts.no = likes.post_no where post_no = ? and likes.is_deleted = ? and likes.user_no = ?) as myLike;";
            $st1 = $pdo2->prepare($querymyLike);
            $querymyScrap = "select exists(select * from posts inner join scrap_relations on posts.no = scrap_relations.post_no where post_no = ? and scrap_relations.is_deleted = ?) as myScrap;";
            $st2 = $pdo2->prepare($querymyScrap);
            $querymyPost = "select exists (select * from posts inner join users on posts.user_no = users.no where posts.no = ? and posts.user_no = ? )as myPost;";
            $st3 = $pdo2->prepare($querymyPost);
            $queryimageUrl = "select url as imageUrl from posts inner join post_image_relations inner join images on posts.no = post_image_relations.post_no and posts.registered_timestamp = images.is_registered where posts.no =? and images.is_deleted = ? order by image_sqeunce;";
            $st4 = $pdo2->prepare($queryimageUrl);
            $querycomments = "select count(*)as commentsCount from posts inner join comments on comments.post_no = posts.no where posts.no = ? and comments.is_deleted = ? order by comments.comment_sequence;";
            $st5 = $pdo2->prepare($querycomments);
            $queryNational = "select locations.name as nationalName from posts inner join locations on posts.location_no = locations.nationalNo where posts.no = ?;";
            $st6 = $pdo2->prepare($queryNational);

            $pdo2->beginTransaction();
            $st1->execute([$postNo, $is_deleted, $userNo]);
            $st2->execute([$postNo, $is_deleted]);
            $st3->execute([$postNo, $userNo]);
            $st4->execute([$postNo, $is_deleted]);
            $st5->execute([$postNo, $is_deleted]);
            $st6->execute([$postNo]);
            $pdo2->commit();

            $st1->setFetchMode(PDO::FETCH_ASSOC);
            $res1 = $st1->fetchAll();
            $st2->setFetchMode(PDO::FETCH_ASSOC);
            $res2 = $st2->fetchAll();
            $st3->setFetchMode(PDO::FETCH_ASSOC);
            $res3 = $st3->fetchAll();
            $st4->setFetchMode(PDO::FETCH_ASSOC);
            $res4 = $st4->fetchAll();
            $st5->setFetchMode(PDO::FETCH_ASSOC);
            $res5 = $st5->fetchAll();
            $st6->setFetchMode(PDO::FETCH_ASSOC);
            $res6 = $st6->fetchAll();


            $st1 = null;
            $st2 = null;
            $st3 = null;
            $st4 = null;
            $st5 = null;
            $st6 = null;
            $pdo2 = null;

            $st = null;
            $pdo = null;

            $myLike = $res1[0];
            $myScrap = $res2[0];
            $myPost = $res3[0];
            $url = $res4;
            $commentsCount = $res5[0];
            $nationalName = $res6[0];

//        $row->post = $res[$int];
//        $row->post['myLike'] = $myLike['myLike'];
//        $row->post['myScrap'] = $myScrap['myScrap'];
//        $row->post['myPost'] = $myPost['myPost'];
//        $row->post['imageUrl'] = $url;
//        $row->post['commentsCount'] = $commentsCount['commentsCount'];
//        $row->post['nationalName'] = $nationalName['nationalName'];
//        echo json_encode($post);
            $int = $int + 1;

            $row['myLike'] = $myLike['myLike'];
            $row['myScrap'] = $myScrap['myScrap'];
            $row['myPost'] = $myPost['myPost'];
            $row['imageUrl'] = $url;
            $row['commentsCount'] = $commentsCount['commentsCount'];
            $row['nationalName'] = $nationalName['nationalName'];

            array_push($post_result, $row);

        }

        return $post_result;
    } catch (Exception $e) {
        echo $e;
    }
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

function postCheck($postNo)
{
    $is_deleted = 'N';
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from scrap_relations where post_no = ? and is_deleted = ?)as exist
              union
              select exists(select * from posts where  no = ? and is_deleted = ?)as exist;";
//        echo $query;
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$postNo, $is_deleted, $postNo, $is_deleted]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    $firstResult = $res[0]['exist'];
    $secondResult = $res[1]['exist'];

    if ($firstResult and $secondResult) {
        return 1;
    } else {
        return 0;
    }

}

function scrapCheck($userNo, $scrapNo)
{
    $is_deleted = 'N';
    $pdo = pdoSqlConnect();
    $query = "select exists(select * from scraps where user_no = ? and no = ? and is_deleted = ?)as exist;";
//        echo $query;
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userNo, $scrapNo, $is_deleted]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}

function likeCheck($userNo, $postNo)
{
    $is_deleted = 'N';

    $pdo = pdoSqlConnect();
    $query = "select exists(select * from likes where user_no = ? and post_no = ? and is_deleted = ?)as exist;";
//        echo $query;
    $st = $pdo->prepare($query);
    //    $st->execute([$param,$param]);
    $st->execute([$userNo, $postNo, $is_deleted]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res[0]['exist'];
}


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

    $st = null;
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

    $st = null;
    $pdo = null;

    return array("intval" => intval($res[0]["exist"]), "email" => $email);
}


//
//function test()
//{
//    try {
//        $pdo = pdoSqlConnect();
//        $queryUpdateOutstanding =
//            "UPDATE user
//SET available_to_use = available_to_use + (SELECT amount as money from repay_history where repay_no = ?),
//    outstanding      = outstanding - (SELECT amount as money from repay_history where repay_no = ?) - (SELECT discount from repay_history where repay_no = ?)
//WHERE user_no = (SELECT user_no as money from repay_history where repay_no = ?);";
//        $st1 = $pdo->prepare($queryUpdateOutstanding);
//
//        $queryUpdateCouponHistory =
//            "UPDATE use_coupon_history SET status =
//IF ((SELECT outstanding from user where user_no = (SELECT user_no as money from repay_history where repay_no = ?)) <= 0, 'D', 'N')
//WHERE user_no = (SELECT user_no as money from repay_history where repay_no = ?);";
//        $st2 = $pdo->prepare($queryUpdateCouponHistory);
//
//        $queryUpdateRepayHistoru = "UPDATE repay_history SET status = 'S' WHERE repay_no = ?;";
//        $st3 = $pdo->prepare($queryUpdateRepayHistoru);
//
//        $queryUpdateLateFee = "UPDATE user SET late_fee = late_fee - (SELECT amount as money from repay_history where repay_no = ?) WHERE user_no = (SELECT user_no from repay_history where repay_no = ?);";
//        $st4 = $pdo->prepare($queryUpdateLateFee);
//
//        $queryUpdateZeroLateFee = "UPDATE user SET late_fee = 0 WHERE late_fee < 0;";
//        $st5 = $pdo->prepare($queryUpdateZeroLateFee);
//
//        $pdo->beginTransaction();
//        $st1->execute([$repay_no, $repay_no, $repay_no, $repay_no]);
//        $st2->execute([$repay_no, $repay_no]);
//        $st3->execute([$repay_no]);
//        $st4->execute([$repay_no, $repay_no]);
//        $st5->execute([]);
//
//        $pdo->commit();
//
//        $st1 = null;
//        $st2 = null;
//        $st3 = null;
//        $pdo = null;
//
//        return 1;
//
//    } catch (Exception $e) {
//        if ($pdo->inTransaction()) {
//            $pdo->rollback();
//        }
//        throw $e;
//        return 2;
//    }
//}
