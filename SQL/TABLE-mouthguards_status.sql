
-- drop table dbname.tb_mouthguards_status;

CREATE TABLE dbname.tb_mouthguards_status (
id_mouthguards_status int UNSIGNED NOT NULL AUTO_INCREMENT,
status varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_mouthguards_status)
);


-- alter table SQL section
 -- alter table dbname.tb_mouthguards_status add status varchar(250);
-- alter table SQL section

