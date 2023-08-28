
-- drop table dbname.tb_specialties;

CREATE TABLE dbname.tb_specialties (
id_specialties int UNSIGNED NOT NULL AUTO_INCREMENT,
specialties varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_specialties)
);


-- alter table SQL section
 -- alter table dbname.tb_specialties add specialties varchar(250);
-- alter table SQL section

