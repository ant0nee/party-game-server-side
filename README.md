# party-game
## Software
* The game should allow unlimited players, played using a website

## Design

### Overview
* Points you gain in minigames add to your total 
* games should award roughly a maximum of 10 points

## API Usage

Apis can be found in the api folder. Apis are also hosted at http://inviolate-bureau.000webhostapp.com/

Rename dummy_DAO.php to DAO.php to test the apis on your local system

### mobile client
#### newUser.php
```
method: POST

inputs: 
  username - username for the user (string)
  code - gameCode for the game (string)
outputs: 
  success - true if operation successful (boolean)
  sessionId - the sessionID for the user (String)
result: 
  User created and session for that user started

```
#### canSubmit.php
```
method: GET

inputs: 
  username - username for the user (string)
  gameId - the ID number for the game (int)
outputs: 
  success - true if operation successful (boolean)
  canSubmit - true if the user is able to submit an answer (if the answer is null)
```
#### setAnswer.php
```
method: GET

inputs: 
  username - username for the user (string)
  gameId - the ID number for the game (int)
  sessionId - the sessionID for the user (String)
outputs: 
  success - true if operation successful (boolean)
result: 
  answer set for the user
```
### web client
#### newGame.php
```
method: GET

outputs: 
  success - true if operation successful (boolean)
  iterations - how many times the code had to loop to create the unique gameCode (int)
  gameId - the ID number for the game (int)
  code - gameCode for the game (string)
  secret - a secret identifier for the game (String)
result:
  new game added to database
```
#### addPoints.php
```
method: GET

inputs: 
  method
  gameId - the ID number for the game (int)
  secret - a secret identifier for the game (String)
  username - username for the user (string)
  points - number of points to give the user (int)
outputs: 
  success - true if operation successful (boolean)
result: 
  points given to user
```
#### stopAnswers.php
```
method: GET

inputs: 
  gameId - the ID number for the game (int)
  secret - a secret identifier for the game (String)
outputs: 
  success - true if operation successful (boolean)
result:
  answers set to blank string
```
#### clearAnswers.php
```
method: GET

inputs: 
  gameId - the ID number for the game (int)
  secret - a secret identifier for the game (String)
outputs: 
  success - true if operation successful (boolean)
result: 
  answers set to null
```
#### getUsers.php
```
method: GET

inputs: 
  gameId - the ID number for the game (int)
  secret - a secret identifier for the game (String)
outputs: 
  An array of users containing their username, answer and score
```
#### updateGameTime.php
```
method: GET

inputs: 
  gameId - the ID number for the game (int)
  secret - a secret identifier for the game (String)
outputs: 
  success - true if operation successful (boolean)
result: 
  game time updated so it isn't automaticly deleted
```
