<?php

namespace FooDBar\Jobs;

require __DIR__ . "/../../vendor/reinerlanz/frame/src/Job.php";

use \Frame\Job as Job;

use \Frame\Condition            as Condition;

use \FooDBar\EmailModel					as EmailModel;
use \FooDBar\UsersModel					as UsersModel;
use \FooDBar\ProductsSourceModel			as ProductsSourceModel;
use \FooDBar\AmountTypeModel				as AmountTypeModel;
use \FooDBar\EbonProductsModel				as EbonProductsModel;
use \FooDBar\EbonProductsParsedModel			as EbonProductsParsedModel;

use \FooDBar\App\StatusController as StatusController;

class UpdateEMailJob extends Job {
	const APP_STATUS_EMAIL = "EMAIL";

	const EMAIL_STATUS_RECEIVED 	= 0;
	const EMAIL_STATUS_TYPE 	= 1;
	const EMAIL_STATUS_PROCESSED	= 2;

	const EMAIL_TYPE_REWE_EBON 	= 0;

	public static function get_msg_part_text($mbox, $msgno, $partno, $p) {
		if ($partno == 0) {
			$data = imap_body($mbox, $msgno);
		} else {
			$data = imap_fetchbody($mbox, $msgno, $partno);
		}

		if ($p->encoding == ENCQUOTEDPRINTABLE) {
			$data = qouted_printable_decode($data);
		} else if ($p->encoding == ENCBASE64) {
			$data = base64_decode($data);
		}

		$result = array();
		if ($data && ($p->type == TYPETEXT || $p->type == TYPEMESSAGE)) {
			$result[$partno] = $data;
		}

		if (isset($p->{"parts"})) {
			foreach ($p->parts as $partno_s => $p_s) {
				$p_no_s = $partno . '.' . ($partno_s + 1);
				$result = array_merge($result, self::get_msg_part_text($mbox, $msgno, $p_no_s, $p));
			}
		}

		return $result;
	}

	public function get_mail() {
		$host = $this->config->getConfigValue(array('email', 'host'));
		$port = $this->config->getConfigValue(array('email', 'port'));
		$flags = $this->config->getConfigValue(array('email', 'flags'));
		$folder = $this->config->getConfigValue(array('email', 'folder'));
		$user = $this->config->getConfigValue(array('email', 'user'));
		$password = $this->config->getConfigValue(array('email', 'password'));

		$mbox = imap_open("{" . $host . ":" . $port . $flags . "}" . $folder, $user, $password);

		$headers = imap_headers($mbox);

		$result = array();
		$result["status"] = false;
		if ($headers == false) {
		} else {
			$msgs = imap_sort($mbox, SORTDATE, 1, SE_UID);

			foreach ($msgs as $msguid) {
				$msgno = imap_msgno($mbox, $msguid);
				$header = imap_headerinfo($mbox, $msgno);
				if ($header->{"Unseen"} == "U" || $header->{"Recent"} == "N") {
					$date = date("Y-m-d H:i:s", $header->{"udate"});
					$subject = $header->{"subject"};
					$toaddress = $header->{"to"}[0]->{"mailbox"} . "@" . $header->{"to"}[0]->{"host"};
					$fromaddress = $header->{"from"}[0]->{"mailbox"} . "@" . $header->{"from"}[0]->{"host"};

					$structure = imap_fetchstructure($mbox, $msgno);
					$msg = array();
					if (!isset($structure->{"parts"})) {
						$msg = self::get_msg_part_text($mbox, $msgno, 0, $structure);
					} else {
						foreach ($structure->parts as $partno => $p) {
							$msg = array_merge($msg, self::get_msg_part_text($mbox, $msgno, $partno+1, $p));
						}
					}

					$em = new EmailModel();
					$em->setDatetime($date);
					$em->setSubject($subject);
					$em->setToaddress($toaddress);
					$em->setFromaddress($fromaddress);
					$em->setMsg(json_encode($msg, JSON_INVALID_UTF8_SUBSTITUTE));
					$em->setStatus(self::EMAIL_STATUS_RECEIVED);
					$em->setStatusMsg(json_encode(array("status" => true)));
					$em->insert();

					imap_delete($mbox, $msgno);
				}
			}

			$result["status"] = true;
		}

		imap_close($mbox, CL_EXPUNGE);

		return $result;
	}

