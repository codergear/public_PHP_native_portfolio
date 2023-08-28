
-- drop table dbname.tb_payment_methods;

CREATE TABLE dbname.tb_payment_methods (
id_payment_methods int UNSIGNED NOT NULL AUTO_INCREMENT,
payment_method varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_payment_methods)
);


-- alter table SQL section
 -- alter table dbname.tb_payment_methods add payment_method varchar(250);
-- alter table SQL section

