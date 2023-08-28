
-- drop table dbname.tb_support_tickets;

CREATE TABLE dbname.tb_support_tickets (
id_support_tickets int UNSIGNED NOT NULL AUTO_INCREMENT,
tickets_user varchar(250),
tickets_status varchar(250),
tickets_date varchar(20),
description varchar(500),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_support_tickets)
);


-- alter table SQL section
 -- alter table dbname.tb_support_tickets add tickets_user varchar(250);
 -- alter table dbname.tb_support_tickets add tickets_status varchar(250);
 -- alter table dbname.tb_support_tickets add tickets_date varchar(20);
 -- alter table dbname.tb_support_tickets add description varchar(500);
-- alter table SQL section

