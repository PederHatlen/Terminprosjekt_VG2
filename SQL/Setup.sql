CREATE DATABASE binærchatdb;

USE binærchatdb;

CREATE TABLE users (
    user_id INT not null PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) not null UNIQUE,
    password VARCHAR(255) not null,
    created_at DATETIME DEFAULT current_timestamp
);

CREATE TABLE tokens (
    token_id INT not null PRIMARY KEY AUTO_INCREMENT,
    user_id INT not null,
    token VARCHAR(255) not null UNIQUE,
    created_at DATETIME not Null,
    expires_at DATETIME not Null,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE conversations (
    conversation_id INT not null PRIMARY KEY AUTO_INCREMENT,
    created_at DATETIME not null DEFAULT current_timestamp
);

CREATE TABLE conversation_users (
    conversation_id INT not null,
    user_id INT not null,
    FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    PRIMARY KEY (conversation_id, user_id)
);

CREATE TABLE messages (
    message_id INT not null PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT not null,
    sender_id INT not null,
    messagetext VARCHAR(255) not null,
    sent_at DATETIME not Null DEFAULT current_timestamp,
    FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id),
    FOREIGN KEY (sender_id) REFERENCES users(user_id)
);