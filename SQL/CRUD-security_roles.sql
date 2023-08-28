
DROP PROCEDURE if exists dbname.CRUD_security_roles;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_security_roles(
    IN _name varchar(250),
    IN _id_user varchar(250),
    IN _id_security_roles INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_security_roles(
        name,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _name,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    name,
    id_security_roles,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_roles where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    name,
    id_security_roles,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_roles where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_security_roles set 
        name = _name,
        date_UMO=now(),user_UMO=_id_user where id_security_roles=_id_security_roles ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_security_roles set active=0,date_UMO=now(),user_UMO=_id_user where id_security_roles= _id_security_roles ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_security_roles;
    select count, msg from (
      select 0 as count, 'security_roles' as msg
      union all
      select count(*) as count, 'Users Profile' as msg from tb_security_users  where active = 1 and role = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

