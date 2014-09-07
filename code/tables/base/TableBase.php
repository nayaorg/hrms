<?php
abstract class Table extends TableBase  {
	const C_WS_ID = "WORKSTATION_ID" ;
	const C_MODIFY_BY = "MODIFY_BY";
	const C_MODIFY_DATE = "MODIFY_DATE";
	const C_CREATE_BY = "CREATE_BY";
	const C_CREATE_DATE = "CREATE_DATE";
}
abstract class TableBase {
	const C_ORG_ID = "ORG_ID" ;
	const C_COY_ID = "COY_ID";
}
?>