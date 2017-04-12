DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS game;
CREATE TABLE game (

	gameId INT NOT NULL AUTO_INCREMENT,	 
	lastConnected DATETIME NOT NULL,
	firstConnected DATETIME NOT NULL,
	secret VARCHAR(255) NOT NULL, 
	code VARCHAR(4) NOT NULL UNIQUE, 
	answerType INT NULL, 
	PRIMARY KEY (gameId)

);
CREATE TABLE user (

	gameId INT NOT NULL,
	username VARCHAR(15) NOT NULL,
	score INT NOT NULL,
	answer VARCHAR(50) NULL,
	sessionId VARCHAR(255) NOT NULL,
	timeJoined DATETIME NOT NULL, 
	PRIMARY KEY (gameId, username),
	FOREIGN KEY (gameId) REFERENCES game(gameId) ON DELETE CASCADE
);