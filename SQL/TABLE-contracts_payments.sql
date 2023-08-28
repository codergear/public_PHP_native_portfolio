
-- drop table dbname.tb_contracts_payments;

CREATE TABLE dbname.tb_contracts_payments (
id_contracts_payments int UNSIGNED NOT NULL AUTO_INCREMENT,
transaction_id varchar(250),
contract varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_contracts_payments)
);


-- alter table SQL section
 -- alter table dbname.tb_contracts_payments add transaction_id varchar(250);
 -- alter table dbname.tb_contracts_payments add contract varchar(250);
-- alter table SQL section

