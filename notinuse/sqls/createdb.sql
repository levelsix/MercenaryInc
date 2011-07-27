USE c_cs108_cchan91;

DROP TABLE IF EXISTS users;
 -- remove table if it already exists and start from scratch
 -- footlength is in inches for the person. not inches of the shoe

CREATE TABLE users (
    ID int NOT NULL AUTO_INCREMENT,
    username VARCHAR(64),
    password VARCHAR(64),
    PRIMARY KEY (ID)
);

ALTER TABLE users AUTO_INCREMENT = 1;

INSERT INTO users (username, password) VALUES
       ("Conrad", "pw"), 
       ("King King", "pw");



