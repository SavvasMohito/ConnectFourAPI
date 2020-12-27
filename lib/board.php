<?php
function show_board()
{
    global $mysqli;

    $sql = 'select * from board';
    $st = $mysqli->prepare($sql);

    $st->execute();
    $res = $st->get_result();
    
    header('Content-type: application/json');
    $js_enc = json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
    print $js_enc;
    
}

function reset_board()
{
    global $mysqli;
    $sql = 'call clear_board()';
    $mysqli->query($sql);
    show_board();
}

function print_board()
{
    global $mysqli;
    global $board;

    $sql = 'select * from board';
    $st = $mysqli->prepare($sql);

    $st->execute();
    $res = $st->get_result();

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

    // Print column numbers
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
    
}

function show_column($col)
{
    ;
}

function place_piece($col)
{
    global $board;
    print_board();
    echo $board[0][0];
    //Check for filled column
    if ($board[0][$col] != "-") {
        for ($i=5; $i >= 0; $i--){
            if ($board [$i][$col] == "-") {
                //isnert_piece("INSERT 'O' TO")
                echo "UPDATE `adise20`.`board` SET `symbol` = '" . $symbol . "' WHERE (`x` = '" . $x . "') and (`y` = '" . $y . "');";
                break;
            };
        }
    }else{
        echo "This column is full! Try another one.";
    }


    $board[5][$col] = 'X';
    print_board();
}

function insert_piece($x, $y, $symbol)
{
    global $mysqli;
    
    //$sql = "UPDATE `adise20`.`board` SET `symbol` = '" . $symbol . "' WHERE (`x` = '" . $x . "') and (`y` = '" . $y . "');";
    $st = $mysqli->prepare($sql);

    $st->execute();
}
?>
