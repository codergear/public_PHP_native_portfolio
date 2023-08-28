
DROP PROCEDURE if exists dbname.CRUD_security_menu;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_security_menu(
    IN _fa_icon varchar(250),
    IN _name varchar(250),
    IN _location varchar(250),
    IN _parameter varchar(250),
    IN _id_user varchar(250),
    IN _id_security_menu INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_security_menu(
        fa_icon,
        name,
        location,
        parameter,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _fa_icon,
        _name,
        _location,
        _parameter,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    fa_icon,
    name,
    location,
    parameter,
    id_security_menu,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_menu where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    fa_icon,
    name,
    location,
    parameter,
    id_security_menu,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_menu where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_security_menu set 
        fa_icon = _fa_icon,
        name = _name,
        location = _location,
        parameter = _parameter,
        date_UMO=now(),user_UMO=_id_user where id_security_menu=_id_security_menu ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_security_menu set active=0,date_UMO=now(),user_UMO=_id_user where id_security_menu= _id_security_menu ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_security_menu;
    select count, msg from (
      select 0 as count, 'security_menu' as msg
      union all
      select count(*) as count, 'Sub menu' as msg from tb_security_sub_menu  where active = 1 and main_menu = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

