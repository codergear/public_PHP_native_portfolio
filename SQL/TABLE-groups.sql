
-- drop table dbname.tb_groups;

CREATE TABLE dbname.tb_groups (
id_groups int UNSIGNED NOT NULL AUTO_INCREMENT,
status varchar(20),
agreement_date varchar(20),
entity_name varchar(250),
principal_name varchar(250),
principal_last_name varchar(250),
principal_title varchar(250),
principal_full_name varchar(250),
phone varchar(250),
email varchar(250),
group_rate varchar(250),
afiliate varchar(250),
address varchar(250),
city varchar(250),
state varchar(250),
zip_code varchar(250),
pay_by varchar(250),
routing_number varchar(250),
account_number varchar(250),
cardpointe_token varchar(250),
linked_user varchar(250),
notes varchar(250),
logo varchar(250),
filemanagerlist varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_groups)
);


-- drop table dbname.tb_groups_plans;

CREATE TABLE dbname.tb_groups_plans (
id int UNSIGNED NOT NULL AUTO_INCREMENT,
id_groups int UNSIGNED NOT NULL,
id_plans int UNSIGNED NOT NULL,
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id)
);

-- alter table SQL section
 -- alter table dbname.tb_groups add status varchar(20);
 -- alter table dbname.tb_groups add agreement_date varchar(20);
 -- alter table dbname.tb_groups add entity_name varchar(250);
 -- alter table dbname.tb_groups add principal_name varchar(250);
 -- alter table dbname.tb_groups add principal_last_name varchar(250);
 -- alter table dbname.tb_groups add principal_title varchar(250);
 -- alter table dbname.tb_groups add principal_full_name varchar(250);
 -- alter table dbname.tb_groups add phone varchar(250);
 -- alter table dbname.tb_groups add email varchar(250);
 -- alter table dbname.tb_groups add group_rate varchar(250);
 -- alter table dbname.tb_groups add afiliate varchar(250);
 -- alter table dbname.tb_groups add address varchar(250);
 -- alter table dbname.tb_groups add city varchar(250);
 -- alter table dbname.tb_groups add state varchar(250);
 -- alter table dbname.tb_groups add zip_code varchar(250);
 -- alter table dbname.tb_groups add pay_by varchar(250);
 -- alter table dbname.tb_groups add routing_number varchar(250);
 -- alter table dbname.tb_groups add account_number varchar(250);
 -- alter table dbname.tb_groups add cardpointe_token varchar(250);
 -- alter table dbname.tb_groups add linked_user varchar(250);
 -- alter table dbname.tb_groups add notes varchar(250);
 -- alter table dbname.tb_groups add logo varchar(250);
 -- alter table dbname.tb_groups add filemanagerlist varchar(250);
-- alter table SQL section

