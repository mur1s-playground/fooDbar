var Recipes = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.widget = new Widget("Recipes");

	this.recipe_consumption_group_data = null;

	this.elem = this.widget.elem;
	this.elem.style.display = "none";

	this.menu_elem = document.createElement("table");

	this.changed = true;

        this.data_table = new DataTable(this, "recipe_consumption_group",
                                {       "ProductsIds": { "title": "Products", "header": { "type": "text", "text": "Products", "text_class": "datatable_header" },  "join_list": { "model": "products", "field": "Name" } },
                                        "Amounts": { "title": "Amounts", "header": { "type": "text", "text": "Amounts", "text_class": "datatable_header" }, "assoc_list": { "id": 0 } },
                                        "MinPrice": { "title": "MinPrice", "header": { "type": "text", "text": "MinPrice", "text_class": "datatable_header" } },
                                        "MaxPrice": { "title": "MaxPrice", "header": { "type": "text", "text": "MaxPrice", "text_class": "datatable_header" } },
                                        "Datetime": { "title": "Datetime", "header": { "type": "text", "text": "Datetime", "text_class": "datatable_header" } },
                                        "Mj": { "title": "Kj", "header": { "type": "text", "text": "Mj", "text_class": "datatable_header" } },
                                        "NFatPercent": { "title": "Fat%", "header": { "type": "text", "text": "Fat%", "text_class": "datatable_header" } },
                                        "NCarbsPercent": { "title": "Carbs%", "header": { "type": "text", "text": "Carbs%", "text_class": "datatable_header" } },
                                        "NProteinPercent": { "title": "Protein%", "header": { "type": "text", "text": "Protein%", "text_class": "datatable_header" } },
                                        "NFiberPercent": { "title": "Fiber%", "header": { "type": "text", "text": "Fiber%", "text_class": "datatable_header" } },
                                        "NSaltPercent": { "title": "Salt%", "header": { "type": "text", "text": "Salt%", "text_class": "datatable_header" } },
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
                                },
				{ },
				{ }
				);

	this.recipe_consumption_group = document.createElement("table");
	this.recipe_consumption_group.className = "recipes_cg_table";

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

                        recipes.changed_f();
                        recipes.changed = false;
		}
	}

	this.update = function() {
		if (this.changed) {
			this.changed = false;

			var update_btn = document.createElement("button");
			update_btn.innerHTML = "U";
			update_btn.onclick = function() {
				 var p = { };
                                recipes.db.query_post("recipe/processconsumption/update", p, recipes.on_process_consumption_update_result);
			}

			if (user.login_data != null && products.product_data != null) {
				this.widget.content.appendChild(update_btn);
				this.widget.content.appendChild(this.recipe_consumption_group);
				var p = { };
                                recipes.db.query_post("recipe/processconsumption/get", p, recipes.on_process_consumption_result);
			} else {
				this.widget.content.innerHTML = "";
				this.elem.style.display = "none";
			}
		}
	}
}
