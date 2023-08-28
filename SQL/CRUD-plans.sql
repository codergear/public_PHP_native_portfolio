
DROP PROCEDURE if exists dbname.CRUD_plans;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_plans(
    IN _status varchar(250),
    IN _description varchar(250),
    IN _underwriter varchar(250),
    IN _protection varchar(10),
    IN _underwriter_cost varchar(10),
    IN _sales_price varchar(10),
    IN _id_user varchar(250),
    IN _id_plans INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_plans(
        status,
        description,
        underwriter,
        protection,
        underwriter_cost,
        sales_price,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _status,
        _description,
        _underwriter,
        _protection,
        _underwriter_cost,
        _sales_price,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    status,
    description,
    underwriter,
    protection,
    underwriter_cost,
    sales_price,
    id_plans,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_plans where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    status,
    description,
    underwriter,
    protection,
    underwriter_cost,
    sales_price,
    id_plans,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_plans where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_plans set 
        status = _status,
        description = _description,
        underwriter = _underwriter,
        protection = _protection,
        underwriter_cost = _underwriter_cost,
        sales_price = _sales_price,
        date_UMO=now(),user_UMO=_id_user where id_plans=_id_plans ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_plans set active=0,date_UMO=now(),user_UMO=_id_user where id_plans= _id_plans ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_plans;
    select count, msg from (
      select 0 as count, 'plans' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

