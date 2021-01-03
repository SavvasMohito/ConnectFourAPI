<?php
function show_users()
{
    global $mysqli;
    $sql = 'select player,symbol from players';
    $st = $mysqli->prepare($sql);
    $st->execute();
    $res = $st->get_result();
    header('Content-type: application/json');
    print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}
function show_user($b)
{
    global $mysqli;
    $sql = 'select player,symbol from players where symbol=?';
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $b);
    $st->execute();
    $res = $st->get_result();
    header('Content-type: application/json');
    print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function set_user($b,$input)
{
    if(!isset($input['player'])) {
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"No player given."]);
        exit;
    }
    $player=$input['player'];
    global $mysqli;
    $sql = 'select count(*) as c from players where symbol=? and player is not null';
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $b);
    $st->execute();
    $res = $st->get_result();
    $r = $res->fetch_all(MYSQLI_ASSOC);
    if($r[0]['c']>0) {
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"Player $b is already set. Please select another symbol."]);
        exit;
    }
    $sql = 'update players set player=?, token=md5(CONCAT( ?, NOW())) where symbol=?';
    $st2 = $mysqli->prepare($sql);
    $st2->bind_param('sss', $player, $player, $b);
    $st2->execute();
    
    update_game_status();
    $sql = 'select * from players where symbol=?';
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $b);
    $st->execute();
    $res = $st->get_result();
    header('Content-type: application/json');
    print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
    
    
}

function handle_user($method, $b, $input)
{
    if($method=='GET') {
        show_user($b);
    } else if($method=='PUT') {
        set_user($b, $input);
    }
}

function current_symbol($token)
{
    
    global $mysqli;
    if($token==null) {return(null);
    }
    $sql = 'select * from players where token=?';
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $token);
    $st->execute();
    $res = $st->get_result();
    if($row=$res->fetch_assoc()) {
        return($row['symbol']);
    }
    return(null);
}
?>
