
DROP PROCEDURE if exists dbname.CRUD_mouthguards;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_mouthguards(
    IN _manufacturer varchar(250),
    IN _model varchar(250),
    IN _notes varchar(250),
    IN _id_user varchar(250),
    IN _id_mouthguards INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_mouthguards(
        manufacturer,
        model,
        notes,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _manufacturer,
        _model,
        _notes,
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
    model,
    notes,
    id_mouthguards,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_mouthguards where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    manufacturer,
    model,
    notes,
    id_mouthguards,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_mouthguards where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_mouthguards set 
        manufacturer = _manufacturer,
        model = _model,
        notes = _notes,
        date_UMO=now(),user_UMO=_id_user where id_mouthguards=_id_mouthguards ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_mouthguards set active=0,date_UMO=now(),user_UMO=_id_user where id_mouthguards= _id_mouthguards ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_mouthguards;
    select count, msg from (
      select 0 as count, 'mouthguards' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

