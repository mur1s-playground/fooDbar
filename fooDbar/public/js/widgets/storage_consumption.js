var StorageConsumption = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.consumption_data = null;

	this.data_table = new DataTable(this, "consumption",
                                {
					"StoragesId":  { "title": "Storage", "header": { "type": "text", "text": "Storage", "text_class": "datatable_header" }, "join": { "model": "storages", "field": "Desc" } },
					"ProductsId": { "title": "Product", "header": { "type": "text", "text": "Product", "text_class": "datatable_header" }, "join": { "model": "products", "field": "Name" } },
					"Amount": { "title": "Amount", "header": { "type": "text", "text": "Amount", "text_class": "datatable_header" } },
					"Datetime": { "title": "Consumption", "header": { "type": "text", "text": "Consumption", "text_class": "datatable_header" } },
					"User": { "title": "User", "header": { "type": "text", "text": "User", "text_class": "datatable_header" } }
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
									}
                                                       }
                                },
                                {
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
						storage.storage_data[storage_item]["StoragesId"] == storages_select.options[storages_select.selectedIndex].value) {
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
			} else {
				products_select.options[o].disabled = "disabled";
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
					if (storage_consumption.consumption.children.length > 2) {
		                                storage_consumption.consumption.insertBefore(storage_consumption.data_table.get_data_row(resp["new_consumption_item"][item], { "storages": storage.storages, "products": products.product_data } ), storage_consumption.consumption.children[2]);
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
					storage_consumption.elem.style.display = "block";
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
