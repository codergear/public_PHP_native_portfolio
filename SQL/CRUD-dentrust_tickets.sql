
DROP PROCEDURE if exists dbname.CRUD_dentalCare_tickets;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_dentalCare_tickets(
    IN _tickets_user varchar(250),
    IN _tickets_status varchar(250),
    IN _tickets_date varchar(250),
    IN _patient_name varchar(250),
    IN _contract_code varchar(250),
    IN _notes varchar(500),
    IN _id_user varchar(250),
    IN _id_dentalCare_tickets INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_dentalCare_tickets(
        tickets_user,
        tickets_status,
        tickets_date,
        patient_name,
        contract_code,
        notes,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _tickets_user,
        _tickets_status,
        _tickets_date,
        _patient_name,
        _contract_code,
        _notes,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    tickets_user,
    tickets_status,
    tickets_date,
    patient_name,
    contract_code,
    notes,
    id_dentalCare_tickets,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_dentalCare_tickets where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    tickets_user,
    tickets_status,
    tickets_date,
    patient_name,
    contract_code,
    notes,
    id_dentalCare_tickets,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_dentalCare_tickets where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_dentalCare_tickets set 
        tickets_user = _tickets_user,
        tickets_status = _tickets_status,
        tickets_date = _tickets_date,
        patient_name = _patient_name,
        contract_code = _contract_code,
        notes = _notes,
        date_UMO=now(),user_UMO=_id_user where id_dentalCare_tickets=_id_dentalCare_tickets ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_dentalCare_tickets set active=0,date_UMO=now(),user_UMO=_id_user where id_dentalCare_tickets= _id_dentalCare_tickets ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_dentalCare_tickets;
    select count, msg from (
      select 0 as count, 'dentalCare_tickets' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

