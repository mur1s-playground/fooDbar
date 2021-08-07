var DataTable = function(parent, id_suffix, fields, insert_fields, row_options) {
	this.parent = parent;
	this.id_suffix = id_suffix;
	this.fields = fields;
	this.insert_fields = insert_fields;
	this.row_options = row_options;

	this.get_inserted_values = function() {
		var p_sub = {};
		for (var insert in this.insert_fields) {
			if (!this.insert_fields.hasOwnProperty(insert)) continue;
                      	if (insert === "add_button") continue;
                        if (this.insert_fields[insert].hasOwnProperty("join")) {
	                	var select_elem = document.getElementById(this.parent.widget.name + "_" + this.id_suffix + "_" + insert);
                                p_sub[insert] = select_elem.options[select_elem.selectedIndex].value;
                        } else {
				if (this.insert_fields[insert]["type"] != null) {
					if (this.insert_fields[insert]["type"] == "checkbox") {
						p_sub[insert] = document.getElementById(this.parent.widget.name + "_" + this.id_suffix + "_" + insert).checked;
					}
				} else {
	                                p_sub[insert] = document.getElementById(this.parent.widget.name + "_" + this.id_suffix + "_" + insert).value;
				}
                        }
		}
		return p_sub;
	}

	this.get_header_row = function() {
		var row_elem = document.createElement("tr");

		for (var field in this.fields) {
                        if (!this.fields.hasOwnProperty(field)) continue;
			var col = document.createElement("td");
			if (this.fields[field]["header"]["type"] == "img") {
				var img = document.createElement("img");
				img.src = this.fields[field]["header"]["img_src"];
				img.className = this.fields[field]["header"]["img_class"];
				img.title = this.fields[field]["title"];
				col.appendChild(img);
			} else {
				col.appendChild(document.createTextNode(this.fields[field]["header"]["text"]));
			}
			row_elem.appendChild(col);
		}

		var col = document.createElement("td");
		row_elem.appendChild(col);

		return row_elem;
	}

	this.get_insert_row = function(data, join_opts = null) {
		var row_elem = document.createElement("tr");

		for (var field in this.fields) {
			if (!this.fields.hasOwnProperty(field)) continue;
			var col = document.createElement("td");

			if (this.insert_fields[field] != null) {
				if (this.fields[field]["join"] == null) {
					var input = document.createElement("input");
					if (this.insert_fields[field]["type"] != null) {
						input.type = this.insert_fields[field]["type"];
					}
					input.id = parent.widget.name + "_" + this.id_suffix + "_" + field;
					if (data != null) {
						input.value = data[field];
					}
					input.placeholder = this.insert_fields[field]["placeholder"];
	                                if (this.insert_fields[field]["oninput"]) {
        	                                input.oninput = this.insert_fields[field]["oninput"];
                	                }
                        	        col.appendChild(input);
				} else {
					var select = document.createElement("select");
					select.id = parent.widget.name + "_" + this.id_suffix + "_" + field;
					if (this.insert_fields[field]["onchange"]) {
                                                select.onchange = this.insert_fields[field]["onchange"];
                                        }


					for (var opt in join_opts[this.fields[field]["join"]["model"]]) {
						var option = document.createElement("option");
						option.value = join_opts[this.fields[field]["join"]["model"]][opt]["Id"];
						option.appendChild(document.createTextNode(join_opts[this.fields[field]["join"]["model"]][opt][this.fields[field]["join"]["field"]]));
						select.appendChild(option);
					}

					if (data != null) {
						for (var o = 0; o < select.options.length; o++) {
							if (select.options[o].value == data[field]) {
								select.selectedIndex = o;
								break;
							}
						}
					}

					col.appendChild(select);
				}
				if (this.insert_fields[field]["button"]) {
					var button = document.createElement("button");
					button.id = parent.widget.name + "_" + this.id_suffix + "_" + field + "_button";
					button.title = this.insert_fields[field]["button"]["title"];
					if (this.insert_fields[field]["button"]["type"] == "text") {
						button.appendChild(document.createTextNode(this.insert_fields[field]["button"]["text"]));
					}
					button.onclick = this.insert_fields[field]["button"]["onclick"];
					col.appendChild(button);
				}
			} else {
				var span = document.createElement("span");
				span.id = parent.widget.name + "_" + this.id_suffix + "_" + field;
				col.appendChild(span);
			}
			row_elem.appendChild(col);
		}

		var col = document.createElement("td");
		var add_button = document.createElement("button");
		add_button.id = parent.widget.name + "_" + this.id_suffix + "_add_button";
       	        add_button.obj = this.parent;
               	add_button.innerHTML = "&#xFF0B;";
                add_button.onclick = this.insert_fields["add_button"]["onclick"];
		col.appendChild(add_button);

		var field = "add_button";
		if (this.insert_fields[field]["button"]) {
                	var button = document.createElement("button");
                        button.id = parent.widget.name + "_" + this.id_suffix + "_" + field + "_button";
                        button.title = this.insert_fields[field]["button"]["title"];
                        if (this.insert_fields[field]["button"]["type"] == "text") {
                        	button.innerHTML = this.insert_fields[field]["button"]["text"];
                        }
                        button.onclick = this.insert_fields[field]["button"]["onclick"];
                        col.appendChild(button);
                }

		row_elem.appendChild(col);

		return row_elem;
	}

	this.get_data_row = function(data, join_opts = null) {
                var row_elem = document.createElement("tr");
		row_elem.id = parent.widget.name + "_" + this.id_suffix + "_" + data["Id"];

		for (var field in this.fields) {
			if (!this.fields.hasOwnProperty(field)) continue;
			var col = document.createElement("td");
			col.id = parent.widget.name + "_" + this.id_suffix + "_" + data["Id"] + "_" + field;

			var data_field = null;
			if (this.fields[field]["join"] == null) {
				data_field = data[field];
			} else {
				data_field = join_opts[this.fields[field]["join"]["model"]][data[field]][this.fields[field]["join"]["field"]];
			}
			if (data_field != null) {
				col.appendChild(document.createTextNode(data_field));
			} else {
				col.appendChild(document.createTextNode("n/A"));
			}
			row_elem.appendChild(col);
		}

		var col = document.createElement("td");
		for (var option in this.row_options) {
			if (!this.row_options.hasOwnProperty(option)) continue;
			var button = document.createElement("button");
			button.obj = data;
			if (this.row_options[option]["type"] == "text") {
				button.innerHTML = this.row_options[option].innerHTML = this.row_options[option]["text"];
			}
			button.onclick = this.row_options[option]["onclick"];
			col.appendChild(button);
		}
		row_elem.appendChild(col);

		return row_elem;
	}
}
