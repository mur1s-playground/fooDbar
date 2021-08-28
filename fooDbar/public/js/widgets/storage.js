var Storage = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.storage_data = null;
	this.storages = null;
	this.products_source = null;
	this.join_opts = null;

	this.storage_has_product = function(product_id) {
		if (this.storage_data == null) return false;
		for (var s in this.storage_data) {
			if (this.storage_data.hasOwnProperty(s)) {
				if (this.storage_data[s]["DatetimeEmpty"] != null) continue;
				if (this.storage_data[s]["ProductsId"] == product_id) {
					return true;
				}
			}
		}
		return false;
	}

	this.set_circle_view = function() {
		menu.circle_view["box_center"].innerHTML = "";
	}

	this.data_table = new DataTable(this, "storage",
                                {
					"StoragesId":  { "title": "Storage", "header": { "type": "text", "text": "", "text_class": "datatable_header" }, "join": { "model": "storages", "field": "Desc" } },
					"ProductsId": { "title": "Product", "header": { "type": "img", "img_src": "./img/symbol_products.svg", "img_class": "datatable_header" }, "join": { "model": "products", "field": "Name" } },
					"ProductsSourceId": { "title": "Source", "header": { "type": "img", "img_src": "./img/symbol_shopping_list.svg", "img_class": "datatable_header" }, "join": { "model": "products_source", "field": "Name" } },
					"Amount": { "title": "Amount", "header": { "type": "img", "img_src": "./img/symbol_weight.svg", "img_class": "datatable_header" } },
					"Price": { "title": "Price", "header": { "type": "img", "img_src": "./img/symbol_price.svg", "img_class": "datatable_header" } },
					"DatetimeInsert": { "title": "Insert", "header": { "type": "text", "text": "", "text_class": "datatable_header" } },
					"DatetimeOpen": { "title": "Open", "header": { "type": "text", "text": "", "text_class": "datatable_header" } },
//                                        "DatetimeEmpty": { "title": "Empty", "header": { "type": "text", "text": "Empty", "text_class": "datatable_header" } }
                                },
                                {
					"StoragesId": { "placeholder": "Storage", "join": "storages" },
                                        "ProductsId": { "placeholder": "Product", "join": "products" },
					"ProductsSourceId": { "placeholder": "Source", "join": "products_source"},
                                        "Amount": { "placeholder": "Amount" },
					"Price": { "placeholder": "Price" },
                                        "add_button": { "onclick":  function() {
										var p = {
							                                "storage_item" : this.obj.data_table.get_inserted_values()
						                                };
							                        this.obj.db.query_post("storage/index/insert", p, this.obj.on_storage_add_response);
									}
                                                       }
                                },
                                {
                                        "Delete": { "title": "Delete", "type": "text", "text": "&#128465;", "onclick":
									function() {
							                        var p = {
							                                "storage_item_id": this.obj["Id"]
						        	                };
						                	        var r = confirm("Delete storage item " + this.obj["Id"] + "?");
						                        	if (r == 1) {
						                                	storage.db.query_post("storage/index/remove", p, storage.on_remove_response);
							                        }
							                }
                                                },
					"Divide": { "title": "Divide evenly onto consumptions", "type": "text", "text": "&#xf7;", "onclick":
                                                                        function() {
                                                                                menu.circle_view["box_center"].innerHTML = "";

                                                                                var ne_container = document.createElement("div");
                                                                                ne_container.style.width = "196px";
                                                                                ne_container.style.height = "196px";
                                                                                var ne = new NumberEntry([196, 196], storage.divide_entry, this.obj["Id"], "> Target Amount");
                                                                                ne_container.appendChild(ne.elem);
                                                                                menu.circle_view["box_center"].appendChild(ne_container);
                                                                        }
                                                },
					"Multiply": { "title": "Multiply proportionally onto/from consumptions", "type": "text", "text": "&#xb7;", "onclick":
                                                                        function() {
										menu.circle_view["box_center"].innerHTML = "";

                                                                                var ne_container = document.createElement("div");
                                                                                ne_container.style.width = "196px";
                                                                                ne_container.style.height = "196px";
                                                                                var ne = new NumberEntry([196, 196], storage.multiply_entry, this.obj["Id"], "> Target Amount");
                                                                                ne_container.appendChild(ne.elem);
                                                                                menu.circle_view["box_center"].appendChild(ne_container);
                                                                        }
                                                }
                                }
                        );

	this.widget = new Widget("Storage");

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

	this.storage = document.createElement("table");
	this.storage.className = "storage";
	this.widget.content.appendChild(this.storage);

	this.divide_entry = function(state_nr, number) {
		var storage_elem = storage.storage_data[state_nr];
                var amount = parseFloat(storage_elem["Amount"]);
                var p = {
                        "storage_item_id": state_nr,
                        "storage_target_amount": number
                };
		if (number < amount) {
			var r = confirm("Divide storage item " + state_nr + " onto consumptions?");
                        if (r == 1) {
                                storage.db.query_post("storage/index/divide", p, storage.on_set_response);
                        }

		}
	}

	this.multiply_entry = function(state_nr, number) {
		var storage_elem = storage.storage_data[state_nr];
                var amount = parseFloat(storage_elem["Amount"]);
		var p = {
                	"storage_item_id": state_nr,
                        "storage_target_amount": number
                };
                if (number != amount) {
                        var r = confirm("Multiply storage item " + state_nr + " onto/from consumptions?");
                        if (r == 1) {
                                storage.db.query_post("storage/index/multiply", p, storage.on_set_response);
                        }
		}
	}

	this.on_set_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			storage.changed_f();
			storage.set_circle_view();
		}
	}

	this.on_remove_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var to_delete_element = document.getElementById(storage.widget.name + "_storage_" + resp["deleted_storage_item"]["Id"]);
			storage.storage.removeChild(to_delete_element);
			storage.changed_f();
		}
	}

	this.on_storage_add_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			storage.storage_data[resp["new_storage_item"]["Id"]] = resp["new_storage_item"];
			if (storage.storage.children.length > 2) {
				storage.storage.insertBefore(storage.data_table.get_data_row(resp["new_storage_item"], storage.join_opts ), storage.storage.children[2]);
			} else {
				storage.storage.appendChild(storage.data_table.get_data_row(resp["new_storage_item"], storage.join_opts ));
			}
			storage.changed_f();
			storage.changed = false;
		}
	}

        this.on_storage_response = function() {
                var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			storage.storage_data = resp["storage"];
			storage.products_source = resp["products_source"];
			storage.storages = resp["storages"];

			storage.join_opts = {"storages": storage.storages, "products": products.product_data, "products_source": storage.products_source };

			storage.storage.innerHTML = "";

			storage.storage.appendChild(storage.data_table.get_header_row());

			var idx = [];
			for (var storage_item in storage.storage_data) {
				if (storage.storage_data.hasOwnProperty(storage_item)) {
					idx.push(parseInt(storage_item));
				}
			}
			if (idx.length > 0) {
				idx.sort(function(a,b) {return b-a});
				for (var i = 0; i < idx.length; i++) {
					if (i == 0) {
                        	        	storage.storage.appendChild(storage.data_table.get_insert_row(storage.storage_data[idx[i]],  storage.join_opts ));
					}
					var row = storage.data_table.get_data_row(storage.storage_data[idx[i]], storage.join_opts );
					if (storage.storage_data[idx[i]]["Amount"] == 0) {
						row.style.display = "none";
					}

	                                storage.storage.appendChild(row);
				}
			} else {
				 storage.storage.appendChild(storage.data_table.get_insert_row(null, storage.join_opts ));
			}
			storage.changed_f();
			storage.changed = false;
		}
        }

	this.update = function() {
		if (this.changed) {
			this.changed = false;

			if (user.login_data != null) {
				if (products.product_data != null) {
					var p = {};
					storage.db.query_post("storage/index", p, storage.on_storage_response);
				}
			} else {
				storage.elem.style.display = "none";
				storage.storage.innerHTML = "";
				storage.storage_data = null;
			}
		}
	}
}
