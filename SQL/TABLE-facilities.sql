
-- drop table dbname.tb_facilities;

CREATE TABLE dbname.tb_facilities (
id_facilities int UNSIGNED NOT NULL AUTO_INCREMENT,
name varchar(250),
phone varchar(250),
email varchar(250),
contact_person varchar(250),
address varchar(250),
city varchar(250),
state varchar(250),
zip_code varchar(250),
notes varchar(250),
logo varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_facilities)
);


-- alter table SQL section
 -- alter table dbname.tb_facilities add name varchar(250);
 -- alter table dbname.tb_facilities add phone varchar(250);
 -- alter table dbname.tb_facilities add email varchar(250);
 -- alter table dbname.tb_facilities add contact_person varchar(250);
 -- alter table dbname.tb_facilities add address varchar(250);
 -- alter table dbname.tb_facilities add city varchar(250);
 -- alter table dbname.tb_facilities add state varchar(250);
 -- alter table dbname.tb_facilities add zip_code varchar(250);
 -- alter table dbname.tb_facilities add notes varchar(250);
 -- alter table dbname.tb_facilities add logo varchar(250);
-- alter table SQL section

