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
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Is acctually just id, but needed a way to retrieve it after createon + created_at is usefull
CREATE TABLE conversations (
    conversation_id INT not null PRIMARY KEY AUTO_INCREMENT,
    created_by INT not null,
    created_at DATETIME not null DEFAULT current_timestamp
);

-- Where all the users are stared, and bound to a conversation, is theoretically ready for group chats
CREATE TABLE conversation_users (
    conversation_id INT not null,
    user_id INT not null,
    FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    PRIMARY KEY (conversation_id, user_id)
);

-- All the messages from all the chats are stored here, thay are retrieved by conversation_id
CREATE TABLE messages (
    message_id INT not null PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT not null,
    sender_id INT not null,
    messagetext VARCHAR(255) not null,
    sent_at DATETIME not Null DEFAULT current_timestamp,
    FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id),
    FOREIGN KEY (sender_id) REFERENCES users(user_id)
);

CREATE TABLE brukerstøtte (
    ticket_id INT not null PRIMARY KEY AUTO_INCREMENT,
    messagetext varchar(255) not null,
    email varchar(255) not null,
    user_id int(255),
    sent_at DATETIME not Null DEFAULT current_timestamp,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
);