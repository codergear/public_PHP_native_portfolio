
DROP PROCEDURE if exists dbname.CRUD_security_actions;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_security_actions(
    IN _action varchar(250),
    IN _id_user varchar(250),
    IN _id_security_actions INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_security_actions(
        action,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _action,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    action,
    id_security_actions,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_actions where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    action,
    id_security_actions,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_actions where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_security_actions set 
        action = _action,
        date_UMO=now(),user_UMO=_id_user where id_security_actions=_id_security_actions ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_security_actions set active=0,date_UMO=now(),user_UMO=_id_user where id_security_actions= _id_security_actions ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_security_actions;
    select count, msg from (
      select 0 as count, 'security_actions' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

