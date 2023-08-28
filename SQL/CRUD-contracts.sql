
DROP PROCEDURE if exists dbname.CRUD_contracts;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_contracts(
    IN _contract varchar(250),
    IN _contract_status varchar(250),
    IN _is_cancelled varchar(250),
    IN _contract_date varchar(250),
    IN _full_name varchar(250),
    IN _first_name nvarchar(250),
    IN _last_name nvarchar(250),
    IN _email nvarchar(250),
    IN _phone nvarchar(250),
    IN _address nvarchar(250),
    IN _city nvarchar(250),
    IN _state varchar(250),
    IN _zip_code varchar(250),
    IN _surgery_date varchar(20),
    IN _doctor varchar(5),
    IN _facility varchar(5),
    IN _plan varchar(5),
    IN _premium nvarchar(250),
    IN _payment_method varchar(5),
    IN _card_number varchar(20),
    IN _card_valid_to nvarchar(20),
    IN _card_cvv nvarchar(250),
    IN _routing_number nvarchar(250),
    IN _account_number nvarchar(250),
    IN _pay_by nvarchar(250),
    IN _coordinator_name varchar(250),
    IN _group_name varchar(250),
    IN _id_user varchar(250),
    IN _id_contracts INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_contracts(
        contract,
        contract_status,
        is_cancelled,
        contract_date,
        full_name,
        first_name,
        last_name,
        email,
        phone,
        address,
        city,
        state,
        zip_code,
        surgery_date,
        doctor,
        facility,
        plan,
        premium,
        payment_method,
        card_number,
        card_valid_to,
        card_cvv,
        routing_number,
        account_number,
        pay_by,
        coordinator_name,
        group_name,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _contract,
        _contract_status,
        _is_cancelled,
        _contract_date,
        _full_name,
        _first_name,
        _last_name,
        _email,
        _phone,
        _address,
        _city,
        _state,
        _zip_code,
        _surgery_date,
        _doctor,
        _facility,
        _plan,
        _premium,
        _payment_method,
        _card_number,
        _card_valid_to,
        _card_cvv,
        _routing_number,
        _account_number,
        _pay_by,
        _coordinator_name,
        _group_name,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    contract,
    contract_status,
    is_cancelled,
    contract_date,
    full_name,
    first_name,
    last_name,
    email,
    phone,
    address,
    city,
    state,
    zip_code,
    surgery_date,
    doctor,
    facility,
    plan,
    premium,
    payment_method,
    card_number,
    card_valid_to,
    card_cvv,
    routing_number,
    account_number,
    pay_by,
    coordinator_name,
    group_name,
    id_contracts,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_contracts where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    contract,
    contract_status,
    is_cancelled,
    contract_date,
    full_name,
    first_name,
    last_name,
    email,
    phone,
    address,
    city,
    state,
    zip_code,
    surgery_date,
    doctor,
    facility,
    plan,
    premium,
    payment_method,
    card_number,
    card_valid_to,
    card_cvv,
    routing_number,
    account_number,
    pay_by,
    coordinator_name,
    group_name,
    id_contracts,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_contracts where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_contracts set 
        contract = _contract,
        contract_status = _contract_status,
        is_cancelled = _is_cancelled,
        contract_date = _contract_date,
        full_name = _full_name,
        first_name = _first_name,
        last_name = _last_name,
        email = _email,
        phone = _phone,
        address = _address,
        city = _city,
        state = _state,
        zip_code = _zip_code,
        surgery_date = _surgery_date,
        doctor = _doctor,
        facility = _facility,
        plan = _plan,
        premium = _premium,
        payment_method = _payment_method,
        card_number = _card_number,
        card_valid_to = _card_valid_to,
        card_cvv = _card_cvv,
        routing_number = _routing_number,
        account_number = _account_number,
        pay_by = _pay_by,
        coordinator_name = _coordinator_name,
        group_name = _group_name,
        date_UMO=now(),user_UMO=_id_user where id_contracts=_id_contracts ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_contracts set active=0,date_UMO=now(),user_UMO=_id_user where id_contracts= _id_contracts ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_contracts;
    select count, msg from (
      select 0 as count, 'contracts' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

