
-- drop table dbname.tb_security_roles;

CREATE TABLE dbname.tb_security_roles (
id_security_roles int UNSIGNED NOT NULL AUTO_INCREMENT,
name varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_security_roles)
);


-- alter table SQL section
 -- alter table dbname.tb_security_roles add name varchar(250);
-- alter table SQL section

