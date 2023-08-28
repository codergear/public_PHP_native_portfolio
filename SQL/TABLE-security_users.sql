
-- drop table dbname.tb_security_users;

CREATE TABLE dbname.tb_security_users (
id_security_users int UNSIGNED NOT NULL AUTO_INCREMENT,
users_status varchar(250),
user varchar(250),
pass varchar(250),
full_name varchar(250),
origin varchar(250),
phone varchar(250),
email varchar(250),
role varchar(250),
photo varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_security_users)
);


-- alter table SQL section
 -- alter table dbname.tb_security_users add users_status varchar(250);
 -- alter table dbname.tb_security_users add user varchar(250);
 -- alter table dbname.tb_security_users add pass varchar(250);
 -- alter table dbname.tb_security_users add full_name varchar(250);
 -- alter table dbname.tb_security_users add origin varchar(250);
 -- alter table dbname.tb_security_users add phone varchar(250);
 -- alter table dbname.tb_security_users add email varchar(250);
 -- alter table dbname.tb_security_users add role varchar(250);
 -- alter table dbname.tb_security_users add photo varchar(250);
-- alter table SQL section

