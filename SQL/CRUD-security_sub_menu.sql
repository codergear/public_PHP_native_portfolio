
DROP PROCEDURE if exists dbname.CRUD_security_sub_menu;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_security_sub_menu(
    IN _main_menu varchar(250),
    IN _name varchar(250),
    IN _location varchar(250),
    IN _parameter varchar(250),
    IN _id_user varchar(250),
    IN _id_security_sub_menu INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_security_sub_menu(
        main_menu,
        name,
        location,
        parameter,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _main_menu,
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
    main_menu,
    name,
    location,
    parameter,
    id_security_sub_menu,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_sub_menu where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    main_menu,
    name,
    location,
    parameter,
    id_security_sub_menu,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_security_sub_menu where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_security_sub_menu set 
        main_menu = _main_menu,
        name = _name,
        location = _location,
        parameter = _parameter,
        date_UMO=now(),user_UMO=_id_user where id_security_sub_menu=_id_security_sub_menu ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_security_sub_menu set active=0,date_UMO=now(),user_UMO=_id_user where id_security_sub_menu= _id_security_sub_menu ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_security_sub_menu;
    select count, msg from (
      select 0 as count, 'security_sub_menu' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

