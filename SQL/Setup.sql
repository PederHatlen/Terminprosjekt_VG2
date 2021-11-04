CREATE DATABASE binærchatdb;

USE binærchatdb;

CREATE TABLE users (
    id INT not null PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) not null UNIQUE,
    password VARCHAR(255) not null,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tokens (
    token_id INT not null PRIMARY KEY AUTO_INCREMENT,
    user_id INT not null UNIQUE,
    token  VARCHAR(255) not null UNIQUE,
    created_at DATETIME not Null,
    expires_at DATETIME not Null,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE conversations (
    conversation_id INT not null PRIMARY KEY AUTO_INCREMENT,
    user1_id INT not null,
    user2_id  INT not null,
    created_at DATETIME not Null DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user1_id) REFERENCES users(id),
    FOREIGN KEY (user2_id) REFERENCES users(id)
);

CREATE TABLE messages (
    message_id INT not null PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT not null,
    sender_id INT not null,
    messagetext VARCHAR(255) not null,
    sent_at DATETIME not Null DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(conversation_id),
    FOREIGN KEY (sender_id) REFERENCES users(id)
);