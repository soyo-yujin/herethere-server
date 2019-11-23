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

        case "updateFCMToken":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];

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
            }

            $token = $req->FCMToken;
            http_response_code(200);

            $res->isSuccess = updateFCMToken($userNo, $token);
            $res->code = 100;
            $res->message = "성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "pushHistory":
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];

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
            }
            http_response_code(200);


            $res->result = getPushHistory($userNo);
            $res->code = 100;
            $res->message = "성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;

        case "getMyPost":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
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
            } else if ($isintval === 1) {
                $getMyPost = getUserFeed($userNo, $page);

                if ($getMyPost == false) {
                    $res->isSuccess = False;
                    $res->code = 200;
                    $res->message = "내 피드조회를 실패";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                } else {
                    $res->result = $getMyPost;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "내 피드조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                return;

            }

            break;

        case "getMyGallery":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
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
            } else if ($isintval === 1) {
                $getMyPost = getGallery($userNo, $page);

                if ($getMyPost == false) {
                    $res->isSuccess = False;
                    $res->code = 200;
                    $res->message = "내 갤러리 실패";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                } else {
                    $res->result = $getMyPost;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "내 피드조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                return;

            }

            break;

        case "getUserProfile":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];

//            $userNo = convert_to_userNo($email);
            $userNo = $vars["userNo"];

//            echo "$userNo";

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
                http_response_code(200);
                $res->result = getUser($userNo);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "유저정보 조회를 성공했습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;

        case "getUserPost":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $page = (int)$vars["page"];

            $userNo = $vars["userNo"];

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                $getMyPost = getUserFeed($userNo, $page);

                if ($getMyPost == false) {
                    $res->isSuccess = False;
                    $res->code = 200;
                    $res->message = "내 피드조회를 실패";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                } else {
                    $res->result = $getMyPost;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "내 피드조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                return;

            }

            break;

        case "getUserGallery":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $page = (int)$vars["page"];

            $userNo = $vars["userNo"];

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            } else if ($isintval === 1) {
                $getMyPost = getGallery($userNo, $page);

                if ($getMyPost == false) {
                    $res->isSuccess = False;
                    $res->code = 200;
                    $res->message = "내 갤러리 실패";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                } else {
                    $res->result = $getMyPost;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "내 피드조회 성공";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                }
                return;

            }

            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
