import { Controller } from "@hotwired/stimulus"
import axios from "axios"
import u from "umbrellajs"

export default class extends Controller {
    static values = {
        url: String,
        search: String,
    }
    connect() {
        super.connect();
        u('.loader').addClass('d-none');
    }

    load({ params: { state } }) {
        let url = this.urlValue.replace('---', state);
        u('.loader').removeClass('d-none');
        u('.mobileStates').removeClass('menu-open');
        axios.get(url)
            .then(response => this.display(state, response.data))
            .catch(error => this.error(error));
    }

    error(error) {
        console.log(error);
        alert('Es gab einen Fehler');
        u(document.getElementsByClassName('loader')).addClass('d-none');
    }

    /**
     *
     * @param {string} state
     * @param {array} data
     * @returns {boolean}
     */
    display(state, data) {
        let grid = u(document.getElementById('documents'));
        let html = '<div>';
        if (data.length === 0) {
            html +=`Keine Daten vorhanden`;
        } else  {
            html += `<div style="font-weight: bold; text-decoration: underline">${data[0].state}</div>`;
            for (let i = 0; i < data.length; i++) {
                html +=`<div class="download"><img src="${data[i].image}" style="max-height: 50px; width: auto; margin-right: 5px"><a class="wsa-link" href="${data[i].url}" target="_blank">${data[i].name}</a>`;
                if (data[i].description.length > 0) {
                    html += `<div style="margin-left: 2.5vw">${data[i].description}</div>`;
                }
                html += '</div>';
            }
        }
        html += '</div>';

        grid.html(html);
        u('.loader').addClass('d-none');

        return true;
    }

    search(event) {
        let searchText = event.currentTarget.value;
        if (searchText.length < 3) {
            searchText = '-';
        }
        let url = this.searchValue.replace('---', encodeURI(searchText.trim()));
        axios.get(url)
            .then(response => this.display('', response.data))
            .catch(error => this.error(error));
    }
    menu() {
        let menu = u('.mobileStates');
        menu.toggleClass('menu-open');
    }
}
