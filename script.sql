DROP DATABASE IF EXISTS knjiznica;
CREATE DATABASE knjiznica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
use knjiznica;

create table user (
id int not null primary key auto_increment,
firstname varchar(50) not null,
lastname varchar(50) not null,
email varchar(100) not null,
pass char(60) not null,
role varchar(50),
active boolean default 0,
verifyed boolean default 0,
email_activation_key varchar(50) not null
)engine=InnoDB;

create unique index unique_email on user(email);

create table book (
id int not null primary key auto_increment,
author varchar(100) not null,
name varchar(100) not null,
amount int not null,
image varchar(250),
active boolean default true,
zanr int not null
)engine=InnoDB;

create table borrow (
id int not null primary key auto_increment,
user int not null,
book int not null,
returned boolean default 0,
borrowedAt datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
returnedAt datetime
)engine=InnoDB;

create table membership (
id int not null primary key auto_increment,
user int not null,
payedAt datetime not null default CURRENT_TIMESTAMP,
expires datetime not null,
status boolean default 1
)engine=InnoDB;

create table zanr (
    id int not null primary key auto_increment,
    name varchar(100) not null
)engine=InnoDB;

alter table book add foreign key (zanr) references zanr(id);

alter table borrow add foreign key (user) references user(id);
alter table borrow add foreign key (book) references book(id);

alter table membership add foreign key (user) references user(id);