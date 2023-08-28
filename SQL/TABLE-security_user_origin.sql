
-- drop table dbname.tb_security_user_origin;

CREATE TABLE dbname.tb_security_user_origin (
id_security_user_origin int UNSIGNED NOT NULL AUTO_INCREMENT,
origin varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_security_user_origin)
);


-- alter table SQL section
 -- alter table dbname.tb_security_user_origin add origin varchar(250);
-- alter table SQL section

