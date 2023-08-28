
DROP PROCEDURE if exists dbname.CRUD_affiliates_status;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_affiliates_status(
    IN _status varchar(250),
    IN _id_user varchar(250),
    IN _id_affiliates_status INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_affiliates_status(
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
    id_affiliates_status,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_affiliates_status where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    status,
    id_affiliates_status,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_affiliates_status where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_affiliates_status set 
        status = _status,
        date_UMO=now(),user_UMO=_id_user where id_affiliates_status=_id_affiliates_status ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_affiliates_status set active=0,date_UMO=now(),user_UMO=_id_user where id_affiliates_status= _id_affiliates_status ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_affiliates_status;
    select count, msg from (
      select 0 as count, 'affiliates_status' as msg
      union all
      select count(*) as count, 'Affiliates' as msg from tb_affiliates  where active = 1 and status = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

