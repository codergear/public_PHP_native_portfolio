
DROP PROCEDURE if exists dbname.CRUD_occupation;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_occupation(
    IN _occupation varchar(250),
    IN _id_user varchar(250),
    IN _id_occupation INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_occupation(
        occupation,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _occupation,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    occupation,
    id_occupation,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_occupation where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    occupation,
    id_occupation,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_occupation where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_occupation set 
        occupation = _occupation,
        date_UMO=now(),user_UMO=_id_user where id_occupation=_id_occupation ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_occupation set active=0,date_UMO=now(),user_UMO=_id_user where id_occupation= _id_occupation ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_occupation;
    select count, msg from (
      select 0 as count, 'occupation' as msg
      union all
      select count(*) as count, 'Affiliates' as msg from tb_affiliates  where active = 1 and occupation = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

