
DROP PROCEDURE if exists dbname.CRUD_group_principal_terms_conditions;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_group_principal_terms_conditions(
    IN _principal_group nvarchar(250),
    IN _doc_1 varchar(250),
    IN _doc_2 varchar(250),
    IN _doc_3 varchar(250),
    IN _id_user varchar(250),
    IN _id_group_principal_terms_conditions INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_group_principal_terms_conditions(
        principal_group,
        doc_1,
        doc_2,
        doc_3,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _principal_group,
        _doc_1,
        _doc_2,
        _doc_3,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    principal_group,
    doc_1,
    doc_2,
    doc_3,
    id_group_principal_terms_conditions,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_group_principal_terms_conditions where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    principal_group,
    doc_1,
    doc_2,
    doc_3,
    id_group_principal_terms_conditions,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_group_principal_terms_conditions where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_group_principal_terms_conditions set 
        principal_group = _principal_group,
        doc_1 = _doc_1,
        doc_2 = _doc_2,
        doc_3 = _doc_3,
        date_UMO=now(),user_UMO=_id_user where id_group_principal_terms_conditions=_id_group_principal_terms_conditions ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_group_principal_terms_conditions set active=0,date_UMO=now(),user_UMO=_id_user where id_group_principal_terms_conditions= _id_group_principal_terms_conditions ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_group_principal_terms_conditions;
    select count, msg from (
      select 0 as count, 'group_principal_terms_conditions' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

