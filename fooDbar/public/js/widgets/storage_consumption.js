var StorageConsumption = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.consumption_data = null;

	this.data_table = new DataTable(this, "consumption",
                                {
					"StoragesId":  { "title": "Storage", "header": { "type": "text", "text": "", "text_class": "datatable_header" }, "join": { "model": "storages", "field": "Desc" } },
					"ProductsId": { "title": "Product", "header": { "type": "img", "img_src": "/img/symbol_storage.svg", "img_class": "datatable_header" }, "join": { "model": "products", "field": "Name" } },
					"Amount": { "title": "Amount", "header": { "type": "img", "img_src": "/img/symbol_weight.svg", "img_class": "datatable_header" } },
					"Datetime": { "title": "Consumption", "header": { "type": "img", "img_src": "/img/symbol_person.svg", "img_class": "datatable_header" } },
					"User": { "title": "User", "header": { "type": "text", "text": "", "text_class": "datatable_header" } }
                                },
                                {
					"StoragesId": { "placeholder": "Storage", "join": "storages", "onchange": function() { storage_consumption.on_select_storage_insert_change(); } },
                                        "ProductsId": { "placeholder": "Product", "join": "products" },
                                        "Amount": { "placeholder": "Amount" },
					"Datetime": { "placeholder": "2021-07-31 22:59:59" },
                                        "add_button": { "onclick":  function() {
										var p = {
							                                "consumption_item" : this.obj.data_table.get_inserted_values()
						                                };
							                        this.obj.db.query_post("storage/consumption/add", p, this.obj.on_consumption_add_response);
									},
							"button": { "title": "Trash", "type": "text", "text": "&#128465;", "onclick": function() {
																	var p = {
																		"consumption_item": storage_consumption.data_table.get_inserted_values(),
																		"trash": true
																	};
																	storage_consumption.db.query_post("storage/consumption/add", p, storage_consumption.on_consumption_add_response);
																}
								}
							}
                                },
                                {
					"Edit": { "title": "Edit", "type": "text", "text": "&#x1f589;", "onclick":
                                                                        function() {
										var data = storage_consumption.consumption_data[this.obj["Id"]];

										var apply_button = { "title": "Apply", "type": "text", "text": "&#10003;", "onclick": function() {
	                                                                                                      var p = {
        	                                                                                                      "consumption_item": storage_consumption.data_table.get_inserted_values(this.obj["Id"])
                                                                                                              };
                                                                                                              storage_consumption.db.query_post("storage/consumption/edit", p, storage_consumption.on_consumption_edit_response);
                                                                                                        }
                                                                		};

										var cancel_button = { "title": "Cancel", "type": "text", "text": "&#xd7;", "onclick": function() {
														var edit_row = document.getElementById(storage_consumption.widget.name + "_consumption_" + this.obj["Id"]);
														var data_row = storage_consumption.data_table.get_data_row(storage_consumption.consumption_data[this.obj["Id"]], { "storages": storage.storages, "products": products.product_data });
														edit_row.parentNode.replaceChild(data_row, edit_row);
                                                                                                        }

										};

										var button_override = {
											"apply": apply_button,
											"cancel": cancel_button
										};
										var edit_row = storage_consumption.data_table.get_edit_row(data, { "storages": storage.storages, "products": products.product_data }, button_override);
										var data_row = document.getElementById(storage_consumption.widget.name + "_consumption_" + this.obj["Id"]);
										data_row.parentNode.replaceChild(edit_row, data_row);
                                                                        }
                                                },
                                        "Delete": { "title": "Delete", "type": "text", "text": "&#128465;", "onclick":
									function() {
							                        var p = {
							                                "consumption_item_id": this.obj["Id"]
						        	                };
						                	        var r = confirm("Delete consumption item " + this.obj["Id"] + "?");
						                        	if (r == 1) {
						                                	storage_consumption.db.query_post("storage/consumption/undo", p, storage_consumption.on_undo_response);
							                        }
							                }
                                                }
                                }
                        );

	this.widget = new Widget("Consumption");

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

	this.consumption = document.createElement("table");
	this.consumption.className = "consumption";
	this.widget.content.appendChild(this.consumption);

	this.set_circle_view = function() {
		var table_data = storage_consumption.consumption_calc_table_data;

		var today = table_data["Today"];
                var d31 = table_data["31d"];

		var mj_clip = 90 - ((today["MJPerDay"]/parseFloat(demand.demand_data["MJPerDay"]["target"]) * 65));
                if (mj_clip < 0) {
                        mj_clip = 0;
                }

		menu.circle_view["progressbar_top"].style.background 		= "linear-gradient(90deg, red 0%, red 10%, yellow 40%, green 75%, yellow 82.5%, red 87%)";
		menu.circle_view["progressbar_top"].style.clipPath 		= "inset(0 " + mj_clip + "% 0 0)";

		var price_clip = 90 - ((today["PricePerDay"]/d31["PricePerDay"]) * 65);
                if (price_clip < 0) {
                        price_clip = 0;
                }
		menu.circle_view["progressbar_bottom"].style.background 	= "linear-gradient(90deg, green 0%, green 40%, yellow 82.5%, red 87%)";
                menu.circle_view["progressbar_bottom"].style.clipPath 		= "inset(0 " + price_clip + "% 0 0)";

		menu.circle_view["progressbar_target_line"].style.display	= "block";
		menu.circle_view["progressbar_target_line"].style.clipPath 	= "inset(0 23% 0 75%)";

		menu.circle_view["box_top"].innerHTML 		= today["MJPerDay"] + " MJ";
		menu.circle_view["box_bottom"].innerHTML	= today["PricePerDay"] + " &euro;";

		menu.circle_view["box_left_0"].innerHTML	= "&#x394; " + Math.round((d31["PricePerDay"] - today["PricePerDay"]) * 100)/100 + " &euro;";
		menu.circle_view["box_left_1"].innerHTML	= "&#216; " + Math.round(d31["PricePerDay"] * 100)/100 + " &euro;";
		menu.circle_view["box_left_2"].innerHTML	= "&#8721; " + Math.round(d31["Price"]) + " &euro;";

		menu.circle_view["box_right_0"].innerHTML	= "&#x394; " + Math.round((parseFloat(demand.demand_data["MJPerDay"]["target"]) - today["MJPerDay"]) * 100)/100 + " MJ";
		menu.circle_view["box_right_1"].innerHTML	= "&#216; " + Math.round(d31["MJPerDay"] * 100)/100 + " MJ";
		menu.circle_view["box_right_2"].innerHTML	= "&#8721; " +  Math.round(d31["MJ"]) + " MJ";

		menu.circle_view["box_center"].innerHTML	= "";

		var nutrition_container 			= document.createElement("div");
		nutrition_container.id 				= "consumption_circle_nutrition_container";

		var n_fat_box 					= document.createElement("span");
                n_fat_box.id 					= "consumption_circle_nutrition_fat_box";
		n_fat_box.className 				= "consumption_circle_nutrition_box";
                n_fat_box.innerHTML 				= today["FatPercent"] + " %";
                nutrition_container.appendChild(n_fat_box);

                var n_carbs_box 				= document.createElement("span");
                n_carbs_box.id 					= "consumption_circle_nutrition_carbs_box";
		n_carbs_box.className				= "consumption_circle_nutrition_box";
                n_carbs_box.innerHTML 				= today["CarbsPercent"] + " %";
                nutrition_container.appendChild(n_carbs_box);

                var n_protein_box 				= document.createElement("span");
                n_protein_box.id				= "consumption_circle_nutrition_protein_box";
		n_protein_box.className				= "consumption_circle_nutrition_box";
                n_protein_box.innerHTML 			= today["ProteinPercent"] + " %";
                nutrition_container.appendChild(n_protein_box);


					//TMP
		var fat_target 		= 27.5;
		var carbs_target 	= 50.0;
		var protein_target 	= 22.5;

		var fat_ind = (today["FatPercent"]/fat_target) * 65;
                if (fat_ind > 78) fat_ind = 78;

		var n_fat_ind					= document.createElement("div");
		n_fat_ind.id					= "consumption_circle_nutrition_fat_box_ind";
		n_fat_ind.className				= "consumption_circle_nutrition_ind";
		n_fat_ind.style.height = fat_ind + "%";
		nutrition_container.appendChild(n_fat_ind);

		var carbs_ind = (today["CarbsPercent"]/carbs_target) * 65;
                if (carbs_ind > 78) carbs_ind = 78;

                var n_carbs_ind                                 = document.createElement("div");
                n_carbs_ind.id                                  = "consumption_circle_nutrition_carbs_box_ind";
                n_carbs_ind.className                           = "consumption_circle_nutrition_ind";
                n_carbs_ind.style.height = carbs_ind + "%";
                nutrition_container.appendChild(n_carbs_ind);

		var protein_ind = (today["ProteinPercent"]/protein_target) * 65;
                if (protein_ind > 78) protein_ind = 78;

                var n_protein_ind                                = document.createElement("div");
                n_protein_ind.id                                 = "consumption_circle_nutrition_protein_box_ind";
                n_protein_ind.className                          = "consumption_circle_nutrition_ind";
                n_protein_ind.style.height = protein_ind + "%";
                nutrition_container.appendChild(n_protein_ind);

		var n_target_line                               = document.createElement("div");
                n_target_line.id                                = "consumption_circle_nutrition_target_line";
                nutrition_container.appendChild(n_target_line);

	 	var n_fat_tgt 					= document.createElement("span")
		n_fat_tgt.id					= "consumption_circle_nutrition_fat_box_tgt";
		n_fat_tgt.className				= "consumption_circle_nutrition_box_tgt";
                n_fat_tgt.innerHTML 				= "27.5 %";
		nutrition_container.appendChild(n_fat_tgt);

                var n_carbs_tgt					= document.createElement("span");
		n_carbs_tgt.id 					= "consumption_circle_nutrition_carbs_box_tgt";
		n_carbs_tgt.className                           = "consumption_circle_nutrition_box_tgt";
                n_carbs_tgt.innerHTML 				= "50.0 %";
		nutrition_container.appendChild(n_carbs_tgt);

                var n_protein_tgt				= document.createElement("span");
		n_protein_tgt.id				= "consumption_circle_nutrition_protein_box_tgt";
		n_protein_tgt.className                         = "consumption_circle_nutrition_box_tgt";
                n_protein_tgt.innerHTML 			= "22.5 %";
		nutrition_container.appendChild(n_protein_tgt);


		var n_fat_box_img   		         	= document.createElement("img");
                n_fat_box_img.id             			= "consumption_circle_nutrition_fat_img";
                n_fat_box_img.className      			= "consumption_circle_nutrition_img";
                n_fat_box_img.src            			= "/img/symbol_fat.svg";
                nutrition_container.appendChild(n_fat_box_img);

		var n_carbs_box_img            			= document.createElement("img");
                n_carbs_box_img.id             			= "consumption_circle_nutrition_carbs_img";
                n_carbs_box_img.className      			= "consumption_circle_nutrition_img";
                n_carbs_box_img.src            			= "/img/symbol_carbs.svg";
                nutrition_container.appendChild(n_carbs_box_img);

		var n_protein_box_img                           = document.createElement("img");
                n_protein_box_img.id                            = "consumption_circle_nutrition_protein_img";
                n_protein_box_img.className                     = "consumption_circle_nutrition_img";
                n_protein_box_img.src                           = "/img/symbol_muscle.svg";
                nutrition_container.appendChild(n_protein_box_img);

		menu.circle_view["box_center"].appendChild(nutrition_container);
	}

	this.set_consumption_circle = function() {
		if (menu.active_widget == storage_consumption.widget.name) {
			storage_consumption.set_circle_view();
		}
	}

	this.do_consumption_calc = function(date_f, date_t, username) {
                var time_diff = (date_t.getTime() - date_f.getTime()) / (3600 * 24 * 1000);

		var max_time = null;
		var min_time = null;

                var c_data = storage_consumption.consumption_data;
                var s_data = storage.storage_data;
                var p_data = products.product_data;

                var price_r = 0;
                var kj_r = 0;
		var fat_r = 0;
		var carbs_r = 0;
		var protein_r = 0;
		var salt_r = 0;
		var fiber_r = 0;
                for (var c in c_data) {
	                if (c_data.hasOwnProperty(c)) {
        	                var c_time = new Date(c_data[c]["Datetime"].replace(" ", "T"));
                                if (c_time.getTime() >= date_f.getTime() && c_time.getTime() < date_t.getTime()) {
					if (c_data[c]["User"] !== username) continue;
					if (min_time == null || min_time > c_time.getTime()) {
						min_time = c_time.getTime();
					}
					if (max_time == null || max_time < c_time.getTime()) {
						max_time = c_time.getTime();
					}

                	                var p = p_data[c_data[c]["ProductsId"]];
                                        var p_at_id = p["AmountTypeId"];

                                        var s = s_data[c_data[c]["StorageId"]];
                                        var s_price = parseFloat(s["Price"]);

                                        var p_amount = parseFloat(p["Amount"]);
                                        var p_kj = parseFloat(p["Kj"]);

					var n_fat = parseFloat(p["NFat"]);
					var n_carbs = parseFloat(p["NCarbs"]);
					var n_protein = parseFloat(p["NProtein"]);
					var n_salt = parseFloat(p["NSalt"]);
					var n_fiber = parseFloat(p["NFiber"]);

                                        var c_amount = parseFloat(c_data[c]["Amount"]);
                                        var c_amount_price = c_amount/p_amount * s_price;

                                        price_r += c_amount_price;

					var c_a = c_amount;
                                        if (p_at_id == 1) { //g
						c_a /= 100;
                                        } else if (p_at_id == 2) { //l
						c_a *= 10;
                                        } //else pc. *= 1
					if (!isNaN(p_kj)) kj_r += c_a * p_kj;
					if (!isNaN(n_fat)) fat_r += c_a * n_fat;
					if (!isNaN(n_carbs)) carbs_r += c_a * n_carbs;
					if (!isNaN(n_protein)) protein_r += c_a * n_protein;
					if (!isNaN(n_salt)) salt_r += c_a * n_salt;
					if (!isNaN(n_fiber)) fiber_r += c_a * n_fiber;
                                }
                        }
        	}
		time_diff = Math.ceil((max_time - min_time) / (24 * 3600 * 1000));
		if (time_diff == 0) time_diff = 1;
		return [time_diff, price_r, kj_r, fat_r, carbs_r, protein_r, salt_r, fiber_r];
	}

	this.consumption_calc_table_data = null;

	this.get_consumption_calc_default = function() {
		var ccd_row = document.createElement("tr");
		ccd_row.id = storage_consumption.widget.name + "ccd_row";
		ccd_row.style.display = "none";

		var date_now = new Date();

		var time_diff_31 = 31 * 24 * 3600 * 1000;
		var date_31 = new Date();
		date_31.setTime(date_now.getTime() - time_diff_31);
		var result_31 = storage_consumption.do_consumption_calc(date_31, date_now, user.login_data["username"]);

		var result_31_trash = storage_consumption.do_consumption_calc(date_31, date_now, "Trash");
		result_31_trash[0] = result_31[0];

		var time_diff_7 = 7 * 24 * 3600 * 1000;
                var date_7 = new Date();
                date_7.setTime(date_now.getTime() - time_diff_7);
                var result_7 = storage_consumption.do_consumption_calc(date_7, date_now, user.login_data["username"]);

		var time_diff_24 = 24 * 3600 * 1000;
                var date_24 = new Date();
                date_24.setTime(date_now.getTime() - time_diff_24);
                var result_24 = storage_consumption.do_consumption_calc(date_24, date_now, user.login_data["username"]);

		var date_t = new Date();
		date_t.setTime(date_now.getTime());
		date_t.setHours(0);
		date_t.setMinutes(0);
		date_t.setSeconds(0);
		date_t.setMilliseconds(0);
		var result_t = storage_consumption.do_consumption_calc(date_t, date_now, user.login_data["username"]);

		var get_data_row = function(result) {
			var r = {
				"Timespan": Math.round(result[0] * 100) / 100,
				"Price": Math.round(result[1] * 100) / 100,
				"MJ": Math.round(result[2]/10) / 100,
				"PricePerDay": Math.round(result[1] / result[0] * 100) / 100,
				"MJPerDay": Math.round(result[2] / (result[0] * 10)) / 100,
				"MaintainDiff": "",
				"TargetDiff": "",
				"Fat": Math.round(result[3] * 100) / 100,
				"Carbs": Math.round(result[4] * 100) / 100,
				"Protein": Math.round(result[5] * 100) / 100,
				"Salt": Math.round(result[6] * 100) / 100,
				"Fiber": Math.round(result[7] * 100) / 100,
				"FatPercent": "",
				"CarbsPercent": "",
				"ProteinPercent": "",
				"FiberPercent": "",
				"SaltPercent": ""
			};

			var total_n = r["Fat"] + r["Carbs"] + r["Protein"] + r["Salt"] + r["Fiber"];
			if (total_n == 0) total_n = 1;
			var cols = ["Fat", "Carbs", "Protein", "Salt", "Fiber"];
			for (var col in cols) {
				r[cols[col] + "Percent"] = Math.round(r[cols[col]]/total_n * 10000) / 100;
				r[cols[col]] = "" + r[cols[col]] + " (" + (Math.round(r[cols[col]]/total_n * 10000) / 100) + "%)";
			}

			return r;
		}

		var table_data = {
			"31d &#128465;": get_data_row(result_31_trash),
			"31d": get_data_row(result_31),
			"7d": get_data_row(result_7),
			"24h": get_data_row(result_24),
			"Today": get_data_row(result_t)
		};

		storage_consumption.consumption_calc_table_data = table_data;

		var rows = ["Today", "24h", "7d", "31d"];

		for (var row in rows) {
				if (demand.demand_data != null) {
					table_data[rows[row]]["MaintainDiff"] = Math.round((demand.demand_data["MJPerDay"]["maintain"] - table_data[rows[row]]["MJPerDay"]) * 100) / 100;
					table_data[rows[row]]["TargetDiff"] = Math.round((demand.demand_data["MJPerDay"]["target"] - table_data[rows[row]]["MJPerDay"]) * 100) / 100;
				} else {
					table_data[rows[row]]["MaintainDiff"] = "n/A";
        	                        table_data[rows[row]]["TargetDiff"] = "n/A";
				}
		}
		rows.push("31d &#128465;");

		var titles = ["Price (&#8364;)", "MJ", "Price/Day (&#8364;)", "MJ/Day", "&#x394;MJ Maintain", "&#x394;MJ Target", "Fat", "Carbs", "Protein", "Salt", "Fiber"];
		var cols = ["Price", "MJ", "PricePerDay", "MJPerDay", "MaintainDiff", "TargetDiff", "Fat", "Carbs", "Protein", "Salt", "Fiber"];
		var cols_h_class = ["", "", "", "", "menu_user_state", "menu_user_target", "", "", "", "", ""];

		var tbl = document.createElement("table");
		tbl.id = storage_consumption.widget.name + "_consumption_calc_table";
		var row_1 = document.createElement("tr");
		var col_1 = document.createElement("td");
		col_1.innerHTML = "Timespan";
		row_1.appendChild(col_1);
		for (var col in cols) {
			var col_ = document.createElement("td");
			col_.innerHTML = titles[col];
			col_.className = cols_h_class[col];
			row_1.appendChild(col_);
		}
		tbl.appendChild(row_1);
		for (var row in rows) {
			var row_ = document.createElement("tr");
			var col_1 = document.createElement("td");
			col_1.innerHTML = rows[row];
			row_.appendChild(col_1);
			for (var col in cols) {
				var col_ = document.createElement("td");
				col_.innerHTML = table_data[rows[row]][cols[col]];
				row_.appendChild(col_);
			}
			tbl.appendChild(row_);
		}

		var consumption_tbl = document.createElement("td");
                consumption_tbl.colSpan = "6";


		var row_sp = document.createElement("tr");
                var col_sp = document.createElement("td");
                col_sp.innerHTML = "&nbsp;";
                col_sp.colSpan = "12";
                row_sp.appendChild(col_sp);
                tbl.appendChild(row_sp);

                var row = document.createElement("tr");
                row.className = "consumption_not_calculated";

                var cols = ["Time", "Price", "MJ", "PricePerDay", "MJPerDay", "MaintainDiff", "TargetDiff", "Fat", "Carbs", "Protein", "Salt", "Fiber"];
                for (var col in cols) {
                        var spn_result = document.createElement("span");
                        spn_result.id = storage_consumption.widget.name + "_calc_result_" + cols[col];

                        var col_ = document.createElement("td");
                        col_.appendChild(spn_result);
                        row.appendChild(col_);
                }
                tbl.appendChild(row);


                consumption_tbl.appendChild(tbl);
		ccd_row.appendChild(consumption_tbl);

		return ccd_row;
	}

	this.get_consumption_calc = function() {
		var cc_row = document.createElement("tr");

		var consumption_calc = document.createElement("td");
		consumption_calc.colSpan = "6";
		cc_row.appendChild(consumption_calc);

		var time_from = document.createElement("input");
		time_from.style.display = "none";
		time_from.id = storage_consumption.widget.name + "_calc_time_from";
		time_from.placeholder = "e.g. 2021-07-23 00:00:00";
		consumption_calc.appendChild(time_from);

		var time_to = document.createElement("input");
		time_to.style.display = "none";
		time_to.id = storage_consumption.widget.name + "_calc_time_to";
		time_to.placeholder = "e.g. 2021-07-30 23:59:59";
		consumption_calc.appendChild(time_to);

		var calc_button = document.createElement("button");
		calc_button.style.display = "none";
		calc_button.id = storage_consumption.widget.name + "_calc_btn";
		calc_button.innerHTML = "&#10003;";
		calc_button.onclick = function() {
			var time_f = document.getElementById(storage_consumption.widget.name + "_calc_time_from").value;
			var time_t = document.getElementById(storage_consumption.widget.name + "_calc_time_to").value;

			var date_f = new Date(time_f.replace(" ", "T"));
	                var date_t = new Date(time_t.replace(" ", "T"));

			var result = storage_consumption.do_consumption_calc(date_f, date_t, user.login_data["username"]);
			var time_diff = result[0];
			var price_r = result[1];
			var kj_r = result[2];
			var fat_r = result[3];
			var carbs_r = result[4];
			var protein_r = result[5];
			var salt_r = result[6];
			var fiber_r = result[7];

			var t_r_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_Time");
			t_r_elem.innerHTML = Math.round(time_diff * 100) / 100 + "d";

			var p_r_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_Price");
			p_r_elem.innerHTML = Math.round(price_r * 100) / 100;

			var mj_r_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_MJ");
			mj_r_elem.innerHTML = Math.round(kj_r/10) / 100;

			var p_avg_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_PricePerDay");
                        p_avg_elem.innerHTML = Math.round(price_r / time_diff * 100) / 100;

			var mj_avg_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_MJPerDay");
			mj_avg_elem.innerHTML = Math.round(kj_r / (time_diff * 10)) / 100;

			var fat_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_Fat");
			fat_elem.innerHTML = Math.round(fat_r * 100) / 100;

			var carbs_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_Carbs");
			carbs_elem.innerHTML = Math.round(carbs_r * 100) / 100;

			var protein_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_Protein");
			protein_elem.innerHTML = Math.round(protein_r * 100) / 100;

			var salt_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_Salt");
			salt_elem.innerHTML = Math.round(salt_r * 100) / 100;

			var fiber_elem = document.getElementById(storage_consumption.widget.name + "_calc_result_Fiber");
			fiber_elem.innerHTML = Math.round(fiber_r * 100) / 100;

			fiber_elem.parentNode.parentNode.className = "consumption_calculated";
		}
		consumption_calc.appendChild(calc_button);

		var visibility_toggle = document.createElement("button");
		var v_img = document.createElement("img");
		v_img.src = "/img/symbol_search.svg";
		v_img.style.width = "25px";
		visibility_toggle.appendChild(v_img);
		visibility_toggle.onclick = function() {
			var ccd_row = document.getElementById(storage_consumption.widget.name + "ccd_row");
			if (ccd_row.style.display == "none") {
				ccd_row.style.display = "table-row";
				document.getElementById(storage_consumption.widget.name + "_calc_time_from").style.display = "inline";
				document.getElementById(storage_consumption.widget.name + "_calc_time_to").style.display = "inline";
				document.getElementById(storage_consumption.widget.name + "_calc_btn").style.display = "inline";
			} else {
				ccd_row.style.display = "none";
				document.getElementById(storage_consumption.widget.name + "_calc_time_from").style.display = "none";
                                document.getElementById(storage_consumption.widget.name + "_calc_time_to").style.display = "none";
                                document.getElementById(storage_consumption.widget.name + "_calc_btn").style.display = "none";
			}
		}
		consumption_calc.appendChild(visibility_toggle);

		consumption_calc.appendChild(document.createElement("br"));

		return cc_row;
	}

	this.on_select_storage_insert_change = function() {
		var storages_select = document.getElementById(storage_consumption.widget.name + "_consumption_StoragesId");
		var products_select = document.getElementById(storage_consumption.widget.name + "_consumption_ProductsId");
		var add_button = document.getElementById(storage_consumption.widget.name + "_consumption_add_button");

		var selected_is_enabled = false;
		var something_enabled = false;
		var selectable_idx = -1;

		for (var o = 0; o < products_select.options.length; o++) {
			var found = false;
			for (var storage_item in storage.storage_data) {
				if (storage.storage_data.hasOwnProperty(storage_item)) {
					if (storage.storage_data[storage_item]["ProductsId"] == products_select.options[o].value &&
						storage.storage_data[storage_item]["StoragesId"] == storages_select.options[storages_select.selectedIndex].value &&
							parseFloat(storage.storage_data[storage_item]["Amount"]) > 0
								) {
							if (selectable_idx == -1) selectable_idx = o;
							if (products_select.selectedIndex == o) {
								selected_is_enabled = true;
							}
							something_enabled = true;
							found = true;
							break;
					}
				}
			}
			if (found === true) {
				products_select.options[o].disabled = false;
				products_select.options[o].hidden = false;
			} else {
				products_select.options[o].disabled = "disabled";
				products_select.options[o].hidden = "hidden";
			}
		}
		if (something_enabled === true) {
			if (!selected_is_enabled) {
				products_select.selectedIndex = selectable_idx;
			}
			products_select.disabled = false;
			add_button.disabled = false;
		} else {
			products_select.disabled = true;
                        add_button.disabled = true;
		}
	}

	this.on_consumption_edit_response = function() {
//		var resp = JSON.parse(this.responseText);
		storage.changed_f();
	}

	this.on_undo_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var to_delete_element = document.getElementById(storage_consumption.widget.name + "_consumption_" + resp["deleted_consumption_item"]["Id"]);
			storage_consumption.consumption.removeChild(to_delete_element);

			//storage_consumption.changed_f();
			//rev dep
                        storage.changed_f();
		}
	}

	this.on_consumption_add_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			for (var item in resp["new_consumption_item"]) {
				if (resp["new_consumption_item"].hasOwnProperty(item)) {
					storage_consumption.consumption_data[resp["new_consumption_item"][item]["Id"]] = resp["new_consumption_item"][item];
					if (storage_consumption.consumption.children.length > 4) {
		                                storage_consumption.consumption.insertBefore(storage_consumption.data_table.get_data_row(resp["new_consumption_item"][item], { "storages": storage.storages, "products": products.product_data } ), storage_consumption.consumption.children[4]);
                		        } else {
                                		storage_consumption.consumption.appendChild(storage_consumption.data_table.get_data_row(resp["new_consumption_item"][item], { "storages": storage.storages, "products": products.product_data } ));
		                        }
				}
			}

			//rev dep
			storage.changed_f();
		}
	}

        this.on_consumption_response = function() {
                var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			storage_consumption.consumption_data = resp["consumption"];
			storage_consumption.consumption.innerHTML = "";

			storage_consumption.consumption.appendChild(storage_consumption.get_consumption_calc());
			storage_consumption.consumption.appendChild(storage_consumption.get_consumption_calc_default());

			storage_consumption.set_consumption_circle();

			storage_consumption.consumption.appendChild(storage_consumption.data_table.get_header_row());

			var idx = [];
			for (var consumption_item in storage_consumption.consumption_data) {
				if (storage_consumption.consumption_data.hasOwnProperty(consumption_item)) {
					idx.push(parseInt(consumption_item));
				}
			}
			if (idx.length > 0) {
				idx.sort(function(a,b) {return b-a});
				for (var i = 0; i < idx.length; i++) {
					if (i == 0) {
                        	        	storage_consumption.consumption.appendChild(storage_consumption.data_table.get_insert_row(storage_consumption.consumption_data[idx[i]],  { "storages": storage.storages, "products": products.product_data } ));
					}
	                                storage_consumption.consumption.appendChild(storage_consumption.data_table.get_data_row(storage_consumption.consumption_data[idx[i]], { "storages": storage.storages, "products": products.product_data } ));
				}
			} else {
				 storage_consumption.consumption.appendChild(storage_consumption.data_table.get_insert_row(null, { "storages": storage.storages, "products": products.product_data } ));
			}
			storage_consumption.on_select_storage_insert_change();
		}
        }

	this.update = function() {
		if (this.changed) {
			this.changed = false;

			if (user.login_data != null) {
				if (storage.storage_data != null) {
					var p = {};
					storage_consumption.db.query_post("storage/consumption", p, storage_consumption.on_consumption_response);
				}
			} else {
				storage_consumption.elem.style.display = "none";
				storage_consumption.consumption.innerHTML = "";
				storage_consumption.consumption_data = null;
			}
		}
	}
}
