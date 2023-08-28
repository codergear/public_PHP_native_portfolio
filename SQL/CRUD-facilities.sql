
DROP PROCEDURE if exists dbname.CRUD_facilities;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_facilities(
    IN _name varchar(250),
    IN _phone varchar(250),
    IN _email varchar(250),
    IN _contact_person varchar(250),
    IN _address varchar(250),
    IN _city varchar(250),
    IN _state varchar(250),
    IN _zip_code varchar(250),
    IN _notes varchar(250),
    IN _logo varchar(250),
    IN _id_user varchar(250),
    IN _id_facilities INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_facilities(
        name,
        phone,
        email,
        contact_person,
        address,
        city,
        state,
        zip_code,
        notes,
        logo,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _name,
        _phone,
        _email,
        _contact_person,
        _address,
        _city,
        _state,
        _zip_code,
        _notes,
        _logo,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    name,
    phone,
    email,
    contact_person,
    address,
    city,
    state,
    zip_code,
    notes,
    logo,
    id_facilities,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_facilities where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    name,
    phone,
    email,
    contact_person,
    address,
    city,
    state,
    zip_code,
    notes,
    logo,
    id_facilities,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_facilities where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_facilities set 
        name = _name,
        phone = _phone,
        email = _email,
        contact_person = _contact_person,
        address = _address,
        city = _city,
        state = _state,
        zip_code = _zip_code,
        notes = _notes,
        logo = _logo,
        date_UMO=now(),user_UMO=_id_user where id_facilities=_id_facilities ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_facilities set active=0,date_UMO=now(),user_UMO=_id_user where id_facilities= _id_facilities ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_facilities;
    select count, msg from (
      select 0 as count, 'facilities' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

