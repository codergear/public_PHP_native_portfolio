
-- drop table dbname.tb_mouthguards_models;

CREATE TABLE dbname.tb_mouthguards_models (
id_mouthguards_models int UNSIGNED NOT NULL AUTO_INCREMENT,
models varchar(250),
manufacturer varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_mouthguards_models)
);


-- alter table SQL section
 -- alter table dbname.tb_mouthguards_models add models varchar(250);
 -- alter table dbname.tb_mouthguards_models add manufacturer varchar(250);
-- alter table SQL section

