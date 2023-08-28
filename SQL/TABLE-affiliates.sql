
-- drop table dbname.tb_affiliates;

CREATE TABLE dbname.tb_affiliates (
id_affiliates int UNSIGNED NOT NULL AUTO_INCREMENT,
status varchar(250),
agreement_date varchar(20),
afiliate_level varchar(5),
manager_afiliate varchar(250),
affiliate_to varchar(250),
first_name varchar(250),
last_name varchar(250),
full_name varchar(250),
birth_date varchar(20),
phone varchar(250),
email varchar(250),
preferred_method_of_contact varchar(250),
occupation varchar(250),
company varchar(250),
commission varchar(250),
address varchar(250),
city varchar(250),
state varchar(250),
zip_code varchar(250),
notes varchar(250),
picture varchar(250),
filemanagerlist varchar(250),
linked_user varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_affiliates)
);


-- alter table SQL section
 -- alter table dbname.tb_affiliates add status varchar(250);
 -- alter table dbname.tb_affiliates add agreement_date varchar(20);
 -- alter table dbname.tb_affiliates add afiliate_level varchar(5);
 -- alter table dbname.tb_affiliates add manager_afiliate varchar(250);
 -- alter table dbname.tb_affiliates add affiliate_to varchar(250);
 -- alter table dbname.tb_affiliates add first_name varchar(250);
 -- alter table dbname.tb_affiliates add last_name varchar(250);
 -- alter table dbname.tb_affiliates add full_name varchar(250);
 -- alter table dbname.tb_affiliates add birth_date varchar(20);
 -- alter table dbname.tb_affiliates add phone varchar(250);
 -- alter table dbname.tb_affiliates add email varchar(250);
 -- alter table dbname.tb_affiliates add preferred_method_of_contact varchar(250);
 -- alter table dbname.tb_affiliates add occupation varchar(250);
 -- alter table dbname.tb_affiliates add company varchar(250);
 -- alter table dbname.tb_affiliates add commission varchar(250);
 -- alter table dbname.tb_affiliates add address varchar(250);
 -- alter table dbname.tb_affiliates add city varchar(250);
 -- alter table dbname.tb_affiliates add state varchar(250);
 -- alter table dbname.tb_affiliates add zip_code varchar(250);
 -- alter table dbname.tb_affiliates add notes varchar(250);
 -- alter table dbname.tb_affiliates add picture varchar(250);
 -- alter table dbname.tb_affiliates add filemanagerlist varchar(250);
 -- alter table dbname.tb_affiliates add linked_user varchar(250);
-- alter table SQL section

