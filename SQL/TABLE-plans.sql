
-- drop table dbname.tb_plans;

CREATE TABLE dbname.tb_plans (
id_plans int UNSIGNED NOT NULL AUTO_INCREMENT,
status varchar(250),
description varchar(250),
underwriter varchar(250),
protection varchar(10),
underwriter_cost varchar(10),
sales_price varchar(10),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_plans)
);


-- alter table SQL section
 -- alter table dbname.tb_plans add status varchar(250);
 -- alter table dbname.tb_plans add description varchar(250);
 -- alter table dbname.tb_plans add underwriter varchar(250);
 -- alter table dbname.tb_plans add protection varchar(10);
 -- alter table dbname.tb_plans add underwriter_cost varchar(10);
 -- alter table dbname.tb_plans add sales_price varchar(10);
-- alter table SQL section

