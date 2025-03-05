import { Controller } from "@hotwired/stimulus"
import axios from "axios"
import u from "umbrellajs"

export default class extends Controller {
    static values = {
        url: String
    }
    connect() {
        super.connect();
        u(document.getElementsByClassName('loader')).addClass('d-none')
        u('#state').on("change", e => {
            this.selected(e.currentTarget.value)
        })
    }

    selected(state) {
        console.log(state)
        axios.get(
            `/admin/documents/wahlkreise/${encodeURI(state)}`
        )
            .then(response => this.changeDropdown(response.data))
            .catch(error => {
                console.log(error);
                alert('Es gab einen Fehler');
                u(document.getElementsByClassName('loader')).addClass('d-none');
            })
    }

    changeDropdown(data) {
        console.log(data)
        let area = u('#area').html('');
        for ( let value of data ) {
            area.append(value)
        }
        return true;
    }

    sendDataDirektkandidat() {
        let uu = document.getElementById('addNumber').value;
        if (uu === '') {
            uu = '0';
        }
        let uuUnapproved = document.getElementById('addNumberUnapproved').value;
        if (uuUnapproved === '') {
            uuUnapproved = '0';
        }
        let payload = {
            'state': document.getElementById('state').value,
            'area': document.getElementById('area').value,
            'uus': uu,
            'uusUnapproved': uuUnapproved,
            'type': 'Direktkandidaten',
            'csrftoken': document.getElementById('csrf_token').value,
        };
        u(document.getElementsByClassName('loader')).removeClass('d-none')
        axios.post(this.urlValue, payload)
            .then(function (response) {
                document.getElementById('addNumber').value = response.data['approved'];
                document.getElementById('addNumberUnapproved').value = response.data['unApproved'];
                u(document.getElementsByClassName('loader')).addClass('d-none');
            })
            .catch(function (error) {
                console.log(error);
                alert('Es gab einen Fehler');
                u(document.getElementsByClassName('loader')).addClass('d-none');
            });
    }

    sendDataLandesliste() {
        let uu = document.getElementById('addNumberLL').value;
        if (uu === '') {
            uu = '0';
        }
        let uuUnapproved = document.getElementById('addNumberUnapprovedLL').value;
        if (uuUnapproved === '') {
            uuUnapproved = '0';
        }
        let payload = {
            'state': document.getElementById('stateLL').value,
            'area': 'Landesliste',
            'uus': uu,
            'uusUnapproved': uuUnapproved,
            'type': 'Landesliste',
            'csrftoken': document.getElementById('csrf_token_ll').value,
        };
        u(document.getElementsByClassName('loader')).removeClass('d-none')
        axios.post(this.urlValue, payload)
            .then(function (response) {
                document.getElementById('addNumberLL').value = response.data['approved'];
                document.getElementById('addNumberUnapprovedLL').value = response.data['unApproved'];
                u(document.getElementsByClassName('loader')).addClass('d-none');
            })
            .catch(function (error) {
                console.log(error);
                alert('Es gab einen Fehler');
                u(document.getElementsByClassName('loader')).addClass('d-none');
            });
    }
}
