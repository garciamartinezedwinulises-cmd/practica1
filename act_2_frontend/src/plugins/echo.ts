import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

(window as any).Pusher = Pusher

Pusher.logToConsole = true 

const echo = new Echo({
  broadcaster: 'reverb',
  key: 'sjta5ejdmpxzugvtwoud', 
  wsHost: 'localhost',
  wsPort: 8080,
  wssPort: 8080,
  forceTLS: false,
  enabledTransports: ['ws'],
  // 🔄 CORRECCIÓN: Quitamos el "/api" para que apunte al endpoint nativo de Laravel 
  authEndpoint: 'http://127.0.0.1:8000/broadcasting/auth', 
  auth: {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  }
})

export default echo