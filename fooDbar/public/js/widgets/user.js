var User = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.action = "login";
	this.login_data = null;
	this.allergies = null;

	this.has_unset_allergies = false;

	this.widget = new Widget("User");

	this.elem = this.widget.elem;
	this.elem.style.display = "none";

	this.changed = true;

	this.changed_f = function() {
		this.changed = true;
		if (this.change_dependencies != null) {
			for (var i = 0; i < this.change_dependencies.length; i++) {
				this.change_dependencies[i].changed_f();
			}
		}
	}

        this.on_login_response = function() {
                var resp = JSON.parse(this.responseText);
                if (resp["status"] == true) {
                        user.login_data = resp["login_data"];
			user.allergies = resp["allergies"];
			user.has_unset_allergies = false;
			if (user.allergies["has_unset_allergies"] == true) {
				user.has_unset_allergies = true;

				var unset_allergies_elem = document.createElement("div");
				unset_allergies_elem.innerHTML = "Please fill in your allergy settings by accessing user settings.";
				unset_allergies_elem.style.background = "yellow";

				messagebox.message_add(unset_allergies_elem, 1000, "no-class", "has_unset_allergies", true);
			}
                        user.changed_f();
                } else {
			var l_elem = document.createElement("div");
                        l_elem.innerHTML = "Log in failed.";
                        l_elem.style.background = "red";

                        messagebox.message_add(l_elem, 1000, "no-class", "login_failure", true);
		}
        }

	this.login_form_create = function() {
		var login = document.createElement("div");
		login.id = this.widget.name + "_login";

		var login_email = document.createElement("input");
		login_email.id = this.widget.name + "_login_email";
		login_email.placeholder = "E-Mail";
		login.appendChild(login_email);

		var login_password = document.createElement("input");
		login_password.type = "password";
		login_password.id = this.widget.name + "_login_password";
		login_password.placeholder = "Password";
		login.appendChild(login_password);

		var login_submit = document.createElement("button");
		login_submit.obj = this;
		login_submit.innerHTML = "<img src='/img/symbol_login.svg' style='width: 30px;' />";
		login_submit.title = "Log in";
		login_submit.onclick = function() {
			var email = document.getElementById(this.obj.widget.name + "_login_email").value;
			var password = document.getElementById(this.obj.widget.name + "_login_password").value;
			var p = { "email": email, "password": password };
			this.obj.db.query_post("users/login", p, user.on_login_response);
		}
		login.appendChild(login_submit);

		var register = document.createElement("button");
		register.obj = this;
		register.innerHTML = "&#x00AE;";
		register.title = "Register";
		register.onclick = function() {
			user.action = "register";
			user.changed = true;
		}
		login.appendChild(register);

		return login;
	}

	this.on_logout_response = function() {
		var resp = JSON.parse(this.responseText);
                if (resp["status"] == true) {
                        user.login_data = null;
			user.action = "login";
                        user.changed_f();
                }
	}

	this.logged_in_create = function() {
		var logged_in = document.createElement("table");

		var logged_in_row = document.createElement("tr");
		logged_in.appendChild(logged_in_row);

		var logged_in_col_1 = document.createElement("td");
                logged_in_col_1.appendChild(document.createTextNode(this.login_data["username"]));
		logged_in_row.appendChild(logged_in_col_1);

		var logged_in_col_2 = document.createElement("td");
		logged_in_col_2.style.textAlign = "right";
		logged_in_row.appendChild(logged_in_col_2)
;
		var settings_button = document.createElement("button");
		settings_button.appendChild(document.createTextNode("\u2630"));
		settings_button.title = "Settings";
		settings_button.onclick = function() {
			user.action = "settings";
			user.changed = true;
		}
		logged_in_col_2.appendChild(settings_button);

                var logout_button = document.createElement("button");
                logout_button.innerHTML = "<img src='/img/symbol_logout.svg' style='width: 30px;' />";
		logout_button.title = "Log out";
                logout_button.onclick = function() {
                	var p = {};
	                user.db.query_post("users/login/logout", p, user.on_logout_response);
                }
                logged_in_col_2.appendChild(logout_button);

		return logged_in;
	}

	this.on_register_response = function() {
                var resp = JSON.parse(this.responseText);
                if (resp["status"] == true) {
                        user.login_data = null;
                        user.action = "login";
                        user.changed = true;
                } else {
			var reg_elem = document.createElement("div");
                        reg_elem.innerHTML = resp["error"];
                        reg_elem.style.background = "yellow";

                        messagebox.message_add(reg_elem, 1000, "no-class", "reg_failed", true);
		}
	}

	this.register_form_create = function() {
		var register_form = document.createElement("div");

		var input_username = document.createElement("input");
		input_username.id = this.widget.name + "_username";
		input_username.placeholder = "Username";
		register_form.appendChild(input_username);

		var input_email = document.createElement("input");
		input_email.id = this.widget.name + "_email";
		input_email.placeholder = "E-Mail";
		register_form.appendChild(input_email);

		var input_password = document.createElement("input");
		input_password.type = "password";
		input_password.id = this.widget.name + "_password";
		input_password.placeholder = "Password";
		register_form.appendChild(input_password);

		var input_password_2 = document.createElement("input");
		input_password_2.type = "password";
		input_password_2.id = this.widget.name + "_password2";
		input_password_2.placeholder = "Password";
		register_form.appendChild(input_password_2);

		var birthdate = document.createElement("input");
		birthdate.id = this.widget.name + "_birthdate";
		birthdate.placeholder = "Date of birth (YYYY-MM-DD)";
		register_form.appendChild(birthdate);

		var gender = document.createElement("select");
		gender.id = this.widget.name + "_gender";

		var gender_option_0 = document.createElement("option");
		gender_option_0.innerHTML = "Male";
		gender.appendChild(gender_option_0);

		var gender_option_1 = document.createElement("option");
		gender_option_1.innerHTML = "Female";
		gender.appendChild(gender_option_1);

		register_form.appendChild(gender);

		var register_submit = document.createElement("button");
		register_submit.obj = this;
		register_submit.innerHTML = "&#10003;";
		register_submit.title = "Register";
		register_submit.onclick = function() {
			var input_username = document.getElementById(this.obj.widget.name + "_username");
			var input_email = document.getElementById(this.obj.widget.name + "_email");
			var input_password = document.getElementById(this.obj.widget.name + "_password");
			var input_password2 = document.getElementById(this.obj.widget.name + "_password2");
			var input_birthdate = document.getElementById(this.obj.widget.name + "_birthdate");
			var select_gender = document.getElementById(this.obj.widget.name + "_gender");

			if (input_password.value !== input_password2.value) {
				console.log("passwords don't match");
			} else {
				var p = {
					"username": input_username.value,
					"email": input_email.value,
					"password": input_password.value,
					"birthdate": input_birthdate.value,
					"gender_id": select_gender.selectedIndex
				};
				user.db.query_post("users/login/register", p, user.on_register_response);
			}
		}
		register_form.appendChild(register_submit);

		var register_cancel = document.createElement("button");
		register_cancel.obj = this;
		register_cancel.innerHTML = "&#xd7;";
		register_cancel.title = "Cancel";
		register_cancel.onclick = function() {
			this.obj.action = "login";
			this.obj.changed = true;
		}
		register_form.appendChild(register_cancel);

		return register_form;
	}

	this.on_settings_password_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user.action = "logged_in";

			var pw_elem = document.createElement("div");
                        pw_elem.innerHTML = "Password changed successfully.";
                        pw_elem.style.background = "green";

                        messagebox.message_add(pw_elem, 1000, "no-class", "password_changed", true);

			user.changed_f();
		} else {
			if (resp["error"] == "wrong password") {
				var pw_elem = document.createElement("div");
	                     	pw_elem.innerHTML = "Password change failed: Wrong password!";
        	                pw_elem.style.background = "red";

	                        messagebox.message_add(pw_elem, 1000, "no-class", "password_not_changed", true);
			}
		}
	}

	this.on_settings_response = function() {
		var resp = JSON.parse(this.responseText);
                if (resp["status"] == true) {
                        user.action = "logged_in";
			user.allergies = resp["allergies"];
                        user.changed_f();
                }
	}

	this.settings_form_create = function() {
		var settings_form = document.createElement("div");

		var settings_pw = document.createElement("div");
		settings_pw.className = "subset";
		settings_form.appendChild(settings_pw);

		var span_pw = document.createElement("span");
		span_pw.innerHTML = "Change password";
		settings_pw.appendChild(span_pw);

		var input_password_c = document.createElement("input");
		input_password_c.type = "password";
		input_password_c.id = this.widget.name + "_password_current";
		input_password_c.placeholder = "Current password";
		settings_pw.appendChild(input_password_c);

                var input_password = document.createElement("input");
                input_password.type = "password";
                input_password.id = this.widget.name + "_password";
                input_password.placeholder = "New password";
                settings_pw.appendChild(input_password);

                var input_password_2 = document.createElement("input");
                input_password_2.type = "password";
                input_password_2.id = this.widget.name + "_password2";
                input_password_2.placeholder = "New password";
                settings_pw.appendChild(input_password_2);


		var apply_btn = document.createElement("button");
                apply_btn.obj = this;
                apply_btn.innerHTML = "&#10003;";
                apply_btn.title = "Apply";
                apply_btn.onclick = function() {
			if (document.getElementById(user.widget.name + "_password").value === document.getElementById(user.widget.name + "_password2").value) {
	                        var p = { "passwords" : {
					"current_password": document.getElementById(user.widget.name + "_password_current").value,
					"password": document.getElementById(user.widget.name + "_password").value
				} };
	                        user.db.query_post("users/login/updatepassword", p, user.on_settings_password_response);
			} else {
				var pw_elem = document.createElement("div");
        	                pw_elem.innerHTML = "Password mismatch.";
                	        pw_elem.style.background = "yellow";

                        	messagebox.message_add(pw_elem, 1000, "no-class", "password_mismatch", true);
			}
                }
                settings_pw.appendChild(apply_btn);

                var cancel_btn = document.createElement("button");
                cancel_btn.obj = this;
                cancel_btn.innerHTML = "&#xd7;";
                cancel_btn.title = "Cancel";
                cancel_btn.onclick = function() {
                        this.obj.action = "logged_in";
                        this.obj.changed = true;
                }
                settings_pw.appendChild(cancel_btn);

		var settings_pa = document.createElement("div");
		settings_pa.className = "subset";
		settings_form.appendChild(settings_pa);

		var span_t = document.createElement("div");
		span_t.innerHTML = "Allergies";
		settings_pa.appendChild(span_t);

		var table = document.createElement("table");

		var c = 0;
		var row = null;
		for (var allergy in this.allergies) {
			if (this.allergies.hasOwnProperty(allergy)) {
				if (!allergy.startsWith('A')) continue;

				if (c % 3 == 0) {
					row = document.createElement("tr");
					table.appendChild(row);
				}
				c++;

				var col = document.createElement("td");
				col.style.textAlign = "right";
				row.appendChild(col);

				var checkbox_lbl = document.createElement("span");
				checkbox_lbl.innerHTML = allergy.substring(1);
				col.appendChild(checkbox_lbl);


				var col_2 = document.createElement("td");
				col_2.style.textAlign = "left";
				row.appendChild(col_2);

				var checkbox = document.createElement("input");
				checkbox.id = this.widget.name + "_" + allergy;
				checkbox.type = "checkbox";
				if (this.allergies[allergy] == null) {
					if (allergy.startsWith('ANot')) {
						checkbox.checked = true;
					} else {
						checkbox.checked = false;
					}
				} else {
					if (this.allergies[allergy] == 1) {
						checkbox.checked = true;
					} else {
						checkbox.checked = false;
					}
				}
				col_2.appendChild(checkbox);
			}
		}

		var row = document.createElement("tr");
		var col = document.createElement("td");
		col.colSpan = "6";
		row.appendChild(col);
		table.appendChild(row);

		settings_pa.appendChild(table);

		var apply_button = document.createElement("button");
		apply_button.obj = this;
		apply_button.innerHTML = "&#10003;";
		apply_button.title = "Apply";
		apply_button.onclick = function() {
			var p = { "allergies" : {} };
			for (var allergy in this.obj.allergies) {
                	        if (this.obj.allergies.hasOwnProperty(allergy)) {
        	                        if (!allergy.startsWith('A')) continue;
					p["allergies"][allergy] = document.getElementById(this.obj.widget.name + "_" + allergy).checked;
				}
			}
			user.db.query_post("users/login/update", p, user.on_settings_response);
		}
		col.appendChild(apply_button);

		var cancel_button = document.createElement("button");
                cancel_button.obj = this;
                cancel_button.innerHTML = "&#xd7;";
		cancel_button.title = "Cancel";
                cancel_button.onclick = function() {
                        this.obj.action = "logged_in";
                        this.obj.changed = true;
                }
                col.appendChild(cancel_button);


                var settings_ps = document.createElement("div");
                settings_ps.className = "subset";
                settings_form.appendChild(settings_ps);

                var span_ps = document.createElement("div");
                span_ps.innerHTML = "Products Sources";
                settings_ps.appendChild(span_ps);

		var input_name = document.createElement("input");
		input_name.id = user.widget.name + "_ps_find_Name";
		input_name.placeholder = "Name";
		settings_ps.appendChild(input_name);

		var input_address = document.createElement("input");
		input_address.id = user.widget.name + "_ps_find_Address";
		input_address.placeholder = "Address";
		settings_ps.appendChild(input_address);

		var input_zipcode = document.createElement("input");
		input_zipcode.id = user.widget.name + "_ps_find_Zipcode";
		input_zipcode.placeholder = "Zipcode";
		settings_ps.appendChild(input_zipcode);

		var input_city = document.createElement("input");
		input_city.id = user.widget.name + "_ps_find_City";
		input_city.placeholder = "City";
		settings_ps.appendChild(input_city);

		var find_button = document.createElement("button");
		find_button.innerHTML = "&#128269;";
		find_button.title = "Search";
		find_button.style.fontSize = "20px";
		find_button.onclick = function() {
			var p = {
				"products_source_search": {
					"Name": document.getElementById(user.widget.name + "_ps_find_Name").value,
					"Address": document.getElementById(user.widget.name + "_ps_find_Address").value,
					"Zipcode": document.getElementById(user.widget.name + "_ps_find_Zipcode").value,
					"City": document.getElementById(user.widget.name + "_ps_find_City").value,
				}
			};
			user.db.query_post("products/source/find", p, user.on_products_source_find_response);
		}
		settings_ps.appendChild(find_button);

		settings_ps.appendChild(document.createElement("br"));

		var find_select = document.createElement("select");
		find_select.id = user.widget.name + "_ps_find_result";
		find_select.style.display = "none";
		settings_ps.appendChild(find_select);

		return settings_form;
	}

	this.on_products_source_find_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var select = document.getElementById(user.widget.name + "_ps_find_result");
			select.innerHTML = "";
			var has_result = false;
			for (var r in resp["products_source"]) {
				if (resp["products_source"].hasOwnProperty(r)) {
					has_result = true;
					var current = resp["products_source"][r];
					var option = document.createElement("option");
					option.value = r;
					option.innerHTML = current["Name"] + ", " + current["Address"] + ", " + current["Zipcode"] + " " + current["City"];
					select.appendChild(option);
				}
			}

			var add_button = document.getElementById(user.widget.name + "_ps_find_result_add");

			if (!has_result) {
				var option = document.createElement("option");
                                option.value = 0;
				option.innerHTML = "No result";
				select.disabled = "disabled";
				select.appendChild(option);

				if (add_button != null) {
					add_button.disabled = "disabled";
				}
			} else {
				if (add_button == null) {
					var add_button = document.createElement("button");
					add_button.id = user.widget.name + "_ps_find_result_add";
					add_button.obj = select;
					add_button.innerHTML = "&#xFF0B;";
					add_button.onclick = function() {
						var p = {
							'new_users_products_source_item': { 'ProductsSourceId': this.obj.options[this.obj.selectedIndex].value }
						}
						user.db.query_post("users/productssource/insert", p, user.on_users_productssource_insert_response);
					}
					select.parentNode.appendChild(add_button);
				} else {
					add_button.disabled = false;
				}
				select.disabled = false;
			}
			select.style.display = "inline-block";
		}
	}

	this.on_users_productssource_insert_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var products_source_added = document.createElement("div");
                        products_source_added.innerHTML = "Products source added.";
                        products_source_added.style.background = "green";

                        messagebox.message_add(products_source_added, 1000, "no-class", "products_source_addded", true);

			user.action = "logged_in";
			user.changed_f();
		} else {
			var products_source_not_added = document.createElement("div");
                        products_source_not_added.innerHTML = resp["error"];
                        products_source_not_added.style.background = "yellow";

			messagebox.message_add(products_source_not_added, 1000, "no-class", "products_source_not_added", true);
		}
	}

	this.update = function() {
		if (this.changed) {
			this.elem.style.display = "block";

			this.changed = false;

			this.widget.content.innerHTML = "";

			if (this.login_data != null) {		/* LOGGED IN */
				if (this.action === "settings") {
					this.widget.content.appendChild(this.settings_form_create());
				} else {
					this.widget.content.appendChild(this.logged_in_create());
				}
			} else if (this.action === "login"){	/* LOGGED OUT */
				this.widget.content.appendChild(this.login_form_create());
			} else if (this.action === "register") { /* REGISTER */
				this.widget.content.appendChild(this.register_form_create());
			}
		}
	}
}
