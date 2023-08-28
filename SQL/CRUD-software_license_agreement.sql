
DROP PROCEDURE if exists dbname.CRUD_software_license_agreement;

DELIMITER [PROCEDURE]
CREATE PROCEDURE dbname.CRUD_software_license_agreement(
    IN _document_content longtext,
    IN _id_user varchar(250),
    IN _id_software_license_agreement INT UNSIGNED,
    IN _cmd_extend varchar(250),
    IN _cmd INT UNSIGNED)
BEGIN
/* CREATE */
  if _cmd = 1 then
    INSERT INTO  tb_software_license_agreement(
        document_content,
        active,
        user_UMO,
        date_UMO
    )VALUES( 
        _document_content,
        '1',
        _id_user,
        now()
    ); 

    select @@identity as last_id;
  end if;


/*   READ   */
  if _cmd = 2 then
    SELECT 
    document_content,
    id_software_license_agreement,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_software_license_agreement where active=1 order by date_UMO desc;
  end if;

/*   READ BY   */
  if _cmd = 21 then
    set @param = _cmd_extend;
    set @Expression = CONCAT("SELECT 
    document_content,
    id_software_license_agreement,
    user_umo as '_user_umo',
    date_umo as '_date_umo'
    FROM tb_software_license_agreement where active=1 and ",@param," order by date_UMO desc; ");
    PREPARE expression_query FROM @Expression;
    EXECUTE expression_query ;
  end if;


/*  UPDATE  */
  if _cmd = 3 then
    update tb_software_license_agreement set 
        document_content = _document_content,
        date_UMO=now(),user_UMO=_id_user where id_software_license_agreement=_id_software_license_agreement ;
  end if;

/*  DELETE  */ 
  if _cmd = 4 then
    update tb_software_license_agreement set active=0,date_UMO=now(),user_UMO=_id_user where id_software_license_agreement= _id_software_license_agreement ;
  end if;

/*  DELETE Referential integrity  */ 
  if _cmd = 41 then
    set @item_id = _id_software_license_agreement;
    select count, msg from (
      select 0 as count, 'software_license_agreement' as msg
    ) tbl;
  end if;

end;
[PROCEDURE]
DELIMITER ;

