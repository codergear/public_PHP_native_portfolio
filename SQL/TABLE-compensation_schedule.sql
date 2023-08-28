
-- drop table dbname.tb_compensation_schedule;

CREATE TABLE dbname.tb_compensation_schedule (
id_compensation_schedule int UNSIGNED NOT NULL AUTO_INCREMENT,
document_content longtext,
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_compensation_schedule)
);


-- alter table SQL section
 -- alter table dbname.tb_compensation_schedule add document_content longtext;
-- alter table SQL section

