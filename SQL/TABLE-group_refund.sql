
-- drop table dbname.tb_group_refund;

CREATE TABLE dbname.tb_group_refund (
id_group_refund int UNSIGNED NOT NULL AUTO_INCREMENT,
group_detail nvarchar(250),
amount varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_group_refund)
);


-- alter table SQL section
 -- alter table dbname.tb_group_refund add group_detail nvarchar(250);
 -- alter table dbname.tb_group_refund add amount varchar(250);
-- alter table SQL section

