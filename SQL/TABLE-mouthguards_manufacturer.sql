
-- drop table dbname.tb_mouthguards_manufacturer;

CREATE TABLE dbname.tb_mouthguards_manufacturer (
id_mouthguards_manufacturer int UNSIGNED NOT NULL AUTO_INCREMENT,
manufacturer varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_mouthguards_manufacturer)
);


-- alter table SQL section
 -- alter table dbname.tb_mouthguards_manufacturer add manufacturer varchar(250);
-- alter table SQL section

