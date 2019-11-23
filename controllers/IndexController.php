<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";
//const EMAIL_VALID_CHECK_TYPE = 0;
//const NICK_VALID_CHECK_TYPE = 1;
//const SIGN_UP_CHECK_TYPE = 2;

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);

    switch ($handler) {
        case "index":
            echo "API Server";
            break;
        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
        /*
         * API No. 0
         * API Name : 테스트 API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "email":

            $email= $req->email;
//            $patternEmail = "/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i"; // 이메일 형식
//            echo "email : $email";
            if(strlen($email) < 1)
            {
                $res->isSuccess = false;
                $res->code = 101;
                $res->message = "이메일을 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

//            if (!preg_match($patternEmail, $email))
//            {
//                $res->isSuccess = false;
//                $res->code = 191;
//                $res->message = "잘못된 이메일 형식입니다";
//                echo json_encode($res, JSON_NUMERIC_CHECK);
//                return;
//            }
            $isalreadyEmail  =  emailcheckGuest($email);
            if($isalreadyEmail == 0)
            {
                //중복없음
                http_response_code(200);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "이메일 중복 확인에 성공하였습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
            if ($isalreadyEmail == 1)
            {
                //중복에러
                $res->isSuccess = false;
                $res->code = 102;
                $res->message = "이미 있는 이메일 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;

        case "nickname":

            $nickname= $req->nickname;
            http_response_code(200);

            if(strlen($nickname) < 1)
            {
                $res->isSuccess = false;
                $res->code = 104;
                $res->message = "닉네임을 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $isalreadyNickname  =  nicknamecheckGuest($nickname);
            if($isalreadyNickname == 0)
            {
                //중복없음
                http_response_code(200);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "닉네임 중복 확인에 성공하였습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
            }
            if ($isalreadyNickname == 1)
            {
                //중복에러
                $res->isSuccess = false;
                $res->code = 105;
                $res->message = "이미 있는 닉네임 입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }

            break;

        case "guest": //토큰 생성 API

            $password= $req->password; //비밀번호 형식 체크
            $name= $req->name;
            $birth= $req->birth; //숫자만
            $nickname= $req->nickname; //한글
            $email= $req->email;
//            $patternPw = "/^[a-z0-9_]{6,10}$/"; // 4자 이상 10자 이하 영소문자/숫자/_ 허용
//            $pattenBirth = "/^[0-9]$/"; // 8자리 숫자만

//        echo "$nickname";

            if(strlen($password) < 1 or strlen($name) < 1 or strlen($birth) < 1 or strlen($nickname) < 1  or strlen($email) < 1)
            {
                $res->isSuccess = false;
                $res->code = 107;
                $res->message = "모든 항목을 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            if(strlen($password) > 0 and strlen($name) > 0 and strlen($birth) > 0 and strlen($nickname) > 0 and strlen($email) > 0)
            {
                $isalreadyEmail  =  emailcheckGuest($email);
                $isalreadyNickname  =  nicknamecheckGuest($nickname);
                if ($isalreadyEmail == 0 and $isalreadyNickname == 0)
                {
                    signUp($email, $name, $birth, $password, $nickname);
                    http_response_code(200);
                    $jwt = getJWToken($email, $password, JWT_SECRET_KEY);
                    $res->result->jwt = $jwt;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "개인정보 저장을 성공하였습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                else
                {
                    $res->isSuccess = false;
                    $res->code = 199;
                    $res->message = "이미 있는 이메일 이거나 닉네임 입니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

            }

            break;

        case "Certified":

            $url= $req->url;
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];

            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            }
            else if ($isintval === 1)
            {
                if(strlen($url) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 109;
                    $res->message = "URL을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                $isinsertImage = insertImage($url);
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "사진 저장을 성공하였습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;


        case "getArea":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];


            if ($isintval === 0) //토큰 검증 여부
            {
                $res->isSuccess = FALSE;
                $res->code = 201;
                $res->message = "유효하지 않은 토큰입니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                addErrorLogs($errorLogs, $res, $req); //에러로그 오류
                return;
            }
            else if ($isintval === 1)
            {
                $res->result = getArea();
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "지역목록 조회를 성공하였습니다";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }
            break;

        case "postArea":

            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];

            $result = isValidHeader($jwt, JWT_SECRET_KEY);
            $isintval = $result['intval'];
            $email = $result['email'];
            $result= $req->result;

            $count = 0;
            foreach($result as $nationalNo => $value)
            {
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
            else if ($isintval === 1)
            {
                if(count($national) < 1)
                {
                    $res->isSuccess = false;
                    $res->code = 112;
                    $res->message = "관심지역을 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

                if(count($national) > 0)
                {
                    $area = $req->area;
                    http_response_code(200);
                    postArea($userNo, $national);
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "관심지역 설정 저장을 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }

            }

            break;

        case "login":

            $email= $req->email;
            $password= $req->password;

            $loginResult = login($email, $password);

            if(strlen($email) > 0 and strlen($password) > 0)
            {
                if($loginResult == 0)
                {
                    $res->isSuccess = false;
                    $res->code = 114;
                    $res->message = "아이디와 비밀번호를 올바르게 입력해주세요";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
                else if($loginResult == 1)
                {
                    http_response_code(200);
                    $jwt = getJWToken($email, $password, JWT_SECRET_KEY);
                    $res->result->jwt = $jwt;
                    $res->isSuccess = TRUE;
                    $res->code = 100;
                    $res->message = "로그인을 성공했습니다";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    return;
                }
            }
            else if (strlen($email) < 1 or strlen($password) < 1)
            {
                $res->isSuccess = false;
                $res->code = 115;
                $res->message = "아이디와 비밀번호를 입력해주세요";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                return;
            }

            break;

        /*
         * API No. 0
         * API Name : 테스트 Path Variable API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testDetail":
            http_response_code(200);
            $res->result = testDetail($vars["testNo"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 0
         * API Name : 테스트 Body & Insert API
         * 마지막 수정 날짜 : 19.04.29
         */
        case "testPost":
            http_response_code(200);
            $res->result = testPost($req->name);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "테스트 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
