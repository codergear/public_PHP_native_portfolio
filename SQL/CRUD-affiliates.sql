
DROP PROCEDURE if exists dbname.CRUD_affiliates;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_affiliates(
    IN _status varchar(250),
    IN _agreement_date varchar(20),
    IN _afiliate_level varchar(5),
    IN _manager_afiliate varchar(250),
    IN _affiliate_to varchar(250),
    IN _first_name varchar(250),
    IN _last_name varchar(250),
    IN _full_name varchar(250),
    IN _birth_date varchar(20),
    IN _phone varchar(250),
    IN _email varchar(250),
    IN _preferred_method_of_contact varchar(250),
    IN _occupation varchar(250),
    IN _company varchar(250),
    IN _commission varchar(250),
    IN _address varchar(250),
    IN _city varchar(250),
    IN _state varchar(250),
    IN _zip_code varchar(250),
    IN _notes varchar(250),
    IN _picture varchar(250),
    IN _filemanagerlist varchar(250),
    IN _linked_user varchar(250),
    IN _id_user varchar(250),
    IN _id_affiliates INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_affiliates(
        status,
        agreement_date,
        afiliate_level,
        manager_afiliate,
        affiliate_to,
        first_name,
        last_name,
        full_name,
        birth_date,
        phone,
        email,
        preferred_method_of_contact,
        occupation,
        company,
        commission,
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
        _agreement_date,
        _afiliate_level,
        _manager_afiliate,
        _affiliate_to,
        _first_name,
        _last_name,
        _full_name,
        _birth_date,
        _phone,
        _email,
        _preferred_method_of_contact,
        _occupation,
        _company,
        _commission,
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
    agreement_date,
    afiliate_level,
    manager_afiliate,
    affiliate_to,
    first_name,
    last_name,
    full_name,
    birth_date,
    phone,
    email,
    preferred_method_of_contact,
    occupation,
    company,
    commission,
    address,
    city,
    state,
    zip_code,
    notes,
    picture,
    filemanagerlist,
    linked_user,
    id_affiliates,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_affiliates where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    status,
    agreement_date,
    afiliate_level,
    manager_afiliate,
    affiliate_to,
    first_name,
    last_name,
    full_name,
    birth_date,
    phone,
    email,
    preferred_method_of_contact,
    occupation,
    company,
    commission,
    address,
    city,
    state,
    zip_code,
    notes,
    picture,
    filemanagerlist,
    linked_user,
    id_affiliates,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_affiliates where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_affiliates set 
        status = _status,
        agreement_date = _agreement_date,
        afiliate_level = _afiliate_level,
        manager_afiliate = _manager_afiliate,
        affiliate_to = _affiliate_to,
        first_name = _first_name,
        last_name = _last_name,
        full_name = _full_name,
        birth_date = _birth_date,
        phone = _phone,
        email = _email,
        preferred_method_of_contact = _preferred_method_of_contact,
        occupation = _occupation,
        company = _company,
        commission = _commission,
        address = _address,
        city = _city,
        state = _state,
        zip_code = _zip_code,
        notes = _notes,
        picture = _picture,
        filemanagerlist = _filemanagerlist,
        linked_user = _linked_user,
        date_UMO=now(),user_UMO=_id_user where id_affiliates=_id_affiliates ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_affiliates set active=0,date_UMO=now(),user_UMO=_id_user where id_affiliates= _id_affiliates ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_affiliates;
    select count, msg from (
      select 0 as count, 'affiliates' as msg
      union all
      select count(*) as count, 'Affiliates' as msg from tb_affiliates  where active = 1 and affiliate_to = @item_id
      union all
      select count(*) as count, 'Groups' as msg from tb_groups  where active = 1 and afiliate = @item_id
      union all
      select count(*) as count, 'Afiliate Pay Processing' as msg from tb_afiliate_pay_processing  where active = 1 and payee = @item_id
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

