
DROP PROCEDURE if exists dbname.CRUD_security_users;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_security_users(
    IN _users_status varchar(250),
    IN _user varchar(250),
    IN _pass varchar(250),
    IN _full_name varchar(250),
    IN _origin varchar(250),
    IN _phone varchar(250),
    IN _email varchar(250),
    IN _role varchar(250),
    IN _photo varchar(250),
    IN _id_user varchar(250),
    IN _id_security_users INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_security_users(
        users_status,
        user,
        pass,
        full_name,
        origin,
        phone,
        email,
        role,
        photo,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _users_status,
        _user,
        _pass,
        _full_name,
        _origin,
        _phone,
        _email,
        _role,
        _photo,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    users_status,
    user,
    pass,
    full_name,
    origin,
    phone,
    email,
    role,
    photo,
    id_security_users,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_users where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    users_status,
    user,
    pass,
    full_name,
    origin,
    phone,
    email,
    role,
    photo,
    id_security_users,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_users where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_security_users set 
        users_status = _users_status,
        user = _user,
        pass = _pass,
        full_name = _full_name,
        origin = _origin,
        phone = _phone,
        email = _email,
        role = _role,
        photo = _photo,
        date_UMO=now(),user_UMO=_id_user where id_security_users=_id_security_users ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_security_users set active=0,date_UMO=now(),user_UMO=_id_user where id_security_users= _id_security_users ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_security_users;
    select count, msg from (
      select 0 as count, 'security_users' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

