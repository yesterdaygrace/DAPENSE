import './bootstrap';

import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;

// Initialize Lucide icons
createIcons({ icons });

// Reinitialize after Livewire navigations (SPA)
document.addEventListener('livewire:navigated', () => {
    createIcons({ icons });
});

Alpine.start();
