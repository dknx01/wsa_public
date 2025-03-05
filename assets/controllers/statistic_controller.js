import { Controller } from "@hotwired/stimulus"
import axios from "axios"
import u from "umbrellajs"

export default class extends Controller {
    static values = {
        url: String
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
        let grid = u(document.getElementById('statistic-grid'));
        let html = `<div class="statistic-header">${state.toUpperCase()}</div>`
        html += `<div class="statistic-needed">Benötigt</div>`
        if (data.length === 0) {
            html +=`<div class="statistic-status"></div>
                    <div class="statistic-result">Keine Daten vorhanden</div>
                    <div class="statistic-needed-number"></div>
                    `
        } else  {
            for (let i = 0; i < data.length; i++) {
                html +=`<div class="statistic-status" id="statistic_status_${i}" data-id="${i}" data-area="${data[i].name}">${data[i].status}</div><div class="statistic-name" id="statistic_name_${i}" data-id="${i}" data-area="${ data[i].name }">${ data[i].name }<span class="badge text-bg-secondary">${data[i].type}</span></div>
                    <div class="statistic-result download" id="statistic_result_${i}" data-area="${ data[i].name }">
                        ${data[i].approved} bestätigte <div class="progress" role="progressbar" aria-label="${ data[i].name }" aria-valuenow="${ data[i].approvedPercentage }" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar overflow-visible" style="width: ${ data[i].approvedPercentage }%; background: linear-gradient(90deg in oklab, ${ data[i].colors })"> ${ data[i].approved }</div>
                        </div>
                        ${data[i].total} inkl. Unbestätigte (${data[i].unapproved})<div class="progress" role="progressbar" aria-label="${ data[i].name }" aria-valuenow="${ data[i].unapprovedPercentage }" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar overflow-visible" style="width: ${ data[i].unapprovedPercentage }%; background: linear-gradient(90deg in oklab, ${ data[i].unapprovedColors })"> ${ data[i].total }</div>
                        </div>
                    </div>
                    <div class="statistic-needed-number" id="statistic_needed_${i}" data-area="${ data[i].name }">${data[i].max}</div>
                    `
            }
        }

        grid.html(html);
        u('.loader').addClass('d-none');

        return true;
    }

    search(event) {
        let searchText = event.currentTarget.value;
        if (searchText.length < 3) {
            let classes = ['statistic-name', 'statistic-status', 'statistic-result', 'statistic-needed-number'];

            for (let i = 0; i < classes.length; i++) {
                let els = [...document.getElementsByClassName(classes[i])];
                for (let j = 0; j < els.length; j++) {
                    let sel = '#' + els[j].id;
                    if (sel === '#') {
                        continue;
                    }
                    u(sel).removeClass('d-none');
                }
            }
            return;
        }
        let ids = ['statistic_name_', 'statistic_status_', 'statistic_result_', 'statistic_needed_'];

            let elements = document.getElementsByClassName('statistic-name');

            if (elements.length < 1) {
                return;
            }
            for (let i = 0; i < elements.length; i++) {
                let area = elements[i].getAttribute('data-area')
                let regEx = RegExp(`^.*${searchText.toLowerCase()}.*$`)
                if (!area.toLowerCase().match(regEx)) {
                    let id = elements[i].getAttribute('data-id');
                    for (let j = 0; j < ids.length; j++) {
                        let identifier = `#${ids[j]}${id}`
                        u(identifier).addClass('d-none');
                    }
                }
            }
    }

    menu() {
        let menu = u('.mobileStates');
        menu.toggleClass('menu-open');
    }
}
