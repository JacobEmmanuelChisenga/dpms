import './bootstrap';

import Alpine from 'alpinejs';
import { nrcInput } from './nrc-input';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('nrcInput', nrcInput);
});

Alpine.start();
