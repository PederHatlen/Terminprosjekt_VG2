CREATE DATABASE binærchatdb;

USE binærchatdb;

-- Users, the password is hashed and salted in php, created_at is just there for context/debuging
CREATE TABLE users (
    user_id INT not null PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) not null UNIQUE,
    password VARCHAR(255) not null,
    created_at DATETIME DEFAULT current_timestamp
);

-- Just tokens with the normal created_at and expires_at
CREATE TABLE tokens (
    token_id INT not null PRIMARY KEY AUTO_INCREMENT,
    user_id INT not null,
    token VARCHAR(255) not null UNIQUE,
    created_at DATETIME not Null,
    expires_at DATETIME not Null,
    FOREIGN KEY (user_id) REFERENCES users(user_id) on delete cascade
);

-- Is acctually just id and flags, but created by is good for testing/helping + created_at is usefull
-- Created by is not foreign key, becouse i don't want to delete the conversation when one person has left.
CREATE TABLE conversations (
    conversation_id INT not null PRIMARY KEY AUTO_INCREMENT,
    created_by INT not null,
    isGroupChat TINYINT not null DEFAULT 0,
    created_at DATETIME not null DEFAULT current_timestamp
);

-- Where all the users are stared, and bound to a conversation, is theoretically ready for group chats
CREATE TABLE conversation_users (
    conversation_id INT not null,
    user_id INT not null,
    color char(7) not null,
    isAdmin TINYINT NOT NULL DEFAULT 0,
    FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id) on delete cascade,
    FOREIGN KEY (user_id) REFERENCES users(user_id) on delete cascade,
    PRIMARY KEY (conversation_id, user_id)
);

-- All the messages from all the chats are stored here, thay are retrieved by conversation_id
CREATE TABLE messages (
    message_id INT not null PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT not null,
    sender_id INT not null,
    messagetext VARCHAR(255) not null,
    sent_at DATETIME not Null DEFAULT current_timestamp,
    FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id) on delete cascade,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) on delete cascade
);

-- Basic help-ticket system
CREATE TABLE help_tickets (
    ticket_id INT not null PRIMARY KEY AUTO_INCREMENT,
    messagetext varchar(255) not null,
    email varchar(255) not null,
    user_id int, -- Can be non existent if user fx. has problems logging in
    sent_at DATETIME not Null DEFAULT current_timestamp
);