	public static function get_types() {
		$cond = new Condition("[c1]", array(
			"[c1]" => [
				[EmailModel::class, EmailModel::FIELD_STATUS],
				Condition::COMPARISON_EQUALS,
				[Condition::CONDITION_CONST, self::EMAIL_STATUS_RECEIVED]
			]
		));

		$em = new EmailModel();
		$em->find($cond);

		while ($em->next()) {
			$type = -1;

			/* $type = self::EMAIL_TYPE_REWE_EBON */

			if ($type > -1) {
				$em->setStatus(self::EMAIL_STATUS_TYPE);
				$em->setStatusMsg(json_encode(array("status" => true, "type" => $type)));
				$em->save();
			} else {
				$em->setStatusMsg(json_encode(array("status" => false, "error" => "unrecognized type")));
				$em->save();
			}
		}
	}

	public static function get_user($fromaddress) {
		$cond = new Condition("[c1]", array(
			"[c1]" => [
				[UsersModel::class, UsersModel::FIELD_EMAIL],
				Condition::COMPARISON_LIKE,
				[Condition::CONDITION_CONST, $fromaddress]
			]
		));

		$um = new UsersModel();
		$um->find($cond);

		if ($um->next()) {
			return $um->getId();
		}
		return -1;
	}

	public static function parse_rewe_ebon($user_id, $em) {
		$result = array("status" => false);
		$error = array();
		if (false) {
			$products_source_type = "rewe";
			$products_source_type_id = 2;

			/* PARSE MARKET */
			$address = "";
			$zipcode = "";
			$city = "";

			/* PARSE PRODUCTS */
			$ebon_products = array();

			/*
				$ebon_products[0] = array(
					"Name" 		=> "RISPENTOMATE",
					"Amount" 	=> 0.910,
					"AmountType"	=> "kg",
					"Price"		=> 3.49
				);
				$ebon_products[1] = array(
					"Name"		=> "MALZMEHRKORNBROT",
					"Amount"	=> null,
					"AmountType"	=> null,
					"Price"		=> 1.79
				);
				$ebon_products[2] => array(
					"Name"		=> "BUTTERMILCH",
					"Amount"	=> 3,
					"AmountType"	=> "Stk",
					"Price"		=> 0.39
				);
			*/

			/* PRODUCTS_SOURCE_ID */
			$products_source_id = -1;

			$ps_cond = new Condition("[c1] AND [c2] AND [c3] AND [c4]", array(
				"[c1]" => [
					[ProductsSourceModel::class, ProductsSourceModel::FIELD_ADDRESS],
					Condition::COMPARISON_LIKE,
					[Condition::CONDITION_CONST, $address]
				],
				"[c2]" => [
					[ProductsSourceModel::class, ProductsSourceModel::FIELD_ZIPCODE],
					Condition::COMPARISON_LIKE,
					[Condition::CONDITION_CONST, $zipcode]
				],
				"[c3]" => [
					[ProductsSourceModel::class, ProductsSourceModel::FIELD_CITY],
					Condition::COMPARISON_LIKE,
					[Condition::CONDITION_CONST, $city]
				],
				"[c4]" => [
					[ProductsSourceModel::class, ProductsSourceModel::FIELD_PRODUCTS_SOURCE_TYPE_ID],
                                        Condition::COMPARISON_EQUALS,
                                        [Condition::CONDITION_CONST, $products_source_type_id]
				]
			));

			$ps = new ProductsSourceModel();
			$ps->find($ps_cond);
			if ($ps->next()) {
				$products_source_id = $ps->getId();
			} else {
				$ps = new ProductsSourceModel();
				$ps->setAddress($address);
				$ps->setZipcode($zipcode);
				$ps->setCity($city);
				$ps->setProductsSourceTypeId($products_source_type_id);
				$ps->insert();
				$products_source_id = $ps->getId();
			}

			/* PRODUCTS_SOURCE_IDS */
			$ps_ids = array();

			$ps_cond = new Condition("[c1]", array(
				"[c1]" => [
					[ProductsSourceModel::class, ProductsSourceModel::FIELD_PRODUCTS_SOURCE_TYPE_ID],
					Condition::COMPARISON_EQUALS,
					[Condition::CONDITION_CONST, $products_source_type_id]
				]
			));
			$ps = new ProductsSourceModel();
			$ps->find($ps_cond);
			while ($ps->next()) {
				$ps_ids[] = $ps->getId();
			}

			/* AMOUNT_TYPES */
			$amount_types = array();
			$at = new AmountTypeModel();
			$at->find();
			while ($at->next()) {
				$amount_types[$at->getName()] = $at->getId();
			}

			/* EBON_PRODUCTS_IDS / EBON_PRODUCTS_PARSED */
			$epps = array();
			foreach ($ebon_products as $e_id => $ebon_product) {
				$ebon_products[$e_id]["EbonProductsId"] = -1;

				$ep_cond = new Condition("[c1] AND [c2]", array(
					"[c1]" => [
						[EbonProductsModel::class, EbonProductsModel::FIELD_NAME],
						Condition::COMPARISON_LIKE,
						[Condition::CONDITION_CONST, $ebon_product["Name"]]
					],
					"[c2]" => [
						[EbonProductsModel::class, EbonProductsModel::FIELD_PRODUCTS_SOURCE_ID],
						Condition::COMPARISON_IN,
						[Condition::CONDITION_CONST_ARRAY, $ps_ids]
					]
				));

				$ep = new EbonProductsModel();
				$ep->find($ep_cond);
				while ($ep->next()) {
					$ebon_products[$e_id]["EbonProductsId"] = $ep->getId();
					if ($ep->getProductsSourceId() == $products_source_id) {
						break;
					}
				}
				if ($ebon_products[$e_id]["EbonProductsId"] == -1) {
					$ep = new EbonProductsModel();
					$ep->setProductsSourceId($products_source_id);
					$ep->setName($ebon_product["Name"]);
					$ep->insert();
					$ebon_products[$e_id]["EbonProductsId"] = $ep->getId();
				}

				/* PARSED */
				$epp = new EbonProductsParsedModel();
				$epp->setUsersId($user_id);
				$epp->setDatetime($em->getDatetime());
				$epp->setProductsSourceId($products_source_id);
				$epp->setEbonProductsId($ebon_products[$e_id]["EbonProductsId"]);
				$epp->setAmount($ebon_products[$e_id]["Amount"]);
				if (!is_null($ebon_products[$e_id]["AmountType"])) {
					if (isset($amount_type[$ebon_products[$e_id]["AmountType"]])) {
						$epp->setAmountType($amount_type[$ebon_products[$e_id]["AmountType"]]);
					} else {
						$error[] = "unknown amount type: " . $ebon_products[$e_id]["AmountType"];
					}
				}
				$epps[] = $epp;
			}

			if (count($error) == 0) {
				foreach ($epps as $epp) {
					$epp->insert(false);
				}
				$result["status"] = true;
			} else {
				$result["status"] = false;
				$result["error"] = $error;
			}
		} else {
			$result["status"] = false;
			$result["error"] = "parsing error";
		}

		return $result;
	}

