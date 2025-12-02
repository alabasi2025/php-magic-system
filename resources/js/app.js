import './bootstrap'

import { createApp } from 'vue';

import SystemOverview from './components/developer/SystemOverview.vue';
import ArtisanCommands from './components/developer/ArtisanCommands.vue';
import CodeGenerator from './components/developer/CodeGenerator.vue';

const app = createApp({});

app.component('system-overview', SystemOverview);
app.component('artisan-commands', ArtisanCommands);
app.component('code-generator', CodeGenerator);

app.mount('#app');
