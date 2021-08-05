var Demand = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.widget = new Widget("Demand");

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

	this.demand = document.createElement("div");
	this.demand.id = this.widget.name + "_demand";
	this.widget.content.appendChild(this.demand);

        this.on_demand_response = function() {
                var resp = JSON.parse(this.responseText);
		demand.demand.innerHTML = this.responseText;
        }

	this.update = function() {
		if (this.changed) {
			this.changed = false;

			if (user.login_data != null) {
				demand.elem.style.display = "block";
				var p = {};
				demand.db.query_post("demand/index", p, demand.on_demand_response);
			} else {
				demand.elem.style.display = "none";
				this.demand.innerHTML = "";
			}
		}
	}
}