	public static function parse_emails() {
		$cond = new Condition("[c1]", array(
                        "[c1]" => [
                                [EmailModel::class, EmailModel::FIELD_STATUS],
                                Condition::COMPARISON_EQUALS,
                                [Condition::CONDITION_CONST, self::EMAIL_STATUS_TYPE]
                        ]
                ));

                $em = new EmailModel();
                $em->find($cond);

		while ($em->next()) {
			$user_id = self::get_user($em->getFromaddress);

			$status = json_decode($em->getStatusMsg());

			if ($user_id == -1) {
				$status["status"] = false;
				$status["error"] = "unrecognized user";
				$em->setStatusMsg(json_encode($status));
				$em->save();
			} else {
				$processed_result = array();
				$processed_result["status"] = false;

				$status = json_decode($em->getStatusMsg());
				if ($status["type"] == self::EMAIL_TYPE_REWE_EBON) {
					$processed_result = self::parse_rewe_ebon($user_id, $em);
				} /* else if (...) {

				} */ else {
					$processed_result["error"] = "email type not processed";
				}

				if ($processed_result["status"] == true) {
					$em->setStatus(self::EMAIL_STATUS_PROCESSED);
                		        $em->setStatusMsg(json_encode(array("status" => true)));
		                        $em->save();
				} else {
					foreach ($processed_result as $key => $value) {
						$status[$key] = $value;
					}
					$em->setStatusMsg(json_encode($status));
					$em->save();
				}
			}
		}
	}

