
DROP PROCEDURE if exists dbname.CRUD_contracts_payments;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_contracts_payments(
    IN _transaction_id varchar(250),
    IN _contract varchar(250),
    IN _id_user varchar(250),
    IN _id_contracts_payments INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_contracts_payments(
        transaction_id,
        contract,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _transaction_id,
        _contract,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    transaction_id,
    contract,
    id_contracts_payments,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_contracts_payments where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    transaction_id,
    contract,
    id_contracts_payments,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_contracts_payments where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_contracts_payments set 
        transaction_id = _transaction_id,
        contract = _contract,
        date_UMO=now(),user_UMO=_id_user where id_contracts_payments=_id_contracts_payments ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_contracts_payments set active=0,date_UMO=now(),user_UMO=_id_user where id_contracts_payments= _id_contracts_payments ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_contracts_payments;
    select count, msg from (
      select 0 as count, 'contracts_payments' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

