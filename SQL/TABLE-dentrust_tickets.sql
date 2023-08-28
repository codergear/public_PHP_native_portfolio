
-- drop table dbname.tb_dentalCare_tickets;

CREATE TABLE dbname.tb_dentalCare_tickets (
id_dentalCare_tickets int UNSIGNED NOT NULL AUTO_INCREMENT,
tickets_user varchar(250),
tickets_status varchar(250),
tickets_date varchar(250),
patient_name varchar(250),
contract_code varchar(250),
notes varchar(500),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_dentalCare_tickets)
);


-- alter table SQL section
 -- alter table dbname.tb_dentalCare_tickets add tickets_user varchar(250);
 -- alter table dbname.tb_dentalCare_tickets add tickets_status varchar(250);
 -- alter table dbname.tb_dentalCare_tickets add tickets_date varchar(250);
 -- alter table dbname.tb_dentalCare_tickets add patient_name varchar(250);
 -- alter table dbname.tb_dentalCare_tickets add contract_code varchar(250);
 -- alter table dbname.tb_dentalCare_tickets add notes varchar(500);
-- alter table SQL section

