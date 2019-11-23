<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";
const FCM_SERVER_KEY = "AAAAlvHpLX0:APA91bFragmSbcQL3AxalcKWt0rtp-8TXcvvSnQIWWleoMJlzqtAZhfYmqo3CvuKi5aFMDL2COTKVMO4feMd_dSQQ-4jhv0DlLUddpJ7TicFkjaSa-QcR0KZTNiDofXDc8SypOZUpiow";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 0
         * API Name : JWT 유효성 검사 테스트 API
         * 마지막 수정 날짜 : 19.04.25
         */

        case "home":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
//            $nationalNo = $req->nationalNo;
            $page = $req->page;
            $size = $req->size;


            $result = $req->result;

            $count = 0;
            foreach ($result as $nationalNo => $value) {
                $nationalId = $value->nationalNo;
                $national[$count++] = $nationalId;
            }


            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                //피드 조회 api
//                if(strlen($nationalNo) < 1)
//                {
//                    $res->isSuccess = false;
//                    $res->code = 116;
//                    $res->message = "국가번호를 입력해주세요";
//                    echo json_encode($res, JSON_NUMERIC_CHECK);
//                    return;
//                }

                if (count($national) < 1) {
                    $res->isSuccess = false;
                    $res->code = 112;
                    $res->message = "관심지역을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($page) < 1 or strlen($size) < 1) {
                    $res->isSuccess = false;
                    $res->code = 116;
                    $res->message = "페이징과 사이즈를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($nationalNo) > 0 and strlen($page) > 0 and strlen($size) > 0) {
//                    echo json_encode($national);
                    $res->result = getHome($national, $userNo, $page, $size);
                    $res->code = 100;
                    $res->message = "피드조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }

            }

            break;


        case "postPost":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $nationalNo = $req->nationalNo;
            $text = $req->text;
            $photo = $req->photo;

            $count = 0;
            foreach ($photo as $url => $value) {
                $urlResult = $value->url;
                $photoResult[$count++] = $urlResult;
            }

            $userNo = convert_to_userNo($email);

