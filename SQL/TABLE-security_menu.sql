
-- drop table dbname.tb_security_menu;

CREATE TABLE dbname.tb_security_menu (
id_security_menu int UNSIGNED NOT NULL AUTO_INCREMENT,
fa_icon varchar(250),
name varchar(250),
location varchar(250),
parameter varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_security_menu)
);


-- alter table SQL section
 -- alter table dbname.tb_security_menu add fa_icon varchar(250);
 -- alter table dbname.tb_security_menu add name varchar(250);
 -- alter table dbname.tb_security_menu add location varchar(250);
 -- alter table dbname.tb_security_menu add parameter varchar(250);
-- alter table SQL section

