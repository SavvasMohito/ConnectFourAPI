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
