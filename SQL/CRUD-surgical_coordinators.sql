
DROP PROCEDURE if exists dbname.CRUD_surgical_coordinators;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_surgical_coordinators(
    IN _status varchar(250),
    IN _first_name varchar(250),
    IN _last_name varchar(250),
    IN _full_name varchar(250),
    IN _birthday varchar(20),
    IN _phone varchar(250),
    IN _email varchar(250),
    IN _preferred_method_of_contact varchar(250),
    IN _group_detail varchar(250),
    IN _group_manager varchar(250),
    IN _address varchar(250),
    IN _city varchar(250),
    IN _state varchar(250),
    IN _zip_code varchar(250),
    IN _notes varchar(250),
    IN _picture varchar(250),
    IN _filemanagerlist varchar(250),
    IN _linked_user varchar(250),
    IN _id_user varchar(250),
    IN _id_surgical_coordinators INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_surgical_coordinators(
        status,
        first_name,
        last_name,
        full_name,
        birthday,
        phone,
        email,
        preferred_method_of_contact,
        group_detail,
        group_manager,
        address,
        city,
        state,
        zip_code,
        notes,
        picture,
        filemanagerlist,
        linked_user,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _status,
        _first_name,
        _last_name,
        _full_name,
        _birthday,
        _phone,
        _email,
        _preferred_method_of_contact,
        _group_detail,
        _group_manager,
        _address,
        _city,
        _state,
        _zip_code,
        _notes,
        _picture,
        _filemanagerlist,
        _linked_user,
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
    first_name,
    last_name,
    full_name,
    birthday,
    phone,
    email,
    preferred_method_of_contact,
    group_detail,
    group_manager,
    address,
    city,
    state,
    zip_code,
    notes,
    picture,
    filemanagerlist,
    linked_user,
    id_surgical_coordinators,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_surgical_coordinators where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    status,
    first_name,
    last_name,
    full_name,
    birthday,
    phone,
    email,
    preferred_method_of_contact,
    group_detail,
    group_manager,
    address,
    city,
    state,
    zip_code,
    notes,
    picture,
    filemanagerlist,
    linked_user,
    id_surgical_coordinators,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_surgical_coordinators where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_surgical_coordinators set 
        status = _status,
        first_name = _first_name,
        last_name = _last_name,
        full_name = _full_name,
        birthday = _birthday,
        phone = _phone,
        email = _email,
        preferred_method_of_contact = _preferred_method_of_contact,
        group_detail = _group_detail,
        group_manager = _group_manager,
        address = _address,
        city = _city,
        state = _state,
        zip_code = _zip_code,
        notes = _notes,
        picture = _picture,
        filemanagerlist = _filemanagerlist,
        linked_user = _linked_user,
        date_UMO=now(),user_UMO=_id_user where id_surgical_coordinators=_id_surgical_coordinators ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_surgical_coordinators set active=0,date_UMO=now(),user_UMO=_id_user where id_surgical_coordinators= _id_surgical_coordinators ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_surgical_coordinators;
    select count, msg from (
      select 0 as count, 'surgical_coordinators' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

