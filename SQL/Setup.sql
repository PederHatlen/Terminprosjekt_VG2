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
    user_id VARCHAR(255) not null UNIQUE,
    token  VARCHAR(255) not null  UNIQUE,
    created_at DATETIME not Null,
    expires_at DATETIME not Null 
);