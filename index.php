<?php

require './pdos/DatabasePdo.php';
require './pdos/IndexPdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

//echo "test_server";

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
    $r->addRoute('GET', '/', ['IndexController', 'index']); //index
    $r->addRoute('POST', '/email', ['IndexController', 'email']); //email- 회원가입 API
    $r->addRoute('POST', '/nickname', ['IndexController', 'nickname']); //nickname - 회원가입 API
    $r->addRoute('POST', '/guest', ['IndexController', 'guest']); //회원가입 API - 토큰 생성 API
    $r->addRoute('POST', '/url', ['IndexController', 'Certified']); //학교인증 API
    $r->addRoute('POST', '/area', ['IndexController', 'postArea']); //관심지역 설정 API
    $r->addRoute('POST', '/user', ['IndexController', 'login']); //로그인 API
    $r->addRoute('GET', '/area', ['IndexController', 'getArea']); //관심지역 출력 API
    $r->addRoute('GET', '/user', ['MyController', 'user']); //유저정보 출력 API
    $r->addRoute('PATCH', '/detailUser', ['MyController', 'patchUser']); //유저상세정보 수정 API
    $r->addRoute('GET', '/detailUser', ['MyController', 'detailUser']); //유저상세정보 조회 API
    $r->addRoute('GET', '/likeArea', ['MyController', 'myArea']); // 관심지역 조회 API 게시물 작성시 필요
    $r->addRoute('POST', '/post', ['MainController', 'postPost']); //게시글 작성 API
    $r->addRoute('GET', '/post/{postNo}', ['MainController', 'getPost']); //게시글 상세보기 조회  API 개발 진행 필요
    $r->addRoute('POST', '/like/{postNo}', ['MainController', 'postLike']); //좋아요 누르기 API
    $r->addRoute('DELETE', '/like/{postNo}', ['MainController', 'deleteLike']); //좋아요 취소 API
    $r->addRoute('POST', '/scrap', ['MainController', 'postScrap']); //스크랩북 추가 (생성) API
    $r->addRoute('GET', '/scrap', ['MainController', 'getScrap']); //스크랩북 목록 조회 API
    $r->addRoute('POST', '/scrap/{scrapNo}/post/{postNo}', ['MainController', 'doScrap_post']); //게시글 스크랩 하기 API
    $r->addRoute('DELETE', '/scrap/{scrapNo}/post/{postNo}', ['MainController', 'deleteScrap_post']); //게시글 스크랩 해제 API

    $r->addRoute('PATCH', '/scrap/{scrapNo}', ['MainController', 'patchScrap']); //스크랩북 수정 API

    $r->addRoute('GET', '/test/{testNo}', ['IndexController', 'testDetail']);
//    $r->addRoute('POST', '/test', ['IndexController', 'testPost']);

    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);


//    $r->addRoute('GET', '/users', 'get_all_users_handler');
//    // {id} must be a number (\d+)
//    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//    // The /{title} suffix is optional
//    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            case 'MyController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/MyController.php';
                break;
            /*case 'ProductController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ProductController.php';
                break;
            case 'SearchController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/SearchController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;
            case 'ElementController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/ElementController.php';
                break;
            case 'AskFAQController':
                $handler = $routeInfo[1][1]; $vars = $routeInfo[2];
                require './controllers/AskFAQController.php';
                break;*/
        }

        break;
}
