<?php
function updateFCMToken($userNo, $token)
{
    if (checkDuplicatedFcmToken($token)) {
        refreshFcmToken($token);
    }
    $pdo = pdoSqlConnect();
    $query = "UPDATE users SET fcmToken=? WHERE no=?";
    $params = [$token, $userNo];


    $st = $pdo->prepare($query);
    $st->execute($params);

    $st = null;
    $pdo = null;

    return true;
}

//이미 같은토큰이 있는지 검사
function checkDuplicatedFcmToken($token)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT COUNT(*) as cnt FROM users WHERE fcmToken = ?;";
    $params = [$token];


    $st = $pdo->prepare($query);
    $st->execute($params);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    if ($res[0]['cnt'] > 0) {
        return 1;
    } else {
        return 2;
    }
}

//해당토큰 초기화
function refreshFcmToken($token)
{
    $pdo = pdoSqlConnect();
    $query = "UPDATE users SET fcmToken = 'empty' WHERE fcmToken = ?;";
    $params = [$token];


    $st = $pdo->prepare($query);
    $st->execute($params);
    $st->setFetchMode(PDO::FETCH_ASSOC);

    $st = null;
    $pdo = null;
}


//유저 닉네임 가져오기
function getNickname($userNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT nickname FROM users WHERE no = ?";

    $st = $pdo->prepare($query);
    $st->execute([$userNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    $nickname = $res[0]['nickname'];
    return $nickname;
}

//Post의 userNo 가져오기
function getUserNoFromPost($postNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT user_no FROM posts WHERE no = ?";

    $st = $pdo->prepare($query);
    $st->execute([$postNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;


    $userNo = $res[0]['user_no'];

    return $userNo;
}


//FCM 토큰 가져오기
function getFCMTokens($userNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT fcmToken FROM users WHERE no = ?";

    $st = $pdo->prepare($query);
    $st->execute([$userNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    $token = $res[0]['fcmToken'];

    return $token;
}

//push history Insert
function insertPushHistory($userNo, $otherUserNo, $type, $postNo)
{
    try {
        $pdo = pdoSqlConnect();
        $query = "INSERT INTO push_history (userNo, otherUserNo, type, postNo) VALUES (?,?,?,?);";
        $st = $pdo->prepare($query);
        $st->bindParam(1, $userNo, PDO::PARAM_INT);
        $st->bindParam(2, $otherUserNo, PDO::PARAM_INT);
        $st->bindParam(3, $type, PDO::PARAM_INT);
        $st->bindParam(4, $postNo, PDO::PARAM_INT);

        $st->execute();

        $st = null;
        $pdo = null;
    } catch (Exception $e) {
        echo $e;
    }
}

function getPushHistory($userNo)
{
    $pdo = pdoSqlConnect();
    $query = "SELECT nickname as userName, url as profileUrl, type, contents as content, postNo FROM push_history, users, images, posts WHERE posts.no = push_history.postNo AND users.no = userNo AND images.no = profile_image_no AND otherUserNo = ? ORDER BY created_at DESC;";

    $st = $pdo->prepare($query);
    $st->execute([$userNo]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st = null;
    $pdo = null;

    return $res;
}

function getUserFeed($userNo, $page)
{
    try {
        $int = (int)0;
        $post_result = array();
        $is_deleted = (string)'N';
        $pdo = pdoSqlConnect();
        $query = "select getlike.postNo,
       nickname,
       getlike.user_no           as userNo,
       profileUrl,
       getlike.registered_timestamp,
       contents,
       likedCount,
       count(scrap_relations.no) as scrapCount
from (
         select getUser.postNo,
                nickname,
                getUser.user_no,
                profileUrl,
                registered_timestamp,
                contents,
                count(likes.no) as likedCount
         from (select postinUSER.no as postNo,
                      nickname,
                      postinUSER.user_no,
                      images.url    as profileUrl,
                      postinUSER.name,
                      postinUSER.registered_timestamp,
                      contents
               from users
                        inner join images
                        inner join (select *
                                    from posts
                                             inner join locations on posts.location_no = locations.nationalNo
                                    where (user_no = :userNo)
                                      and is_deleted = :deleted1) postinUSER
                                   on users.no = postinUSER.user_no and users.profile_image_no = images.no and
                                      postinUSER.user_no = users.no) getUser
                  left outer join likes
                                  on getUser.postNo = likes.post_no and likes.is_deleted = :deleted2
         group by getUser.postNo, nickname, profileUrl, registered_timestamp, contents) getlike
         left outer join scrap_relations on getlike.postNo = scrap_relations.post_no and scrap_relations.is_deleted = :deleted3
group by getlike.postNo, nickname, profileUrl, registered_timestamp, contents, likedCount
order by registered_timestamp DESC
limit :page, 20 ;";

        $st = $pdo->prepare($query);
        $st->bindParam(':userNo', $userNo, PDO::PARAM_INT);
        $st->bindParam(':deleted1', $is_deleted, PDO::PARAM_STR);
        $st->bindParam(':deleted2', $is_deleted, PDO::PARAM_STR);
        $st->bindParam(':deleted3', $is_deleted, PDO::PARAM_STR);
        $st->bindParam(':page', $page, PDO::PARAM_INT);
        $st->execute();
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

        $st = null;
        $pdo = null;

//        return $res;

        foreach ($res as $row) {
            $postNo = $row['postNo'];

            $pdo2 = pdoSqlConnect();
            $querymyLike = "select exists(select * from posts inner join likes on posts.no = likes.post_no where post_no = ? and likes.is_deleted = ? and likes.user_no = ?) as myLike;";
            $st1 = $pdo2->prepare($querymyLike);
            $querymyScrap = "select exists(select * from posts inner join scrap_relations on posts.no = scrap_relations.post_no where post_no = ? and scrap_relations.is_deleted = ? and scrap_relations.user_no = ?) as myScrap;";
            $st2 = $pdo2->prepare($querymyScrap);
            $querymyPost = "select exists (select * from posts inner join users on posts.user_no = users.no where posts.no = ? and posts.user_no = ? )as myPost;";
            $st3 = $pdo2->prepare($querymyPost);
            $queryimageUrl = "select distinct  url as imageUrl from posts inner join post_image_relations inner join images on posts.no = post_image_relations.post_no and posts.registered_timestamp = images.is_registered where posts.no =? and images.is_deleted = ?;";
            $st4 = $pdo2->prepare($queryimageUrl);
            $querycomments = "select count(*)as commentsCount from posts inner join comments on comments.post_no = posts.no where posts.no = ? and comments.is_deleted = ? order by comments.comment_sequence;";
            $st5 = $pdo2->prepare($querycomments);
            $queryNational = "select locations.name as nationalName from posts inner join locations on posts.location_no = locations.nationalNo where posts.no = ?;";
            $st6 = $pdo2->prepare($queryNational);

            $pdo2->beginTransaction();
            $st1->execute([$postNo, $is_deleted, $userNo]);
            $st2->execute([$postNo, $is_deleted, $userNo]);
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

function getGallery($userNo, $page)
{
    try {
        $pdo = pdoSqlConnect();
        $query = "SELECT url, post_no as postNo FROM post_image_relations INNER JOIN images INNER JOIN posts WHERE post_image_no = images.no AND user_no = :userNo AND posts.no = post_image_relations.post_no limit :page, 18 ;";

        $st = $pdo->prepare($query);
        $st->bindParam(':userNo', $userNo, PDO::PARAM_INT);
        $st->bindParam(':page', $page, PDO::PARAM_INT);
        $st->execute();
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $res = $st->fetchAll();

        $st = null;
        $pdo = null;

        return $res;
    } catch (Exception $e) {
        echo $e;
    }
}