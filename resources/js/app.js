import './bootstrap';
import './common';
import './file-upload-preview';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Sidebar store
Alpine.store('sidebar', {
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    },
    open() {
        this.open = true;
    }
});

Alpine.start();
