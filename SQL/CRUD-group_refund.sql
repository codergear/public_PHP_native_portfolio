
DROP PROCEDURE if exists dbname.CRUD_group_refund;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_group_refund(
    IN _group_detail nvarchar(250),
    IN _amount varchar(250),
    IN _id_user varchar(250),
    IN _id_group_refund INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_group_refund(
        group_detail,
        amount,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _group_detail,
        _amount,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    group_detail,
    amount,
    id_group_refund,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_group_refund where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    group_detail,
    amount,
    id_group_refund,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_group_refund where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_group_refund set 
        group_detail = _group_detail,
        amount = _amount,
        date_UMO=now(),user_UMO=_id_user where id_group_refund=_id_group_refund ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_group_refund set active=0,date_UMO=now(),user_UMO=_id_user where id_group_refund= _id_group_refund ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_group_refund;
    select count, msg from (
      select 0 as count, 'group_refund' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

