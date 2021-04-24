# sql-island

## Preparation
* ```chmod 777 DBs```
* ```chmod 777 Logs```

## Preparation of the Certificate Database
* ```mkdir ../db```
* ```chmod 777 ../db```
* ```sqlite3 ../db/certs.sqlite3```
* ```create table certs(cert_id char(10) primary key, game_id varchar(80), name varchar(50), game_time timestamp default current_timestamp);```
