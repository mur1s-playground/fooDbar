var Demand = function(db, change_dependencies) {
	this.db = db;
	this.change_dependencies = change_dependencies;

	this.widget = new Widget("Demand");

	this.elem = this.widget.elem;
	this.elem.style.display = "none";

	this.demand_data = null;

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
		if (resp["status"] == true) {
			demand.demand_data = {};
			demand.demand_data["MJPerDay"] = resp["MJPerDay"];

			demand.demand.innerHTML = "";

			demand.demand.appendChild(document.createTextNode("MJ/Day to maintain state "));

			var sp = document.createElement("span");
			sp.innerHTML = demand.demand_data["MJPerDay"]["maintain"];
			sp.className = "menu_user_state";
			demand.demand.appendChild(sp);

			demand.demand.appendChild(document.createElement("br"));

			demand.demand.appendChild(document.createTextNode("MJ/Day to reach target "));

			var sp2 = document.createElement("span");
                        sp2.innerHTML = demand.demand_data["MJPerDay"]["target"];
			sp2.className = "menu_user_target";
                        demand.demand.appendChild(sp2);
		}
        }

	this.update = function() {
		if (this.changed) {
			this.changed = false;

			if (user.login_data != null) {
				var p = {};
				demand.db.query_post("demand/index", p, demand.on_demand_response);
			} else {
				demand.elem.style.display = "none";
				this.demand.innerHTML = "";
			}
		}
	}
}
