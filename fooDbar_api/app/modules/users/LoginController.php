<?php

namespace FooDBar;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'parentpath')) . "Condition.php";
use \Frame\Condition as Condition;

require $GLOBALS['Boot']->config->getConfigValue(array('dbmodel', 'path')) . "UsersModel.php";

class LoginController {
    private $DefaultController = true;
    private $DefaultAction = "login";

    public static function requireAuth() {
	$user = $GLOBALS['AUTH'];
        $result = array("status" => false);
        if (is_null($user)) {
                $result["error"] = "permission denied";
                exit(json_encode($result, JSON_PRETTY_PRINT));
        }
	return $user;
    }

    public static function validateLogin($login_data) {
	$email = $login_data->{"email"};
	$token = $login_data->{"token"};
	if (!is_null($email) && !is_null($token)) {
		$condition = new Condition("[c1] AND [c2]", array(
                    "[c1]" =>   [
                                    [UsersModel::class, UsersModel::FIELD_EMAIL],
                                    Condition::COMPARISON_EQUALS,
                                    [Condition::CONDITION_CONST, $email]
                        ],
                    "[c2]" =>   [
                                    [UsersModel::class, UsersModel::FIELD_TOKEN],
                                    Condition::COMPARISON_EQUALS,
                                    [Condition::CONDITION_CONST, $token]
                                ]
                ));

		$user = new UsersModel();
                $user->find($condition);
                if ($user->next()) {
			return $user;
		}
	}
	return null;
    }

    public function loginAction() {
	$data = $GLOBALS['POST'];

	$email = $data->{"email"};
	$password = $data->{"password"};

	$result = array();
	$result["status"] = false;
	if (!is_null($email) && !is_null($password)) {
		$condition = new Condition("[c1] AND [c2]", array(
	            "[c1]" =>   [
	                            [UsersModel::class, UsersModel::FIELD_EMAIL],
                	            Condition::COMPARISON_EQUALS,
        	                    [Condition::CONDITION_CONST, $email]
                        ],
        	    "[c2]" =>   [
	                            [UsersModel::class, UsersModel::FIELD_PASSWORD],
	                            Condition::COMPARISON_EQUALS,
                        	    [Condition::CONDITION_CONST, hash('sha256', $password)]
                	        ]
        	));

		$user = new UsersModel();
		$user->find($condition);
		if ($user->next()) {
			$token = hash('sha256', random_bytes(256));
			$user->setToken($token);
			$user->save();
			$result["status"] = true;
			$result["login_data"] = new \stdClass();
			$result["login_data"]->{'user_id'} = intval($user->getId());
			$result["login_data"]->{'username'} = $user->getName();
			$result["login_data"]->{'email'} = $user->getEmail();
			$result["login_data"]->{'token'} = $token;

			$GLOBALS['Boot']->loadModule("allergies", "Allergy");
			$result["allergies"] = AllergyController::getAllergyValues($user);
		}
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function logoutAction() {
	$user = LoginController::requireAuth();

	$user->setToken("");
	$result["status"] = true;

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function updateAction() {
	$user = LoginController::requireAuth();

	$values = $GLOBALS['POST']->{'allergies'};

	$GLOBALS['Boot']->loadModule("allergies", "Allergy");
	AllergyController::setAllergyValues($user, $values);

	$result["status"] = true;
	$result["allergies"] = AllergyController::getAllergyValues($user);
        exit(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function registerAction() {
        $result = array();
        $result["status"] = false;

	$condition = new Condition("[c1] AND [c2]", array(
                    "[c1]" =>   [
                                    [UsersModel::class, UsersModel::FIELD_EMAIL],
                                    Condition::COMPARISON_EQUALS,
                                    [Condition::CONDITION_CONST, $email]
                        ]
		));
	$user_exists = new UsersModel();
	$user_exists->find($condition);
	if ($user_exists->next()) {
		$result["error"] = "User already exists";
	} else {
		$data = $GLOBALS['POST'];

		$user = new UsersModel();
		$user->setName($data->{"username"});
		$user->setEmail($data->{"email"});
		$user->setPassword(hash('sha256', $data->{"password"}));
		$user->setBirthdate($data->{"birthdate"});
		$user->setGenderId($data->{"gender_id"});
		$user->insert();

		if (!is_null($user->getId())) {
			$result["status"] = true;
			$result["user_id"] = $user->getId();
		} else {
			$result["error"] = "User not created";
		}
	}

	exit(json_encode($result, JSON_PRETTY_PRINT));
    }
}
