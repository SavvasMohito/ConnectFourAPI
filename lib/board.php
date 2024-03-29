<?php
function show_board()
{
    $board_json = read_board("json");
    
    header('Content-type: application/json');
    $js_enc = json_encode($board_json, JSON_PRETTY_PRINT);
    print $js_enc;
    
}

function read_board($type)
{
    global $mysqli;

    $sql = 'select * from board';
    $st = $mysqli->prepare($sql);

    $st->execute();
    $res = $st->get_result();

    if ($type == "json") {
        return ($res->fetch_all(MYSQLI_ASSOC));
    }else if ($type == "array") {
        $board = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array()
        );        
        // Import SQL result in multidimentional array
        for ($i = 0; $i<6; $i++){
            for ($j = 0; $j<7; $j++){
                
                $row = mysqli_fetch_array($res);
    
                $symbol = $row['symbol'];
                if ($symbol == null) {
                    $board[$i][$j] = "-";
                }else{
                    $board[$i][$j] = $symbol;
                }
            }
        }

        return $board;
    }
    
}

function reset_board()
{
    global $mysqli;
    $sql = 'call clear_board()';
    $mysqli->query($sql);
}

function print_board()
{
    update_game_status();
    global $mysqli;

    $board = read_board("array");

    // Print column numbers
    echo"\n*SCORE 4 GAME*\n";
    for ($j = 0; $j<7; $j++){
        echo $j + 1 . " ";
    }
    echo "\n";

    // Print the SQL board as a table
    for ($i = 0; $i<6; $i++){
        for ($j = 0; $j<7; $j++){
            echo $board[$i][$j] . " ";
            if ($j == 6) {
                echo "\n";
            }
        }
    }
    $status = read_status();
    echo "\nGame Status: " . $status['status'];
    if ($status['status'] != "started") {
        echo ". Waiting for players to join.";
    }else{
        echo ". Waiting for player " . $status['p_turn'] . " to play.";
    }
}

function show_column($col)
{
    global $mysqli;

    $sql = 'select * from board where y=?';
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $col);
    $st->execute();
    $res = $st->get_result();
    $column = $res->fetch_all(MYSQLI_ASSOC);

    header('Content-type: application/json');
    $js_enc = json_encode($column, JSON_PRETTY_PRINT);
    print $js_enc;
}

function place_piece($col, $token)
{
    global $mysqli;

    if($token==null || $token=='') {
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"token is not set."]);
        exit;
    }
    
    $symbol = current_symbol($token);
    if($symbol==null) {
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"You are not a player of this game."]);
        exit;
    }
    $status = read_status();
    if($status['status']!='started') {
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"Game is not in action."]);
        exit;
    }
    if($status['p_turn']!=$symbol) {
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"It is not your turn."]);
        exit;
    }
    
    $board = read_board("array");

    //Check for filled column
    if ($board[0][$col - 1] == "-") {
        for ($i=5; $i >= 0; $i--){
            if ($board [$i][$col - 1] == "-") {
                insert_piece($i + 1, $col, $symbol);
                break;
            }
        }
        
        //Update Player's Last Change
        $sql = 'update players set last_action = NOW() where token=?';
        $st = $mysqli->prepare($sql);
        $st->bind_param('s', $token);
        $st->execute();

        print_board();

        //Check if this is the winning move
        if(winning_move($symbol)) {
            $sql = 'select player from players where token=?';
            $st = $mysqli->prepare($sql);
            $st->bind_param('s', $token);
            $st->execute();
            $res = $st->get_result();
            $row=$res->fetch_assoc();
            echo "\nGame Ended! Result: Player " . $row['player'] . " wins!";
            end_game($symbol);
            exit;
        }

        if(check_draw()) {
            echo "\nGame Ended! Result: DRAW! No winners. :(";
            end_game("D");
            exit;
        }

    }else{
        header("HTTP/1.1 400 Bad Request");
        print json_encode(['errormesg'=>"This column (" .$col . ") is full! Try another one."]);
    }
}


function insert_piece($x, $y, $symbol)
{
    global $mysqli;

    $sql = 'update `adise20`.`board` SET `symbol` = ? where (`x` = ?) and (`y` = ?);';
    
    $st = $mysqli->prepare($sql);
    $st->bind_param('sss', $symbol, $x, $y);
    $st->execute();

    //Update Game Status Board
    if ($symbol == "O") {
        $newsymbol = "X";
    }else if ($symbol == "X") {
        $newsymbol = "O";
    }
    $sql = 'update game_status set p_turn=?;';
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $newsymbol);
    $st->execute();
}

function winning_move($piece)
{
    $board = read_board("array");

    //Check for horizontal win
    for ($j = 0; $j < 4; $j++){
        for ($i = 0; $i < 6; $i++){
            if ($board[$i][$j] == $piece and $board[$i][$j + 1] == $piece and $board[$i][$j + 2] == $piece and $board[$i][$j + 3] == $piece) {
                return true;
            }
        }
    }

    //Check for vertical win
    for ($j = 0; $j < 7; $j++){
        for ($i = 0; $i < 3; $i++){
            if ($board[$i][$j] == $piece and $board[$i + 1][$j] == $piece and $board[$i + 2][$j] == $piece and $board[$i + 3][$j] == $piece) {
                return true;
            }
        }
    }
    //Check for positive diagonal win
    for ($j = 0; $j < 4; $j++){
        for ($i = 3; $i < 6; $i++){
            if ($board[$i][$j] == $piece and $board[$i - 1][$j + 1] == $piece and $board[$i - 2][$j + 2] == $piece and $board[$i - 3][$j + 3] == $piece) {
                return true;
            }
        }
    }
    //Check for negative diagonal win
    for ($j = 0; $j < 4; $j++){
        for ($i = 0; $i < 3; $i++){
            if ($board[$i][$j] == $piece and $board[$i + 1][$j + 1] == $piece and $board[$i + 2][$j + 2] == $piece and $board[$i + 3][$j + 3] == $piece) {
                return true;
            }
        }
    }
}

function check_draw()
{
    $board = read_board("array");
    $counter = 0;
    for ($j = 0; $j < 7; $j++){
        if ($board[0][$j] != "-") {
            $counter += 1;
        }
    }
    if ($counter == 7) {
        return true;
    }else{
        return false;
    }
}
?>
