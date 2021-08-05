var UserTarget = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.target = null;

	this.data_table = new DataTable(this, "target",
                                {       "Bmi": { "title": "BMI", "header": { "type": "img", "img_src": "/img/symbol_bmi.svg", "img_class": "datatable_header" } },
                                        "Weight": { "title": "Weight", "header": { "type": "img", "img_src": "/img/symbol_person_weight.svg", "img_class": "datatable_header" } },
                                        "FatPercent": { "title": "Fat %", "header": { "type": "img", "img_src": "/img/symbol_fat.svg", "img_class": "datatable_header" } },
                                        "MusclePercent": { "title": "Muscle %", "header": { "type": "img", "img_src": "/img/symbol_muscle.svg", "img_class": "datatable_header" } },
                                        "DateInsert": { "title": "Date", "header": { "type": "img", "img_src": "/img/symbol_calendar.svg", "img_class": "datatable_header" } }
                                },
                                {       "Bmi": { "placeholder": "BMI", "oninput": function() { user_target.on_insert_weight_factor_change(); } },
                                        "FatPercent": { "placeholder": "Fat%" },
                                        "MusclePercent": { "placeholder": "Muscle%" },
                                        "add_button": { "onclick":  function() {
										var p = {
							                                "target" : {
                	        			        			        "Bmi": document.getElementById(this.obj.widget.name + "_target_Bmi").value,
							                                        "FatPercent": document.getElementById(this.obj.widget.name + "_target_FatPercent").value,
				                        			                "MusclePercent": document.getElementById(this.obj.widget.name + "_target_MusclePercent").value
											}
						                                };
							                        this.obj.db.query_post("users/target/insert", p, this.obj.on_target_add_response);
									}
                                                       }
                                },
                                {
                                        "Delete": { "title": "Delete", "type": "text", "text": "&#128465;", "onclick":
									function() {
							                        var p = {
							                                "target_id": this.obj["Id"]
						        	                };
						                	        var r = confirm("Delete target from " + this.obj["DateInsert"] + "?");
						                        	if (r == 1) {
						                                	user_target.db.query_post("users/target/remove", p, user_target.on_remove_response);
							                        }
							                }
                                                }
                                }
                        );

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

	this.on_remove_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var to_delete_element = document.getElementById(user_target.widget.name + "_target_" + resp["deleted_target"]["Id"]);
			user_target.user_target.removeChild(to_delete_element);
			user_target.changed_f();
		}
	}

	this.get_target_node = function(target) {
		var weight_val = "";
                if (user_state.newest_height != null) {
                        weight_val = Math.round(parseFloat(target["Bmi"])*parseFloat(user_state.newest_height)*parseFloat(user_state.newest_height) * 100)/100;
                }
		target["Weight"] = weight_val;

		return user_target.data_table.get_data_row(target);
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
		var bmi = document.getElementById(user_target.widget.name + "_target_Bmi");
		if (user_state.newest_height != null) {
        	        var weight = document.getElementById(user_target.widget.name + "_target_Weight");
			weight.innerHTML = Math.round(parseFloat(bmi.value)*parseFloat(user_state.newest_height)*parseFloat(user_state.newest_height) * 100)/100;
		}
	}

        this.on_user_target_response = function() {
                var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			user_target.target = resp["target"];
			user_target.user_target.innerHTML = "";

			user_target.user_target.appendChild(user_target.data_table.get_header_row());

			if (resp["no_target"] == true) {
				var no_target_elem = document.createElement("div");
                                no_target_elem.innerHTML = "Please insert your current target.";
                                no_target_elem.style.background = "yellow";
				messagebox.message_add(no_target_elem, 1000, "no-class", "no_state", true);

                                user_target.user_target.appendChild(user_target.data_table.get_insert_row(null));
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
                                                user_target.user_target.appendChild(user_target.data_table.get_insert_row(user_target.target[idx[i]]));
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
			this.changed = false;

			if (user.login_data != null) {
				user_target.elem.style.display = "block";
				var p = {};
				user_target.db.query_post("users/target", p, user_target.on_user_target_response);
			} else {
				user_target.elem.style.display = "none";
				user_target.user_target.innerHTML = "";
				user_target.target = null;
			}
		}
	}
}
