
DROP PROCEDURE if exists dbname.CRUD_patient_transaction_fee;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_patient_transaction_fee(
    IN _transaction_fee varchar(250),
    IN _id_user varchar(250),
    IN _id_patient_transaction_fee INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_patient_transaction_fee(
        transaction_fee,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _transaction_fee,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    transaction_fee,
    id_patient_transaction_fee,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_patient_transaction_fee where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    transaction_fee,
    id_patient_transaction_fee,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_patient_transaction_fee where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_patient_transaction_fee set 
        transaction_fee = _transaction_fee,
        date_UMO=now(),user_UMO=_id_user where id_patient_transaction_fee=_id_patient_transaction_fee ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_patient_transaction_fee set active=0,date_UMO=now(),user_UMO=_id_user where id_patient_transaction_fee= _id_patient_transaction_fee ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_patient_transaction_fee;
    select count, msg from (
      select 0 as count, 'patient_transaction_fee' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

