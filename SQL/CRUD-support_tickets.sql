
DROP PROCEDURE if exists dbname.CRUD_support_tickets;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_support_tickets(
    IN _tickets_user varchar(250),
    IN _tickets_status varchar(250),
    IN _tickets_date varchar(20),
    IN _description varchar(500),
    IN _id_user varchar(250),
    IN _id_support_tickets INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_support_tickets(
        tickets_user,
        tickets_status,
        tickets_date,
        description,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _tickets_user,
        _tickets_status,
        _tickets_date,
        _description,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    tickets_user,
    tickets_status,
    tickets_date,
    description,
    id_support_tickets,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_support_tickets where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    tickets_user,
    tickets_status,
    tickets_date,
    description,
    id_support_tickets,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_support_tickets where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_support_tickets set 
        tickets_user = _tickets_user,
        tickets_status = _tickets_status,
        tickets_date = _tickets_date,
        description = _description,
        date_UMO=now(),user_UMO=_id_user where id_support_tickets=_id_support_tickets ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_support_tickets set active=0,date_UMO=now(),user_UMO=_id_user where id_support_tickets= _id_support_tickets ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_support_tickets;
    select count, msg from (
      select 0 as count, 'support_tickets' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

