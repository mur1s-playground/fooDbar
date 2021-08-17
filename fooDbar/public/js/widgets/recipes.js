var Recipes = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.widget = new Widget("Recipes");

	this.recipe_consumption_group_data = null;
	this.recipe_consumption_group_agg_data = null;
	this.recipe_consumption_group_rr_data = null;
	this.recipe_consumption_group_rr_demand_data = null;

	this.elem = this.widget.elem;
	this.elem.style.display = "none";

	this.menu_elem = document.createElement("table");

	this.changed = true;

	this.recipes_sub_menu = document.createElement("div");
	this.recipes_sub_menu.className = "recipes_sub_menu";

	this.on_menu_btn_click = function(action) {
		var tabs = ["Query", "data_table_Agg", "data_table_Raw"];
		for (var t in tabs) {
			if (tabs[t] == action) {
				document.getElementById(recipes.widget.name + "_" + tabs[t]).style.display = "inline";
			} else {
				document.getElementById(recipes.widget.name + "_" + tabs[t]).style.display = "none";
			}
		}
	}

	this.get_recipes_sub_menu = function() {
		recipes.recipes_sub_menu.innerHTML = "";

		var query_btn = document.createElement("button");
		query_btn.style.backgroundImage = "linear-gradient(to right, #cccccc, var(--recipes_color))";
		var search_img = document.createElement("img");
                search_img.src = "/img/symbol_search.svg";
                search_img.style.width = "25px";
                query_btn.appendChild(search_img);
		var recipe_img = document.createElement("img");
                recipe_img.src = "/img/symbol_recipe.svg";
                recipe_img.style.width = "25px";
                query_btn.appendChild(recipe_img);
		query_btn.onclick = function() {
			recipes.on_menu_btn_click("Query");
		}
		recipes.recipes_sub_menu.appendChild(query_btn);

		var agg_btn = document.createElement("button");
		agg_btn.style.backgroundImage = "linear-gradient(to right, var(--recipes_color), var(--storage_color))";
		var recipe_img = document.createElement("img");
		recipe_img.src = "/img/symbol_recipe.svg";
		recipe_img.style.width = "25px";
		agg_btn.appendChild(recipe_img);
		var storage_img = document.createElement("img");
		storage_img.src = "/img/symbol_storage.svg";
		storage_img.style.width = "25px";
		agg_btn.appendChild(storage_img);
		agg_btn.onclick = function() {
			recipes.on_menu_btn_click("data_table_Agg");
		}
		recipes.recipes_sub_menu.appendChild(agg_btn);
/*
		var raw_btn = document.createElement("button");
		raw_btn.innerHTML = "R";
		raw_btn.onclick = function() {
			recipes.on_menu_btn_click("data_table_Raw");
		}
		recipes.recipes_sub_menu.appendChild(raw_btn);
*/
/*
		var update_btn = document.createElement("button");
		var refresh_img = document.createElement("img");
		refresh_img.src = "/img/symbol_refresh.svg";
		refresh_img.style.width = "25px";
		update_btn.appendChild(refresh_img);

                update_btn.onclick = function() {
                	var p = { };
                        recipes.db.query_post("recipe/processconsumption/update", p, recipes.on_process_consumption_update_result);
                }
		recipes.recipes_sub_menu.appendChild(update_btn);
*/
	}

        this.data_table = new DataTable(this, "recipe_consumption_group",
                                {       "ProductsIds": { "title": "Products", "header": { "type": "text", "text": "Products", "text_class": "datatable_header" },  "join_list": { "model": "products", "field": "Name" } },
                                        "Amounts": { "title": "Amounts", "header": { "type": "text", "text": "Amounts", "text_class": "datatable_header" }, "assoc_list": { "id": 0 } },
                                        "MinPrice": { "title": "MinPrice", "header": { "type": "text", "text": "MinPrice", "text_class": "datatable_header" } },
                                        "MaxPrice": { "title": "MaxPrice", "header": { "type": "text", "text": "MaxPrice", "text_class": "datatable_header" } },
                                        "Datetime": { "title": "Datetime", "header": { "type": "text", "text": "Datetime", "text_class": "datatable_header" } },
                                        "Mj": { "title": "Mj", "header": { "type": "text", "text": "Mj", "text_class": "datatable_header" } },
                                        "NFatPercent": { "title": "Fat%", "header": { "type": "text", "text": "Fat%", "text_class": "datatable_header" } },
                                        "NCarbsPercent": { "title": "Carbs%", "header": { "type": "text", "text": "Carbs%", "text_class": "datatable_header" } },
                                        "NProteinPercent": { "title": "Protein%", "header": { "type": "text", "text": "Protein%", "text_class": "datatable_header" } },
                                        "NFiberPercent": { "title": "Fiber%", "header": { "type": "text", "text": "Fiber%", "text_class": "datatable_header" } },
                                        "NSaltPercent": { "title": "Salt%", "header": { "type": "text", "text": "Salt%", "text_class": "datatable_header" } },
/*
                                        "ANotVegetarian": { "title": "Not Vegetarian", "header": { "type": "text", "text": "Not Vegetarian", "text_class": "datatable_header" } },
                                        "ANotVegan": { "title": "Not Vegan", "header": { "type": "text", "text": "Not Vegan", "text_class": "datatable_header" } },
                                        "AGluten": { "title": "Gluten", "header": { "type": "text", "text": "Gluten", "text_class": "datatable_header" } },
                                        "ACrustaceans": { "title": "Crustaceans", "header": { "type": "text", "text": "Crustaceans", "text_class": "datatable_header" } },
                                        "AEggs": { "title": "Eggs", "header": { "type": "text", "text": "Eggs", "text_class": "datatable_header" } },
                                        "AFish": { "title": "Fish", "header": { "type": "text", "text": "Fish", "text_class": "datatable_header" } },
                                        "APeanuts": { "title": "Peanuts", "header": { "type": "text", "text": "Peanuts", "text_class": "datatable_header" } },
                                        "ASoybeans": { "title": "Soybeans", "header": { "type": "text", "text": "Soybeans", "text_class": "datatable_header" } },
                                        "AMilk": { "title": "Milk", "header": { "type": "text", "text": "Milk", "text_class": "datatable_header" } },
                                        "ANuts": { "title": "Nuts", "header": { "type": "text", "text": "Nuts", "text_class": "datatable_header" } },
                                        "ACeleriac": { "title": "Celeriac", "header": { "type": "text", "text": "Celeriac", "text_class": "datatable_header" } },
                                        "AMustard": { "title": "Mustard", "header": { "type": "text", "text": "Mustard", "text_class": "datatable_header" } },
                                        "ASesam": { "title": "Sesam", "header": { "type": "text", "text": "Sesam", "text_class": "datatable_header" } },
                                        "ASulfur": { "title": "Sulfur", "header": { "type": "text", "text": "Sulfur", "text_class": "datatable_header" } },
                                        "ALupins": { "title": "Lupins", "header": { "type": "text", "text": "Lupins", "text_class": "datatable_header" } },
                                        "AMolluscs": { "title": "Molluscs", "header": { "type": "text", "text": "Molluscs", "text_class": "datatable_header" } }
*/
                                },
				{ },
				{ }
				);

	this.is_product_in_storage = function(product_id) {
		if (storage.storage_has_product(product_id)) {
			return "recipes_product_in_storage";
		}
		return "";
	}


	this.data_table_agg = new DataTable(this, "recipe_consumption_group",
                                {
					"ProductsIds": { "title": "Products", "header": { "type": "img", "img_src": "/img/symbol_products.svg", "img_class": "datatable_header_recipes" },  "join_list": { "model": "products", "field": "Name", "classname_callback": this.is_product_in_storage } },
                                        "PricePerMjMin": { "title": "PricePerMjMin", "header": { "type": "img", "img_src": "/img/symbol_min.svg", "img_class": "datatable_header_recipes" } },
					"PricePerMjAvg": { "title": "PricePerMjAvg", "header": { "type": "img", "img_src": "/img/symbol_pricepermj.svg", "img_class": "datatable_header_recipes" } },
                                        "PricePerMjMax": { "title": "PricePerMjMax", "header": { "type": "img", "img_src": "/img/symbol_max.svg", "img_class": "datatable_header_recipes" } },
					"MjMin": { "title": "MjMin", "header": { "type": "img", "img_src": "/img/symbol_min.svg", "img_class": "datatable_header_recipes" } },
					"MjAvg": { "title": "MjAvg", "header": { "type": "img", "img_src": "/img/symbol_mj.svg", "img_class": "datatable_header_recipes" } },
                                        "MjMax": { "title": "MjMax", "header": { "type": "img", "img_src": "/img/symbol_max.svg", "img_class": "datatable_header_recipes" } },
					"NFatPercentMin": { "title": "Fat% Min", "header": { "type": "img", "img_src": "/img/symbol_min.svg", "img_class": "datatable_header_recipes" } },
                                        "NFatPercentAvg": { "title": "Fat% Avg", "header": { "type": "img", "img_src": "/img/symbol_fat.svg", "img_class": "datatable_header_recipes" } },
					"NFatPercentMax": { "title": "Fat% Max", "header": { "type": "img", "img_src": "/img/symbol_max.svg", "img_class": "datatable_header_recipes" } },
					"NCarbsPercentMin": { "title": "Carbs% Min", "header": { "type": "img", "img_src": "/img/symbol_min.svg", "img_class": "datatable_header_recipes" } },
                                        "NCarbsPercentAvg": { "title": "Carbs% Avg", "header": { "type": "img", "img_src": "/img/symbol_carbs.svg", "img_class": "datatable_header_recipes" } },
					"NCarbsPercentMax": { "title": "Carbs% Max", "header": { "type": "img", "img_src": "/img/symbol_max.svg", "img_class": "datatable_header_recipes" } },
					"NProteinPercentMin": { "title": "Protein% Min", "header": { "type": "img", "img_src": "/img/symbol_min.svg", "img_class": "datatable_header_recipes" } },
                                        "NProteinPercentAvg": { "title": "Protein% Avg", "header": { "type": "img", "img_src": "/img/symbol_muscle.svg", "img_class": "datatable_header_recipes" } },
					"NProteinPercentMax": { "title": "Protein% Max", "header": { "type": "img", "img_src": "/img/symbol_max.svg", "img_class": "datatable_header_recipes" } },
                                        "NFiberPercentMin": { "title": "Fiber% Min", "header": { "type": "img", "img_src": "/img/symbol_min.svg", "img_class": "datatable_header_recipes" } },
					"NFiberPercentAvg": { "title": "Fiber% Avg", "header": { "type": "img", "img_src": "/img/symbol_fiber.svg", "img_class": "datatable_header_recipes" } },
					"NFiberPercentMax": { "title": "Fiber% Max", "header": { "type": "img", "img_src": "/img/symbol_max.svg", "img_class": "datatable_header_recipes" } },
					"NSaltPercentMin": { "title": "Salt% Min", "header": { "type": "img", "img_src": "/img/symbol_min.svg", "img_class": "datatable_header_recipes" } },
					"NSaltPercentAvg": { "title": "Salt% Avg", "header": { "type": "img", "img_src": "/img/symbol_salt.svg", "img_class": "datatable_header_recipes" } },
                                        "NSaltPercentMax": { "title": "Salt% Max", "header": { "type": "img", "img_src": "/img/symbol_max.svg", "img_class": "datatable_header_recipes" } },
                                },
                                { },
                                { }
                                );

	this.data_table_rr = new DataTable(this, "recipe_consumption_group_rr",
                                {
                                        "ProductsIds": { "title": "Products", "header": { "type": "img", "img_src": "/img/symbol_products.svg", "img_class": "datatable_header_recipes" },  "join_list": { "model": "products", "field": "Name", "classname_callback": this.is_product_in_storage } },
					"Amounts": { "title": "Amounts", "header": { "type": "text", "text": "Amounts", "text_class": "datatable_header" }, "assoc_list": { "id": 0 } },
//                                      "PricePerMjAvg": { "title": "PricePerMjAvg", "header": { "type": "img", "img_src": "/img/symbol_pricepermj.svg", "img_class": "datatable_header_recipes" } },
                                        "Mj": { "title": "Mj", "header": { "type": "img", "img_src": "/img/symbol_mj.svg", "img_class": "datatable_header_recipes" } },
                                        "NFatPercent": { "title": "Fat%", "header": { "type": "img", "img_src": "/img/symbol_fat.svg", "img_class": "datatable_header_recipes" } },
                                        "NCarbsPercent": { "title": "Carbs%", "header": { "type": "img", "img_src": "/img/symbol_carbs.svg", "img_class": "datatable_header_recipes" } },
                                        "NProteinPercent": { "title": "Protein%", "header": { "type": "img", "img_src": "/img/symbol_muscle.svg", "img_class": "datatable_header_recipes" } },
                                },
                                { },
                                { }
                                );

	this.on_recipe_request_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			recipes.recipe_consumption_group_rr_data = resp["recipes"]
			recipes.recipe_consumption_group_rr_demand_data = resp["product_demand"];
                        recipes.recipe_consumption_group_rr.innerHTML = "";

			var demand = false;
			for (var pd in resp["product_demand"]) {
				if (resp["product_demand"].hasOwnProperty(pd)) {
					var pd_demand = document.createElement("tr");
					var td0 = document.createElement("td");
					td0.innerHTML = products.product_data[pd]["Name"];
					pd_demand.appendChild(td0);
					var td1 = document.createElement("td");
					td1.innerHTML = Math.round(resp["product_demand"][pd] * 100)/100;
					pd_demand.appendChild(td1);
					recipes.recipe_consumption_group_rr.appendChild(pd_demand);
					demand = true;
				}
			}

			if (demand) {
				var pd_demand = document.createElement("tr");
                                var td0 = document.createElement("td")

				var btn = document.createElement("button");
				btn.style.backgroundColor = "var(--shopping_list_color)";
				var shopping_list_img = document.createElement("img");
				shopping_list_img.src = "/img/symbol_shopping_list.svg";
				shopping_list_img.style.width = "25px";
				btn.appendChild(shopping_list_img);
				btn.title= "Add to shopping list";
				btn.onclick = function() {
					shopping_list.shopping_list_append(recipes.recipe_consumption_group_rr_demand_data);
					menu.switch_tab("Shopping List");
				}
				td0.appendChild(btn);
				pd_demand.appendChild(td0);
				recipes.recipe_consumption_group_rr.appendChild(pd_demand);
			}

                        recipes.recipe_consumption_group_rr.appendChild(recipes.data_table_rr.get_header_row());

                        for (var cg in recipes.recipe_consumption_group_rr_data) {
                                if (recipes.recipe_consumption_group_rr_data.hasOwnProperty(cg)) {
                                        recipes.recipe_consumption_group_rr.appendChild(recipes.data_table_rr.get_data_row(recipes.recipe_consumption_group_rr_data[cg], { "products": products.product_data }));
                                }
                        }
		}
	}

	this.on_daily_user_preset_insert_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var day = resp["Day"];
			recipes.recipe_consumption_group_query_daily_presets.push(JSON.parse(resp["recipe_request_daily_preset_item"]["Preset"]));
			recipes.recipe_consumption_group_query_daily_presets_selected[day] = recipes.recipe_consumption_group_query_daily_presets.length - 1;

			var row = document.getElementById(recipes.widget.name + "_recipes_query_row" + day);
                        recipes.recipe_consumption_group_query.replaceChild(recipes.recipe_consumption_group_query_get_day(day), row);
		}
	}

	this.on_get_daily_user_preset_response = function() {
		recipes.recipe_consumption_group_query_daily_presets = [
                	{ "Name": "target automatic", "Demand": "auto", "DemandLock": true, "MealsPerDay": 3, "MealsPerDayWeights": [0.3, 0.5, 0.2], "TotalNDist": [0.275, 0.5, 0.225] },
	                { "Name": "custom example", "Demand": 13.37, "DemandLock": true, "MealsPerDay": 2, "MealsPerDayWeights": [0.6, 0.4], "TotalNDist": [0.2, 0.45, 0.35] }
        	];
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			for (var preset in resp["recipe_request_daily_preset"]) {
				if (resp["recipe_request_daily_preset"].hasOwnProperty(preset)) {
					recipes.recipe_consumption_group_query_daily_presets.push(JSON.parse(resp["recipe_request_daily_preset"][preset]["Preset"]));
				}
			}
		}
		recipes.get_recipes_query();
		recipes.recipe_query_demand_info();
	}

	this.recipe_consumption_group_query_days = 3;
	this.recipe_consumption_group_query_daily_presets = [
		{ "Name": "target automatic", "Demand": "auto", "DemandLock": true, "MealsPerDay": 3, "MealsPerDayWeights": [0.3, 0.5, 0.2], "TotalNDist": [0.275, 0.5, 0.225] },
		{ "Name": "custom example", "Demand": 13.37, "DemandLock": true, "MealsPerDay": 2, "MealsPerDayWeights": [0.6, 0.4], "TotalNDist": [0.2, 0.45, 0.35] }
	];
	this.recipe_consumption_group_query_daily_presets_selected = [0, 0, 0];

	this.recipe_consumption_group_query_get_day = function(day) {
		var row = document.createElement("tr");
		row.id = recipes.widget.name + "_recipes_query_row" + day;
		row.style.marginTop = "20px";

                var td_0 = document.createElement("td");
                var select_daily_preset = document.createElement("select");
                var dp_ct = 0;
                for (var dp in this.recipe_consumption_group_query_daily_presets) {
                        var option = document.createElement("option");
                        option.innerHTML = this.recipe_consumption_group_query_daily_presets[dp_ct]["Name"];
			if (this.recipe_consumption_group_query_daily_presets_selected[day] == dp_ct) {
				option.selected = true;
			}
                        select_daily_preset.appendChild(option);
                        dp_ct++;
                }
		select_daily_preset.day = day;
		select_daily_preset.row = row;
		select_daily_preset.onchange = function() {
			recipes.recipe_consumption_group_query_daily_presets_selected[day] = this.selectedIndex;
			recipes.recipe_consumption_group_query.replaceChild(recipes.recipe_consumption_group_query_get_day(this.day), this.row);
			recipes.recipe_query_demand_info();
		}
		td_0.appendChild(select_daily_preset);
		row.appendChild(td_0);

		var demand_value = 0;
		if (this.recipe_consumption_group_query_daily_presets[this.recipe_consumption_group_query_daily_presets_selected[day]]["Demand"] == "auto") {
			demand_value = Math.round(parseFloat(demand.demand_data["MJPerDay"]["target"]) * 100) / 100;
		} else {
			demand_value = this.recipe_consumption_group_query_daily_presets[this.recipe_consumption_group_query_daily_presets_selected[day]]["Demand"];
		}

		var td_1 = document.createElement("td");
		var input_demand = document.createElement("input");
		input_demand.id = this.widget.name + "_recipes_query_demand" + day;
		input_demand.disabled = true;
		input_demand.value = demand_value;
		td_1.appendChild(input_demand);
		row.appendChild(td_1);

		var td_2 = document.createElement("td");
		var input_demand_lock = document.createElement("input");
		input_demand_lock.is_auto = this.recipe_consumption_group_query_daily_presets[this.recipe_consumption_group_query_daily_presets_selected[day]]["Demand"] == "auto";
		input_demand_lock.day = day;
		input_demand_lock.type = "checkbox";
		input_demand_lock.title = "lock total demand";
		input_demand_lock.checked = this.recipe_consumption_group_query_daily_presets[this.recipe_consumption_group_query_daily_presets_selected[day]]["DemandLock"];
		input_demand_lock.onchange = function() {
			var multislider = document.getElementById(recipes.widget.name + "_multislider_col" + this.day).multislider;
			multislider.set_lock_sum(this.checked);
			this.is_auto = false;
			document.getElementById(recipes.widget.name + "_recipes_save_daily_preset" + this.day).style.visibility = "visible";
		}
		td_2.appendChild(input_demand_lock);
		row.appendChild(td_2);

		var td_3 = document.createElement("td");
		var select_meals_per_day = document.createElement("select");
		select_meals_per_day.title = "Meals per day";
		for (var o = 0; o < 7; o++) {
			var option = document.createElement("option");
			option.innerHTML = o;
			if (this.recipe_consumption_group_query_daily_presets[this.recipe_consumption_group_query_daily_presets_selected[day]]["MealsPerDay"] == o) {
				option.selected = true;
			}
			select_meals_per_day.appendChild(option);
		}
		select_meals_per_day.day = day;
		select_meals_per_day.demand_value = demand_value;
		select_meals_per_day.onchange = function() {
			var multislider = document.getElementById(recipes.widget.name + "_multislider_col" + this.day).multislider;

			var values = [];
			for (var v = 0; v < this.selectedIndex; v++) {
				values.push(1.0/this.selectedIndex);
			}

			if (this.selectedIndex == 0) {
				multislider.elem_bar.sum_elem.value = 0;
			} else {
				multislider.elem_bar.sum_elem.value = this.demand_value;
			}
			recipes.recipe_query_demand_info();
			multislider.set_thumb_count(this.selectedIndex, values);
			document.getElementById(recipes.widget.name + "_recipes_save_daily_preset" + this.day).style.visibility = "visible";
		}
		td_3.appendChild(select_meals_per_day);
		row.appendChild(td_3);

		var multislider_max = input_demand.value;
		if (select_meals_per_day.selectedIndex == 0) {
		} else if (select_meals_per_day.selectedIndex == 1) {
			multislider_max = parseFloat(input_demand.value * 2);
		}

		var td_4 = document.createElement("td");
		td_4.id = this.widget.name + "_multislider_col" + day;
		var multislider_mpd = new MultiSlider(	"recipes_csize_range_" + day,
							this.recipe_consumption_group_query_daily_presets[this.recipe_consumption_group_query_daily_presets_selected[day]]["MealsPerDay"],
							this.recipe_consumption_group_query_daily_presets[this.recipe_consumption_group_query_daily_presets_selected[day]]["MealsPerDayWeights"],
							[0, input_demand.value],
							input_demand,
							input_demand_lock.checked
							);
		multislider_mpd.elem_bar.onchange_callback = function() {
			document.getElementById(recipes.widget.name + "_recipes_save_daily_preset" + day).style.visibility = "visible";
			recipes.recipe_query_demand_info();
		}
		td_4.multislider = multislider_mpd;
		td_4.appendChild(multislider_mpd.elem);
		row.appendChild(td_4);

		var td_5 = document.createElement("td");
		td_5.id = this.widget.name + "_n_dist_col" + day;

		var p100 = document.createElement("input");
		p100.value = 100;
		p100.style.display = "none";
		td_5.appendChild(p100);
		td_5.appendChild(document.createElement("br"));
		td_5.appendChild(document.createElement("br"));
		var multislider_ndist = new MultiSlider( "recipes_n_dist_range_" + day,
							3,
							this.recipe_consumption_group_query_daily_presets[this.recipe_consumption_group_query_daily_presets_selected[day]]["TotalNDist"],
							[0, 100],
							p100,
							true,
							["var(--fat_color)", "var(--carbs_color)", "var(--muscle_color)"],
							["Fat", "Carbs", "Protein"],
							["/img/symbol_fat.svg", "/img/symbol_carbs.svg", "/img/symbol_muscle.svg"]
							);
		multislider_ndist.elem_bar.onchange_callback = function() {
                        document.getElementById(recipes.widget.name + "_recipes_save_daily_preset" + day).style.visibility = "visible";
                }
		td_5.appendChild(multislider_ndist.elem);
		td_5.appendChild(document.createElement("br"));
                td_5.appendChild(document.createElement("br"));
		row.appendChild(td_5);

		var td_6 = document.createElement("td");
		td_6.style.visibility = "hidden";
		td_6.id = recipes.widget.name + "_recipes_save_daily_preset" + day;

		var save_preset_name = document.createElement("input");
		save_preset_name.placeholder = "Preset name";
		td_6.appendChild(save_preset_name);

		var save_preset_btn = document.createElement("button");
		save_preset_btn.sp_name = save_preset_name;
		save_preset_btn.demand_elem = input_demand;
		save_preset_btn.input_demand_lock = input_demand_lock;
		save_preset_btn.mpd_select = select_meals_per_day;
		save_preset_btn.mpd_multislider = multislider_mpd;
		save_preset_btn.ndist_multislider = multislider_ndist;
		save_preset_btn.innerHTML = "&#x1F5B4;";
		save_preset_btn.day = day;
		save_preset_btn.title = "Save as preset";
		save_preset_btn.onclick = function() {
			var demand_value = this.demand_elem.value;
			if (input_demand_lock.is_auto) {
				demand_value = "auto";
			}
			var p = {
				"recipe_request_daily_preset": {
					"Day": this.day,		//UI

					"PresetName": this.sp_name.value,
					"Preset": JSON.stringify({ "Name": this.sp_name.value, "Demand": demand_value, "DemandLock": this.input_demand_lock.checked, "MealsPerDay": this.mpd_select.selectedIndex, "MealsPerDayWeights": this.mpd_multislider.get_values_for_config(), "TotalNDist": this.ndist_multislider.get_values_for_config() })
				}
			};
			recipes.db.query_post("recipe/index/insertdailyuserpreset", p, recipes.on_daily_user_preset_insert_response);
		};
		td_6.appendChild(save_preset_btn);
		row.appendChild(td_6);

		return row;
	}

	this.recipe_query_demand_info = function() {
		var total = 0;
		for (var d = 0; d < recipes.recipe_consumption_group_query_days; d++) {
			var value = parseFloat(document.getElementById(recipes.widget.name + "_recipes_query_demand" + d).value);
			total += value;
		}
		total = Math.round(total * 100) / 100;
		var i = Math.round(total / recipes.recipe_consumption_group_query_days * 100) / 100;
		document.getElementById(recipes.widget.name + "_request_mj_info").value = "" + total + " (\u2205 " + i + ")";
	}

	this.recipe_consumption_group_query_get_days = function() {
		for (var d = 0; d < this.recipe_consumption_group_query_days; d++) {
			var row = this.recipe_consumption_group_query_get_day(d);
			this.recipe_consumption_group_query.appendChild(row);
		}
		var row = document.createElement("tr");
		row.appendChild(document.createElement("td"));
		var td_1 = document.createElement("td");
		var input_mj = document.createElement("input");
		input_mj.id = recipes.widget.name + "_request_mj_info";
		input_mj.disabled = true;
		td_1.appendChild(input_mj);

		row.appendChild(td_1);
		this.recipe_consumption_group_query.appendChild(row);
	}

	this.get_recipes_query = function() {
		this.recipe_consumption_group_query.innerHTML = "";

		var row0 = document.createElement("tr");
		var td0 = document.createElement("td");
		var select_daycount = document.createElement("select");
		for (var d = 1; d < 8; d++) {
			var option = document.createElement("option");
			option.innerHTML = d;
			select_daycount.appendChild(option);
		}
		select_daycount.selectedIndex = this.recipe_consumption_group_query_days - 1;
		select_daycount.onchange = function() {
			var days = this.selectedIndex + 1;
			for (var d = recipes.recipe_consumption_group_query_daily_presets_selected.length; d < days; d++) {
				recipes.recipe_consumption_group_query_daily_presets_selected.push(0);
			}
			recipes.recipe_consumption_group_query_days = days;
			recipes.get_recipes_query();
			recipes.recipe_query_demand_info();
		};
		td0.appendChild(select_daycount);
		row0.appendChild(td0);
		var td00 = document.createElement("td");
                td00.colSpan = 6;
		row0.appendChild(td00);
		this.recipe_consumption_group_query.appendChild(row0);

		this.recipe_consumption_group_query_get_days();

		var row = document.createElement("tr");
		var td1 = document.createElement("td");
		td1.colSpan = 7;

		var q_btn = document.createElement("button");
		var search_img = document.createElement("img");
		search_img.src = "/img/symbol_search.svg";
		search_img.style.width = "25px";
		q_btn.appendChild(search_img);
                q_btn.onclick = function() {
                        var demand_value = document.getElementById(recipes.widget.name + "_request_mj_info").value.split(' ')[0];
                        var parts = [];
                        var n_dist = [];
                        for (var d = 0; d < recipes.recipe_consumption_group_query_days; d++) {
                                var mpd_elem = document.getElementById("recipes_csize_range_" + d);
                                parts.push(mpd_elem.get_values());
                                var ndist_elem = document.getElementById("recipes_n_dist_range_" + d);
                                n_dist.push(ndist_elem.get_values());
                        }

                        var p = {
                                "recipes_request": {
                                        "demand": demand_value,
                                        "days": recipes.recipe_consumption_group_query_days,
                                        "parts": parts,
                                        "n_dist": n_dist
                                }
                        };
                        recipes.db.query_post("recipe/index/request", p, recipes.on_recipe_request_response);
                }

		td1.appendChild(q_btn);
		row.appendChild(td1);
		this.recipe_consumption_group_query.appendChild(row);

		var row2 = document.createElement("tr");
		var td2 = document.createElement("td");
		td2.colSpan = 7;
		td2.appendChild(this.recipe_consumption_group_rr);
		row2.appendChild(td2);
		this.recipe_consumption_group_query.appendChild(row2);
	}

	this.recipe_consumption_group_query = document.createElement("table");
	this.recipe_consumption_group_query.id = this.widget.name + "_Query";
	this.recipe_consumption_group_query.className = "recipes_query";

	this.recipe_consumption_group_rr = document.createElement("table");
	this.recipe_consumption_group_rr.className = "recipes_cg_agg_table";

	this.recipe_consumption_group = document.createElement("table");
	this.recipe_consumption_group.id = this.widget.name + "_data_table_Raw";
	this.recipe_consumption_group.className = "recipes_cg_table";
	this.recipe_consumption_group.style.display = "none";

	this.recipe_consumption_group_agg = document.createElement("table");
	this.recipe_consumption_group_agg.id = this.widget.name + "_data_table_Agg";
	this.recipe_consumption_group_agg.className = "recipes_cg_agg_table";
	this.recipe_consumption_group_agg.style.display = "none";

	this.changed_f = function() {
		this.changed = true;
		if (this.change_dependencies != null) {
			for (var i = 0; i < this.change_dependencies.length; i++) {
				this.change_dependencies[i].changed_f();
			}
		}
	}

	this.on_process_consumption_update_result = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var p = { };
                        recipes.db.query_post("recipe/processconsumption/get", p, recipes.on_process_consumption_result);
		}
	}

	this.on_process_consumption_result = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			recipes.recipe_consumption_group_data = resp["recipe_consumption_group"]
			recipes.recipe_consumption_group.innerHTML = "";

			recipes.recipe_consumption_group.appendChild(recipes.data_table.get_header_row());

			for (var cg in recipes.recipe_consumption_group_data) {
				if (recipes.recipe_consumption_group_data.hasOwnProperty(cg)) {
					recipes.recipe_consumption_group.appendChild(recipes.data_table.get_data_row(recipes.recipe_consumption_group_data[cg], { "products": products.product_data }));
				}
			}

			recipes.recipe_consumption_group_agg_data = resp["recipe_consumption_group_agg"];
			recipes.recipe_consumption_group_agg.innerHTML = "";

			recipes.recipe_consumption_group_agg.appendChild(recipes.data_table_agg.get_header_row());

			for (var cga in recipes.recipe_consumption_group_agg_data) {
				if (recipes.recipe_consumption_group_agg_data.hasOwnProperty(cga)) {
					recipes.recipe_consumption_group_agg.appendChild(recipes.data_table_agg.get_data_row(recipes.recipe_consumption_group_agg_data[cga], { "products": products.product_data } ));
				}
			}

                        recipes.changed_f();
                        recipes.changed = false;
		}
	}

	this.update = function() {
		if (this.changed) {
			this.changed = false;

			if (user.login_data != null && products.product_data != null && demand.demand_data != null) {
				this.widget.content.innerHTML = "";
				this.get_recipes_sub_menu();
				this.get_recipes_query();
				this.widget.content.appendChild(this.recipes_sub_menu);
				var p = { };
				recipes.db.query_post("recipe/index/getdailyuserpresets", p, recipes.on_get_daily_user_preset_response);
				this.widget.content.appendChild(this.recipe_consumption_group_query);
				recipes.recipe_query_demand_info();
				this.widget.content.appendChild(this.recipe_consumption_group);
				this.widget.content.appendChild(this.recipe_consumption_group_agg);
                                recipes.db.query_post("recipe/processconsumption/get", p, recipes.on_process_consumption_result);
			} else {
				this.widget.content.innerHTML = "";
				this.elem.style.display = "none";
			}
		}
	}
}
