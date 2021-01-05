<?php

ini_set('display_errors', 'on');

require_once "lib/dbconnect.php";
require_once "lib/board.php";
require_once "lib/game.php";
require_once "lib/users.php";


$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);

switch ($r=array_shift($request)) {
case 'board' : 
        switch ($b=array_shift($request)) {
    case '':
    case null: handle_board($method);
        break;
    case 'column': handle_column($method, $request[0], $input);
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
    update_game_status();
    if($method=='GET') {
        //show_board();
        print_board();    
    } else if ($method=='POST') {
        reset_board();
        print_board();
    }
        
}

function handle_column($method, $col, $input)
{
    update_game_status();
    if($method=='GET') {
        show_column($col);
    } else if ($method=='PUT') {
        if ($col >= 1 and $col <= 7) {
            place_piece($col, $input['token']);
        }else{
            header("HTTP/1.1 400 Bad Request");
            print json_encode(['errormesg'=>"Invalid Input. Please select between 1-7."]);
        }
    }    
}
 
function handle_player($method, $request, $input)
{
    update_game_status();
    switch ($b=array_shift($request)) {
    case '':
    case null: if($method=='GET') {show_users($method);
    }
    else {header("HTTP/1.1 400 Bad Request"); 
        print json_encode(['errormesg'=>"Method $method not allowed here."]);
    }
        break;
    case 'O': 
    case 'X': 
        //echo $input;
        handle_user($method, $b, $input);
        break;
    default: header("HTTP/1.1 404 Not Found");
        print json_encode(['errormesg'=>"Player $b not found."]);
        break;
    }
}
