
-- drop table dbname.tb_software_license_agreement;

CREATE TABLE dbname.tb_software_license_agreement (
id_software_license_agreement int UNSIGNED NOT NULL AUTO_INCREMENT,
document_content longtext,
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_software_license_agreement)
);


-- alter table SQL section
 -- alter table dbname.tb_software_license_agreement add document_content longtext;
-- alter table SQL section

