import {Controller} from "@hotwired/stimulus";
import u from 'umbrellajs';
import axios from 'axios';

/* stimulusFetch: 'lazy' */
export default class extends Controller {

    reload({ params: {url } }) {
        axios.get(url)
            .then((response) => this.replaceImage(response.data))
        .catch((error) => {
            console.error(error);
        });
    }

    replaceImage(data) {
        u('#captcha').html(`<img src="${data.captcha}" alt="captcha">`);
        return true;
    }
}
