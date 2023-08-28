
-- drop table dbname.tb_doctors;

CREATE TABLE dbname.tb_doctors (
id_doctors int UNSIGNED NOT NULL AUTO_INCREMENT,
status varchar(250),
first_name varchar(250),
last_name varchar(250),
full_name varchar(250),
phone varchar(250),
email varchar(250),
preferred_method_of_contact varchar(250),
address varchar(250),
city varchar(250),
state varchar(250),
zip_code varchar(250),
group_detail varchar(5),
doctor_fee varchar(10),
linked_user varchar(250),
notes varchar(250),
image varchar(250),
filemanagerlist varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_doctors)
);


-- drop table dbname.tb_doctors_facilities;

CREATE TABLE dbname.tb_doctors_facilities (
id int UNSIGNED NOT NULL AUTO_INCREMENT,
id_doctors int UNSIGNED NOT NULL,
id_facilities int UNSIGNED NOT NULL,
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id)
);

-- drop table dbname.tb_doctors_specialties;

CREATE TABLE dbname.tb_doctors_specialties (
id int UNSIGNED NOT NULL AUTO_INCREMENT,
id_doctors int UNSIGNED NOT NULL,
id_specialties int UNSIGNED NOT NULL,
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id)
);

-- drop table dbname.tb_doctors_surgical_coordinators;

CREATE TABLE dbname.tb_doctors_surgical_coordinators (
id int UNSIGNED NOT NULL AUTO_INCREMENT,
id_doctors int UNSIGNED NOT NULL,
id_surgical_coordinators int UNSIGNED NOT NULL,
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id)
);

-- alter table SQL section
 -- alter table dbname.tb_doctors add status varchar(250);
 -- alter table dbname.tb_doctors add first_name varchar(250);
 -- alter table dbname.tb_doctors add last_name varchar(250);
 -- alter table dbname.tb_doctors add full_name varchar(250);
 -- alter table dbname.tb_doctors add phone varchar(250);
 -- alter table dbname.tb_doctors add email varchar(250);
 -- alter table dbname.tb_doctors add preferred_method_of_contact varchar(250);
 -- alter table dbname.tb_doctors add address varchar(250);
 -- alter table dbname.tb_doctors add city varchar(250);
 -- alter table dbname.tb_doctors add state varchar(250);
 -- alter table dbname.tb_doctors add zip_code varchar(250);
 -- alter table dbname.tb_doctors add group_detail varchar(5);
 -- alter table dbname.tb_doctors add doctor_fee varchar(10);
 -- alter table dbname.tb_doctors add linked_user varchar(250);
 -- alter table dbname.tb_doctors add notes varchar(250);
 -- alter table dbname.tb_doctors add image varchar(250);
 -- alter table dbname.tb_doctors add filemanagerlist varchar(250);
-- alter table SQL section

