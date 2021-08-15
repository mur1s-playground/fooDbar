var UserState = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.state = null;
	this.newest_height = null;

	this.data_table = new DataTable(this, "state",
				{ 	"Bmi": { "title": "BMI", "header": { "type": "img", "img_src": "/img/symbol_bmi.svg", "img_class": "datatable_header" } },
					"Height": { "title": "Height", "header": { "type": "img", "img_src": "/img/symbol_person_height.svg", "img_class": "datatable_header" } },
					"Weight": { "title": "Weight", "header": { "type": "img", "img_src": "/img/symbol_person_weight.svg", "img_class": "datatable_header" } },
					"FatPercent": { "title": "Fat %", "header": { "type": "img", "img_src": "/img/symbol_fat.svg", "img_class": "datatable_header" } },
					"MusclePercent": { "title": "Muscle %", "header": { "type": "img", "img_src": "/img/symbol_muscle.svg", "img_class": "datatable_header" } },
					"BonePercent": { "title": "Bone %", "header": { "type": "img", "img_src": "/img/symbol_bone.svg", "img_class": "datatable_header" } },
					"WaterPercent": { "title": "Water %", "header": { "type": "img", "img_src": "/img/symbol_water.svg", "img_class": "datatable_header" } },
					"Pal": { "title": "PAL", "header": { "type": "img", "img_src": "/img/symbol_pal.svg", "img_class": "datatable_header" } },
					"DatetimeInsert": { "title": "Date", "header": { "type": "img", "img_src": "/img/symbol_calendar.svg", "img_class": "datatable_header" } }
				},
				{	"Height": { "placeholder": "Height in m (eg. 1.80)", "oninput": function() { user_state.on_insert_bmi_factors_change(); } },
					"Weight": { "placeholder": "Weight in kg (eg. 70.5)", "oninput": function() { user_state.on_insert_bmi_factors_change(); } },
					"FatPercent": { "placeholder": "Fat%" },
					"MusclePercent": { "placeholder": "Muscle%" },
					"BonePercent": { "placeholder": "Bone%" },
					"WaterPercent": { "placeholder": "Water%" },
					"Pal": { "placeholder": "PAL", "button": { "title": "Help", "type": "text", "text": "?", "onclick": function() { user_state.toggle_pal_calc(); } } },
					"add_button": { "onclick": function() {
						                        var p = {
						                                "state" : this.obj.data_table.get_inserted_values()
							                        };
						                        this.obj.db.query_post("users/state/insert", p, this.obj.on_state_add_response);
								 }
							}
				},
				{
					"Delete": { "title": "Delete", "type": "text", "text": "&#128465;", "onclick":
								function() {
									var p = {
							                        "state_id": this.obj["Id"]
							                };
							                var r = confirm("Delete state from " + this.obj["DatetimeInsert"] + "?");
							                if (r == 1) {
						                        	user_state.db.query_post("users/state/remove", p, user_state.on_remove_response);
						                	}
								}
						}
				}
			);

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

	this.circle_view_interface = {
                "BMI": null
        };

	this.add_entry_state = {};

	this.add_entry_next = function(state_nr, number) {
		if (state_nr == 0) {
			user_state.add_entry_state["Height"] = number;
			var ne = new NumberEntry([196, 196], user_state.add_entry_next, 1, "> Weight");
                        menu.circle_view["box_center"].children[0].innerHTML = "";
			menu.circle_view["box_center"].children[0].appendChild(ne.elem);
		} else if (state_nr == 1) {
			user_state.add_entry_state["Weight"] = number;
			var ne = new NumberEntry([196, 196], user_state.add_entry_next, 2, "> Fat %");
                        menu.circle_view["box_center"].children[0].innerHTML = "";
                        menu.circle_view["box_center"].children[0].appendChild(ne.elem);
		} else if (state_nr == 2) {
			user_state.add_entry_state["FatPercent"] = number;
                        var ne = new NumberEntry([196, 196], user_state.add_entry_next, 3, "> Muscle %");
                        menu.circle_view["box_center"].children[0].innerHTML = "";
                        menu.circle_view["box_center"].children[0].appendChild(ne.elem);
		} else if (state_nr == 3) {
			user_state.add_entry_state["MusclePercent"] = number;
                        var ne = new NumberEntry([196, 196], user_state.add_entry_next, 4, "> Bone %");
                        menu.circle_view["box_center"].children[0].innerHTML = "";
                        menu.circle_view["box_center"].children[0].appendChild(ne.elem);
		} else if (state_nr == 4) {
			user_state.add_entry_state["BonePercent"] = number;
                        var ne = new NumberEntry([196, 196], user_state.add_entry_next, 5, "> Water %");
                        menu.circle_view["box_center"].children[0].innerHTML = "";
                        menu.circle_view["box_center"].children[0].appendChild(ne.elem);
		} else if (state_nr == 5) {
			user_state.add_entry_state["WaterPercent"] = number;
                        var ne = new NumberEntry([196, 196], user_state.add_entry_next, 6, "> PAL");
                        menu.circle_view["box_center"].children[0].innerHTML = "";
                        menu.circle_view["box_center"].children[0].appendChild(ne.elem);
		} else if (state_nr == 6) {
			user_state.add_entry_state["Pal"] = number;
			menu.circle_view["box_center"].children[0].innerHTML = "";
			menu.circle_view["box_center"].children[0].appendChild(document.createTextNode("Height: " + user_state.add_entry_state["Height"] + " m"));
			menu.circle_view["box_center"].children[0].appendChild(document.createElement("br"));
			menu.circle_view["box_center"].children[0].appendChild(document.createTextNode("Weight: " + user_state.add_entry_state["Weight"] + " KG"));
			menu.circle_view["box_center"].children[0].appendChild(document.createElement("br"));
			menu.circle_view["box_center"].children[0].appendChild(document.createTextNode("Fat: " + user_state.add_entry_state["FatPercent"] + " %"));
			menu.circle_view["box_center"].children[0].appendChild(document.createElement("br"));
			menu.circle_view["box_center"].children[0].appendChild(document.createTextNode("Muscle: " + user_state.add_entry_state["MusclePercent"] + " %"));
			menu.circle_view["box_center"].children[0].appendChild(document.createElement("br"));
			menu.circle_view["box_center"].children[0].appendChild(document.createTextNode("Bone: " + user_state.add_entry_state["BonePercent"] + " %"));
			menu.circle_view["box_center"].children[0].appendChild(document.createElement("br"));
			menu.circle_view["box_center"].children[0].appendChild(document.createTextNode("Water: " + user_state.add_entry_state["WaterPercent"] + " %"));
			menu.circle_view["box_center"].children[0].appendChild(document.createElement("br"));
			menu.circle_view["box_center"].children[0].appendChild(document.createTextNode("PAL: " + user_state.add_entry_state["Pal"]));
			menu.circle_view["box_center"].children[0].appendChild(document.createElement("br"));
			var submit = document.createElement("button");
			submit.innerHTML = "&#10003;";
			submit.onclick = function() {
				var p = {
                                	"state" : user_state.add_entry_state
                                };
                                user_state.db.query_post("users/state/insert", p, user_state.on_state_add_response);
			}
			menu.circle_view["box_center"].children[0].appendChild(submit);
		}
	}

	this.set_circle_view = function() {
		if (user_state.state != null) {
			menu.circle_view["box_top"].innerHTML 			= user_state.circle_view_interface["BMI"] + " BMI";

			menu.circle_view["progressbar_target_line"].style.display       = "block";
	                menu.circle_view["progressbar_target_line"].style.clipPath      = "inset(0 49% 0 49%)";

			menu.circle_view["progressbar_top"].style.background    = "linear-gradient(90deg, red 0%, yellow 40%, green 50%, yellow 60%, red 100%)";

			var bmi_clip = 50 + (parseFloat(user_state.circle_view_interface["BMI"]) - parseFloat(user_target.bmi_current))*(2.0/3.0) * 50;
	                if (bmi_clip < 0) {
        	                bmi_clip = 0;
                	}
			if (bmi_clip > 100) {
				bmi_clip = 100;
			}
			if (bmi_clip < 50) {
	        	        menu.circle_view["progressbar_top"].style.clipPath              = "inset(0 50% 0 " + bmi_clip  + "%)";
			} else {
				menu.circle_view["progressbar_top"].style.clipPath              = "inset(0 " + (100-bmi_clip) + "% 0 50%)";
			}


			menu.circle_view["progress_bar_top"]

			menu.circle_view["box_center"].innerHTML = "";

			var graph_container = document.createElement("div");
			graph_container.style.display = "table-cell";
			graph_container.style.verticalAlign = "middle";
			graph_container.style.paddingLeft = "16px";

			var graph = new Graph([164, 164]);
			graph.add_value_scale("KG");
			graph.add_field("Weight", "KG");

			graph.add_value_scale("%");
			graph.add_field("Fat", "%");
			graph.fields["Fat"]["color"] = "var(--fat_color)";
			graph.add_field("Muscle", "%");
			graph.fields["Muscle"]["color"] = "var(--muscle_color)";

			var idx = [];
                        for (var state in user_state.state) {
                        	if (user_state.state.hasOwnProperty(state)) {
                                	idx.push(state);
                                }
                        }
                        idx.sort(function(a, b) {return a-b});
                        for (var i = 0; i < idx.length; i++) {
				var t = new Date(user_state.state[idx[i]]["DatetimeInsert"].replace(" ", "T"));
				var ts = t.getTime();
				graph.add_field_value("Weight", ts, parseFloat(user_state.state[idx[i]]["Weight"]));
				if (user_state.state[idx[i]]["FatPercent"] != null) {
					graph.add_field_value("Fat", ts, parseFloat(user_state.state[idx[i]]["FatPercent"]));
				}
				if (user_state.state[idx[i]]["MusclePercent"] != null) {
					graph.add_field_value("Muscle", ts, parseFloat(user_state.state[idx[i]]["MusclePercent"]));
				}
				if (i == idx.length - 1) {
					menu.circle_view["box_right_0"].innerHTML = "";
					if (user_state.state[idx[i]]["Weight"] != null) {
						menu.circle_view["box_right_0"].appendChild(document.createTextNode(user_state.state[idx[i]]["Weight"] + " KG "));
					} else {
						menu.circle_view["box_right_0"].appendChild(document.createTextNode("n/A KG "));
					}
					var kg_img = document.createElement("img");
                                        kg_img.src = "/img/symbol_person_weight.svg";
                                        kg_img.style.width = "15px";
                                        kg_img.style.padding = "1px";
                                        kg_img.style.borderRadius = "4px";
                                        kg_img.style.verticalAlign = "middle";
                                        menu.circle_view["box_right_0"].appendChild(kg_img);

					menu.circle_view["box_right_1"].innerHTML = "";
					if (user_state.state[idx[i]]["FatPercent"] != null) {
						menu.circle_view["box_right_1"].appendChild(document.createTextNode(user_state.state[idx[i]]["FatPercent"] + " % "));
					} else {
						menu.circle_view["box_right_1"].appendChild(document.createTextNode("n/A % "));
					}
					var fat_img = document.createElement("img");
                                        fat_img.src = "/img/symbol_fat.svg";
                                        fat_img.style.width = "15px";
                                        fat_img.style.background = "var(--fat_color)";
                                        fat_img.style.padding = "1px";
                                        fat_img.style.borderRadius = "4px";
					fat_img.style.verticalAlign = "middle";
                                        menu.circle_view["box_right_1"].appendChild(fat_img);

					menu.circle_view["box_right_2"].innerHTML = "";
					if (user_state.state[idx[i]]["MusclePercent"] != null) {
						menu.circle_view["box_right_2"].appendChild(document.createTextNode(user_state.state[idx[i]]["MusclePercent"] + " % "));
					} else {
						menu.circle_view["box_right_2"].appendChild(document.createTextNode("n/A % "));
					}
					var muscle_img = document.createElement("img");
                                        muscle_img.src = "/img/symbol_muscle.svg";
                                        muscle_img.style.width = "15px";
                                        muscle_img.style.background = "var(--muscle_color)";
                                        muscle_img.style.padding = "1px";
                                        muscle_img.style.borderRadius = "4px";
					muscle_img.style.verticalAlign = "middle";
                                        menu.circle_view["box_right_2"].appendChild(muscle_img);
				}
			}

			graph.draw_graph();
			graph.draw_scale("KG", "left", 10);
			graph.draw_scale("%", "right", 50);

			graph_container.appendChild(graph.elem);
			menu.circle_view["box_center"].appendChild(graph_container);

			menu.circle_view["box_left_0"].innerHTML = "";

			var add_entry = document.createElement("a");
			add_entry.innerHTML = "&#xFF0B;";
			add_entry.onclick = function() {
				menu.circle_view["box_center"].innerHTML = "";

				user_state.add_entry_state = {};
	                        var ne_container = document.createElement("div");
				ne_container.style.width = "196px";
				ne_container.style.height = "196px";
				var ne = new NumberEntry([196, 196], user_state.add_entry_next, 0, "> Height", user_state.newest_height);
				ne_container.appendChild(ne.elem);
				menu.circle_view["box_center"].appendChild(ne_container);
			}
			menu.circle_view["box_left_0"].appendChild(add_entry);
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
			document.getElementById(user_state.widget.name + "_state_Pal").value = document.getElementById(user_state.widget.name + "_pal_span_result").innerHTML;
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

	this.on_remove_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var to_delete_element = document.getElementById(user_state.widget.name + "_state_" + resp["deleted_state"]["Id"]);
			user_state.user_state.removeChild(to_delete_element);
			delete user_state.state[resp["deleted_state"]["Id"]];
			user_state.changed_f();
		}
	}

	this.get_state_node = function(state) {
		var b_height = parseFloat(state["Height"]);
                var b_weight = parseFloat(state["Weight"]);
                var bmi =  Math.round(b_weight/(b_height * b_height) * 100) / 100;
		state["Bmi"] = bmi;

		return user_state.data_table.get_data_row(state);
	}


	this.on_state_add_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_state.state[resp["new_state"]["Id"]] = resp["new_state"];
			user_state.newest_height = resp["new_state"]["Height"];
			if (user_state.user_state.children.length > 3) {	//2 + PAL calc
				user_state.user_state.insertBefore(user_state.get_state_node(resp["new_state"]), user_state.user_state.children[3]);
			} else {
				user_state.user_state.appendChild(user_state.get_state_node(resp["new_state"]));
			}
			user_state.changed_f();
		}
	}

	this.on_insert_bmi_factors_change = function() {
		var bmi = document.getElementById(user_state.widget.name + "_state_Bmi");
		var height = document.getElementById(user_state.widget.name + "_state_Height");
		var weight = document.getElementById(user_state.widget.name + "_state_Weight");

		if (parseFloat(height.value) > 0) bmi.innerHTML = Math.round(parseFloat(weight.value)/(parseFloat(height.value)*parseFloat(height.value)) * 100) / 100;
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
			user_state.user_state.appendChild(user_state.data_table.get_header_row());

			if (resp["no_state"] == true) {
				var no_state_elem = document.createElement("div");
                                no_state_elem.innerHTML = "Please insert your current status.";
                                no_state_elem.style.background = "yellow";
				messagebox.message_add(no_state_elem, 1000, "no-class", "no_state", true);

                                user_state.user_state.appendChild(user_state.data_table.get_insert_row(null));
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
                                                user_state.user_state.appendChild(user_state.data_table.get_insert_row(user_state.state[idx[i]]));
						user_state.newest_height = user_state.state[idx[i]]["Height"];
					}
					var state_elem = user_state.get_state_node(user_state.state[idx[i]]);
                                        user_state.user_state.appendChild(state_elem);
                                        user_state.on_insert_bmi_factors_change();
					if (i == 0) {
                                                user_state.circle_view_interface["BMI"] = state_elem.children[0].innerHTML;
                                        }
				}
				if (menu.active_widget == user_state.widget.name) {
					user_state.set_circle_view();
				}
				user_state.changed_f();
				user_state.changed = false;
			}
		}
        }

	this.update = function() {
		if (this.changed) {
			this.changed = false;

			if (user.login_data != null) {
				var p = {};
				user_state.db.query_post("users/state", p, user_state.on_user_state_response);
			} else {
				user_state.elem.style.display = "none";
				user_state.user_state.innerHTML = "";
				user_state.state = null;
				user_state.newest_height = null;
			}
		}
	}
}
