var UserState = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.state = null;

	this.widget = new Widget("User State");

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

	this.user_state = document.createElement("div");
	this.widget.content.appendChild(this.user_state);

	this.on_remove_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var to_delete_element = document.getElementById(user_state.widget.name + "_state_elem_" + resp["deleted_state"]["Id"]);
			user_state.user_state.removeChild(to_delete_element);
			user_state.changed_f();
		}
	}

	this.get_state_node = function(state) {
		var state_elem = document.createElement("div");
		state_elem.id = user_state.widget.name + "_state_elem_" + state["Id"];
                state_elem.appendChild(document.createTextNode(JSON.stringify(state)));

		var b_height = parseFloat(state["Height"]);
		var b_weight = parseFloat(state["Weight"]);

		var bmi_elem = document.createElement("span");
		bmi_elem.innerHTML = Math.round(b_weight/(b_height * b_height) * 100) / 100;
		state_elem.appendChild(bmi_elem);

		var delete_button = document.createElement("button");
		delete_button.obj = state;
		delete_button.innerHTML = "delete";
		delete_button.onclick = function() {
			var p = {
				"state_id": this.obj["Id"]
			};
			user_state.db.query_post("users/state/remove", p, user_state.on_remove_response);
		};
		state_elem.appendChild(delete_button);

		return state_elem;
	}


	this.on_state_add_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_state.state[resp["new_state"]["DatetimeInsert"]] = resp["new_state"];
			if (user_state.user_state.childElementCount > 1) {
				user_state.user_state.insertBefore(user_state.get_state_node(resp["new_state"]), user_state.user_state.children[1]);
			} else {
				user_state.user_state.appendChild(user_state.get_state_node(resp["new_state"]));
			}
			user_state.changed_f();
		}
	}

	this.insert_user_state_form_create = function(newest_state_element) {
		var user_state_form = document.createElement("div");

		var height_elem = document.createElement("input");
		height_elem.id = user_state.widget.name + "_height";
		height_elem.placeholder = "Height in m (eg. 1.80)";
		user_state_form.appendChild(height_elem);

		var weight_elem = document.createElement("input");
		weight_elem.id = user_state.widget.name + "_weight";
		weight_elem.placeholder = "Weight in kg (eg. 70.5)";
		user_state_form.appendChild(weight_elem);

		var fat_perc = document.createElement("input");
                fat_perc.id = user_state.widget.name + "_fat_percent";
                fat_perc.placeholder = "Fat% (0-100 or empty)";
                user_state_form.appendChild(fat_perc);

		var muscle_perc = document.createElement("input");
                muscle_perc.id = user_state.widget.name + "_muscle_percent";
                muscle_perc.placeholder = "Muscle% (0-100 or empty)";
                user_state_form.appendChild(muscle_perc);

                var bone_perc = document.createElement("input");
                bone_perc.id = user_state.widget.name + "_bone_percent";
                bone_perc.placeholder = "Bone% (0-100 or empty)";
                user_state_form.appendChild(bone_perc);

                var water_perc = document.createElement("input");
                water_perc.id = user_state.widget.name + "_water_percent";
                water_perc.placeholder = "Water% (0-100 or empty)";
                user_state_form.appendChild(water_perc);

		var slider_container = document.createElement("table");
		slider_container.style.width = "100%";
		var slider_row = document.createElement("tr");
		slider_container.appendChild(slider_row);
		var slider_c1 = document.createElement("td");
		slider_c1.innerHTML = "Sedentary/Light activity";
		slider_c1.title = "(140-169) 153 => 8h sleep, 1h personal care, 1h eating, 1h cooking, 8h sitting (office work, seeling produce, tending shop), 1h general household work, 1h driving car, 1h walking, 2h light activities (watching TV, chatting)";
		slider_c1.style.textAlign = "center";
		slider_row.appendChild(slider_c1);
		var slider_c2 = document.createElement("td");
		slider_c2.innerHTML = "Active/Moderatly active";
		slider_c2.title = "(170-199) 176 => 8h sleep, 1h personal care, 1h eating, 8h standing or carrying light loads (waiting tables, arranging merchandise), 1h using the bus, 1h walking without load, 1h low intensity aerobic exercise, 3h light activities (watching TV, chatting)";
		slider_c2.style.textAlign = "center";
		slider_row.appendChild(slider_c2);
		var slider_c3 = document.createElement("td");
		slider_c3.innerHTML = "Vigorous/Vigorously active";
		slider_c3.title = "(200-240) 225 => 8h sleep, 1h personal care, 1h eating, 1h cooking, 6h non-mechanized agricultural work (planting, weeding, gathering), 1h collecting water/wood, 1h non-mechanized domestic chores (sweeping, washing clothes and dishes by hand), 1h walking without load, 4h miscellaneous light activities";
		slider_c3.style.textAlign = "center";
		slider_row.appendChild(slider_c3);

		var slider_row2 = document.createElement("tr");
		slider_container.appendChild(slider_row2);
		var slider_c21 = document.createElement("td");
		slider_c21.colSpan = "3";

		var pal_slider = document.createElement("input");
		pal_slider.obj = this;
		pal_slider.id = user_state.widget.name + "_pal_slider";
		pal_slider.style.width = "100%";
		pal_slider.type = "range";
		pal_slider.min = "100";
		pal_slider.max = "340";
		pal_slider.value = "176";
		pal_slider.onchange = function() {
			document.getElementById(this.obj.widget.name + "_pal").value = this.value;
		}
		slider_c21.appendChild(pal_slider);
		slider_row2.appendChild(slider_c21);

		var pal_input = document.createElement("input");
		pal_input.obj = this;
		pal_input.id = user_state.widget.name + "_pal";
		pal_input.value = "176";
		pal_input.oninput = function() {
			if (this.value < 100) this.value = 100;
			if (this.value > 340) this.value = 340;
			document.getElementById(this.obj.widget.name + "_pal_slider").value = this.value;
		}

		var slider_row3 = document.createElement("tr");
		slider_container.appendChild(slider_row3);
		var slider_col31 = document.createElement("td");
		slider_col31.colSpan = "3";
		slider_col31.appendChild(pal_input);
		slider_row3.appendChild(slider_col31);

		user_state_form.appendChild(slider_container);

                if (newest_state_element != null) {
                        height_elem.value = newest_state_element["Height"];
			weight_elem.value = newest_state_element["Weight"];
			if (newest_state_element["FatPercent"] != null) {
                                fat_perc.value = newest_state_element["FatPercent"];
                        }
                        if (newest_state_element["MusclePercent"] != null) {
                                muscle_perc.value = newest_state_element["MusclePercent"];
                        }
			if (newest_state_element["BonePercent"] != null) {
				bone_perc.value = newest_state_element["BonePercent"];
			}
			if (newest_state_element["WaterPercent"] != null) {
                                water_perc.value = newest_state_element["WaterPercent"];
                        }
			pal_slider.value = newest_state_element["Pal"] * 100;
			pal_input.value = newest_state_element["Pal"] * 100;
                }

		var state_add_button = document.createElement("button");
		state_add_button.obj = this;
		state_add_button.innerHTML = "Add state";
		state_add_button.onclick = function() {
			var p = {
				"state" : {
					"Height": document.getElementById(this.obj.widget.name + "_height").value,
					"Weight": document.getElementById(this.obj.widget.name + "_weight").value,
					"FatPercent": document.getElementById(this.obj.widget.name + "_fat_percent").value,
					"MusclePercent": document.getElementById(this.obj.widget.name + "_muscle_percent").value,
					"BonePercent": document.getElementById(this.obj.widget.name + "_bone_percent").value,
					"WaterPercent": document.getElementById(this.obj.widget.name + "_water_percent").value,
					"Pal": document.getElementById(this.obj.widget.name + "_pal").value
				}
			};
			this.obj.db.query_post("users/state/insert", p, this.obj.on_state_add_response);
		}
		user_state_form.appendChild(state_add_button);

		return user_state_form;
	}

        this.on_user_state_response = function() {
                var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_state.state = resp["state"];
			user_state.user_state.innerHTML = "";
			if (resp["no_state"] == true) {
				var no_state_elem = document.createElement("div");
                                no_state_elem.innerHTML = "Please insert your current status.";
                                no_state_elem.style.background = "yellow";
				messagebox.message_add(no_state_elem, 1000, "no-class", "no_state", true);

				var form_elem = user_state.insert_user_state_form_create(null);
                                user_state.user_state.appendChild(form_elem);
			} else {
				var first = true;
				for (var state in user_state.state) {
					if (user_state.state.hasOwnProperty(state)) {
						if (first == true) {
							var form_elem = user_state.insert_user_state_form_create(user_state.state[state]);
							user_state.user_state.appendChild(form_elem);
							first = false;
						}
						var state_elem = user_state.get_state_node(user_state.state[state]);
						user_state.user_state.appendChild(state_elem);
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
				user_state.db.query_post("users/state", p, user_state.on_user_state_response);
			} else {
				user_state.user_state.innerHTML = "";
			}
		}
	}
}
