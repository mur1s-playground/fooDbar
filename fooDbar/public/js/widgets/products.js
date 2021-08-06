var Products = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.product_data = null;
	this.join_opts = {};

	this.data_table = new DataTable(this, "products",
                                {       "Name": { "title": "Name", "header": { "type": "text", "text": "Name", "text_class": "datatable_header" } },
					"ProductsSourceId": { "title": "Products Source", "header": { "type": "text", "text": "Products source", "text_class": "datatable_header" }, "join": { "model": "products_source", "field": "Name" } },
					"Amount": { "title": "Amount", "header": { "type": "text", "text": "Amount", "text_class": "datatable_header" } },
					"AmountTypeId": { "title": "Unit", "header": { "type": "text", "text": "Unit", "text_class": "datatable_header" }, "join": { "model": "amount_type", "field": "Name" } },
					"Price": { "title": "Price", "header": { "type": "text", "text": "Price", "text_class": "datatable_header" } },
					"LastSeen": { "title": "Last Seen", "header": { "type": "text", "text": "Last seen", "text_class": "datatable_header" } },
					"Kj": { "title": "Kj", "header": { "type": "text", "text": "Kj", "text_class": "datatable_header" } },
					"NFat": { "title": "Fat", "header": { "type": "text", "text": "Fat", "text_class": "datatable_header" } },
					"NCarbs": { "title": "Carbs", "header": { "type": "text", "text": "Carbs", "text_class": "datatable_header" } },
                                        "NProtein": { "title": "Protein", "header": { "type": "text", "text": "Protein", "text_class": "datatable_header" } },
                                        "NFiber": { "title": "Fiber", "header": { "type": "text", "text": "Fiber", "text_class": "datatable_header" } },
                                        "NSalt": { "title": "Salt", "header": { "type": "text", "text": "Salt", "text_class": "datatable_header" } },
                                        "NCalcium": { "title": "Calcium", "header": { "type": "text", "text": "Calcium", "text_class": "datatable_header" } },
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
                                {
					"Name": { "placeholder": "Name" },
                                        "ProductsSourceId": { "placeholder": "Products Source", "join": "products_source" },
                                        "Amount": { "placeholder": "Amount" },
                                        "AmountTypeId": { "placeholder": "Unit", "join": "amount_type" },
                                        "Price": { "placeholder": "Price" },
                                        "Kj": { "placeholder": "Kj" },
                                        "NFat": { "placeholder": "Fat" },
                                        "NCarbs": { "placeholder": "Carbs" },
                                        "NProtein": { "placeholder": "Protein" },
                                        "NFiber": { "placeholder": "Fiber" },
                                        "NSalt": { "placeholder": "Salt" },
                                        "NCalcium": { "placeholder": "Calcium" },
                                        "ANotVegetarian": { "placeholder": "Not Vegetarian", "type": "checkbox" },
                                        "ANotVegan": { "placeholder": "Not Vegan", "type": "checkbox" },
                                        "AGluten": { "placeholder": "Gluten", "type": "checkbox" },
                                        "ACrustaceans": { "placeholder": "Crustaceans", "type": "checkbox" },
                                        "AEggs": { "placeholder": "Eggs", "type": "checkbox" },
                                        "AFish": { "placeholder": "Fish", "type": "checkbox" },
                                        "APeanuts": { "placeholder": "Peanuts", "type": "checkbox" },
                                        "ASoybeans": { "placeholder": "Soybeans", "type": "checkbox" },
                                        "AMilk": { "placeholder": "Milk", "type": "checkbox" },
                                        "ANuts": { "placeholder": "Nuts", "type": "checkbox" },
                                        "ACeleriac": { "placeholder": "Celeriac", "type": "checkbox" },
                                        "AMustard": { "placeholder": "Mustard", "type": "checkbox" },
                                        "ASesam": { "placeholder": "Sesam", "type": "checkbox" },
                                        "ASulfur": { "placeholder": "Sulfur", "type": "checkbox" },
                                        "ALupins": { "placeholder": "Lupins", "type": "checkbox" },
                                        "AMolluscs": { "placeholder": "Molluscs", "type": "checkbox" },
                                        "add_button": { "onclick":  function() {
										var p = {
							                                "product" : this.obj.data_table.get_inserted_values()
						                                };
							                        this.obj.db.query_post("products/index/insert", p, this.obj.on_product_add_response);
									}
                                                       }
                                },
                                {
                                        "Delete": { "title": "Delete", "type": "text", "text": "&#128465;", "onclick":
									function() {
							                        var p = {
							                                "product_id": this.obj["Id"]
						        	                };
						                	        var r = confirm("Delete product " + this.obj["Name"] + "?");
						                        	if (r == 1) {
						                                	products.db.query_post("products/index/remove", p, products.on_remove_response);
							                        }
							                }
                                                }
                                }
                        );

	this.widget = new Widget("Products");

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

	this.products = document.createElement("table");
	this.products.className = "products";
	this.widget.content.appendChild(this.products);

	this.on_remove_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var to_delete_element = document.getElementById(products.widget.name + "_products_" + resp["deleted_product"]["Id"]);
			products.products.removeChild(to_delete_element);
			products.changed_f();
		}
	}

	this.on_product_add_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			products.product_data[resp["new_product"]["Id"]] = resp["new_product"];
			if (products.products.children.length > 2) {
				products.products.insertBefore(products.data_table.get_data_row(resp["new_product"], products.join_opts), products.products.children[2]);
			} else {
				products.products.appendChild(products.data_table.get_data_row(resp["new_product"], products.join_opts));
			}
			products.changed_f();
			products.changed = false;
		}
	}

        this.on_products_response = function() {
                var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			products.product_data = resp["products"];
			products.join_opts["products_source"] = resp["products_source"];
			products.join_opts["amount_type"] = resp["amount_type"];
			products.products.innerHTML = "";

			products.products.appendChild(products.data_table.get_header_row());

			var idx = [];
			for (var product in products.product_data) {
				if (products.product_data.hasOwnProperty(product)) {
					idx.push(parseInt(product));
				}
			}
			if (idx.length > 0) {
				idx.sort(function(a,b) {return b-a});
				for (var i = 0; i < idx.length; i++) {
					if (i == 0) {
                        	        	products.products.appendChild(products.data_table.get_insert_row(products.product_data[idx[i]], products.join_opts));
					}
	                                products.products.appendChild(products.data_table.get_data_row(products.product_data[idx[i]], products.join_opts));
				}
			} else {
				 products.products.appendChild(products.data_table.get_insert_row(null, products.join_opts));
			}
			products.changed_f();
			products.changed = false;
		}
        }

	this.update = function() {
		if (this.changed) {
			this.changed = false;

			if (user.login_data != null) {
				products.elem.style.display = "block";
				var p = {};
				products.db.query_post("products/index", p, products.on_products_response);
			} else {
				products.elem.style.display = "none";
				products.products.innerHTML = "";
				products.product_data = null;
			}
		}
	}
}
