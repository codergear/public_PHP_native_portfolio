
DROP PROCEDURE if exists dbname.CRUD_states;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_states(
    IN _state_name varchar(50),
    IN _state_code varchar(5),
    IN _id_user varchar(250),
    IN _id_states INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_states(
        state_name,
        state_code,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _state_name,
        _state_code,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    state_name,
    state_code,
    id_states,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_states where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    state_name,
    state_code,
    id_states,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_states where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_states set 
        state_name = _state_name,
        state_code = _state_code,
        date_UMO=now(),user_UMO=_id_user where id_states=_id_states ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_states set active=0,date_UMO=now(),user_UMO=_id_user where id_states= _id_states ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_states;
    select count, msg from (
      select 0 as count, 'states' as msg
      union all
      select count(*) as count, 'Affiliates' as msg from tb_affiliates  where active = 1 and state = @item_id
      union all
      select count(*) as count, 'Doctors' as msg from tb_doctors  where active = 1 and state = @item_id
      union all
      select count(*) as count, 'Surgical Coordinators' as msg from tb_surgical_coordinators  where active = 1 and state = @item_id
      union all
      select count(*) as count, 'Facilities' as msg from tb_facilities  where active = 1 and state = @item_id
      union all
      select count(*) as count, 'Groups' as msg from tb_groups  where active = 1 and state = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

