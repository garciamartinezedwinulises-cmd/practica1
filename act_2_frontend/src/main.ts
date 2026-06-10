import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './plugins/axios'
import { vCan } from './directives/can'


const app = createApp(App)

app.use(createPinia())
app.use(router)

app.directive('can', vCan) // <- Esto habilita el uso de v-can en los templates [cite: 227]

app.mount('#app')