
-- drop table dbname.tb_surgical_coordinators;

CREATE TABLE dbname.tb_surgical_coordinators (
id_surgical_coordinators int UNSIGNED NOT NULL AUTO_INCREMENT,
status varchar(250),
first_name varchar(250),
last_name varchar(250),
full_name varchar(250),
birthday varchar(20),
phone varchar(250),
email varchar(250),
preferred_method_of_contact varchar(250),
group_detail varchar(250),
group_manager varchar(250),
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
PRIMARY KEY (id_surgical_coordinators)
);


-- alter table SQL section
 -- alter table dbname.tb_surgical_coordinators add status varchar(250);
 -- alter table dbname.tb_surgical_coordinators add first_name varchar(250);
 -- alter table dbname.tb_surgical_coordinators add last_name varchar(250);
 -- alter table dbname.tb_surgical_coordinators add full_name varchar(250);
 -- alter table dbname.tb_surgical_coordinators add birthday varchar(20);
 -- alter table dbname.tb_surgical_coordinators add phone varchar(250);
 -- alter table dbname.tb_surgical_coordinators add email varchar(250);
 -- alter table dbname.tb_surgical_coordinators add preferred_method_of_contact varchar(250);
 -- alter table dbname.tb_surgical_coordinators add group_detail varchar(250);
 -- alter table dbname.tb_surgical_coordinators add group_manager varchar(250);
 -- alter table dbname.tb_surgical_coordinators add address varchar(250);
 -- alter table dbname.tb_surgical_coordinators add city varchar(250);
 -- alter table dbname.tb_surgical_coordinators add state varchar(250);
 -- alter table dbname.tb_surgical_coordinators add zip_code varchar(250);
 -- alter table dbname.tb_surgical_coordinators add notes varchar(250);
 -- alter table dbname.tb_surgical_coordinators add picture varchar(250);
 -- alter table dbname.tb_surgical_coordinators add filemanagerlist varchar(250);
 -- alter table dbname.tb_surgical_coordinators add linked_user varchar(250);
-- alter table SQL section

