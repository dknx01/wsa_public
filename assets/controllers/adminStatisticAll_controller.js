import { Controller } from "@hotwired/stimulus"
import { TableSortController } from "stimulus-library";
import axios from "axios"
import u from "umbrellajs"

export default class extends TableSortController {
    connect() {
        super.connect();
    }
}
