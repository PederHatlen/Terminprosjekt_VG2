CREATE DATABASE binærchatdb;

USE binærchatdb;

-- Users, the password is hashed and salted in php, created_at is just there for context/debuging
CREATE TABLE users (
	user_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	username TEXT NOT NULL UNIQUE,
	password TEXT NOT NULL,
	created_at DATETIME DEFAULT NOW()
);

-- Just tokens with the normal created_at and expires_at
CREATE TABLE tokens (
	token_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	user_id INT NOT NULL,
	token VARCHAR(255) NOT NULL UNIQUE,
	created_at DATETIME NOT NULL DEFAULT NOW(),
	expires_at DATETIME NOT NULL,
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Is acctually just id and flags, but created by is good for testing/helping + created_at is usefull
-- Created by is not foreign key, becouse i don't want to delete the conversation when one person has left.
CREATE TABLE conversations (
	conversation_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	created_by INT NOT NULL,
	isGroupChat TINYINT NOT NULL DEFAULT 0,
	created_at DATETIME NOT NULL DEFAULT NOW()
);

-- Where all the users are stared, and bound to a conversation, is theoretically ready for group chats
CREATE TABLE conv_users (
	conversation_id INT NOT NULL,
	user_id INT NOT NULL,
	color CHAR(7) NOT NULL,
	isAdmin TINYINT NOT NULL DEFAULT 0,
	FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id) ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	PRIMARY KEY (conversation_id, user_id)
);

-- Websocket tokens, used instead of having user data in client
CREATE TABLE ws_tokens (
	ws_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	conversation_id INT NOT NULL,
	user_id INT NOT NULL,
	token VARCHAR(255) NOT NULL,
	active TINYINT NOT NULL DEFAULT 1,
	expires_at DATETIME NOT NULL,
	FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id) ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	UNIQUE(token, active)
);

-- All the messages from all the chats are stored here, thay are retrieved by conversation_id
CREATE TABLE messages (
	message_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	conversation_id INT NOT NULL,
	sender_id INT NOT NULL,
	messagetext TEXT NOT NULL,
	sent_at DATETIME NOT NULL DEFAULT NOW(),
	FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id) ON DELETE CASCADE,
	FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Basic help-ticket system
CREATE TABLE help_tickets (
	ticket_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	messagetext TEXT NOT NULL,
	email VARCHAR(255) NOT NULL,
	user_id INT, -- Can be non existent if user fx. has problems logging in
	sent_at DATETIME NOT NULL DEFAULT NOW()
);