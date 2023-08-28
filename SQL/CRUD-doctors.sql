
DROP PROCEDURE if exists dbname.CRUD_doctors;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_doctors(
    IN _status varchar(250),
    IN _first_name varchar(250),
    IN _last_name varchar(250),
    IN _full_name varchar(250),
    IN _phone varchar(250),
    IN _email varchar(250),
    IN _preferred_method_of_contact varchar(250),
    IN _address varchar(250),
    IN _city varchar(250),
    IN _state varchar(250),
    IN _zip_code varchar(250),
    IN _group_detail varchar(5),
    IN _doctor_fee varchar(10),
    IN _linked_user varchar(250),
    IN _notes varchar(250),
    IN _image varchar(250),
    IN _filemanagerlist varchar(250),
    IN _id_user varchar(250),
    IN _id_doctors INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_doctors(
        status,
        first_name,
        last_name,
        full_name,
        phone,
        email,
        preferred_method_of_contact,
        address,
        city,
        state,
        zip_code,
        group_detail,
        doctor_fee,
        linked_user,
        notes,
        image,
        filemanagerlist,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _status,
        _first_name,
        _last_name,
        _full_name,
        _phone,
        _email,
        _preferred_method_of_contact,
        _address,
        _city,
        _state,
        _zip_code,
        _group_detail,
        _doctor_fee,
        _linked_user,
        _notes,
        _image,
        _filemanagerlist,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;

/*   CREATE MULTISELECT FACILITIES   */
  if _cmd = 12 then
    INSERT INTO  tb_doctors_facilities(id_doctors, id_facilities, active, user_UMO, date_UMO)VALUES(_id_doctors,_cmd_extend,1,_id_user,now());
  end if;

/*   CREATE MULTISELECT SPECIALTIES   */
  if _cmd = 13 then
    INSERT INTO  tb_doctors_specialties(id_doctors, id_specialties, active, user_UMO, date_UMO)VALUES(_id_doctors,_cmd_extend,1,_id_user,now());
  end if;

/*   CREATE MULTISELECT SURGICAL_COORDINATORS   */
  if _cmd = 14 then
    INSERT INTO  tb_doctors_surgical_coordinators(id_doctors, id_surgical_coordinators, active, user_UMO, date_UMO)VALUES(_id_doctors,_cmd_extend,1,_id_user,now());
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    status,
    first_name,
    last_name,
    full_name,
    phone,
    email,
    preferred_method_of_contact,
    address,
    city,
    state,
    zip_code,
    group_detail,
    doctor_fee,
    linked_user,
    notes,
    image,
    filemanagerlist,
    id_doctors,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_doctors where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    status,
    first_name,
    last_name,
    full_name,
    phone,
    email,
    preferred_method_of_contact,
    address,
    city,
    state,
    zip_code,
    group_detail,
    doctor_fee,
    linked_user,
    notes,
    image,
    filemanagerlist,
    id_doctors,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_doctors where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;

/*   READ MULTISELECT FACILITIES   */
  if _cmd = 22 then
    SELECT S.id_facilities, S.name from tb_doctors_facilities P
        left join tb_facilities S on S.id_facilities = P.id_facilities
        where P.id_doctors= _id_doctors ;
  end if;

/*   READ MULTISELECT SPECIALTIES   */
  if _cmd = 23 then
    SELECT S.id_specialties, S.specialties from tb_doctors_specialties P
        left join tb_specialties S on S.id_specialties = P.id_specialties
        where P.id_doctors= _id_doctors ;
  end if;

/*   READ MULTISELECT SURGICAL_COORDINATORS   */
  if _cmd = 24 then
    SELECT S.id_surgical_coordinators, S.full_name from tb_doctors_surgical_coordinators P
        left join tb_surgical_coordinators S on S.id_surgical_coordinators = P.id_surgical_coordinators
        where P.id_doctors= _id_doctors ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_doctors set 
        status = _status,
        first_name = _first_name,
        last_name = _last_name,
        full_name = _full_name,
        phone = _phone,
        email = _email,
        preferred_method_of_contact = _preferred_method_of_contact,
        address = _address,
        city = _city,
        state = _state,
        zip_code = _zip_code,
        group_detail = _group_detail,
        doctor_fee = _doctor_fee,
        linked_user = _linked_user,
        notes = _notes,
        image = _image,
        filemanagerlist = _filemanagerlist,
        date_UMO=now(),user_UMO=_id_user where id_doctors=_id_doctors ;
    delete from tb_doctors_facilities where id_doctors=_id_doctors;
    delete from tb_doctors_specialties where id_doctors=_id_doctors;
    delete from tb_doctors_surgical_coordinators where id_doctors=_id_doctors;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_doctors set active=0,date_UMO=now(),user_UMO=_id_user where id_doctors= _id_doctors ;
    delete from tb_doctors_facilities where id_doctors=_id_doctors;
    delete from tb_doctors_specialties where id_doctors=_id_doctors;
    delete from tb_doctors_surgical_coordinators where id_doctors=_id_doctors;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_doctors;
    select count, msg from (
      select 0 as count, 'doctors' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

