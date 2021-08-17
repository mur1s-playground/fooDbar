var Menu = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.active_widget = "none";
	this.widgets_names = [ "User State", "User Target", "Demand", "Consumption", "Storage", "Products", "Recipes", "Shopping List" ];

	this.widgets_circle_view_setter = [
				user_state.set_circle_view,
				user_target.set_circle_view,
                                null,
                                storage_consumption.set_circle_view,
                                null,
                                null,
                                null,
                                null,
				];

	this.circle_view = {};

	this.circle_view_menu_time = document.createElement("div");
	this.circle_view_menu_time.style.display = "table-cell";
	this.circle_view_menu_time.style.verticalAlign = "middle";
	this.circle_view_menu_time.style.fontSize = "30px";
	this.circle_view_menu_time_f = function() {
		var date = new Date();
		var h = date.getHours();
		var m = date.getMinutes();
		var s = date.getSeconds();
		menu.circle_view_menu_time.innerHTML = ("0" + h).substr(-2) + ":" + ("0" + m).substr(-2) + ":" + ("0" + s).substr(-2);
	}
	setInterval(this.circle_view_menu_time_f, 1000);

	this.circle_view_menu = {
 		"Recipes": { "tab": "Recipes", "imgs": [ "/img/symbol_recipe.svg" ], "target": "box_left_0" },
		"User Target": { "tab": "User Target", "imgs": [ "/img/symbol_bmi.svg", "/img/symbol_target.svg" ], "target": "box_left_1" },
		"User State": { "tab": "User State", "imgs": [ "/img/symbol_person_height.svg", "/img/symbol_person_weight.svg" ], "target": "box_left_2" },
		"Shopping List": { "tab": "Shopping List", "imgs": [ "/img/symbol_shopping_list.svg" ], "target": "box_right_0" },
		"Storage": { "tab": "Storage", "imgs": [ "/img/symbol_storage.svg" ], "target": "box_right_1" },
		"Products": { "tab": "Products", "imgs": [ "/img/symbol_products.svg" ], "target": "box_right_2" },
		"Consumption": { "tab": "Consumption", "imgs": [ "/img/symbol_person.svg" ], "target": "box_top", "className": "circle_menu_img_tb_corr" }
	};

	this.clear_circle_view = function () {
		this.circle_view["progressbar_top"].style.clipPath = "inset(0 100% 0 0)";
		this.circle_view["progressbar_bottom"].style.clipPath = "inset(0 100% 0 0)";
		this.circle_view["progressbar_target_line"].style.display = "none";

		var boxes = [ "top", "bottom", "center", "left_0", "left_1", "left_2", "right_0", "right_1", "right_2" ];

		for (var b in boxes) {
			this.circle_view["box_" + boxes[b]].innerHTML = "";
		}
	}

	this.set_circle_view = function() {
		for (var v in menu.circle_view_menu) {
			menu.circle_view[menu.circle_view_menu[v]["target"]].innerHTML = "";

			var link = document.createElement("a");
			link.tab = menu.circle_view_menu[v]["tab"];
			link.onclick = function() {
				menu.switch_tab(this.tab);
			}

			for (var img in menu.circle_view_menu[v]["imgs"]) {
				var img_elem = document.createElement("img");
				img_elem.src = menu.circle_view_menu[v]["imgs"][img];
				img_elem.className = "circle_menu_img";
				if (menu.circle_view_menu[v]["className"] != null) {
					img_elem.className += " " + menu.circle_view_menu[v]["className"];
				}
				link.appendChild(img_elem);
			}
			menu.circle_view[menu.circle_view_menu[v]["target"]].appendChild(link);
		}

		this.circle_view["box_center"].innerHTML = "";
		this.circle_view["box_center"].appendChild(menu.circle_view_menu_time);
	}

	this.circle_view_create = function() {
		this.circle_view["base"] 	= document.createElement("div");
		this.circle_view["base"].id 	= "circle_view";

		this.circle_view["progressbar_top_bg"] 			= document.createElement("div");
		this.circle_view["progressbar_top_bg"].className 	= "circle_view_progressbar_top circle_view_progressbar_bg";
		this.circle_view["base"].appendChild(this.circle_view["progressbar_top_bg"]);

		this.circle_view["progressbar_top"]			= document.createElement("div");
		this.circle_view["progressbar_top"].id			= "circle_view_progressbar_top";
		this.circle_view["progressbar_top"].className		= "circle_view_progressbar_top";
		this.circle_view["base"].appendChild(this.circle_view["progressbar_top"]);

		this.circle_view["progressbar_bottom_bg"]               = document.createElement("div");
                this.circle_view["progressbar_bottom_bg"].className     = "circle_view_progressbar_bottom circle_view_progressbar_bg";
                this.circle_view["base"].appendChild(this.circle_view["progressbar_bottom_bg"]);

		this.circle_view["progressbar_bottom"]                  = document.createElement("div");
                this.circle_view["progressbar_bottom"].id               = "circle_view_progressbar_bottom";
                this.circle_view["progressbar_bottom"].className        = "circle_view_progressbar_bottom";
                this.circle_view["base"].appendChild(this.circle_view["progressbar_bottom"]);

		this.circle_view["progressbar_target_line"]		   = document.createElement("div");
		this.circle_view["progressbar_target_line"].id		   = "circle_view_progressbar_target_line";
		this.circle_view["progressbar_target_line"].style.clipPath = "inset(0 25% 0 75%)";
		this.circle_view["base"].appendChild(this.circle_view["progressbar_target_line"]);

		this.circle_view["base_bg"]				= document.createElement("img");
		this.circle_view["base_bg"].id				= "circle_view_bg";
		this.circle_view["base_bg"].src				= "/img/consumption_circle.png";
		this.circle_view["base_bg"].onclick 			= function() {
			menu.switch_tab("none");
			menu.set_circle_view();
		};
		this.circle_view["base"].appendChild(this.circle_view["base_bg"]);

		this.circle_view["box_top"]				= document.createElement("span");
		this.circle_view["box_top"].id				= "circle_view_box_top";
		this.circle_view["base"].appendChild(this.circle_view["box_top"]);

		this.circle_view["box_bottom"]                          = document.createElement("span");
                this.circle_view["box_bottom"].id                       = "circle_view_box_bottom";
                this.circle_view["base"].appendChild(this.circle_view["box_bottom"]);

		this.circle_view["box_center"]				= document.createElement("div");
		this.circle_view["box_center"].id			= "circle_view_box_center";
		this.circle_view["base"].appendChild(this.circle_view["box_center"]);

		this.circle_view["box_right_0"]				= document.createElement("div");
		this.circle_view["box_right_0"].id			= "circle_view_box_right_0";
		this.circle_view["box_right_0"].className		= "circle_view_box_right";
		this.circle_view["base"].appendChild(this.circle_view["box_right_0"]);

                this.circle_view["box_right_1"]                         = document.createElement("div");
                this.circle_view["box_right_1"].id                      = "circle_view_box_right_1";
                this.circle_view["box_right_1"].className               = "circle_view_box_right";
                this.circle_view["base"].appendChild(this.circle_view["box_right_1"]);

                this.circle_view["box_right_2"]                         = document.createElement("div");
                this.circle_view["box_right_2"].id                      = "circle_view_box_right_2";
                this.circle_view["box_right_2"].className               = "circle_view_box_right";
                this.circle_view["base"].appendChild(this.circle_view["box_right_2"]);

                this.circle_view["box_left_0"]                         = document.createElement("div");
                this.circle_view["box_left_0"].id                      = "circle_view_box_left_0";
                this.circle_view["box_left_0"].className               = "circle_view_box_left";
                this.circle_view["base"].appendChild(this.circle_view["box_left_0"]);

		this.circle_view["box_left_1"]                         = document.createElement("div");
                this.circle_view["box_left_1"].id                      = "circle_view_box_left_1";
                this.circle_view["box_left_1"].className               = "circle_view_box_left";
                this.circle_view["base"].appendChild(this.circle_view["box_left_1"]);

		this.circle_view["box_left_2"]                         = document.createElement("div");
                this.circle_view["box_left_2"].id                      = "circle_view_box_left_2";
                this.circle_view["box_left_2"].className               = "circle_view_box_left";
                this.circle_view["base"].appendChild(this.circle_view["box_left_2"]);
	}



	this.widget = new Widget("Menu");

	this.elem = this.widget.elem;
	this.elem.style.display = "none";

	this.menu_elem = document.createElement("div");
	this.menu_elem.style.textAlign = "center";

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
		menu.clear_circle_view();
		for (var w in this.widgets_names) {
			var elem = document.getElementById("wg_" + this.widgets_names[w]);
			if (this.widgets_names[w] == wg) {
				this.active_widget = this.widgets_names[w];
				elem.style.display = "block";
				if (this.widgets_circle_view_setter[w] != null) {
					this.widgets_circle_view_setter[w]();
				}
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
				menu.circle_view_create();
				menu.set_circle_view();
				menu.menu_elem.appendChild(menu.circle_view["base"]);
			} else {
				this.elem.style.display = "none";
			}
		}
	}
}
