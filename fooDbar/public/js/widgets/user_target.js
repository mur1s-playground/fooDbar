var UserTarget = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.target = null;

	this.widget = new Widget("User Target");

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

	this.user_target = document.createElement("table");
	this.user_target.className = "user_target";
	this.widget.content.appendChild(this.user_target);

	this.get_user_target_header = function() {
                var user_target_header = document.createElement("tr");

                var bmi_elem = document.createElement("td");
                var bmi_img = document.createElement("img");
                bmi_img.src = "/img/symbol_bmi.svg";
                bmi_img.style.height = "60px";
                bmi_img.title = "BMI";
                bmi_elem.appendChild(bmi_img);
                user_target_header.appendChild(bmi_elem);

                var weight_elem = document.createElement("td");
                var weight_img = document.createElement("img");
                weight_img.src = "/img/symbol_person_weight.svg";
                weight_img.style.height = "60px";
                weight_img.title = "Weight";
                weight_elem.appendChild(weight_img);
                user_target_header.appendChild(weight_elem);

                var fat_elem = document.createElement("td");
                var fat_img = document.createElement("img");
                fat_img.src = "/img/symbol_fat.svg";
                fat_img.style.height = "60px";
                fat_img.title = "Fat %";
                fat_elem.appendChild(fat_img);
                user_target_header.appendChild(fat_elem);

                var muscle_elem = document.createElement("td");
                var muscle_img = document.createElement("img");
                muscle_img.src = "/img/symbol_muscle.svg";
                muscle_img.style.height = "60px";
                muscle_img.title = "Muscle %";
                muscle_elem.appendChild(muscle_img);
                user_target_header.appendChild(muscle_elem);

		var date_elem = document.createElement("td");
                var date_img = document.createElement("img");
                date_img.src = "/img/symbol_calendar.svg";
                date_img.style.height = "60px";
                date_img.title = "Date";
                date_elem.appendChild(date_img);
                user_target_header.appendChild(date_elem);

                var delete_elem = document.createElement("td");
                user_target_header.appendChild(delete_elem);

		return user_target_header;
	}

	this.on_remove_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var to_delete_element = document.getElementById(user_target.widget.name + "_target_elem_" + resp["deleted_target"]["Id"]);
			user_target.user_target.removeChild(to_delete_element);
			user_target.changed_f();
		}
	}

	this.get_target_node = function(target) {
		var target_elem = document.createElement("tr");
		target_elem.id = user_target.widget.name + "_target_elem_" + target["Id"];

		var bmi_elem = document.createElement("td");
                bmi_elem.appendChild(document.createTextNode(target["Bmi"]));
                target_elem.appendChild(bmi_elem);

		var weight_val = "";
		console.log(user_state.newest_height);
		if (user_state.newest_height != null) {
                        weight_val = Math.round(parseFloat(target["Bmi"])*parseFloat(user_state.newest_height)*parseFloat(user_state.newest_height) * 100)/100;
                }

		var weight_elem = document.createElement("td");
		weight_elem.appendChild(document.createTextNode(weight_val));
		target_elem.appendChild(weight_elem);

                var fat_elem = document.createElement("td");
                if (target["FatPercent"] != null) {
                        fat_elem.appendChild(document.createTextNode(target["FatPercent"]));
                } else {
                        fat_elem.appendChild(document.createTextNode("n/A"));
                }
                target_elem.appendChild(fat_elem);

                var muscle_elem = document.createElement("td");
                if (target["MusclePercent"] != null) {
                        muscle_elem.appendChild(document.createTextNode(target["MusclePercent"]));
                } else {
                        muscle_elem.appendChild(document.createTextNode("n/A"));
                }
                target_elem.appendChild(muscle_elem);

		var date_elem = document.createElement("td");
                date_elem.appendChild(document.createTextNode(target["DateInsert"]));
                target_elem.appendChild(date_elem);

		var del_elem = document.createElement("td");
		var delete_button = document.createElement("button");
		delete_button.obj = target;
		delete_button.innerHTML = "&#xd7;"
		delete_button.title = "Delete";
		delete_button.onclick = function() {
			var p = {
				"target_id": this.obj["Id"]
			};
			var r = confirm("Delete target from " + target["DateInsert"] + "?");
			if (r == 1) {
				user_target.db.query_post("users/target/remove", p, user_target.on_remove_response);
			}
		};
		del_elem.appendChild(delete_button);
		target_elem.appendChild(del_elem);

		return target_elem;
	}


	this.on_target_add_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_target.target[resp["new_target"]["Id"]] = resp["new_target"];
			user_target.user_target.insertBefore(user_target.get_target_node(resp["new_target"]), user_target.user_target.children[1]);
			user_target.changed_f();
		}
	}

	this.on_insert_weight_factor_change = function() {
		var bmi = document.getElementById(user_target.widget.name + "_bmi");
		if (user_state.newest_height != null) {
        	        var weight = document.getElementById(user_target.widget.name + "_weight");
			weight.innerHTML = Math.round(parseFloat(bmi.value)*parseFloat(user_state.newest_height)*parseFloat(user_state.newest_height) * 100)/100;
		}
	}

	this.insert_user_target_form_create = function(newest_target_element) {
		var user_target_form = document.createElement("tr");

		var bmi_col = document.createElement("td");
		var bmi_elem = document.createElement("input");
		bmi_elem.id = user_target.widget.name + "_bmi";
		bmi_elem.placeholder = "BMI (eg 21.0)";
		bmi_elem.oninput = function() {
			user_target.on_insert_weight_factor_change();
		}
		bmi_col.appendChild(bmi_elem);
		user_target_form.appendChild(bmi_col);

		var weight_col = document.createElement("td");
		var weight_span = document.createElement("span");
		weight_span.id = user_target.widget.name + "_weight";
		weight_col.appendChild(weight_span);
		user_target_form.appendChild(weight_col);

		var fat_col = document.createElement("td");
		var fat_perc = document.createElement("input");
                fat_perc.id = user_target.widget.name + "_fat_percent";
                fat_perc.placeholder = "Fat% (0-100 or empty)";
		fat_col.appendChild(fat_perc);
                user_target_form.appendChild(fat_col);

		var muscle_col = document.createElement("td");
		var muscle_perc = document.createElement("input");
                muscle_perc.id = user_target.widget.name + "_muscle_percent";
                muscle_perc.placeholder = "Muscle% (0-100 or empty)";
		muscle_col.appendChild(muscle_perc);
                user_target_form.appendChild(muscle_col);

                if (newest_target_element != null) {
                        bmi_elem.value = newest_target_element["Bmi"];
			if (newest_target_element["FatPercent"] != null) {
                                fat_perc.value = newest_target_element["FatPercent"];
                        }
                        if (newest_target_element["MusclePercent"] != null) {
                                bone_perc.value = newest_target_element["MusclePercent"];
                        }
                }

		var date_col = document.createElement("td");
		user_target_form.appendChild(date_col);

		var add_col = document.createElement("td");
		var target_add_button = document.createElement("button");
		target_add_button.obj = this;
		target_add_button.innerHTML = "&#xFF0B;";
		target_add_button.onclick = function() {
			var p = {
				"target" : {
					"Bmi": document.getElementById(this.obj.widget.name + "_bmi").value,
					"FatPercent": document.getElementById(this.obj.widget.name + "_fat_percent").value,
					"MusclePercent": document.getElementById(this.obj.widget.name + "_muscle_percent").value,
				}
			};
			this.obj.db.query_post("users/target/insert", p, this.obj.on_target_add_response);
		}
		add_col.appendChild(target_add_button);
		user_target_form.appendChild(add_col);

		return user_target_form;
	}

        this.on_user_target_response = function() {
                var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_target.target = resp["target"];
			user_target.user_target.innerHTML = "";

			user_target.user_target.appendChild(user_target.get_user_target_header());

			if (resp["no_target"] == true) {
				var no_target_elem = document.createElement("div");
                                no_target_elem.innerHTML = "Please insert your current target.";
                                no_target_elem.style.background = "yellow";
				messagebox.message_add(no_target_elem, 1000, "no-class", "no_state", true);

				var form_elem = user_target.insert_user_target_form_create(null);
                                user_target.user_target.appendChild(form_elem);
				user_target.on_insert_weight_factor_change();
			} else {
				var idx = [];
				for (var target in user_target.target) {
					if (user_target.target.hasOwnProperty(target)) {
						idx.push(parseInt(target));
					}
				}
				idx.sort(function(a,b) {return b-a});
				for (var i = 0; i < idx.length; i++) {
					if (i == 0) {
						var form_elem = user_target.insert_user_target_form_create(user_target.target[idx[i]]);
                                                user_target.user_target.appendChild(form_elem);
					}
					var target_elem = user_target.get_target_node(user_target.target[idx[i]]);
                                        user_target.user_target.appendChild(target_elem);
                                        user_target.on_insert_weight_factor_change();
				}
			}
		}
        }

	this.update = function() {
		if (this.changed) {
			this.elem.style.display = "block";

			this.changed = false;

			if (user.login_data != null) {
				var p = {};
				user_target.db.query_post("users/target", p, user_target.on_user_target_response);
			} else {
				user_target.user_target.innerHTML = "";
			}
		}
	}
}
