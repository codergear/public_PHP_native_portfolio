
-- drop table dbname.tb_underwriter;

CREATE TABLE dbname.tb_underwriter (
id_underwriter int UNSIGNED NOT NULL AUTO_INCREMENT,
underwriter varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_underwriter)
);


-- alter table SQL section
 -- alter table dbname.tb_underwriter add underwriter varchar(250);
-- alter table SQL section

