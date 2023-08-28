
-- drop table dbname.tb_security_actions;

CREATE TABLE dbname.tb_security_actions (
id_security_actions int UNSIGNED NOT NULL AUTO_INCREMENT,
action varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_security_actions)
);


-- alter table SQL section
 -- alter table dbname.tb_security_actions add action varchar(250);
-- alter table SQL section

