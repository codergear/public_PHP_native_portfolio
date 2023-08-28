
-- drop table dbname.tb_auditor;

CREATE TABLE dbname.tb_auditor (
id_auditor int UNSIGNED NOT NULL AUTO_INCREMENT,
transaction varchar(250),
contract varchar(250),
premium varchar(250),
group_rate varchar(250),
dentalCare_fee varchar(250),
underwriter_cost varchar(250),
affiliate varchar(250),
dentalCare_net varchar(250),
transaction_date varchar(250),
payee_affiliate_name varchar(250),
payee_affiliate_id varchar(250),
payee_group_name varchar(250),
is_payed_group varchar(250),
group_check_number varchar(250),
group_payment_date varchar(250),
payee_group_id varchar(250),
payee_underwriter_name varchar(250),
is_payed_underwriter varchar(250),
payee_underwriter_check_number varchar(250),
payee_underwriter_payment_date varchar(250),
payee_underwriter_id varchar(250),
is_cancelled varchar(250),
is_payed_affiliate varchar(250),
doctor_name varchar(250),
doctor_id varchar(250),
plan_name varchar(250),
plan_id varchar(250),
facility_name varchar(250),
facility_id varchar(250),
patient_name varchar(250),
coordinator_name varchar(250),
coordinator_id varchar(250),
procedure_date varchar(250),
payment_method varchar(250),
a1_affiliate_name varchar(250),
a1_affiliate_is_payed varchar(250),
a1_check_number varchar(250),
a1_payment_date varchar(250),
a1_affiliate_id varchar(250),
a1_affiliate_amount varchar(250),
a2_affiliate_name varchar(250),
a2_affiliate_is_payed varchar(250),
a2_check_number varchar(250),
a2_payment_date varchar(250),
a2_affiliate_id varchar(250),
a2_affiliate_amount varchar(250),
a3_affiliate_name varchar(250),
a3_affiliate_is_payed varchar(250),
a3_check_number varchar(250),
a3_payment_date varchar(250),
a3_affiliate_id varchar(250),
a3_affiliate_amount varchar(250),
contract_status varchar(250),
transaction_fee varchar(250),
pay_by varchar(250),
patient_email varchar(250),
patient_phone varchar(250),
patient_full_address varchar(250),
purchase_date varchar(250),
completion_date varchar(250),
cancelation_date varchar(250),
active TINYINT UNSIGNED,
user_umo int UNSIGNED,
date_umo DATETIME,
PRIMARY KEY (id_auditor)
);


-- alter table SQL section
 -- alter table dbname.tb_auditor add transaction varchar(250);
 -- alter table dbname.tb_auditor add contract varchar(250);
 -- alter table dbname.tb_auditor add premium varchar(250);
 -- alter table dbname.tb_auditor add group_rate varchar(250);
 -- alter table dbname.tb_auditor add dentalCare_fee varchar(250);
 -- alter table dbname.tb_auditor add underwriter_cost varchar(250);
 -- alter table dbname.tb_auditor add affiliate varchar(250);
 -- alter table dbname.tb_auditor add dentalCare_net varchar(250);
 -- alter table dbname.tb_auditor add transaction_date varchar(250);
 -- alter table dbname.tb_auditor add payee_affiliate_name varchar(250);
 -- alter table dbname.tb_auditor add payee_affiliate_id varchar(250);
 -- alter table dbname.tb_auditor add payee_group_name varchar(250);
 -- alter table dbname.tb_auditor add is_payed_group varchar(250);
 -- alter table dbname.tb_auditor add group_check_number varchar(250);
 -- alter table dbname.tb_auditor add group_payment_date varchar(250);
 -- alter table dbname.tb_auditor add payee_group_id varchar(250);
 -- alter table dbname.tb_auditor add payee_underwriter_name varchar(250);
 -- alter table dbname.tb_auditor add is_payed_underwriter varchar(250);
 -- alter table dbname.tb_auditor add payee_underwriter_check_number varchar(250);
 -- alter table dbname.tb_auditor add payee_underwriter_payment_date varchar(250);
 -- alter table dbname.tb_auditor add payee_underwriter_id varchar(250);
 -- alter table dbname.tb_auditor add is_cancelled varchar(250);
 -- alter table dbname.tb_auditor add is_payed_affiliate varchar(250);
 -- alter table dbname.tb_auditor add doctor_name varchar(250);
 -- alter table dbname.tb_auditor add doctor_id varchar(250);
 -- alter table dbname.tb_auditor add plan_name varchar(250);
 -- alter table dbname.tb_auditor add plan_id varchar(250);
 -- alter table dbname.tb_auditor add facility_name varchar(250);
 -- alter table dbname.tb_auditor add facility_id varchar(250);
 -- alter table dbname.tb_auditor add patient_name varchar(250);
 -- alter table dbname.tb_auditor add coordinator_name varchar(250);
 -- alter table dbname.tb_auditor add coordinator_id varchar(250);
 -- alter table dbname.tb_auditor add procedure_date varchar(250);
 -- alter table dbname.tb_auditor add payment_method varchar(250);
 -- alter table dbname.tb_auditor add a1_affiliate_name varchar(250);
 -- alter table dbname.tb_auditor add a1_affiliate_is_payed varchar(250);
 -- alter table dbname.tb_auditor add a1_check_number varchar(250);
 -- alter table dbname.tb_auditor add a1_payment_date varchar(250);
 -- alter table dbname.tb_auditor add a1_affiliate_id varchar(250);
 -- alter table dbname.tb_auditor add a1_affiliate_amount varchar(250);
 -- alter table dbname.tb_auditor add a2_affiliate_name varchar(250);
 -- alter table dbname.tb_auditor add a2_affiliate_is_payed varchar(250);
 -- alter table dbname.tb_auditor add a2_check_number varchar(250);
 -- alter table dbname.tb_auditor add a2_payment_date varchar(250);
 -- alter table dbname.tb_auditor add a2_affiliate_id varchar(250);
 -- alter table dbname.tb_auditor add a2_affiliate_amount varchar(250);
 -- alter table dbname.tb_auditor add a3_affiliate_name varchar(250);
 -- alter table dbname.tb_auditor add a3_affiliate_is_payed varchar(250);
 -- alter table dbname.tb_auditor add a3_check_number varchar(250);
 -- alter table dbname.tb_auditor add a3_payment_date varchar(250);
 -- alter table dbname.tb_auditor add a3_affiliate_id varchar(250);
 -- alter table dbname.tb_auditor add a3_affiliate_amount varchar(250);
 -- alter table dbname.tb_auditor add contract_status varchar(250);
 -- alter table dbname.tb_auditor add transaction_fee varchar(250);
 -- alter table dbname.tb_auditor add pay_by varchar(250);
 -- alter table dbname.tb_auditor add patient_email varchar(250);
 -- alter table dbname.tb_auditor add patient_phone varchar(250);
 -- alter table dbname.tb_auditor add patient_full_address varchar(250);
 -- alter table dbname.tb_auditor add purchase_date varchar(250);
 -- alter table dbname.tb_auditor add completion_date varchar(250);
 -- alter table dbname.tb_auditor add cancelation_date varchar(250);
-- alter table SQL section

