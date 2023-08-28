
DROP PROCEDURE if exists dbname.CRUD_mouthguards_models;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_mouthguards_models(
    IN _models varchar(250),
    IN _manufacturer varchar(250),
    IN _id_user varchar(250),
    IN _id_mouthguards_models INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_mouthguards_models(
        models,
        manufacturer,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _models,
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
    models,
    manufacturer,
    id_mouthguards_models,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_mouthguards_models where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    models,
    manufacturer,
    id_mouthguards_models,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_mouthguards_models where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_mouthguards_models set 
        models = _models,
        manufacturer = _manufacturer,
        date_UMO=now(),user_UMO=_id_user where id_mouthguards_models=_id_mouthguards_models ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_mouthguards_models set active=0,date_UMO=now(),user_UMO=_id_user where id_mouthguards_models= _id_mouthguards_models ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_mouthguards_models;
    select count, msg from (
      select 0 as count, 'mouthguards_models' as msg
      union all
      select count(*) as count, 'Mouthguards' as msg from tb_mouthguards  where active = 1 and model = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

