
DROP PROCEDURE if exists dbname.CRUD_mouthguards_manufacturer;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_mouthguards_manufacturer(
    IN _manufacturer varchar(250),
    IN _id_user varchar(250),
    IN _id_mouthguards_manufacturer INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_mouthguards_manufacturer(
        manufacturer,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _manufacturer,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    manufacturer,
    id_mouthguards_manufacturer,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_mouthguards_manufacturer where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    manufacturer,
    id_mouthguards_manufacturer,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_mouthguards_manufacturer where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_mouthguards_manufacturer set 
        manufacturer = _manufacturer,
        date_UMO=now(),user_UMO=_id_user where id_mouthguards_manufacturer=_id_mouthguards_manufacturer ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_mouthguards_manufacturer set active=0,date_UMO=now(),user_UMO=_id_user where id_mouthguards_manufacturer= _id_mouthguards_manufacturer ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_mouthguards_manufacturer;
    select count, msg from (
      select 0 as count, 'mouthguards_manufacturer' as msg
      union all
      select count(*) as count, 'Mouthguards Models' as msg from tb_mouthguards_models  where active = 1 and manufacturer = @item_id
      union all
      select count(*) as count, 'Mouthguards' as msg from tb_mouthguards  where active = 1 and manufacturer = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

