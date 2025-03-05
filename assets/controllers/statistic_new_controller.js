import {Controller} from "@hotwired/stimulus";
import axios from "axios";
import u from "umbrellajs";
import Notiflix from 'notiflix';

export default class extends Controller {
    static values = {
        url: String
    };

    menu() {
        let menu = u('.uu-admin-menu');
        menu.toggleClass('menu-open');
    }

    connect() {
        super.connect();
        u('.loader').addClass('d-none');
    }

    load({ params: { state } }) {
        let grid = u(document.getElementById('statistic-grid'));
        grid.html('');
        let url = this.urlValue.replace('---', state);
        u('.loader').removeClass('d-none');
        u('.mobileStates').removeClass('menu-open');

        axios.get(url)
            .then(response => this.display(state, response.data))
            .catch(error => this.error(error));
    }
    error(error) {
        Notiflix.Notify.error('Es gab einen Fehler beim Holen der Daten.', {timeout: 5000, position: 'center-center', cssAnimationStyle: 'zoom'});
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
        let html = `<div class="statistic-header">${state.toUpperCase()}</div>`;
        html += `<div class="statistic-needed">Benötigt</div>`;
        if (data.length === 0) {
            Notiflix.Notify.warning('Es konnten keine Daten gefunden werden', {timeout: 5000, position: 'center-center', cssAnimationStyle: 'zoom'});
            html +=`<div class="statistic-status"></div>
                    <div class="statistic-result"></div>
                    <div class="statistic-needed-number"></div>
                    `;
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
                    `;
            }
        }

        grid.html(html);
        u('.loader').addClass('d-none');

        return true;
    }
}
