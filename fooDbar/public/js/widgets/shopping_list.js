var ShoppingList = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.widget = new Widget("Shopping List");

	this.elem = this.widget.elem;
	this.elem.style.display = "none";

	this.data_table = new DataTable(this, "recipe_consumption_group",
                                {
					"OrderId": {  "title": "OrderId", "header": { "type": "text", "text": "", "text_class": "datatable_header" } },
					"Amount": { "title": "Amount", "header": { "type": "img", "img_src": "./img/symbol_weight.svg", "img_class": "datatable_header" } },
                                        "ProductsId": { "title": "Products", "header": { "type": "img", "img_src": "./img/symbol_shopping_list.svg", "img_class": "datatable_header" }, "join": { "model": "products", "field": "Name" } },
					"ProductAmount": { "title": "ProductAmount",  "header": { "type": "img", "img_src": "./img/symbol_weight.svg", "img_class": "datatable_header" } },
					"ProductsSourceIds": { "title": "ProductsSource", "header": { "type": "img", "img_src": "./img/symbol_products.svg", "img_class": "datatable_header" }, "join_list": { "model": "products_source", "field": "Name" } },
                                        "Prices": { "title": "Price", "header": { "type": "text", "text": "", "text_class": "datatable_header" }, "assoc_list": { "id": 4 } }
                                },
                                { },
                                { }
                                );

	this.shopping_list_data = null;
	this.products_source_data = null;

	this.shopping_list = document.createElement("table");
	this.shopping_list.className = "shopping_list";

	this.on_product_demand_insert_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			recipes.recipe_consumption_group_rr_demand_data = {};
			recipes.update_demand_table();
			shopping_list.changed_f();
		}
	}

	this.shopping_list_append = function(data) {
		var p = {
			"products_demand": data
		};

		shopping_list.db.query_post("shopping/index/insert", p, shopping_list.on_product_demand_insert_response);
	}

	this.on_order_all_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			for (var ids in resp["order_shopping_list_ids"]) {
				document.getElementById(shopping_list.widget.name + "_recipe_consumption_group_" + resp["order_shopping_list_ids"][ids] + "_OrderId").innerHTML = resp["OrderId"];
			}
		}
	}

	this.on_shopping_list_item_remove_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var row = document.getElementById(shopping_list.widget.name + "_recipe_consumption_group_" + resp["deleted_item_id"]);
			row.parentNode.removeChild(row);
			shopping_list.changed_f();
			shopping_list.changed = false;
		}
	}

	this.on_storage_add_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			var row = document.getElementById(shopping_list.widget.name + "_recipe_consumption_group_" + resp["removed_shopping_list_item"]["deleted_item_id"]);
			row.parentNode.removeChild(row);
			storage.changed_f();
		}
	}

	this.append_packet_count_and_total_price = function(row, sli) {
		var amount = Math.round(parseFloat(row.children[1].innerHTML) * 100) / 100 ;
		var products_amount = parseFloat(row.children[3].innerHTML);

		var packet_count = parseInt(Math.ceil(amount/products_amount));

		var prices = shopping_list.shopping_list_data[sli]["Prices"].split(";");

		var packet_count_td = document.createElement("td");
		packet_count_td.innerHTML = packet_count;
                row.appendChild(packet_count_td);

		var one_price = null;

                var price_total_td = document.createElement("td");
		for (var price in prices) {
			var price_div = document.createElement("div");
			price_div.innerHTML = Math.round(packet_count * prices[price] * 100) / 100;
			if (one_price == null) one_price = prices[price];
			price_total_td.appendChild(price_div);
		}
		row.appendChild(price_total_td);


		var to_storage_form = document.createElement("td");
		var to_storage_table = document.createElement("table");
		var i_row = storage.data_table.get_insert_row(null,  storage.join_opts );


		i_row.children[1].removeChild(i_row.children[1].children[0]);
		i_row.children[1].appendChild(document.createElement("input"));

		i_row.children[0].children[0].id = shopping_list.widget.name + "_to_storage_StoragesId_" + sli;
		i_row.children[1].children[0].id = shopping_list.widget.name + "_to_storage_ProductsId_" + sli;
		i_row.children[2].children[0].id = shopping_list.widget.name + "_to_storage_ProductsSourceId_" + sli;
		i_row.children[3].children[0].id = shopping_list.widget.name + "_to_storage_Amount_" + sli;
		i_row.children[4].children[0].id = shopping_list.widget.name + "_to_storage_Price_" + sli;
		i_row.children[5].children[0].id = shopping_list.widget.name + "_to_storage_dti_" + sli;
		i_row.children[6].children[0].id = shopping_list.widget.name + "_to_storage_dto_" + sli;
		i_row.children[7].children[0].id = shopping_list.widget.name + "_to_storage_add_btn_" + sli;

		i_row.children[1].children[0].value = shopping_list.shopping_list_data[sli]["ProductsId"];
		i_row.children[1].children[0].disabled = true;
		i_row.children[1].children[0].style.display = "none";

		i_row.children[2].children[0].sli = sli;
		i_row.children[2].children[0].prices = prices;
		i_row.children[2].children[0].onchange = function() {
			var ps_arr = shopping_list.shopping_list_data[this.sli]["ProductsSourceIds"].split(";");
			var prices_arr = shopping_list.shopping_list_data[this.sli]["Prices"].split(";");

			var found = false;
			for (var ps in ps_arr) {
				if (ps_arr[ps] == this.options[this.selectedIndex].value) {
					document.getElementById(shopping_list.widget.name + "_to_storage_Price_" + sli).value = prices_arr[ps];
					found = true;
					break;
				}
			}
			if (!found) {
				document.getElementById(shopping_list.widget.name + "_to_storage_Price_" + sli).value = "";
			}
		}

		i_row.children[3].children[0].value = packet_count * products_amount;

		i_row.children[4].children[0].value = one_price;

		i_row.children[7].children[0].sli = sli;
		i_row.children[7].children[0].onclick = function() {
			var storage_select = document.getElementById(shopping_list.widget.name + "_to_storage_StoragesId_" + this.sli);
			var products_input = document.getElementById(shopping_list.widget.name + "_to_storage_ProductsId_" + this.sli);
			var source_select = document.getElementById(shopping_list.widget.name + "_to_storage_ProductsSourceId_" + this.sli);

			var p = {
                        	"storage_item" : {
							"StoragesId": storage_select.options[storage_select.selectedIndex].value,
							"ProductsId": products_input.value,
							"ProductsSourceId": source_select.options[source_select.selectedIndex].value,
							"Amount": document.getElementById(shopping_list.widget.name + "_to_storage_Amount_" + this.sli).value,
							"Price": document.getElementById(shopping_list.widget.name + "_to_storage_Price_" + this.sli).value,
							"ShoppingListId": this.sli
				}
                        };
                        storage.db.query_post("storage/index/insert", p, shopping_list.on_storage_add_response);
		}

		var delete_td = document.createElement("td");
		var delete_btn = document.createElement("button");
		delete_btn.sli = sli;
		delete_btn.innerHTML = "&#128465;";
		delete_btn.title = "Delete";
		delete_btn.onclick = function() {
			var p = {
				"shopping_list_item_id": this.sli
			}
			shopping_list.db.query_post("shopping/index/remove", p, shopping_list.on_shopping_list_item_remove_response);
		}
		delete_td.appendChild(delete_btn);
		i_row.appendChild(delete_td);

		to_storage_table.appendChild(i_row);
		to_storage_form.appendChild(to_storage_table);

                row.appendChild(to_storage_form);
	}

	this.on_get_response = function() {
		var resp = JSON.parse(this.responseText);
		if (resp["status"] == true) {
			shopping_list.shopping_list_data = resp["shopping_list"];
			shopping_list.products_source_data = resp["products_source"];
                        shopping_list.shopping_list.innerHTML = "";

			var header = shopping_list.data_table.get_header_row();
			var packet_count_td = document.createElement("td");
			header.appendChild(packet_count_td);

			var price_total_td = document.createElement("td");
			var order_all_btn = document.createElement("button");
                        order_all_btn.innerHTML = "&#x2709;";
                        order_all_btn.title = "Order all";
                        order_all_btn.onclick = function() {
                                var p = {};
                                shopping_list.db.query_post("shopping/order/all", p, shopping_list.on_order_all_response);
                        }
                        price_total_td.appendChild(order_all_btn);
			header.appendChild(price_total_td);

			var controls = document.createElement("td");
			header.appendChild(controls);

                        shopping_list.shopping_list.appendChild(header);

                        for (var sli in shopping_list.shopping_list_data) {
                                if (shopping_list.shopping_list_data.hasOwnProperty(sli)) {
					var row = shopping_list.data_table.get_data_row(shopping_list.shopping_list_data[sli], { "products": products.product_data, "products_source": shopping_list.products_source_data } );
					row.children[1].innerHTML = Math.round(row.children[1].innerHTML * 100) / 100;
					shopping_list.append_packet_count_and_total_price(row, sli);
					shopping_list.shopping_list.appendChild(row);
                                }
                        }

			shopping_list.changed_f();
			shopping_list.changed = false;
		}

	}

	this.changed = true;

	this.changed_f = function() {
		this.changed = true;
		if (this.change_dependencies != null) {
			for (var i = 0; i < this.change_dependencies.length; i++) {
				this.change_dependencies[i].changed_f();
			}
		}
	}


	this.update = function() {
		if (this.changed) {
			this.changed = false;

			this.widget.content.innerHTML = "";
			if (user.login_data != null && products.product_data != null) {
				var p = { };
				shopping_list.db.query_post("shopping/index/get", p, shopping_list.on_get_response);
				this.widget.content.appendChild(shopping_list.shopping_list);
			} else {
				this.elem.style.display = "none";
			}
		}
	}
}
