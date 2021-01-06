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
**Read Game Status***
```
GET /status/
```
Returns the Game Status.
