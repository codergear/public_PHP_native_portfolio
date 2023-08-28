
DROP PROCEDURE if exists dbname.CRUD_afiliate_pay_processing;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_afiliate_pay_processing(
    IN _is_cancelled varchar(250),
    IN _payment_status varchar(250),
    IN _begin_date varchar(20),
    IN _end_date varchar(20),
    IN _payment_date varchar(20),
    IN _payee varchar(250),
    IN _check_number varchar(250),
    IN _check_amount varchar(250),
    IN _payment_period varchar(250),
    IN _check_picture varchar(250),
    IN _id_user varchar(250),
    IN _id_afiliate_pay_processing INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_afiliate_pay_processing(
        is_cancelled,
        payment_status,
        begin_date,
        end_date,
        payment_date,
        payee,
        check_number,
        check_amount,
        payment_period,
        check_picture,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _is_cancelled,
        _payment_status,
        _begin_date,
        _end_date,
        _payment_date,
        _payee,
        _check_number,
        _check_amount,
        _payment_period,
        _check_picture,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    is_cancelled,
    payment_status,
    begin_date,
    end_date,
    payment_date,
    payee,
    check_number,
    check_amount,
    payment_period,
    check_picture,
    id_afiliate_pay_processing,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_afiliate_pay_processing where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    is_cancelled,
    payment_status,
    begin_date,
    end_date,
    payment_date,
    payee,
    check_number,
    check_amount,
    payment_period,
    check_picture,
    id_afiliate_pay_processing,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_afiliate_pay_processing where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_afiliate_pay_processing set 
        is_cancelled = _is_cancelled,
        payment_status = _payment_status,
        begin_date = _begin_date,
        end_date = _end_date,
        payment_date = _payment_date,
        payee = _payee,
        check_number = _check_number,
        check_amount = _check_amount,
        payment_period = _payment_period,
        check_picture = _check_picture,
        date_UMO=now(),user_UMO=_id_user where id_afiliate_pay_processing=_id_afiliate_pay_processing ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_afiliate_pay_processing set active=0,date_UMO=now(),user_UMO=_id_user where id_afiliate_pay_processing= _id_afiliate_pay_processing ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_afiliate_pay_processing;
    select count, msg from (
      select 0 as count, 'afiliate_pay_processing' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

