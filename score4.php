<?php
 
ini_set('display_errors', 'on');

require_once "lib/dbconnect.php";
require_once "lib/board.php";
require_once "lib/game.php";
// require_once "lib/users.php";


$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

switch ($r=array_shift($request)) {
case 'board' : 
        switch ($b=array_shift($request)) {
    case '':
    case null: handle_board($method);
        break;
    case 'piece': handle_piece($method, $request[0], $request[1], $input);
        break;
    default: header("HTTP/1.1 404 Not Found");
        break;
        }
    break;
case 'status': 
    if(sizeof($request)==0) {show_status();
    }
    else {header("HTTP/1.1 404 Not Found");
    }
    break;
case 'players': handle_player($method, $request, $input);
    break;
default:  header("HTTP/1.1 404 Not Found");
    exit;
}

function handle_board($method)
{
 
    if($method=='GET') {
            show_board();
    } else if ($method=='POST') {
            reset_board();
        show_board();
    }
        
}

function handle_piece($method, $x,$y,$input)
{
    ;
}
 
function handle_player($method, $request,$input)
{
    ;
}
 
?>
