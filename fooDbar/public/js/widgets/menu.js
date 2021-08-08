var Menu = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.widgets = [
				[ ""		, ""		, ""		, ""		, ""		, ""		, "Recipes"	, "right_a"	, "Shopping List"	, ""		, ""	    ],
				[ ""            , ""            , ""            , ""            , ""            , "top_right_a" , "down_a"	, "top_left_a"  , "down_a"       	, "top_left_a"  , ""        ],
				[ "User State"	, "right_a" 	, "User Target"	, "right_a"	, "Demand"	, "right_a"	, "Consumption"	, "left_a"	, "Storage"		, "left_a"	, "Products"]
			];


	this.menu_content = {
		"right_a": 	 { "type": "img", 	"imgs": [ "/img/symbol_right_arrow.svg"						] },
		"left_a": 	 { "type": "img",	"imgs": [ "/img/symbol_left_arrow.svg"						] },
		"top_right_a": 	 { "type": "img", 	"imgs": [ "/img/symbol_top_right_arrow.svg"					] },
		"top_left_a": 	 { "type": "img", 	"imgs": [ "/img/symbol_top_left_arrow.svg"					] },
		"down_a":     	 { "type": "img",       "imgs": [ "/img/symbol_down_arrow.svg"                                      	] },
		"Recipes": 	 { "type": "button", 	"imgs": [ "/img/symbol_recipe.svg" 						], "class": "menu_recipes"		},
		"Shopping List": { "type": "button", 	"imgs": [ "/img/symbol_shopping_list.svg"					], "class": "menu_shopping_list"	},
		"User State": 	 { "type": "button", 	"imgs": [ "/img/symbol_person_height.svg", "/img/symbol_person_weight.svg" 	], "class": "menu_user_state"		},
		"User Target": 	 { "type": "button", 	"imgs": [ "/img/symbol_bmi.svg"		 , "/img/symbol_target.svg" 		], "class": "menu_user_target" 		},
		"Demand": 	 { "type": "button", 	"imgs": [ "/img/symbol_demand.svg" 						], "class": "menu_demand" 		},
		"Consumption":	 { "type": "button", 	"imgs": [ "/img/symbol_person.svg" 						], "class": "menu_consumption" 		},
		"Storage": 	 { "type": "button", 	"imgs": [ "/img/symbol_storage.svg" 						], "class": "menu_storage" 		},
		"Products": 	 { "type": "button", 	"imgs": [ "/img/symbol_products.svg" 						], "class": "menu_products" 		}
	};

	this.widgets_names = [ "User State", "User Target", "Demand", "Consumption", "Storage", "Products", "Recipes", "Shopping List" ];

	this.widget = new Widget("Menu");

	this.elem = this.widget.elem;
	this.elem.style.display = "none";

	this.menu_elem = document.createElement("table");

	this.changed = true;

	this.changed_f = function() {
		this.changed = true;
		if (this.change_dependencies != null) {
			for (var i = 0; i < this.change_dependencies.length; i++) {
				this.change_dependencies[i].changed_f();
			}
		}
	}

	this.switch_tab = function(wg) {
		for (var w in this.widgets_names) {
			var elem = document.getElementById("wg_" + this.widgets_names[w]);
			if (this.widgets_names[w] == wg) {
				elem.style.display = "block";
			} else {
				elem.style.display = "none";
			}
		}
	}

	this.update = function() {
		if (this.changed) {
			this.changed = false;

			this.widget.content.innerHTML = "";
			this.widget.content.appendChild(this.menu_elem);

			if (user.login_data != null) {		/* LOGGED IN */
				this.elem.style.display = "block";
				this.menu_elem.innerHTML = "";
				for (var row in this.widgets) {
					var r = document.createElement("tr");
					for (var wg in this.widgets[row]) {
						var col = document.createElement("td");

						var spec = this.widgets[row][wg];
						var content = this.menu_content[spec];
						var sp = null;

						if (content != null) {
						if (content["type"] == "button") {
							sp = document.createElement("button");
							sp.spec = spec;
							sp.title = spec;
							sp.className = content["class"];
							sp.onclick = function() {
                                                        	menu.switch_tab(this.spec);
	                                                };
							col.appendChild(sp);
						}

						for (var src in content["imgs"]) {
							if (content["imgs"].hasOwnProperty(src)) {
								var img = document.createElement("img");
								img.src = content["imgs"][src];
								img.title = spec;
								if (sp != null) {
									img.style.height = "30px";
									sp.appendChild(img);
								} else {
									img.style.height = "20px";
									col.appendChild(img);
								}
							}
						}
						}
						r.appendChild(col);
					}
					this.menu_elem.appendChild(r);
				}
			} else {
				this.elem.style.display = "none";
			}
		}
	}
}
