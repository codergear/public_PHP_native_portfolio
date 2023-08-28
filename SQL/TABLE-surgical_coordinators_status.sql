
-- drop table dbname.tb_surgical_coordinators_status;

CREATE TABLE dbname.tb_surgical_coordinators_status (
id_surgical_coordinators_status int UNSIGNED NOT NULL AUTO_INCREMENT,
status varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_surgical_coordinators_status)
);


-- alter table SQL section
 -- alter table dbname.tb_surgical_coordinators_status add status varchar(250);
-- alter table SQL section

