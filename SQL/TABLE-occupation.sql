
-- drop table dbname.tb_occupation;

CREATE TABLE dbname.tb_occupation (
id_occupation int UNSIGNED NOT NULL AUTO_INCREMENT,
occupation varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_occupation)
);


-- alter table SQL section
 -- alter table dbname.tb_occupation add occupation varchar(250);
-- alter table SQL section

