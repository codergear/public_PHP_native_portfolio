
-- drop table dbname.tb_security_access_log;

CREATE TABLE dbname.tb_security_access_log (
id_security_access_log int UNSIGNED NOT NULL AUTO_INCREMENT,
user_id varchar(250),
https varchar(250),
user_agent varchar(250),
accept_language varchar(250),
remote_addr varchar(250),
request_time varchar(250),
request_module varchar(250),
request_action varchar(250),
request_record_id varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_security_access_log)
);


-- alter table SQL section
 -- alter table dbname.tb_security_access_log add user_id varchar(250);
 -- alter table dbname.tb_security_access_log add https varchar(250);
 -- alter table dbname.tb_security_access_log add user_agent varchar(250);
 -- alter table dbname.tb_security_access_log add accept_language varchar(250);
 -- alter table dbname.tb_security_access_log add remote_addr varchar(250);
 -- alter table dbname.tb_security_access_log add request_time varchar(250);
 -- alter table dbname.tb_security_access_log add request_module varchar(250);
 -- alter table dbname.tb_security_access_log add request_action varchar(250);
 -- alter table dbname.tb_security_access_log add request_record_id varchar(250);
-- alter table SQL section

