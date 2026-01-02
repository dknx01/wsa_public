import {Controller} from "@hotwired/stimulus";
import { TableSortController } from "stimulus-library";
import u from "umbrellajs";
import Tablefilter from "../lib/Tablefilter.js"
export default class extends TableSortController {

    connect() {
        super.connect();
        // document.getElementById('wahlkreis_form_year').value = new Date().getFullYear();
        // document.getElementById('wahlkreis_form_threshold').value = 200;
       u("#filter").on("input", (e) => Tablefilter.filterTable(e.target.value, "wahlkreistypeTable"));
    }
}
