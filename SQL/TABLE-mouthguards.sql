
-- drop table dbname.tb_mouthguards;

CREATE TABLE dbname.tb_mouthguards (
id_mouthguards int UNSIGNED NOT NULL AUTO_INCREMENT,
manufacturer varchar(250),
model varchar(250),
notes varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_mouthguards)
);


-- alter table SQL section
 -- alter table dbname.tb_mouthguards add manufacturer varchar(250);
 -- alter table dbname.tb_mouthguards add model varchar(250);
 -- alter table dbname.tb_mouthguards add notes varchar(250);
-- alter table SQL section

