create database myweb
  
use database myweb
  
create  table folders(
  id int auto increment,
  name varchar(255) not null,
  date datetime)

create table upload(
  id int auto increment,
  name varchar(255) not null,
  date datetime not null,
  folder_id int foreign key references folders(id))
