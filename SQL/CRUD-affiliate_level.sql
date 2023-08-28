
DROP PROCEDURE if exists dbname.CRUD_affiliate_level;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_affiliate_level(
    IN _affiliate_level varchar(10),
    IN _description varchar(250),
    IN _id_user varchar(250),
    IN _id_affiliate_level INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_affiliate_level(
        affiliate_level,
        description,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _affiliate_level,
        _description,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    affiliate_level,
    description,
    id_affiliate_level,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_affiliate_level where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    affiliate_level,
    description,
    id_affiliate_level,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_affiliate_level where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_affiliate_level set 
        affiliate_level = _affiliate_level,
        description = _description,
        date_UMO=now(),user_UMO=_id_user where id_affiliate_level=_id_affiliate_level ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_affiliate_level set active=0,date_UMO=now(),user_UMO=_id_user where id_affiliate_level= _id_affiliate_level ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_affiliate_level;
    select count, msg from (
      select 0 as count, 'affiliate_level' as msg
      union all
      select count(*) as count, 'Affiliates' as msg from tb_affiliates  where active = 1 and afiliate_level = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

