import { Controller } from "@hotwired/stimulus"
import axios from "axios"
import u from "umbrellajs"

export default class extends Controller {

    connect() {
        super.connect();
        u('#document_direktkandidaten_state').on("change", e => {
            this.selected(e.currentTarget.value)
        })
    }

    selected(state) {
        axios.get(
            `/admin/documents/wahlkreise/${encodeURI(state)}`
        )
            .then(response => this.changeDropdown(response.data))
        .catch(error => {
            this.error(error)
        })
    }

    error(error) {
        console.log(error);
        alert('Es gab einen Fehler');
        u(document.getElementsByClassName('loader')).addClass('d-none');
    }

    changeDropdown(data) {
        let area = u('#document_direktkandidaten_area').html('');
        for ( let value of data ) {
           area.append(value)
        }
        return true;
    }
}
