import {createIcons} from "lucide"
import LucideItems from "./LucideItems.js"
export default class {
    static initialize(icons) {
        createIcons({
            icons: LucideItems.icons,
        });
    }
}
