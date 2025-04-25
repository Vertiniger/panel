CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apikey VARCHAR(255) NOT NULL,
    maxtime INT DEFAULT 60,
    concurrents INT DEFAULT 1
);

CREATE TABLE apis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apiurl TEXT NOT NULL
);

INSERT INTO users (apikey, maxtime, concurrents)
VALUES ('kavernxyz12', 1200, 15);

INSERT INTO apis (apiurl)
VALUES ('http://vernitiger.net/api.php?key=keygwa&host={host}&port={port}&time={time}&method={method}');
