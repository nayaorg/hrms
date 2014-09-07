<?php
class AttendanceTable extends Table {
	const C_TABLE = "ATTENDANCE" ;
	const C_ID = "ATT_DATE, ATT_EMP_ID";
	const C_EMP_ID = "ATT_EMP_ID";
	const C_DATE = "ATT_DATE";
	const C_TIME_START = "ATT_TIME_START";
	const C_TIME_END = "ATT_TIME_END";
	const C_BREAK_START = "ATT_BREAK_START";
	const C_BREAK_END = "ATT_BREAK_END";
	//const C_OT_START = "ATT_OVERTIME_START";
	//const C_OT_END = "ATT_OVERTIME_END";
	const C_LATE_IN = "ATT_LATE_IN";
	const C_EARLY_OUT = "ATT_EARLY_OUT";
}
?>