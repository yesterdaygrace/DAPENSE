import './bootstrap';

import Alpine from 'alpinejs';
import $ from 'jquery';
import { createIcons, icons } from 'lucide';

window.$ = window.jQuery = $;
window.Alpine = Alpine;
window.lucide = { createIcons };

createIcons({ icons });

Alpine.start();
