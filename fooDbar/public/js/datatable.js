var DataTable = function(parent, id_suffix, fields, insert_fields, row_options) {
	this.parent = parent;
	this.id_suffix = id_suffix;
	this.fields = fields;
	this.insert_fields = insert_fields;
	this.row_options = row_options;

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
			}
			row_elem.appendChild(col);
		}

		var col = document.createElement("td");
		row_elem.appendChild(col);

		return row_elem;
	}

	this.get_insert_row = function(data) {
		var row_elem = document.createElement("tr");

		for (var field in this.fields) {
			if (!this.fields.hasOwnProperty(field)) continue;
			var col = document.createElement("td");

			if (this.insert_fields[field] != null) {
				var input = document.createElement("input");
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
				var span = document.createElement("span");
				span.id = parent.widget.name + "_" + this.id_suffix + "_" + field;
				col.appendChild(span);
			}
			row_elem.appendChild(col);
		}

		var col = document.createElement("td");
		var add_button = document.createElement("button");
       	        add_button.obj = this.parent;
               	add_button.innerHTML = "&#xFF0B;";
                add_button.onclick = this.insert_fields["add_button"]["onclick"];
		col.appendChild(add_button);
		row_elem.appendChild(col);

		return row_elem;
	}

	this.get_data_row = function(data) {
                var row_elem = document.createElement("tr");
		row_elem.id = parent.widget.name + "_" + this.id_suffix + "_" + data["Id"];

		for (var field in this.fields) {
			if (!this.fields.hasOwnProperty(field)) continue;
			var col = document.createElement("td");
			col.id = parent.widget.name + "_" + this.id_suffix + "_" + data["Id"] + "_" + field;
			if (data[field] != null) {
				col.appendChild(document.createTextNode(data[field]));
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
