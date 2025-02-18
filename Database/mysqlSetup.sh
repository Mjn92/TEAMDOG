#!/bin/bash


read -p "enter your mysql username: " USER
read -sp "enter your mysql password: " PASSWORD
echo

DB_NAME="movDB"
TABLE_NAME="users"

CREATE_DB="CREATE DATABASE $DB_NAME;"
CREATE_TABLE="create table $TABLE_NAME ( id int auto_increment primary key, username varchar(50) not null unique, email varchar(100) not null unique, created timestamp default current_timestamp, updated timestamp default current_timestamp on update current_timestamp );"

mysql -u "$USER" -p"$PASSWORD" -e"$CREATE_DB"
mysql -u "$USER" -p"$PASSWORD" -D"$DB_NAME" -e"$CREATE_TABLE"

