<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class EmailModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_DATETIME = 'Datetime';
	const FIELD_SUBJECT = 'Subject';
	const FIELD_TOADDRESS = 'Toaddress';
	const FIELD_FROMADDRESS = 'Fromaddress';
	const FIELD_MSG = 'Msg';
	const FIELD_STATUS = 'Status';
	const FIELD_STATUS_MSG = 'StatusMsg';

	/* int(11) */
	private $Id;

	/* datetime */
	private $Datetime;

	/* varchar(255) */
	private $Subject;

	/* varchar(255) */
	private $Toaddress;

	/* varchar(255) */
	private $Fromaddress;

	/* mediumblob */
	private $Msg;

	/* int(11) */
	private $Status;

	/* blob */
	private $StatusMsg;


	public function __construct($values = null) {
		parent::__construct('email','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"Datetime":{"Field":"datetime","Type":"datetime","Null":"NO","Key":"","Default":null,"Extra":""},"Subject":{"Field":"subject","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Toaddress":{"Field":"toaddress","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Fromaddress":{"Field":"fromaddress","Type":"varchar(255)","Null":"NO","Key":"","Default":null,"Extra":""},"Msg":{"Field":"msg","Type":"mediumblob","Null":"NO","Key":"","Default":null,"Extra":""},"Status":{"Field":"status","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"StatusMsg":{"Field":"status_msg","Type":"blob","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return datetime $this->Datetime */
	public function getDatetime() {
		return $this->Datetime;
	}
	/* @param datetime $Datetime */
	public function setDatetime($Datetime) {
		$this->Datetime = $Datetime;
	}
	/* @return varchar(255) $this->Subject */
	public function getSubject() {
		return $this->Subject;
	}
	/* @param varchar(255) $Subject */
	public function setSubject($Subject) {
		$this->Subject = $Subject;
	}
	/* @return varchar(255) $this->Toaddress */
	public function getToaddress() {
		return $this->Toaddress;
	}
	/* @param varchar(255) $Toaddress */
	public function setToaddress($Toaddress) {
		$this->Toaddress = $Toaddress;
	}
	/* @return varchar(255) $this->Fromaddress */
	public function getFromaddress() {
		return $this->Fromaddress;
	}
	/* @param varchar(255) $Fromaddress */
	public function setFromaddress($Fromaddress) {
		$this->Fromaddress = $Fromaddress;
	}
	/* @return mediumblob $this->Msg */
	public function getMsg() {
		return $this->Msg;
	}
	/* @param mediumblob $Msg */
	public function setMsg($Msg) {
		$this->Msg = $Msg;
	}
	/* @return int(11) $this->Status */
	public function getStatus() {
		return $this->Status;
	}
	/* @param int(11) $Status */
	public function setStatus($Status) {
		$this->Status = $Status;
	}
	/* @return blob $this->StatusMsg */
	public function getStatusMsg() {
		return $this->StatusMsg;
	}
	/* @param blob $StatusMsg */
	public function setStatusMsg($StatusMsg) {
		$this->StatusMsg = $StatusMsg;
	}

}