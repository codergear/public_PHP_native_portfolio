
-- drop table dbname.tb_states;

CREATE TABLE dbname.tb_states (
id_states int UNSIGNED NOT NULL AUTO_INCREMENT,
state_name varchar(50),
state_code varchar(5),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_states)
);


-- alter table SQL section
 -- alter table dbname.tb_states add state_name varchar(50);
 -- alter table dbname.tb_states add state_code varchar(5);
-- alter table SQL section