	public static function get_context($json) {
	        $context = array(
        	        'http' => array(
                	        'method'         =>     'POST',
                        	'header'         =>     "Content-type: application/json\r\n" .
	                                                "Content-Length: " . strlen($json) . "\r\n",
        	                'content'        =>     $json
                	)
	        );
        	$context = stream_context_create($context);
	        return $context;
	}

	public static function get_post($url, $json) {
	        $context = self::get_context($json);
        	$result = json_decode(file_get_contents($url, false, $context));
	        return $result;
	}

	public function update_user_table() {
                $foreign_base_url = $this->config->getConfigValue(array('admin_login', 'foreign_url'));

                $login_action = "users/login";

		$admin_user = $this->config->getConfigValue(array('admin_login', 'user'));
		$admin_password = $this->config->getConfigValue(array('admin_login', 'foreign_password'));

                $foreign_login_json = '{"email": "' . $admin_user . '", "password": "' . $admin_password . '"}';
                $foreign_login_result = self::get_post($foreign_base_url . $login_action, $foreign_login_json);

                if ($foreign_login_result->{"status"} == true) {
                        $tables = array(
                                "Users"
                        );

                        $backup_action = "app/backup";

                        foreach ($tables as $table) {
                                $foreign_data_arr = array(
                                        "login_data" => $foreign_login_result->{'login_data'},
                                        "table" => array(
                                                "name"          =>      $table,
                                                "limit"         =>      10000,
                                                "offset"        =>      0
                                        )
                                );
                                $json = json_encode($foreign_data_arr, JSON_INVALID_UTF8_SUBSTITUTE);
                                $table_result = self::get_post($foreign_base_url . $backup_action, $json);

                                $model_full_name = "\\FooDBar\\" . $table . "Model";

                                $local_data_arr["table"]["rows"] = $table_result->{$model_full_name};
				foreach ($local_data_arr["table"]["rows"] as $nr => $row) {
					$cond = new Condition("[c1]", array(
						"[c1]" => [
							[UsersModel::class, UsersModel::FIELD_ID],
							Condition::COMPARISON_EQUALS,
							[Condition::CONDITION_CONST, $row->{UsersModel::FIELD_ID}]
						]
					));
					$um = new UsersModel();
					$um->find($cond);

					if (!$um->next()) {
						$um = new UsersModel();
					        foreach ($um->fields() as $field_name_camel => $field) {
					                $field_name = $field["Field"];

							if (isset($row->{$field_name_camel})) {
								$setter = "set" . $field_name_camel;
								$um->$setter($row->{$field_name_camel});
							}
						}
						$um->insert(false, true);
					}
				}
                        }
		}
	}

	public function run() {
		$GLOBALS['Boot']->loadModel("EmailModel");
		$GLOBALS['Boot']->loadModel("UsersModel");
		$GLOBALS['Boot']->loadModel("ProductsSourceModel");
		$GLOBALS['Boot']->loadModel("AmountTypeModel");
		$GLOBALS['Boot']->loadModel("EbonProductsModel");
		$GLOBALS['Boot']->loadModel("EbonProductsParsedModel");

		$job_params = parent::getParams();
/*
		if (is_null($job_params)) {
			parent::setJobStatus(parent::JOB_STATUS_ERROR, array("error" => "no params"));
			exit();
		} else if (!isset($job_params->{"date_to"})) {
			parent::setJobStatus(parent::JOB_STATUS_ERROR, array("error" => "missing param date_to"));
			exit();
		}
*/

		$status_fields = array(
			self::APP_STATUS_EMAIL
		);

		$GLOBALS['Boot']->loadModule("app", "Status");
		$fields = StatusController::getFields($status_fields);

		$this->update_user_table();

		$this->get_mail();
		self::get_types();
		self::parse_emails();

		parent::setJobStatus(parent::JOB_STATUS_FINISHED, array("status" => true));
	}
}

$env = getenv('FRAME_ENVIRONMENT');

if ($env == "development") {
    $cfg = "development";
} else {
    $cfg = "live";
}

$upe_job = new UpdateEMailJob("../app/config/app.{$cfg}.json", $argv);
$upe_job->run();
