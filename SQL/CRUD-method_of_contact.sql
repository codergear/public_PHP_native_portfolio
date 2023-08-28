
DROP PROCEDURE if exists dbname.CRUD_method_of_contact;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_method_of_contact(
    IN _method_of_contact varchar(50),
    IN _id_user varchar(250),
    IN _id_method_of_contact INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_method_of_contact(
        method_of_contact,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _method_of_contact,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    method_of_contact,
    id_method_of_contact,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_method_of_contact where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    method_of_contact,
    id_method_of_contact,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_method_of_contact where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_method_of_contact set 
        method_of_contact = _method_of_contact,
        date_UMO=now(),user_UMO=_id_user where id_method_of_contact=_id_method_of_contact ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_method_of_contact set active=0,date_UMO=now(),user_UMO=_id_user where id_method_of_contact= _id_method_of_contact ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_method_of_contact;
    select count, msg from (
      select 0 as count, 'method_of_contact' as msg
      union all
      select count(*) as count, 'Affiliates' as msg from tb_affiliates  where active = 1 and preferred_method_of_contact = @item_id
      union all
      select count(*) as count, 'Doctors' as msg from tb_doctors  where active = 1 and preferred_method_of_contact = @item_id
      union all
      select count(*) as count, 'Surgical Coordinators' as msg from tb_surgical_coordinators  where active = 1 and preferred_method_of_contact = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

