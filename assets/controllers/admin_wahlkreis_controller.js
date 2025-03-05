import {Controller} from "@hotwired/stimulus";

export default class extends Controller {

    connect() {
        document.getElementById('wahlkreis_form_year').value = new Date().getFullYear();
        document.getElementById('wahlkreis_form_threshold').value = 200;
    }
}
