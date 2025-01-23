import './bootstrap';

import { createApp } from 'vue'
import TaskManager from '../views/components/TaskManager.vue';

const app = createApp(TaskManager)

app.mount('#app')