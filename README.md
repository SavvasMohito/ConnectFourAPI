# Project Description
## Game Rules
There is a board with 6 rows and 7 columns. 2 users are playing one after the other. In each turn, the user selects one column to insert his piece in. Each column represent a stack, so it gets filled from the bottom to the top. The first player who manages to align 4 of his pieces in any way possible (vertically, horizontally or diagonally) wins the game!
![](https://upload.wikimedia.org/wikipedia/commons/a/ad/Connect_Four.gif)
## Link of the Game
```
https://users.iee.ihu.gr/~it174883/adise20/ADISE20_174883/score4.php/
```
## How to Play
1. Copy the game link above.
2. Open the cmd/terminal in your OS.
3. Use the curl command as follows:
```
curl https://users.iee.ihu.gr/~it174883/adise20/ADISE20_174883/score4.php/
```
## Syntax
In order to do a POST or PUT request, use the following syntax:
1. Set new Player (:p = Player Symbol. Can be 'O' or 'X')
```
curl -X PUT -H "Content-Type: application/json" -d "{ \"player\": \"player_name\" }" https://users.iee.ihu.gr/~it174883/adise20/ADISE20_174883/score4.php/players/:p
```
2. Fill a column using your token (:x = Selected Column. Can be 1..7)
```
curl -X PUT -H "Content-Type: application/json" -d "{ \"token\": \"token_number\" }" https://users.iee.ihu.gr/~it174883/adise20/ADISE20_174883/score4.php/board/column/:x
```

# API Showcase
## Methods
### Board
**Read Board**
```
GET /board/
```
Returns the Board.

**Reset Board**
```
POST /board/
```
Resets the board and prepares it for the next game. Returns the reseted Board.

### Column
**Read Column**
```
GET /board/column/:x
```
Returns the selected (:x) column. Out of range input will not be accepted.

**Fill Column**
```
PUT /board/column/:x
```
Json Data:
Field | Description | Required
----- | ----------- | --------
token | Player's secret token | yes

Places a piece in the selected (:x) column. If the selected column is full, user is asked to try again. Out of range input will not be accepted.

### Players
**Read all Players**
```
GET /players/
```
Returns all players' information (username and symbol).

**Read Player's info**
```
GET /players/:p
```
Returns selected (:p) player's information. :p can be either 'O' or 'X'.

**Set new Player**
```
PUT /players/:p
```
Json Data:
Field | Description | Required
----- | ----------- | --------
player | Player's Name | yes

Sets the Player on the selected symbol (if available) and returns a token. This token must be saved and used in every round the player plays as an authentication method.

### Status
**Read Game Status**
```
GET /status/
```
Returns the Game Status.

## Entities
### Board
Board is a table which contains the following:
Attribute | Description | Values
--------- | ----------- | ------
x | 'x' coordinate of the slot | 1..6
y | 'y' coordinate of the slot | 1..7
symbol | Symbol placed in the slot | 'O', 'X', null

### Players
Each Player has the following:
Attribute | Description | Values
--------- | ----------- | ------
player | Player's name | String
symbol | Player's selected Symbol| 'O', 'X'
token | Secret token acquired when the player gets set | HEX
last_action | Timestamp with the player's last action | timestamp

### Game Status
The Game's Status is described by the following:
Attribute | Description | Values
--------- | ----------- | ------
status | Current status of the game | 'inactive', 'initialized', 'started', 'ended', 'aborted'
p_turn | Symbol of the player's turn | 'O', 'X', null
result | Symbol of the winner | 'O', 'X', null
last_change | Timestamp with the game's latest change | timestamp
