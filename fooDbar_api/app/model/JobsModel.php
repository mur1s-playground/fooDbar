<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class JobsModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_SCRIPT = 'Script';
	const FIELD_PARAMS = 'Params';
	const FIELD_STATUS = 'Status';
	const FIELD_DATETIME_START = 'DatetimeStart';
	const FIELD_DATETIME_END = 'DatetimeEnd';
	const FIELD_PID = 'Pid';
	const FIELD_RESULT = 'Result';

	/* int(11) */
	private $Id;

	/* varchar(255) */
	private $Script;

	/* blob */
	private $Params;

	/* varchar(255) */
	private $Status;

	/* datetime */
	private $DatetimeStart;

	/* datetime */
	private $DatetimeEnd;

	/* int(11) */
	private $Pid;

	/* blob */
	private $Result;


	public function __construct($values = null) {
		parent::__construct('jobs','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"Script":{"Field":"script","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Params":{"Field":"params","Type":"blob","Null":"YES","Key":"","Default":null,"Extra":""},"Status":{"Field":"status","Type":"varchar(255)","Null":"YES","Key":"","Default":null,"Extra":""},"DatetimeStart":{"Field":"datetime_start","Type":"datetime","Null":"YES","Key":"","Default":null,"Extra":""},"DatetimeEnd":{"Field":"datetime_end","Type":"datetime","Null":"YES","Key":"","Default":null,"Extra":""},"Pid":{"Field":"pid","Type":"int(11)","Null":"YES","Key":"","Default":null,"Extra":""},"Result":{"Field":"result","Type":"blob","Null":"YES","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return varchar(255) $this->Script */
	public function getScript() {
		return $this->Script;
	}
	/* @param varchar(255) $Script */
	public function setScript($Script) {
		$this->Script = $Script;
	}
	/* @return blob $this->Params */
	public function getParams() {
		return $this->Params;
	}
	/* @param blob $Params */
	public function setParams($Params) {
		$this->Params = $Params;
	}
	/* @return varchar(255) $this->Status */
	public function getStatus() {
		return $this->Status;
	}
	/* @param varchar(255) $Status */
	public function setStatus($Status) {
		$this->Status = $Status;
	}
	/* @return datetime $this->DatetimeStart */
	public function getDatetimeStart() {
		return $this->DatetimeStart;
	}
	/* @param datetime $DatetimeStart */
	public function setDatetimeStart($DatetimeStart) {
		$this->DatetimeStart = $DatetimeStart;
	}
	/* @return datetime $this->DatetimeEnd */
	public function getDatetimeEnd() {
		return $this->DatetimeEnd;
	}
	/* @param datetime $DatetimeEnd */
	public function setDatetimeEnd($DatetimeEnd) {
		$this->DatetimeEnd = $DatetimeEnd;
	}
	/* @return int(11) $this->Pid */
	public function getPid() {
		return $this->Pid;
	}
	/* @param int(11) $Pid */
	public function setPid($Pid) {
		$this->Pid = $Pid;
	}
	/* @return blob $this->Result */
	public function getResult() {
		return $this->Result;
	}
	/* @param blob $Result */
	public function setResult($Result) {
		$this->Result = $Result;
	}

}