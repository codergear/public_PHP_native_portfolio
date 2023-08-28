
-- drop table dbname.tb_contracts;

CREATE TABLE dbname.tb_contracts (
id_contracts int UNSIGNED NOT NULL AUTO_INCREMENT,
contract varchar(250),
contract_status varchar(250),
is_cancelled varchar(250),
contract_date varchar(250),
full_name varchar(250),
first_name nvarchar(250),
last_name nvarchar(250),
email nvarchar(250),
phone nvarchar(250),
address nvarchar(250),
city nvarchar(250),
state varchar(250),
zip_code varchar(250),
surgery_date varchar(20),
doctor varchar(5),
facility varchar(5),
plan varchar(5),
premium nvarchar(250),
payment_method varchar(5),
card_number varchar(20),
card_valid_to nvarchar(20),
card_cvv nvarchar(250),
routing_number nvarchar(250),
account_number nvarchar(250),
pay_by nvarchar(250),
coordinator_name varchar(250),
group_name varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_contracts)
);


-- alter table SQL section
 -- alter table dbname.tb_contracts add contract varchar(250);
 -- alter table dbname.tb_contracts add contract_status varchar(250);
 -- alter table dbname.tb_contracts add is_cancelled varchar(250);
 -- alter table dbname.tb_contracts add contract_date varchar(250);
 -- alter table dbname.tb_contracts add full_name varchar(250);
 -- alter table dbname.tb_contracts add first_name nvarchar(250);
 -- alter table dbname.tb_contracts add last_name nvarchar(250);
 -- alter table dbname.tb_contracts add email nvarchar(250);
 -- alter table dbname.tb_contracts add phone nvarchar(250);
 -- alter table dbname.tb_contracts add address nvarchar(250);
 -- alter table dbname.tb_contracts add city nvarchar(250);
 -- alter table dbname.tb_contracts add state varchar(250);
 -- alter table dbname.tb_contracts add zip_code varchar(250);
 -- alter table dbname.tb_contracts add surgery_date varchar(20);
 -- alter table dbname.tb_contracts add doctor varchar(5);
 -- alter table dbname.tb_contracts add facility varchar(5);
 -- alter table dbname.tb_contracts add plan varchar(5);
 -- alter table dbname.tb_contracts add premium nvarchar(250);
 -- alter table dbname.tb_contracts add payment_method varchar(5);
 -- alter table dbname.tb_contracts add card_number varchar(20);
 -- alter table dbname.tb_contracts add card_valid_to nvarchar(20);
 -- alter table dbname.tb_contracts add card_cvv nvarchar(250);
 -- alter table dbname.tb_contracts add routing_number nvarchar(250);
 -- alter table dbname.tb_contracts add account_number nvarchar(250);
 -- alter table dbname.tb_contracts add pay_by nvarchar(250);
 -- alter table dbname.tb_contracts add coordinator_name varchar(250);
 -- alter table dbname.tb_contracts add group_name varchar(250);
-- alter table SQL section

