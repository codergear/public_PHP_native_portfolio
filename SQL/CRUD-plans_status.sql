
DROP PROCEDURE if exists dbname.CRUD_plans_status;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_plans_status(
    IN _status varchar(250),
    IN _id_user varchar(250),
    IN _id_plans_status INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_plans_status(
        status,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _status,
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
    id_plans_status,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_plans_status where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    status,
    id_plans_status,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_plans_status where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_plans_status set 
        status = _status,
        date_UMO=now(),user_UMO=_id_user where id_plans_status=_id_plans_status ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_plans_status set active=0,date_UMO=now(),user_UMO=_id_user where id_plans_status= _id_plans_status ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_plans_status;
    select count, msg from (
      select 0 as count, 'plans_status' as msg
      union all
      select count(*) as count, 'Plans' as msg from tb_plans  where active = 1 and status = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

