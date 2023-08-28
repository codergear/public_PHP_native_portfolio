
-- drop table dbname.tb_group_principal_terms_conditions;

CREATE TABLE dbname.tb_group_principal_terms_conditions (
id_group_principal_terms_conditions int UNSIGNED NOT NULL AUTO_INCREMENT,
principal_group nvarchar(250),
doc_1 varchar(250),
doc_2 varchar(250),
doc_3 varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_group_principal_terms_conditions)
);


-- alter table SQL section
 -- alter table dbname.tb_group_principal_terms_conditions add principal_group nvarchar(250);
 -- alter table dbname.tb_group_principal_terms_conditions add doc_1 varchar(250);
 -- alter table dbname.tb_group_principal_terms_conditions add doc_2 varchar(250);
 -- alter table dbname.tb_group_principal_terms_conditions add doc_3 varchar(250);
-- alter table SQL section

