import axios from "axios"
import u from "umbrellajs"
import Notiflix from 'notiflix';

class Types {

    static get LL_BTW() {return 'LL_BTW';}
    static get DK_BTW() {return 'DK_BTW';}
    static get LL_KW() {return 'LL_KW';}
    static get DK_KW() {return 'DK_KW';}
    static get LL_LTW() {return 'LL_LTW';}
    static get DK_LTW() {return 'DK_LTW';}
}
export default class {

    constructor (urlWahlkreise) {
        this.urlWahlkreise = urlWahlkreise;
    }

    handleTypeChange (type) {
        let wahlkreis = document.getElementById('statistic_wahlkreis');
        let nameContent = '';
        switch (type) {
            case Types.LL_BTW:
            case Types.LL_LTW:
                nameContent ='Landesliste ' + this.getBundeslandValue();
                wahlkreis.value = '';
                wahlkreis.setAttribute('readonly', 'readonly');
                wahlkreis.setAttribute('disabled', 'disabled');
                break;
            case Types.LL_KW:
                nameContent ='Liste ' + this.getWahlkreisText();
                wahlkreis.removeAttribute('readonly');
                wahlkreis.removeAttribute('disabled');
                this.changedState(this.getBundeslandValue());
                break;
            case Types.DK_BTW:
            case Types.DK_KW:
            case Types.DK_LTW:
                nameContent ='Direktkandidat ' + this.getWahlkreisText();
                wahlkreis.removeAttribute('readonly');
                wahlkreis.removeAttribute('disabled');
                this.changedState(this.getBundeslandValue());
                break;
            default:
                return;
        }

        document.getElementById('statistic_name').value= nameContent;
    }

    changedState(state) {
        if (state === '') {
            return;
        }
        let type = this.getTypeValue();
        if (type === Types.LL_BTW) {
            this.handleTypeChange(type);
            return;
        }
        if (type === Types.LL_LTW) {
            this.handleTypeChange(type);
            return;
        }
        let url = this.urlWahlkreise;
        url = url.replace('%23', state, url);
        u(document.getElementsByClassName('loader')).removeClass('d-none');
        axios.get(url)
            .then(response => {this.addWahlkreise(response.data)})
            .catch(error => {this.error(error.message);});
    }

    getBundeslandValue() {
        return document.getElementById('statistic_bundesland').value;
    }

    getWahlkreisText() {
        let wahlkreis = document.getElementById('statistic_wahlkreis');
        if (wahlkreis.selectedIndex === -1) {
            return '';
        }
        return wahlkreis.options[wahlkreis.selectedIndex].text;
    }

    getTypeValue() {
        let typeEl = document.getElementById('statistic_type');
        return typeEl.options[typeEl.selectedIndex].value;
    }

    addWahlkreise(data) {
        let area = u('#statistic_wahlkreis').html('');
        for ( let value of data ) {
            area.append(value);
        }
        area.addClass('add_notify');
        u(document.getElementsByClassName('loader')).addClass('d-none');
        document.getElementById('statistic_wahlkreis').removeAttribute('disabled');
        return true;
    }

    changedWahlkreis() {
        let typeText = this.getTypeValue();
        let name = '';
        let wahlkreis = document.getElementById('statistic_wahlkreis');

        switch (typeText) {
            case Types.LL_BTW:
            case Types.LL_LTW:
                wahlkreis.value = '';
                wahlkreis.setAttribute('readonly', 'readonly');
                wahlkreis.setAttribute('disabled', 'disabled');
                name = 'Landesliste ' + this.getBundeslandValue();
                break
            case Types.LL_KW:
                name = 'Liste ' + this.getWahlkreisText();
                break
            default:
                name = 'Direktkandidat ' + this.getWahlkreisText();
                break;
        }

        document.getElementById('statistic_name').value = name;
    }
    error(error) {
        Notiflix.Notify.error('Es gab einen Fehler beim Holen der Daten.', {timeout: 5000, position: 'center-center', cssAnimationStyle: 'zoom'});
        u(document.getElementsByClassName('loader')).addClass('d-none');
    }
}
