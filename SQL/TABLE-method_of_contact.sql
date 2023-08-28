
-- drop table dbname.tb_method_of_contact;

CREATE TABLE dbname.tb_method_of_contact (
id_method_of_contact int UNSIGNED NOT NULL AUTO_INCREMENT,
method_of_contact varchar(50),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_method_of_contact)
);


-- alter table SQL section
 -- alter table dbname.tb_method_of_contact add method_of_contact varchar(50);
-- alter table SQL section

