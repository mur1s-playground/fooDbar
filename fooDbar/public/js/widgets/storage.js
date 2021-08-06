var Storage = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.storage_data = null;
	this.storages = null;

	this.data_table = new DataTable(this, "storage",
                                {
					"StoragesId":  { "title": "Storage", "header": { "type": "text", "text": "Storage", "text_class": "datatable_header" }, "join": { "model": "storages", "field": "Desc" } },
					"ProductsId": { "title": "Product", "header": { "type": "text", "text": "Product", "text_class": "datatable_header" }, "join": { "model": "products", "field": "Name" } },
					"Amount": { "title": "Amount", "header": { "type": "text", "text": "Amount", "text_class": "datatable_header" } },
					"DatetimeInsert": { "title": "Insert", "header": { "type": "text", "text": "Insert", "text_class": "datatable_header" } },
					"DatetimeOpen": { "title": "Open", "header": { "type": "text", "text": "Open", "text_class": "datatable_header" } },
                                        "DatetimeEmpty": { "title": "Empty", "header": { "type": "text", "text": "Empty", "text_class": "datatable_header" } }
                                },
                                {
					"StoragesId": { "placeholder": "Storage", "join": "storages" },
                                        "ProductsId": { "placeholder": "Product", "join": "products" },
                                        "Amount": { "placeholder": "Amount" },
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
				storage.storage.insertBefore(storage.data_table.get_data_row(resp["new_storage_item"], { "storages": storage.storages, "products": products.product_data } ), storage.storage.children[2]);
			} else {
				storage.storage.appendChild(storage.data_table.get_data_row(resp["new_storage_item"], { "storages": storage.storages, "products": products.product_data } ));
			}
			storage.changed_f();
			storage.changed = false;
		}
	}

        this.on_storage_response = function() {
                var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			storage.storage_data = resp["storage"];
			storage.storages = resp["storages"];
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
                        	        	storage.storage.appendChild(storage.data_table.get_insert_row(storage.storage_data[idx[i]],  { "storages": storage.storages, "products": products.product_data } ));
					}
	                                storage.storage.appendChild(storage.data_table.get_data_row(storage.storage_data[idx[i]], { "storages": storage.storages, "products": products.product_data } ));
				}
			} else {
				 storage.storage.appendChild(storage.data_table.get_insert_row(null, { "storages": storage.storages, "products": products.product_data } ));
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
					storage.elem.style.display = "block";
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
