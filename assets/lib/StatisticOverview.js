import u from "umbrellajs";
import axios from "axios";

class Liste {
    approved = 0;
    unapproved = 0;
    name = '';
    constructor(name, approved, unapproved) {
        this.name = name;
        this.approved = approved;
        this.unapproved = unapproved;
    }

    add(approved, unapproved) {
        this.approved += approved;
        this.unapproved += unapproved;
    }
}
export default class {
    constructor(url, deleteUrl, editUrl) {
        this.url = url;
        this.deletUrl = deleteUrl;
        this.editUrl = editUrl;
        u('ul.uu-admin-menu li span').on('click', e => {this.menuItemSelected(e);});
        u('.admin_new_statistic_menu span').on('click', e => {this.showResults(e);});
        u('.admin_new_statistic_menu div span').addClass('d-none');
    }

    menuItemSelected(event) {
        event.preventDefault();
        event.stopPropagation();
        u(event.target).closest('ul').children('.selected').removeClass('selected');
        u(event.target).parent().addClass('selected');
        //@todo load data
        let state = event.target.dataset.state || '';
        let url = this.url;
        url = url.replace('%23', state, url);
        axios.get(url)
        .then(response => {
            this.createTable(response.data);
        })
        .catch(error => {console.log(error);});
        return true;
    }

    createTable(data) {
        if (data.length === 0) {
            u('#details').html('Nix gefunden');
            u('#total').html('Nix gefunden');
        }
        let listenList = {};
        let wksList = {};

        let table =
            '<div class="table-responsive"><table class="table table-sm table-striped"><thead><tr>\n' +
            '      <th scope="col"></th>\n' +
            '      <th scope="col">Name</th>\n' +
            '      <th scope="col">Bestätigt</th>\n' +
            '      <th scope="col">Unbestätigt</th>\n' +
            '      <th scope="col">Bundesland</th>\n' +
            '      <th scope="col">Typ</th>\n' +
            '      <th scope="col">Erstellt am (geändert am)</th>\n' +
            '      <th scope="col">Erstellt von</th>\n' +
            '      <th scope="col">Kommentar</th>\n' +
            '    </tr>\n' +
            '  </thead>\n' +
            '  <tbody>';
        for (let i = 0; i < data.length; i++) {
            this.handleEntry(data[i], listenList, wksList);
            table += `<tr>`+
                this.actions(data[i].id) +
                `<td data-id="${data[i].id}">${data[i].name}</td>` +
                `<td>${data[i].approved}</td>` +
                `<td>${data[i].unapproved}</td>` +
                `<td>${data[i].state}</td>` +
                `<td>${data[i].type}</td>` +
                `<td>${data[i].createdAt} (${data[i].updatedAt})</td>` +
                `<td>${data[i].createdBy}</td>`+
                `<td><span class="commentIcon" data-comment="commentPopover${data[i].id}">${this.commentIcon()}</span><div class="comment d-none" id="commentPopover${data[i].id}">${data[i].comment}</div></td>` +
                '</tr>';
        }
        table += '</tbody></table></div>';

        u('#details').html(table);
        u('.commentIcon').on('click', e => {
            u(e.target).closest('span').siblings('.comment').toggleClass('d-none');
        });
        u('.menu-item').on('click', e => {
            u(e.target).closest('span').siblings('div .comment').toggleClass('d-none');
        });
        this.createTotal(listenList, wksList);
        u('.admin_new_statistic_menu div span').removeClass('d-none');

    }