//            echo "$userNo";

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {

                if (strlen($text) < 1) {
                    $res->isSuccess = false;
                    $res->code = 117;
                    $res->message = "글내용을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($nationalNo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 118;
                    $res->message = "지역번호을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (count($photo) < 1 and strlen($text) > 0 and strlen($nationalNo) > 0) {
                    http_response_code(200);
                    without_postPost($userNo, $nationalNo, $text);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "게시글 저장을 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (count($photo) > 0 and strlen($text) > 0 and strlen($nationalNo) > 0) {
                    http_response_code(200);
                    postPost($userNo, $nationalNo, $text, $photoResult);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "게시글 저장을 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            break;

        case "getPost":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $postNo = $vars["postNo"];

//            echo "$postNo";

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            }
            else if($isintval === 1)
            {
                if(strlen($postNo) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 100;
                    $res->message = "글번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if(strlen($postNo) > 0)
                {

                    $res->result = getPost($postNo, $userNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "게시글 상세조회를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }

            break;

        case "getComments": //댓글 조회 API 댓글만 조회함

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $postNo = $vars["postNo"];

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                if (strlen($postNo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 144;
                    $res->message = "글번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($postNo) > 0) {
                    $res->result = getCommnets($postNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 조회를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);

                    return;
                }
            }

            break;

        case "postLike":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $postNo = $vars["postNo"]; // 숫자만

//            echo "$postNo";

            $pattern_postNo = "/^[0-9]+$/";

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {

                if (!preg_match($pattern_postNo, $postNo)) {
                    $res->isSuccess = false;
                    $res->code = 120;
                    $res->message = "게시글 번호를 숫자로 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($postNo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 119;
                    $res->message = "게시글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isalreadyLike = likeCheck($userNo, $postNo);

                if ($isalreadyLike == 1) {
                    $res->isSuccess = false;
                    $res->code = 121;
                    $res->message = "이미 좋아요한 게시글 입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($postNo) > 0) {
                    postLike($postNo, $userNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "좋아요를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);

                    $myNickname = getNickname($userNo);
                    $toUserNo = getUserNoFromPost($postNo);

                    if($toUserNo == $userNo){
                        return;
                    }
                    $data = array("title" => "히어데어",
                        "body" => $myNickname . "님이 내 글에 좋아요를 눌렀습니다",
                        "message" => $myNickname . "님이 내 글에 좋아요를 눌렀습니다",
                        "postNo" => $postNo);

//                    echo $toUserNo;
                    $tokens = getFCMTokens($toUserNo);
                    $key = $tokens;

                    $result = sendFcm($key, $data, FCM_SERVER_KEY, 'ANDROID');
                    if ($result == 1) {
                        insertPushHistory((int)$userNo, (int)$toUserNo, 1, $postNo);
                    }

                    return;
                }
            }

            break;

        case "deleteLike":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $postNo = $vars["postNo"]; // 숫자만

//            echo "$postNo";

            $pattern_postNo = "/^[0-9]+$/";

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {

                if (!preg_match($pattern_postNo, $postNo)) {
                    $res->isSuccess = false;
                    $res->code = 123;
                    $res->message = "게시글 번호를 숫자로 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($postNo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 122;
                    $res->message = "게시글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isalreadyLike = likeCheck($userNo, $postNo);

                if ($isalreadyLike == 0) {
                    $res->isSuccess = false;
                    $res->code = 124;
                    $res->message = "좋아요가 되어있지 않은 게시글 입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($postNo) > 0) {
                    deleteLike($userNo, $postNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "좋아요 해제를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }

            break;

        case "postScrap":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $scrapName = $req->name;
            $closure = $req->closure;
            $photo = $req->photo;

            $count = 0;
            foreach ($photo as $url => $value) {
                $urlResult = $value->url;
                $photoResult[$count++] = $urlResult;
            }

            $userNo = convert_to_userNo($email);


            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                if (count($photo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 125;
                    $res->message = "사진 URL을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($scrapName) < 1) {
                    $res->isSuccess = false;
                    $res->code = 126;
                    $res->message = "스크랩북 이름을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($closure) < 1) {
                    $res->isSuccess = false;
                    $res->code = 127;
                    $res->message = "스크랩 공개여부를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                //스크랩북 이름 중복 검사

                if (count($photo) > 0 and strlen($scrapName) > 0 and strlen($closure) > 0) {
                    http_response_code(200);
                    postScrap($photoResult, $scrapName, $closure, $userNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "스크랩북 추가를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);


                    return;
                }

            }
            break;

        case "getScrap": //스크랩북 목록 조회 API

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                http_response_code(200);
                $res->result = getScrap($userNo);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "스크랩북 목록 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;

        case "doScrap_post":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $postNo = $vars["postNo"];
            $scrapNo = $vars["scrapNo"];

//            echo "postNo : $postNo";
//            echo " scrapNo : $scrapNo";

            $pattern_No = "/^[0-9]+$/";

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                if (!preg_match($pattern_No, $postNo)) {
                    $res->isSuccess = false;
                    $res->code = 128;
                    $res->message = "게시글 번호를 숫자로 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (!preg_match($pattern_No, $scrapNo)) {
                    $res->isSuccess = false;
                    $res->code = 129;
                    $res->message = "스크랩북 번호를 숫자로 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($postNo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 130;
                    $res->message = "게시글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($scrapNo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 131;
                    $res->message = "스크랩북 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isexistScrap = scrapCheck($userNo, $scrapNo);
                if ($isexistScrap == 0) {
                    $res->isSuccess = false;
                    $res->code = 132;
                    $res->message = "유효한 스크랩북 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                $isexistPost = postCheck($postNo, $scrapNo);
                if ($isexistPost == 0) {
                    $res->isSuccess = false;
                    $res->code = 133;
                    $res->message = "유효한 게시글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($postNo) > 0 and strlen($scrapNo) > 0) {
                    http_response_code(200);
                    doScrap($scrapNo, $postNo, $userNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "스크랩을 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);


                    $myNickname = getNickname($userNo);
                    $toUserNo = getUserNoFromPost($postNo);

                    if($toUserNo == $userNo){
                        return;
                    }

                    $data = array("title" => "히어데어",
                        "body" => $myNickname . "님이 내 글을 스크랩했습니다.",
                        "message" => $myNickname . "님이 내 글을 스크랩했습니다.",
                        "postNo" => $postNo);

//                    echo $toUserNo;
                    $tokens = getFCMTokens($toUserNo);
                    $key = $tokens;

                    $result = sendFcm($key, $data, FCM_SERVER_KEY, 'ANDROID');
                    if ($result == 1) {
                        insertPushHistory((int)$userNo, (int)$toUserNo, 3, $postNo);
                    }

                    return;
                    return;
                }
            }
            break;

        case "deleteScrap_post":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $postNo = $vars["postNo"];
            $scrapNo = $vars["scrapNo"];

//            echo "postNo : $postNo";
//            echo " scrapNo : $scrapNo";

            $pattern_No = "/^[0-9]+$/";

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                if (!preg_match($pattern_No, $postNo)) {
                    $res->isSuccess = false;
                    $res->code = 134;
                    $res->message = "게시글 번호를 숫자로 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (!preg_match($pattern_No, $scrapNo)) {
                    $res->isSuccess = false;
                    $res->code = 135;
                    $res->message = "스크랩북 번호를 숫자로 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($postNo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 136;
                    $res->message = "게시글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($scrapNo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 137;
                    $res->message = "스크랩북 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

//                echo $userNo, $scrapNo;

                $isexistScrap = scrapCheck($userNo, $scrapNo);

                if ($isexistScrap == 0)
                {
                    $res->isSuccess = false;
                    $res->code = 138;
                    $res->message = "유효한 스크랩북 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isexistPost = postCheck($postNo);

                if ($isexistPost == 0) {
                    $res->isSuccess = false;
                    $res->code = 139;
                    $res->message = "유효한 게시글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (strlen($postNo) > 0 and strlen($scrapNo) > 0) {
                    http_response_code(200);
//
                    dontScap($postNo, $scrapNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "스크랩 해제를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }

            break;

        case "deleteScrap_post_no_scrap":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $postNo = $vars["postNo"];
            $scrapNo = $vars["scrapNo"];
            

            $pattern_No = "/^[0-9]+$/";

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                if (!preg_match($pattern_No, $postNo)) {
                    $res->isSuccess = false;
                    $res->code = 134;
                    $res->message = "게시글 번호를 숫자로 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                
                if (strlen($postNo) < 1) {
                    $res->isSuccess = false;
                    $res->code = 136;
                    $res->message = "게시글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                

                $isexistPost = postCheck($postNo);

                if ($isexistPost == 0) {
                    $res->isSuccess = false;
                    $res->code = 139;
                    $res->message = "유효한 게시글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isScrapedPost = isScrapedPost($userNo, $postNo);

                if ($isScrapedPost == 0) {
                    $res->isSuccess = false;
                    $res->code = 137;
                    $res->message = "스크랩 하지 않은 게시물입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                
                
                $scrapDeleteResult = dontScapByUserNo($postNo, $userNo);

                if ($scrapDeleteResult == 1) {
                    http_response_code(200);
                    dontScap($postNo, $scrapNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "스크랩 해제를 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                else{
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "스크랩 해제에 실패했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
            }

            break;
            
            
        case "patchScrap": //


            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $scrapNo = $vars["scrapNo"];
            $scrapName = $req->name;
            $closure = $req->closure;
            $photo = $req->photo;

            $count = 0;
            foreach ($photo as $url => $value) {
                $urlResult = $value->url;
                $photoResult[$count++] = $urlResult;
            }

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                if (strlen($scrapName) < 1) {
                    $res->isSuccess = false;
                    $res->code = 134;
                    $res->message = "게시글 이름을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }


                if (strlen($closure) < 1) {
                    $res->isSuccess = false;
                    $res->code = 134;
                    $res->message = "공개여부를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if (count($photoResult) < 1) {
                    $res->isSuccess = false;
                    $res->code = 134;
                    $res->message = "사진 URL을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isexistScrap = scrapCheck($userNo, $scrapNo);

                if ($isexistScrap == 0) {
                    $res->isSuccess = false;
                    $res->code = 138;
                    $res->message = "유효한 스크랩북 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

            }

            break;

        case "postComments":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $postNo = $vars["postNo"]; //commentsNo
//            $tagUser= $req->tagUser;
            $content = $req->content;
            $tagNo = $req->tagNo;
//            $photo = $req->photo;

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            }
            else if($isintval === 1)
            {
                if(strlen($content) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 145;
                    $res->message = "글내용을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if( strlen($tagNo) < 1)
                {
                    postComments_without_tag($userNo, $postNo, $content);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 작성을 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);

                    $myNickname = getNickname($userNo);
                    $toUserNo = getUserNoFromPost($postNo);

                    if($toUserNo == $userNo){
                        return;
                    }

                    $data = array("title" => "히어데어",
                        "body" => $myNickname . "님이 내 글에 댓글을 달았습니다",
                        "message" => $myNickname . "님이 내 글에 댓글을 달았습니다",
                        "postNo" => $postNo);

//                    echo $toUserNo;
                    $tokens = getFCMTokens($toUserNo);
                    $key = $tokens;

                    $result = sendFcm($key, $data, FCM_SERVER_KEY, 'ANDROID');
                    if ($result == 1) {
                        insertPushHistory((int)$userNo, (int)$toUserNo, 2, $postNo);
                    }

                    return;
                }
                else if(strlen($tagNo) > 0 and strlen($content) > 0)
                {
                    postComments($userNo, $postNo, $content, $tagNo);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "댓글 작성을 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);

                    return;
                }
            }

            break;

        case "getBundle":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $postNo = $vars["postNo"]; //commentsNo
            $commentsNo = $vars["commentsNo"];
            $tagNo = $_GET['tagNo'];

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            }
            else if($isintval === 1)
            {
                if(strlen($commentsNo) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 146;
                    $res->message = "댓글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if(strlen($tagNo) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 146;
                    $res->message = "태그 댓글 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $res->result = getTagcomments($commentsNo, $tagNo);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "댓글 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;

        case "getscrapHome":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $scrapNo = $vars["scrapNo"];
            $page = (int)$vars["page"];

            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            }
            else if($isintval === 1)
            {
                if(strlen($scrapNo) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 147;
                    $res->message = "스크랩 번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $res->result = getscrapHome($scrapNo, $userNo, $page);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "스크랩북 피드 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;

            }

            break;

        case "searchPost":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $word = $req->word;
            $page = $req->page;

            $result = $req->result;

            $count = 0;
            foreach ($result as $nationalNo => $value) {
                $nationalId = $value->nationalNo;
                $national[$count++] = $nationalId;
            }


            $userNo = convert_to_userNo($email);

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            }
            else if($isintval === 1)
            {
                if(strlen($word) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 147;
                    $res->message = "검색어를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if(count($result) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 148;
                    $res->message = "지역번호를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $res->result = getPost_word($userNo, $national, $word, $page);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "게시글 검색을 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;

            }

                break;


        case "searchNick":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $word = $req->word;
            $page = $req->page;

            $userNo = convert_to_userNo($email);


            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            }
            else if($isintval === 1)
            {
                if(strlen($word) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 147;
                    $res->message = "검색어를 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $res->result = getnick($word, $page);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "닉네임 검색을 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;


        case "validateJwt":
            // jwt 유효성 검사
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }

            http_response_code(200);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";

            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : JWT 생성 테스트 API
         * 마지막 수정 날짜 : 19.04.25
         */

        case "createJwt":
            // jwt 유효성 검사
            if (!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req);
                return;
            }
            http_response_code(200);

            //페이로드에 맞게 다시 설정 요함
            $jwt = getJWToken($userId, $userPw, $loginType, $accessToken, $refreshToken, JWT_SECRET_KEY);
            $res->result->jwt = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
