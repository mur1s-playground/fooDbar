var UserState = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.state = null;
	this.newest_height = null;

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

	this.user_state = document.createElement("table");
	this.user_state.className = "user_state";
	this.widget.content.appendChild(this.user_state);

	this.pal_calc = document.createElement("tr");
	this.pal_calc.style.display = "none";

	this.pal_desc =  [
                        ["sleep",                                                                       1.0],
                        ["personal care (dressing, showering)",                                         2.3],
                        ["eating",                                                                      1.5],
                        ["cooking",                                                                     2.1],
                        ["non-mechanized domestic chores (sweeping, washing clothes and dishes by hand)", 2.3],
                        ["sitting (office work, selling produce, tending shop)",                        1.5],
                        ["standing, carrying light loads (waiting on tables, arranging merchandise)",   2.2],
                        ["general household work",                                                      2.8],
                        ["non mechanized agricultural work (planting, weeding, gathering)",             4.1],
                        ["collecting water/wood",                                                       4.4],
                        ["commuting to/from work on the bus",                                           1.2],
                        ["driving car (from/to work)",                                                  2.0],
                        ["walking (at varying paces without a load)",                                   3.2],
                        ["light activities (watching TV, chatting)",                                    1.4],
                        ["low intensity aerobic exercise",                                              4.2]
                ];

	this.pal_examples = [
		[[8, 0], [1, 1], [1, 2], [1, 3], [8, 5], [1, 7], [1, 11], [1, 12], [2, 13]],
		[[8, 0], [1, 1], [1, 2], [8, 6], [1, 10], [1, 12], [1, 14], [3, 13]],
		[[8, 0], [1, 1], [1, 2], [1, 3], [6, 8], [1, 9], [1, 4], [1, 12], [4, 13]]
	];

	this.on_pal_slider_change = function(sl_id, is_custom = true) {
		var slider_elem = document.getElementById(user_state.widget.name + "_pal_slider_" + sl_id);

 		var span_elem = document.getElementById(user_state.widget.name + "_pal_span_" + sl_id);
                span_elem.innerHTML = Math.round(slider_elem.value/60 * 100)/100;

                var value = slider_elem.value/60 * user_state.pal_desc[sl_id][1];
                var times = slider_elem.value/60;
                for (var d = 0; d < user_state.pal_desc.length; d++) {
                	if (d != sl_id) {
                        	var sp_elem = document.getElementById(user_state.widget.name + "_pal_span_" + d);
                                value += parseFloat(sp_elem.innerHTML) * user_state.pal_desc[d][1];
                                times += parseFloat(sp_elem.innerHTML);
                        }
                }
                var result_elem = document.getElementById(user_state.widget.name + "_pal_span_result");
                result_elem.innerHTML = Math.round(value/times * 100)/100;
		if (is_custom) {
			document.getElementById(user_state.widget.name + "_pal_select").selectedIndex = 3;
		}
	}


	this.get_pal_calc_container = function() {
		var pal_container = document.createElement("td");
		pal_container.colSpan = "10";

		var table = document.createElement("table");
		table.className = "pal_table";

		var example = this.pal_examples[1];

		var row = document.createElement("tr");
		for (var c = 0; c < 2; c++) {
			var col_1 = document.createElement("td");
        	        var col_2 = document.createElement("td");

			if (c == 0) {
				var select_example = document.createElement("select");
				select_example.id = this.widget.name + "_pal_select";
				select_example.onchange = function() {
					for (var d = 0; d < user_state.pal_desc.length; d++) {
						if (this.selectedIndex < user_state.pal_examples.length) {
							var found = false;
							for (var e = 0; e < user_state.pal_examples[this.selectedIndex].length; e++) {
								if (user_state.pal_examples[this.selectedIndex][e][1] == d) {
									var slider_elem = document.getElementById(user_state.widget.name + "_pal_slider_" + d);
									slider_elem.value = user_state.pal_examples[this.selectedIndex][e][0] * 60;
									found = true;
									break;
								}
							}
							if (!found) {
								var slider_elem = document.getElementById(user_state.widget.name + "_pal_slider_" + d);
                                                                slider_elem.value = 0;
							}
							user_state.on_pal_slider_change(d, false);
						}
					}
				};

				var opts = [ "Sedentary/Light activity", "Active/moderately active", "Vigorous/Vigorously active", "custom"];

				for (var o = 0; o < opts.length; o++) {
					var options = document.createElement("option");
					options.innerHTML = opts[o];
					select_example.appendChild(options);
				}
				select_example.selectedIndex = 1;
				col_1.appendChild(select_example);
			}

			col_2.className = "pal_3";
			col_2.colSpan = "2";
                	row.appendChild(col_1);
        	        row.appendChild(col_2);
			col_2.innerHTML = "<img src='/img/symbol_clock.svg' style='height: 30px;' />";
		}
		table.appendChild(row);

		var cost = 0.0;

		for (var d = 0; d < this.pal_desc.length; d++) {
			var row = document.createElement("tr");
		for (var c = 0; c < 2; c++) {
			var col_1 = document.createElement("td");
			var col_2 = document.createElement("td");
			col_2.className = "pal_2";
			var col_3 = document.createElement("td");
			col_3.className = "pal_3";
			row.appendChild(col_1);
			row.appendChild(col_2);
			row.appendChild(col_3);
			if (d < this.pal_desc.length) {
			col_1.innerHTML = this.pal_desc[d][0];

			var slider = document.createElement("input");
			slider.style.minWidth = "100%";
			slider.id = this.widget.name + "_pal_slider_" + d;
			slider.type = "range";
			slider.d = d;
			slider.min = 0;
			slider.max = 24 * 60;
			slider.value = 0;
			for (var e = 0; e < example.length; e++) {
				if (example[e][1] == d) {
					slider.value = example[e][0] * 60;
					break;
				}
			}

			slider.onchange = function() {
				user_state.on_pal_slider_change(this.d);
			};

			var sl_span = document.createElement("span");
			sl_span.id = user_state.widget.name + "_pal_span_" + d;
			sl_span.innerHTML = Math.round(slider.value/60 * 100)/100;

			col_2.appendChild(slider);
			col_3.appendChild(sl_span);
			}
			if (c == 0) d++;
		}
			table.appendChild(row);
		}

		var row = document.createElement("tr");
		row.className = "pal_footer";
                var col_1 = document.createElement("td");
		col_1.colSpan = "6";
                row.appendChild(col_1);
		var sp_res = document.createElement("span");
		sp_res.className = "pal_result";
		sp_res.id = this.widget.name + "_pal_span_result";
		col_1.appendChild(sp_res);

		var apply_button = document.createElement("button");
		apply_button.innerHTML = "&#10003;";
		apply_button.title = "Apply";
		apply_button.onclick = function() {
			document.getElementById(user_state.widget.name + "_pal").value = document.getElementById(user_state.widget.name + "_pal_span_result").innerHTML;
			user_state.toggle_pal_calc();
		}
		col_1.appendChild(apply_button);

		var cancel_button = document.createElement("button");
		cancel_button.innerHTML = "&#xd7;";
		cancel_button.title = "Close";
		cancel_button.onclick = function() {
			user_state.toggle_pal_calc();
		}
		col_1.appendChild(cancel_button);

                table.appendChild(row);

		pal_container.appendChild(table);
		return pal_container;
	}

	this.toggle_pal_calc = function() {
		if (user_state.pal_calc.style.display == "none") {
			user_state.pal_calc.style.display = "table-row";
		} else {
			user_state.pal_calc.style.display = "none";
		}
	}

	this.get_user_state_header = function() {
		var user_state_header = document.createElement("tr");

		var bmi_elem = document.createElement("td");
		var bmi_img = document.createElement("img");
		bmi_img.src = "/img/symbol_bmi.svg";
		bmi_img.style.height = "60px";
		bmi_img.title = "BMI";
		bmi_elem.appendChild(bmi_img);
		user_state_header.appendChild(bmi_elem);

		var height_elem = document.createElement("td");
		var height_img = document.createElement("img");
		height_img.src = "/img/symbol_person_height.svg";
		height_img.style.height = "60px";
		height_img.title = "Height";
		height_elem.appendChild(height_img);
		user_state_header.appendChild(height_elem);

		var weight_elem = document.createElement("td");
		var weight_img = document.createElement("img");
		weight_img.src = "/img/symbol_person_weight.svg";
		weight_img.style.height = "60px";
		weight_img.title = "Weight";
		weight_elem.appendChild(weight_img);
		user_state_header.appendChild(weight_elem);

		var fat_elem = document.createElement("td");
		var fat_img = document.createElement("img");
		fat_img.src = "/img/symbol_fat.svg";
		fat_img.style.height = "60px";
		fat_img.title = "Fat %";
		fat_elem.appendChild(fat_img);
		user_state_header.appendChild(fat_elem);

		var muscle_elem = document.createElement("td");
                var muscle_img = document.createElement("img");
                muscle_img.src = "/img/symbol_muscle.svg";
                muscle_img.style.height = "60px";
		muscle_img.title = "Muscle %";
                muscle_elem.appendChild(muscle_img);
                user_state_header.appendChild(muscle_elem);

                var bone_elem = document.createElement("td");
                var bone_img = document.createElement("img");
                bone_img.src = "/img/symbol_bone.svg";
                bone_img.style.height = "60px";
		bone_img.title = "Bone %";
                bone_elem.appendChild(bone_img);
                user_state_header.appendChild(bone_elem);

                var water_elem = document.createElement("td");
                var water_img = document.createElement("img");
                water_img.src = "/img/symbol_water.svg";
                water_img.style.height = "60px";
		water_img.title = "Water %";
                water_elem.appendChild(water_img);
                user_state_header.appendChild(water_elem);

                var pal_elem = document.createElement("td");
                var pal_img = document.createElement("img");
                pal_img.src = "/img/symbol_pal.svg";
                pal_img.style.height = "60px";
                pal_img.title = "Pal";
                pal_elem.appendChild(pal_img);
                user_state_header.appendChild(pal_elem);

		var date_elem = document.createElement("td");
		var date_img = document.createElement("img");
		date_img.src = "/img/symbol_calendar.svg";
		date_img.style.height = "60px";
		date_img.title = "Date";
		date_elem.appendChild(date_img);
		user_state_header.appendChild(date_elem);

		var delete_elem = document.createElement("td");
		user_state_header.appendChild(delete_elem);

		return user_state_header;
	}

	this.on_remove_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var to_delete_element = document.getElementById(user_state.widget.name + "_state_elem_" + resp["deleted_state"]["Id"]);
			user_state.user_state.removeChild(to_delete_element);
			user_state.changed_f();
		}
	}

	this.get_state_node = function(state) {
		var state_elem = document.createElement("tr");
		state_elem.id = user_state.widget.name + "_state_elem_" + state["Id"];

		var b_height = parseFloat(state["Height"]);
                var b_weight = parseFloat(state["Weight"]);
                var bmi =  Math.round(b_weight/(b_height * b_height) * 100) / 100;

		var bmi_elem = document.createElement("td");
		bmi_elem.appendChild(document.createTextNode(bmi));
		state_elem.appendChild(bmi_elem);

                var height_elem = document.createElement("td");
		height_elem.id = user_state.widget.name + "_state_elem_" + state["Id"] + "_height";
		height_elem.appendChild(document.createTextNode(state["Height"]));
                state_elem.appendChild(height_elem);

                var weight_elem = document.createElement("td");
		weight_elem.appendChild(document.createTextNode(state["Weight"]));
                state_elem.appendChild(weight_elem);

                var fat_elem = document.createElement("td");
		if (state["FatPercent"] != null) {
			fat_elem.appendChild(document.createTextNode(state["FatPercent"]));
		} else {
                        fat_elem.appendChild(document.createTextNode("n/A"));
                }
                state_elem.appendChild(fat_elem);

                var muscle_elem = document.createElement("td");
		if (state["MusclePercent"] != null) {
			muscle_elem.appendChild(document.createTextNode(state["MusclePercent"]));
		} else {
                        muscle_elem.appendChild(document.createTextNode("n/A"));
                }
                state_elem.appendChild(muscle_elem);

                var bone_elem = document.createElement("td");
		if (state["BonePercent"] != null) {
			bone_elem.appendChild(document.createTextNode(state["BonePercent"]));
		} else {
                        bone_elem.appendChild(document.createTextNode("n/A"));
                }
                state_elem.appendChild(bone_elem);

                var water_elem = document.createElement("td");
		if (state["WaterPercent"] != null) {
			water_elem.appendChild(document.createTextNode(state["WaterPercent"]));
		} else {
			water_elem.appendChild(document.createTextNode("n/A"));
		}
                state_elem.appendChild(water_elem);

                var pal_elem = document.createElement("td");
		pal_elem.appendChild(document.createTextNode(state["Pal"]));
                state_elem.appendChild(pal_elem);

		var date_elem = document.createElement("td");
		date_elem.appendChild(document.createTextNode(state["DatetimeInsert"]));
		state_elem.appendChild(date_elem);

		var del_elem = document.createElement("td");
		var delete_button = document.createElement("button");
		delete_button.obj = state;
		delete_button.innerHTML = "&#128465;";
		delete_button.title = "Delete";
		delete_button.onclick = function() {
			var p = {
				"state_id": this.obj["Id"]
			};
			var r = confirm("Delete state from " + this.obj["DatetimeInsert"] + "?");
			if (r == 1) {
				user_state.db.query_post("users/state/remove", p, user_state.on_remove_response);
			}
		};
		del_elem.appendChild(delete_button);
		state_elem.appendChild(del_elem);

		return state_elem;
	}


	this.on_state_add_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_state.state[resp["new_state"]["Id"]] = resp["new_state"];
			user_state.user_state.insertBefore(user_state.get_state_node(resp["new_state"]), user_state.user_state.children[1]);
			user_state.changed_f();
		}
	}

	this.on_insert_bmi_factors_change = function() {
		var bmi = document.getElementById(user_state.widget.name + "_bmi");
		var height = document.getElementById(user_state.widget.name + "_height");
		var weight = document.getElementById(user_state.widget.name + "_weight");

		if (parseFloat(height.value) > 0) bmi.innerHTML = Math.round(parseFloat(weight.value)/(parseFloat(height.value)*parseFloat(height.value)) * 100) / 100;
	}

	this.insert_user_state_form_create = function(newest_state_element) {
		var user_state_form = document.createElement("tr");

		var bmi_col = document.createElement("td");
		var bmi_elem = document.createElement("span");
		bmi_elem.id = user_state.widget.name + "_bmi";
		bmi_col.appendChild(bmi_elem);
		user_state_form.appendChild(bmi_col);

		var height_col = document.createElement("td");
		var height_elem = document.createElement("input");
		height_elem.id = user_state.widget.name + "_height";
		height_elem.placeholder = "Height in m (eg. 1.80)";
		height_elem.oninput = function() { user_state.on_insert_bmi_factors_change(); };
		height_col.appendChild(height_elem);
		user_state_form.appendChild(height_col);

		var weight_col = document.createElement("td");
		var weight_elem = document.createElement("input");
		weight_elem.id = user_state.widget.name + "_weight";
		weight_elem.placeholder = "Weight in kg (eg. 70.5)";
		weight_elem.oninput = function() { user_state.on_insert_bmi_factors_change(); };
		weight_col.appendChild(weight_elem);
		user_state_form.appendChild(weight_col);

		var fat_col = document.createElement("td");
		var fat_perc = document.createElement("input");
                fat_perc.id = user_state.widget.name + "_fat_percent";
                fat_perc.placeholder = "Fat% (0-100 or empty)";
                fat_col.appendChild(fat_perc);
		user_state_form.appendChild(fat_col);

		var muscle_col = document.createElement("td");
		var muscle_perc = document.createElement("input");
                muscle_perc.id = user_state.widget.name + "_muscle_percent";
                muscle_perc.placeholder = "Muscle% (0-100 or empty)";
		muscle_col.appendChild(muscle_perc);
                user_state_form.appendChild(muscle_col);

		var bone_col = document.createElement("td");
                var bone_perc = document.createElement("input");
                bone_perc.id = user_state.widget.name + "_bone_percent";
                bone_perc.placeholder = "Bone% (0-100 or empty)";
                bone_col.appendChild(bone_perc);
		user_state_form.appendChild(bone_col);

		var water_col = document.createElement("td");
                var water_perc = document.createElement("input");
                water_perc.id = user_state.widget.name + "_water_percent";
                water_perc.placeholder = "Water% (0-100 or empty)";
		water_col.appendChild(water_perc);
                user_state_form.appendChild(water_col);

		var pal_col = document.createElement("td");
		var pal_input = document.createElement("input");
		pal_input.obj = this;
		pal_input.id = user_state.widget.name + "_pal";
		pal_input.value = "1.76";
		pal_col.appendChild(pal_input);

		var pal_button = document.createElement("button");
		pal_button.innerHTML = "?";
		pal_button.onclick = function() {
			user_state.toggle_pal_calc();
		};
		pal_col.appendChild(pal_button);
		user_state_form.appendChild(pal_col);

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
			pal_input.value = newest_state_element["Pal"];
                }

		var date_col = document.createElement("td");
		user_state_form.appendChild(date_col);

		var del_col = document.createElement("td");
		var state_add_button = document.createElement("button");
		state_add_button.obj = this;
		state_add_button.innerHTML = "&#xFF0B;";
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
		del_col.appendChild(state_add_button);
		user_state_form.appendChild(del_col);

		return user_state_form;
	}

        this.on_user_state_response = function() {
                var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_state.state = resp["state"];
			user_state.user_state.innerHTML = "";
			user_state.user_state.appendChild(user_state.pal_calc);
			user_state.pal_calc.innerHTML = "";
			user_state.pal_calc.appendChild(user_state.get_pal_calc_container());
			user_state.on_pal_slider_change(0, false);
			user_state.user_state.appendChild(user_state.get_user_state_header());

			if (resp["no_state"] == true) {
				var no_state_elem = document.createElement("div");
                                no_state_elem.innerHTML = "Please insert your current status.";
                                no_state_elem.style.background = "yellow";
				messagebox.message_add(no_state_elem, 1000, "no-class", "no_state", true);

				var form_elem = user_state.insert_user_state_form_create(null);
                                user_state.user_state.appendChild(form_elem);
				user_state.on_insert_bmi_factors_change();
			} else {
				var idx = [];
				for (var state in user_state.state) {
					if (user_state.state.hasOwnProperty(state)) {
						idx.push(state);
					}
				}
				idx.sort(function(a, b) {return b-a});
				for (var i = 0; i < idx.length; i++) {
					if (i == 0) {
	                                        var form_elem = user_state.insert_user_state_form_create(user_state.state[idx[i]]);
                                                user_state.user_state.appendChild(form_elem);
						user_state.newest_height = user_state.state[idx[i]]["Height"];
					}
					var state_elem = user_state.get_state_node(user_state.state[idx[i]]);
                                        user_state.user_state.appendChild(state_elem);
                                        user_state.on_insert_bmi_factors_change();
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
