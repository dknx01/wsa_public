import {Controller} from "@hotwired/stimulus"
import u from "umbrellajs"
import StatisticOverview from "../lib/StatisticOverview.js";
import UUAddHandler from "../lib/UUAddHandler.js";

export default class extends Controller {
    static values = {
        url: String,
        edit: String,
        delete: String,
        wahlkreis: Boolean,
    };
    initialize() {
        super.initialize();
    }

    connect() {
        super.connect();
        this.uuAddHandler = new UUAddHandler(this.urlValue);
        u(document.getElementsByClassName('loader')).addClass('d-none');
        u('#statistic_type').on('change', e => {this.uuAddHandler.handleTypeChange(e.target.value);});
        u('#statistic_bundesland').on('change', e => {this.uuAddHandler.changedState(e.target.value);});
        u('#statistic_wahlkreis').on('change', e => {this.uuAddHandler.changedWahlkreis();});
        if (this.wahlkreisValue !== true) {
            let wahlkreis = document.getElementById('statistic_wahlkreis');
            if (wahlkreis) {
                wahlkreis.value = '';
                wahlkreis.setAttribute('disabled', '');
            }
        }
        new StatisticOverview(this.urlValue, this.deleteValue, this.editValue);
    }

    menu() {
        let menu = u('.uu-admin-menu');
        menu.toggleClass('menu-open');
    }
}
