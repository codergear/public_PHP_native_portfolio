
DROP PROCEDURE if exists dbname.CRUD_security_user_origin;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_security_user_origin(
    IN _origin varchar(250),
    IN _id_user varchar(250),
    IN _id_security_user_origin INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_security_user_origin(
        origin,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _origin,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    origin,
    id_security_user_origin,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_user_origin where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    origin,
    id_security_user_origin,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_user_origin where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_security_user_origin set 
        origin = _origin,
        date_UMO=now(),user_UMO=_id_user where id_security_user_origin=_id_security_user_origin ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_security_user_origin set active=0,date_UMO=now(),user_UMO=_id_user where id_security_user_origin= _id_security_user_origin ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_security_user_origin;
    select count, msg from (
      select 0 as count, 'security_user_origin' as msg
      union all
      select count(*) as count, 'Users Profile' as msg from tb_security_users  where active = 1 and origin = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

