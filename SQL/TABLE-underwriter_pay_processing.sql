
-- drop table dbname.tb_underwriter_pay_processing;

CREATE TABLE dbname.tb_underwriter_pay_processing (
id_underwriter_pay_processing int UNSIGNED NOT NULL AUTO_INCREMENT,
is_cancelled varchar(250),
payment_status varchar(250),
begin_date varchar(20),
end_date varchar(20),
payment_date varchar(20),
payee varchar(250),
check_number varchar(250),
check_amount varchar(250),
payment_period varchar(250),
check_picture varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_underwriter_pay_processing)
);


-- alter table SQL section
 -- alter table dbname.tb_underwriter_pay_processing add is_cancelled varchar(250);
 -- alter table dbname.tb_underwriter_pay_processing add payment_status varchar(250);
 -- alter table dbname.tb_underwriter_pay_processing add begin_date varchar(20);
 -- alter table dbname.tb_underwriter_pay_processing add end_date varchar(20);
 -- alter table dbname.tb_underwriter_pay_processing add payment_date varchar(20);
 -- alter table dbname.tb_underwriter_pay_processing add payee varchar(250);
 -- alter table dbname.tb_underwriter_pay_processing add check_number varchar(250);
 -- alter table dbname.tb_underwriter_pay_processing add check_amount varchar(250);
 -- alter table dbname.tb_underwriter_pay_processing add payment_period varchar(250);
 -- alter table dbname.tb_underwriter_pay_processing add check_picture varchar(250);
-- alter table SQL section

