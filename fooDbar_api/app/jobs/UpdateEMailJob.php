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

	public static function get_msg_part_text($hash, $mbox, $msgno, $partno, $p) {
		if ($partno == 0) {
			$data = imap_body($mbox, $msgno);
		} else {
			$data = imap_fetchbody($mbox, $msgno, $partno);
		}

		if ($p->encoding == ENCQUOTEDPRINTABLE) {
			$data = quoted_printable_decode($data);
		} else if ($p->encoding == ENCBASE64) {
			$data = base64_decode($data);
		}

		$params = array();
		if (isset($p->{"parameters"})) {
			foreach ($p->parameters as $x) {
				$params[strtolower($x->attribute)] = $x->value;
			}
		}
		if (isset($p->{"dparameters"})) {
			foreach ($p->dparameters as $x) {
				$params[strtolower($x->attribute)] = $x->value;
			}
		}

		$result = array();
		$filename = null;

		if (isset($params['filename'])) {
			$filename = $params['filename'];
		} else if (isset($params['name'])) {
			$filename = $params['name'];
		}

		if (!is_null($filename)) {
			$hash_ = hash('sha256', $hash . $partno);

			$result[$partno] = array(
				'filename' => $filename,
				'data' => $hash_
			);
			file_put_contents(__DIR__ . "/tmp/" . $hash_, $data);
			if (strpos($filename, ".pdf") == strlen($filename) - 4) {
				exec("qpdf --qdf --object-streams=disable " . __DIR__ . "/tmp/" . $hash_ . " " . __DIR__ . "/tmp/" . $hash_ . "_d", $output, $retval);
			}
		}

		if ($data && ($p->type == TYPETEXT || $p->type == TYPEMESSAGE)) {
			$result[$partno] = $data;
		}

		if (isset($p->{"parts"})) {
			foreach ($p->parts as $partno_s => $p_s) {
				$p_no_s = $partno . '.' . ($partno_s + 1);
				$result = array_merge($result, self::get_msg_part_text($hash, $mbox, $msgno, $p_no_s, $p_s));
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

					$hash = hash('sha256', $date . $subject . $toaddress . $fromaddress . random_bytes(256));

					$structure = imap_fetchstructure($mbox, $msgno);
					$msg = array();
					if (!isset($structure->{"parts"})) {
						$msg = self::get_msg_part_text($hash, $mbox, $msgno, 0, $structure);
					} else {
						foreach ($structure->parts as $partno => $p) {
							$msg = array_merge($msg, self::get_msg_part_text($hash, $mbox, $msgno, $partno+1, $p));
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

			$msg_arr = json_decode($em->getMsg());
			$param = null;
			foreach($msg_arr as $msg_p) {
				if (@isset($msg_p->{'filename'})) {
					if ($msg_p->{'filename'} == "REWE-eBon.pdf") {
						$type = self::EMAIL_TYPE_REWE_EBON;
						$param = $msg_p->{'data'};
						break;
					}
				}
			}

			if ($type > -1) {
				$em->setStatus(self::EMAIL_STATUS_TYPE);
				$em->setStatusMsg(json_encode(array("status" => true, "type" => $type, "param" => $param)));
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

		$status = json_decode($em->getStatusMsg(), true);
		$pdf = file_get_contents(__DIR__ . "/tmp/" . $status['param'] . "_d");

		/* BFRANGES */
		$bfranges = array();

		$pos = 0;
		while (($f = strpos($pdf, "beginbfrange", $pos)) != false) {
			$fe = strpos($pdf, "endbfrange", $pos);
			$lines = explode("\n", substr($pdf, $f, $fe - $f));
			for ($l = 0; $l < count($lines); $l++) {
				$seg = explode(" ", $lines[$l]);
				if (count($seg) == 3) {
					$seg = str_replace(array("<", ">"), "", $seg);
					$val_0 = hexdec($seg[0]);
					$val_1 = hexdec($seg[1]);
					$val_2 = hexdec($seg[2]);
					for ($v = $val_0; $v <= $val_1; $v++) {
						$bfranges[$v] = $val_2 + $v - $val_0;
					}
				}
			}
			$pos = $fe + 1;
		}

		/* PAGES */
		$pages = array();

		$pos = 0;
		while (($f = strpos($pdf, "Contents for page ", $pos)) != false) {
			$sp = strpos($pdf, "stream", $f);
			$sp_e = strpos($pdf, "endstream", $sp);
			$pos = $sp_e+1;
			$pages[] = array($sp, $sp_e);
		}

		/* TEXT */
		$text = "";
		foreach ($pages as $page) {
			$stream = substr($pdf, $page[0], $page[1] - $page[0]);
			if (preg_match_all("/^<[0-9a-f]*> Tj$/m", $stream, $matches)) {
				foreach ($matches[0] as $match) {
					$m = str_replace(array("<", ">", " Tj"), "", $match);
					$len = strlen($m);
					for ($c = 0; $c < $len; $c += 4) {
						$char_code = substr($m, $c, 4);
						$cc = hexdec($char_code);
						if (isset($bfranges[$cc])) {
							$text .= chr($bfranges[$cc]);
						} else {
							$text .= " ";
						}
					}
					$text .= "\n";
				}
			}
		}
		$lines = explode("\n", $text);

		/* EBON STRUCTURE / ADDRESS / ZIPCODE / CITY */
		$first_non_zero = count($lines);
		$zipcode_line = -1;
		$last_non_prod = -1;
		$sum_line = -1;

		$address = "";
		for ($l = 0; $l < count($lines); $l++) {
			if (strlen(trim($lines[$l])) > 0 && $first_non_zero > $l) {
				$first_non_zero = $l;
			}
			if ($zipcode_line == -1) {
				$tl = explode(" ", trim($lines[$l]));
				if (count($tl) == 2) {
					if (strlen($tl[0]) == 5 && is_numeric($tl[0])) {
						$zipcode_line = $l;
						$zipcode = $tl[0];
						$city = $tl[1];
					}
				}
			}
			if ($last_non_prod == -1) {
				$tl = array_values(array_filter(explode(" ", trim($lines[$l]))));
				if (count($tl) == 1 && $tl[0] == "EUR") {
					$last_non_prod = $l;
				}
			}
			if ($sum_line == -1) {
				$tl = array_values(array_filter(explode(" ", trim($lines[$l]))));
				if (count($tl) == 3) {
					if ($tl[0] == "SUMME" && $tl[1] = "EUR") {
						$sum_line = $l;
					}
				}
			}
		}

		for ($l = $first_non_zero; $l < $zipcode_line; $l++) {
			$address .= trim($lines[$l]);
		}

		/* EBON_PRODUCTS */
		$ebon_products = array();
		for ($p = $last_non_prod + 1; $p < $sum_line - 1; $p++) {
			$l_arr = array_values(array_filter(explode(" ", trim($lines[$p]))));
			if ($lines[$p][0] != " ") { 	//Product line
				$p_name = $l_arr[0];
				for ($a = 1; $a < count($l_arr) - 2; $a++) {
					$p_name .= " " . $l_arr[$a];
				}
				$p_price = floatval(str_replace(",", ".", $l_arr[count($l_arr) - 2]));
				$ebon_products[] = array(
					"Name" => $p_name,
					"Price" => $p_price,
					"AmountType" => null,
					"Amount" => null
				);
			} else {			//Amount line
				$amount = floatval(str_replace(",", ".", $l_arr[0]));
				$amount_type = $l_arr[1];
				$p_price = floatval(str_replace(",", ".", $l_arr[3]));
				$ebon_products[count($ebon_products) -1]["Amount"] = $amount;
				$ebon_products[count($ebon_products) -1]["AmountType"] = $amount_type;
				$ebon_products[count($ebon_products) -1]["Price"] = $p_price;
			}
		}

		if ($zipcode_line > -1 && $last_non_prod > -1 && $sum_line > -1 && count($ebon_products) > 0) {
			$products_source_type = "rewe";
			$products_source_type_id = 2;

			/* PRODUCTS_SOURCE_ID */
			$products_source_id = -1;

			$ps_cond = new Condition("[c1] AND [c2] /*AND [c3] */AND [c4]", array(
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
/*				"[c3]" => [
					[ProductsSourceModel::class, ProductsSourceModel::FIELD_CITY],
					Condition::COMPARISON_LIKE,
					[Condition::CONDITION_CONST, $city]
				],*/
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
					if (isset($amount_types[$ebon_products[$e_id]["AmountType"]])) {
						$epp->setAmountTypeId($amount_types[$ebon_products[$e_id]["AmountType"]]);
					} else {
						$error[] = "unknown amount type: " . $ebon_products[$e_id]["AmountType"];
					}
				}
				$epp->setPrice($ebon_products[$e_id]["Price"]);
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
			$user_id = self::get_user($em->getFromaddress());

			$status = json_decode($em->getStatusMsg(), true);

			if ($user_id == -1) {
				$status["status"] = false;
				$status["error"] = "unrecognized user";
				$em->setStatusMsg(json_encode($status));
				$em->save();
			} else {
				$processed_result = array();
				$processed_result["status"] = false;

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
		return $foreign_login_result;
	}

	public function upload_tables($foreign_login, $ebon_products_max_id, $ebon_products_parsed_max_id) {
		$foreign_base_url = $this->config->getConfigValue(array('admin_login', 'foreign_url'));
                $local_base_url = $this->config->getConfigValue(array('admin_login', "local_url"));

                $login_action = "users/login";

                $admin_user = $this->config->getConfigValue(array('admin_login', 'user'));
                $admin_password_local = $this->config->getConfigValue(array('admin_login', 'local_password'));

                $local_login_json = '{"email": "' . $admin_user . '", "password": "' . $admin_password_local . '"}';
                $local_login_result = self::get_post($local_base_url . $login_action, $local_login_json);

                $foreign_login_result = $foreign_login;

		$max_ids = array(
                                "EbonProducts" => $ebon_products_max_id,
                                "EbonProductsParsed" => $ebon_products_parsed_max_id
                        );
		if ($foreign_login_result->{"status"} == true && $local_login_result->{"status"} == true) {
	                $insert_action = "app/backup/insert";

        	        $tables = array(
                                "EbonProducts",
                                "EbonProductsParsed"
                        );

			$conds = array(
				"EbonProducts" => new Condition("[c1]", array(
                                        "[c1]" => [
                                                [EbonProductsModel::class, EbonProductsModel::FIELD_ID],
                                                Condition::COMPARISON_GREATER,
                                                [Condition::CONDITION_CONST, $ebon_products_max_id]
                                        ]
                                )),
				"EbonProductsParsed" => new Condition("[c1]", array(
                                        "[c1]" => [
                                                [EbonProductsParsedModel::class, EbonProductsParsedModel::FIELD_ID],
                                                Condition::COMPARISON_GREATER,
                                                [Condition::CONDITION_CONST, $ebon_products_parsed_max_id]
                                        ]
                                ))
			);

			$model = array(
				"EbonProducts" => new EbonProductsModel(),
				"EbonProductsParsed" => new EbonProductsParsedModel()
			);

	                foreach ($tables as $table) {
				$rows = new \stdClass();

				$model[$table]->find($conds[$table]);

				while ($model[$table]->next()) {
					$rows->{$model[$table]->getId()} = $model[$table]->toArray();
					$max_ids[$table] = $model[$table]->getId();
				}

				$foreign_data_arr = array(
                                        "login_data" => $foreign_login_result->{'login_data'},
                                        "table" => array(
                                                        "name"          =>      $table,
							"rows"		=>	$rows
                                                )
                                        );

                                $foreign_json = json_encode($foreign_data_arr, JSON_INVALID_UTF8_SUBSTITUTE);
                                $insert_result = self::get_post($foreign_base_url . $insert_action, $foreign_json);
			}
		}
		return $max_ids;
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

		$ebon_products_max_id = -1;
		$ebon_products_parsed_max_id = -1;
		if (isset($fields["app_status"]->{"ebon_products_max_id"})) {
			$ebon_products_max_id = $fields["app_status"]->{"ebon_products_max_id"};
                }
		if (isset($fields["app_status"]->{"ebon_products_parsed_max_id"})) {
			$ebon_products_parsed_max_id = $fields["app_status"]->{"ebon_products_parsed_max_id"};
		}

		$foreign_login_result = $this->update_user_table();

		$this->get_mail();
		self::get_types();
		self::parse_emails();

		$max_ids = $this->upload_tables($foreign_login_result, $ebon_products_max_id, $ebon_products_parsed_max_id);

		$fields = array(
                        self::APP_STATUS_EMAIL => array( "ebon_products_max_id" => $max_ids["EbonProducts"], "ebon_products_parsed_max_id" => $max_ids["EbonProductsParsed"])
                );

                StatusController::setFields($fields);

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
