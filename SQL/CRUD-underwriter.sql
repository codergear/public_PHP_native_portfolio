
DROP PROCEDURE if exists dbname.CRUD_underwriter;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_underwriter(
    IN _underwriter varchar(250),
    IN _id_user varchar(250),
    IN _id_underwriter INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_underwriter(
        underwriter,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _underwriter,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    underwriter,
    id_underwriter,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_underwriter where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    underwriter,
    id_underwriter,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_underwriter where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_underwriter set 
        underwriter = _underwriter,
        date_UMO=now(),user_UMO=_id_user where id_underwriter=_id_underwriter ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_underwriter set active=0,date_UMO=now(),user_UMO=_id_user where id_underwriter= _id_underwriter ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_underwriter;
    select count, msg from (
      select 0 as count, 'underwriter' as msg
      union all
      select count(*) as count, 'Underwriter Pay Processing' as msg from tb_underwriter_pay_processing  where active = 1 and payee = @item_id
      union all
      select count(*) as count, 'Plans' as msg from tb_plans  where active = 1 and underwriter = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

