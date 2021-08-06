<?php

namespace FooDBar;

require_once "/media/public/fooDbar_api/vendor/reinerlanz/frame/src/DB/DBTable.php";

class StoragesMembershipModel extends \Frame\DBTable {

	const FIELD_ID = 'Id';
	const FIELD_STORAGES_ID = 'StoragesId';
	const FIELD_USERS_ID = 'UsersId';

	/* int(11) */
	private $Id;

	/* int(11) */
	private $StoragesId;

	/* int(11) */
	private $UsersId;


	public function __construct($values = null) {
		parent::__construct('storages_membership','{"Id":{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"},"StoragesId":{"Field":"storages_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""},"UsersId":{"Field":"users_id","Type":"int(11)","Null":"NO","Key":"","Default":null,"Extra":""}}', $values);
	}

	/* @return int(11) $this->Id */
	public function getId() {
		return $this->Id;
	}
	/* @param int(11) $Id */
	public function setId($Id) {
		$this->Id = $Id;
	}
	/* @return int(11) $this->StoragesId */
	public function getStoragesId() {
		return $this->StoragesId;
	}
	/* @param int(11) $StoragesId */
	public function setStoragesId($StoragesId) {
		$this->StoragesId = $StoragesId;
	}
	/* @return int(11) $this->UsersId */
	public function getUsersId() {
		return $this->UsersId;
	}
	/* @param int(11) $UsersId */
	public function setUsersId($UsersId) {
		$this->UsersId = $UsersId;
	}

}