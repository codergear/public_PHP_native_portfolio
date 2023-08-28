
-- drop table dbname.tb_provider_transaction_fee;

CREATE TABLE dbname.tb_provider_transaction_fee (
id_provider_transaction_fee int UNSIGNED NOT NULL AUTO_INCREMENT,
transaction_fee varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_provider_transaction_fee)
);


-- alter table SQL section
 -- alter table dbname.tb_provider_transaction_fee add transaction_fee varchar(250);
-- alter table SQL section