    /**
     * @param {Object.<string, Liste>} listen
     * @param {Object.<string, Liste>} wks
     */
    createTotal(listen, wks) {
        let html = '<div class="table-responsive"><table class="table table-sm table-striped"><thead><tr>\n' +
            '      <th scope="col">Name</th>\n' +
            '      <th scope="col">Bestätigt</th>\n' +
            '      <th scope="col">Unbestätigt</th>\n' +
            '    </tr>\n' +
            '  </thead>\n' +
            '  <tbody>';
        html += `<tr><td colspan="3"><span class="text-decoration-underline">Listen</span></td></tr>`;
        for (const entry in listen) {
            html += `<tr><td>${listen[entry].name}</td><td>${listen[entry].approved}</td><td>${listen[entry].unapproved}</td></tr>`;
        }
        html += `<tr><td colspan="3"><span class="text-decoration-underline">Direktkandidaten</span></td></tr>`;
        for (const entry in wks) {
            html += `<tr><td>${wks[entry].name}</td><td>${wks[entry].approved}</td><td>${wks[entry].unapproved}</td></tr>`;
        }
        html += '</tbody></table></div>';
        u('#total').html(html);
    }

    showResults(e) {
        u(e.target).closest('div').children('.selected').removeClass('selected');
        u(e.target).addClass('selected');
        u('#tab-results').children().addClass('d-none');
        u('#tab-results').find(`#${e.target.dataset.tab}`).removeClass('d-none');
    }

    commentIcon() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg width="100%" height="100%" viewBox="0 0 48 33" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><path d="M1.59,0c-0.88,0 -1.59,0.709 -1.59,1.59l0,29.412c0,0.881 0.709,1.591 1.59,1.591l44.151,-0c0.88,-0 1.59,-0.71 1.59,-1.591l-0,-29.412c-0,-0.88 -0.71,-1.59 -1.59,-1.59l-44.151,0l0,0Z" style="fill:#808285;fill-rule:nonzero;"/><rect x="6.731" y="4.92" width="33.868" height="3.33"/><rect x="6.731" y="11.72" width="33.868" height="3.33"/><rect x="6.731" y="18.52" width="33.868" height="3.33"/></svg>';
    }

    actions(id) {
        let deleteUrl = this.deletUrl;
        deleteUrl = deleteUrl.replace('%23', id, deleteUrl);
        let editUrl = this.editUrl;
        editUrl = editUrl.replace('%23', id, editUrl);
        let html ='<td>';
        if (id !== null) {
            html +=`<span class="menu-item" data-it="${id}"><box-icon name='edit'></box-icon></span>`+
                `<div class="comment d-none" id="menu${id}">`
            html +=`<span class="delete" "><a href="${deleteUrl}"><box-icon type='solid' size="sm" animation="burst-hover" name='trash' style="fill: red"></box-icon></a></span>` +
                `<span class="edit" "><a href="${editUrl}"><box-icon size="sm" animation="burst-hover" type='solid' name='edit-alt'></box-icon></a></span>`;
            html += `</div>`;
        }
        html += '</td>'
        return html;
    }

    handleEntry(entry, listen, wks)
    {
        let name = `${entry.name} - ${entry.type}`;
        switch(entry.type) {
            case 'Landesliste (Landeswahl)':
            case 'Liste (Kommunalwahl)':
                listen.hasOwnProperty(name)
                    ? listen[name].add(entry.approved, entry.unapproved)
                    : listen[name] = new Liste(name, entry.approved, entry.unapproved);
                break;
            case 'Landesliste':
                listen.hasOwnProperty(entry.name)
                    ? listen[name].add(entry.approved, entry.unapproved)
                    : listen[name] = new Liste(name, entry.approved, entry.unapproved);
                break;
            case 'Direktkandidat (Landeswahl)':
            case 'Direktkandidat (Kommunalwahl)':
                wks.hasOwnProperty(name)
                    ? wks[name].add(entry.approved, entry.unapproved)
                    : wks[name] = new Liste(name, entry.approved, entry.unapproved);
                break;
            case 'Direktkandidat':
                wks.hasOwnProperty(entry.name)
                    ? wks[name].add(entry.approved, entry.unapproved)
                    : wks[name] = new Liste(name, entry.approved, entry.unapproved);
                break;
            default: return;
        }
    }
}
