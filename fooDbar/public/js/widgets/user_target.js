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

	this.user_target = document.createElement("div");
	this.widget.content.appendChild(this.user_target);

	this.on_remove_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var to_delete_element = document.getElementById(user_target.widget.name + "_target_elem_" + resp["deleted_target"]["Id"]);
			user_target.user_target.removeChild(to_delete_element);
			user_target.changed_f();
		}
	}

	this.get_target_node = function(target) {
		var target_elem = document.createElement("div");
		target_elem.id = user_target.widget.name + "_target_elem_" + target["Id"];
                target_elem.appendChild(document.createTextNode(JSON.stringify(target)));

		var delete_button = document.createElement("button");
		delete_button.obj = target;
		delete_button.innerHTML = "delete";
		delete_button.onclick = function() {
			var p = {
				"target_id": this.obj["Id"]
			};
			user_target.db.query_post("users/target/remove", p, user_target.on_remove_response);
		};
		target_elem.appendChild(delete_button);

		return target_elem;
	}


	this.on_target_add_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_target.target[resp["new_target"]["DateInsert"]] = resp["new_target"];
			if (user_target.user_target.childElementCount > 1) {
				user_target.user_target.insertBefore(user_target.get_target_node(resp["new_target"]), user_target.user_target.children[1]);
			} else {
				user_target.user_target.appendChild(user_target.get_target_node(resp["new_target"]));
			}
			user_target.changed_f();
		}
	}

	this.insert_user_target_form_create = function(newest_target_element) {
		var user_target_form = document.createElement("div");

		var bmi_elem = document.createElement("input");
		bmi_elem.id = user_target.widget.name + "_bmi";
		bmi_elem.placeholder = "BMI (eg 21.0)";
		user_target_form.appendChild(bmi_elem);

		var fat_perc = document.createElement("input");
                fat_perc.id = user_target.widget.name + "_fat_percent";
                fat_perc.placeholder = "Fat% (0-100 or empty)";
                user_target_form.appendChild(fat_perc);

		var muscle_perc = document.createElement("input");
                muscle_perc.id = user_target.widget.name + "_muscle_percent";
                muscle_perc.placeholder = "Muscle% (0-100 or empty)";
                user_target_form.appendChild(muscle_perc);

                if (newest_target_element != null) {
                        bmi_elem.value = newest_target_element["Bmi"];
			if (newest_target_element["FatPercent"] != null) {
                                fat_perc.value = newest_target_element["FatPercent"];
                        }
                        if (newest_target_element["MusclePercent"] != null) {
                                bone_perc.value = newest_target_element["MusclePercent"];
                        }
                }

		var target_add_button = document.createElement("button");
		target_add_button.obj = this;
		target_add_button.innerHTML = "Add target";
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
		user_target_form.appendChild(target_add_button);

		return user_target_form;
	}

        this.on_user_target_response = function() {
                var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_target.target = resp["target"];
			user_target.user_target.innerHTML = "";
			if (resp["no_target"] == true) {
				var no_target_elem = document.createElement("div");
                                no_target_elem.innerHTML = "Please insert your current target.";
                                no_target_elem.style.background = "yellow";
				messagebox.message_add(no_target_elem, 1000, "no-class", "no_state", true);

				var form_elem = user_target.insert_user_target_form_create(null);
                                user_target.user_target.appendChild(form_elem);
			} else {
				var first = true;
				for (var target in user_target.target) {
					if (user_target.target.hasOwnProperty(target)) {
						if (first == true) {
							var form_elem = user_target.insert_user_target_form_create(user_target.target[target]);
							user_target.user_target.appendChild(form_elem);
							first = false;
						}
						var target_elem = user_target.get_target_node(user_target.target[target]);
						user_target.user_target.appendChild(target_elem);
					}
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
