
DROP PROCEDURE if exists dbname.CRUD_security_access_log;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_security_access_log(
    IN _user_id varchar(250),
    IN _https varchar(250),
    IN _user_agent varchar(250),
    IN _accept_language varchar(250),
    IN _remote_addr varchar(250),
    IN _request_time varchar(250),
    IN _request_module varchar(250),
    IN _request_action varchar(250),
    IN _request_record_id varchar(250),
    IN _id_user varchar(250),
    IN _id_security_access_log INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_security_access_log(
        user_id,
        https,
        user_agent,
        accept_language,
        remote_addr,
        request_time,
        request_module,
        request_action,
        request_record_id,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _user_id,
        _https,
        _user_agent,
        _accept_language,
        _remote_addr,
        _request_time,
        _request_module,
        _request_action,
        _request_record_id,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    user_id,
    https,
    user_agent,
    accept_language,
    remote_addr,
    request_time,
    request_module,
    request_action,
    request_record_id,
    id_security_access_log,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_access_log where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    user_id,
    https,
    user_agent,
    accept_language,
    remote_addr,
    request_time,
    request_module,
    request_action,
    request_record_id,
    id_security_access_log,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_access_log where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_security_access_log set 
        user_id = _user_id,
        https = _https,
        user_agent = _user_agent,
        accept_language = _accept_language,
        remote_addr = _remote_addr,
        request_time = _request_time,
        request_module = _request_module,
        request_action = _request_action,
        request_record_id = _request_record_id,
        date_UMO=now(),user_UMO=_id_user where id_security_access_log=_id_security_access_log ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_security_access_log set active=0,date_UMO=now(),user_UMO=_id_user where id_security_access_log= _id_security_access_log ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_security_access_log;
    select count, msg from (
      select 0 as count, 'security_access_log' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

