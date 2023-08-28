
-- drop table dbname.tb_affiliate_level;

CREATE TABLE dbname.tb_affiliate_level (
id_affiliate_level int UNSIGNED NOT NULL AUTO_INCREMENT,
affiliate_level varchar(10),
description varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_affiliate_level)
);


-- alter table SQL section
 -- alter table dbname.tb_affiliate_level add affiliate_level varchar(10);
 -- alter table dbname.tb_affiliate_level add description varchar(250);
-- alter table SQL section

