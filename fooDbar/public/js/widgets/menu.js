var Menu = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.widgets = ["User State", "User Target", "Demand", "Consumption", "Storage", "Products"];
	this.widgets_buttons = {
		"User State": { "imgs": [ "/img/symbol_person_height.svg", "/img/symbol_person_weight.svg" ], "class": "menu_user_state"},
		"User Target": { "imgs":[ "/img/symbol_bmi.svg", "/img/symbol_target.svg" ], "class": "menu_user_target" },
		"Demand": { "imgs":[ "/img/symbol_demand.svg" ] , "class": "menu_demand" },
		"Consumption": { "imgs":[ "/img/symbol_person.svg" ], "class": "menu_consumption" },
		"Storage": { "imgs":[ "/img/symbol_storage.svg" ] , "class": "menu_storage" },
		"Products": { "imgs":[ "/img/symbol_products.svg" ], "class": "menu_products" }
	};

	this.widget = new Widget("Menu");

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

	this.switch_tab = function(wg) {
		for (var w in this.widgets) {
			var elem = document.getElementById("wg_" + this.widgets[w]);
			if (w == wg) {
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

			if (user.login_data != null) {		/* LOGGED IN */
				this.elem.style.display = "block";

				for (var wg in this.widgets) {
					var sp = document.createElement("button");
					sp.wg = wg;
					sp.title = this.widgets[wg];
					sp.className = this.widgets_buttons[this.widgets[wg]]["class"];

					for (var src in this.widgets_buttons[this.widgets[wg]]["imgs"]) {
						if (this.widgets_buttons[this.widgets[wg]]["imgs"].hasOwnProperty(src)) {
							var img = document.createElement("img");
							img.src = this.widgets_buttons[this.widgets[wg]]["imgs"][src];
							img.style.height = "30px";
							img.title = this.widgets[wg];
							sp.appendChild(img);
						}
					}

					sp.onclick = function() {
						menu.switch_tab(this.wg);
					};
					this.widget.content.appendChild(sp);

					var img = document.createElement("img");
                                        img.style.height = "20px";
                                        img.style.verticalAlign = "middle";
					if (wg < 3) {
						img.src = "/img/symbol_right_arrow.svg";
						this.widget.content.appendChild(img);
					} else if (wg < 5) {
						img.src = "/img/symbol_left_arrow.svg";
						this.widget.content.appendChild(img);
					}
				}
			} else {
				this.elem.style.display = "none";
			}
		}
	}
}
