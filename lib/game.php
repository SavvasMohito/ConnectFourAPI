<?php
function show_status()
{
    
    global $mysqli;
    
    check_abort();
    
    $sql = 'select * from game_status';
    $st = $mysqli->prepare($sql);

    $st->execute();
    $res = $st->get_result();

    header('Content-type: application/json');
    print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);

}

function check_abort()
{
    global $mysqli;
    $sql = 'call check_abort()';
    $mysqli->query($sql);
}

function read_status()
{
    global $mysqli;

    $sql = 'select * from game_status';
    $st = $mysqli->prepare($sql);

    $st->execute();
    $res = $st->get_result();
    $status = $res->fetch_assoc();
    return($status);
}
function update_game_status()
{
    global $mysqli;

    $status = read_status();
    $new_status=null;
    $new_turn=null;
    $result=null;
    
    //check abort
    check_abort();

    //Remove Inactive Players
    $st3=$mysqli->prepare('select count(*) as aborted from players WHERE last_action< (NOW() - INTERVAL 15 MINUTE)');
    $st3->execute();
    $res3 = $st3->get_result();
    $aborted = $res3->fetch_assoc()['aborted'];
    if($aborted>0) {
        $sql = "UPDATE players SET player=NULL, token=NULL, last_action=NULL WHERE last_action< (NOW() - INTERVAL 15 MINUTE)";
        $st2 = $mysqli->prepare($sql);
        $st2->execute();
        if($status['status']=='started') {
            $new_status='aborted';
            switch($status['p_turn']){
            case 'O': $result = 'X';
                break;
            case 'X': $result = 'O';
                break;
            }
        }
    }

    
    $sql = 'select count(*) as c from players where player is not null';
    $st = $mysqli->prepare($sql);
    $st->execute();
    $res = $st->get_result();
    $active_players = $res->fetch_assoc()['c'];
    
    switch($active_players) {
    case 0: $new_status="inactive";
        reset_board();
        break;
    case 1: $new_status='initialized';
        reset_board();
        $sql = 'select * from players where player is not null';
        $st = $mysqli->prepare($sql);
        $st->execute();
        $res = $st->get_result();
        $new_turn = $res->fetch_assoc()['symbol'];
        break;
    case 2: $new_status='started'; 
        if($status['p_turn']==null) {
            $new_turn='O'; // It was not started before...
        }else{
            $new_turn = $status['p_turn'];
        }
        $result = $status['result'];
        break;
    }

    $sql = 'update game_status set status=?, p_turn=?, result=?';
    $st = $mysqli->prepare($sql);
    $st->bind_param('sss', $new_status, $new_turn, $result);
    $st->execute();
}

function end_game($result)
{
    global $mysqli;

    $sql = 'update game_status set status="ended", result=?;';
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $result);
    $st->execute();

    $sql = 'update players set player=NULL, token=NULL, last_action=NULL';
    $st = $mysqli->prepare($sql);
    $st->execute();
}

?>
