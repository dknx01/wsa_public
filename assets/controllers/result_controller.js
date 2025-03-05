import { Controller } from "@hotwired/stimulus"
import u from "umbrellajs"

export default class extends Controller {
    connect() {
        super.connect();
        u('.result-details').addClass('d-none');
        u('.result-state').on('click', e => {this.show(e)});
    }

    show(e) {
        let target = u(u(e.target).closest('a')).data('target');
        u(`#${target}`).toggleClass('d-none');
    }
}
