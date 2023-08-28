
-- drop table dbname.tb_non_competition_and_confidentiality_agreement;

CREATE TABLE dbname.tb_non_competition_and_confidentiality_agreement (
id_non_competition_and_confidentiality_agreement int UNSIGNED NOT NULL AUTO_INCREMENT,
document_content longtext,
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_non_competition_and_confidentiality_agreement)
);


-- alter table SQL section
 -- alter table dbname.tb_non_competition_and_confidentiality_agreement add document_content longtext;
-- alter table SQL section

