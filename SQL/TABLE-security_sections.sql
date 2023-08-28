
-- drop table dbname.tb_security_sections;

CREATE TABLE dbname.tb_security_sections (
id_security_sections int UNSIGNED NOT NULL AUTO_INCREMENT,
action varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_security_sections)
);


-- alter table SQL section
 -- alter table dbname.tb_security_sections add action varchar(250);
-- alter table SQL section

