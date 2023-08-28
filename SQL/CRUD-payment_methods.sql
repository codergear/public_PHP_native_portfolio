
DROP PROCEDURE if exists dbname.CRUD_payment_methods;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_payment_methods(
    IN _payment_method varchar(250),
    IN _id_user varchar(250),
    IN _id_payment_methods INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_payment_methods(
        payment_method,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _payment_method,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    payment_method,
    id_payment_methods,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_payment_methods where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    payment_method,
    id_payment_methods,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_payment_methods where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_payment_methods set 
        payment_method = _payment_method,
        date_UMO=now(),user_UMO=_id_user where id_payment_methods=_id_payment_methods ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_payment_methods set active=0,date_UMO=now(),user_UMO=_id_user where id_payment_methods= _id_payment_methods ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_payment_methods;
    select count, msg from (
      select 0 as count, 'payment_methods' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

