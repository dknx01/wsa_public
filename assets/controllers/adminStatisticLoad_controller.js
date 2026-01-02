import { Controller } from "@hotwired/stimulus"
import axios from "axios"
import u from "umbrellajs"

export default class extends Controller {
    connect() {
        super.connect();
        u("#switchLoadAll").on("click", e => this.loadAll(e))
    }

    loadAll(e) {
        if(e.target.checked) {
            axios.get("/admin/statistic/partial/all")
                .then(response => {
                    u("#content").html(response.data)
                })
        } else {
            axios.get("/admin/statistic/partial/all/active")
                .then(response => {
                    u("#content").html(response.data)
                })
        }
    }
}
