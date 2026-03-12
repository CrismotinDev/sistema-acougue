import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
// ADICIONE ESTAS DUAS LINHAS ABAIXO:
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
  components, // Registra os componentes (v-card, v-btn, etc)
  directives, // Registra as diretivas (v-ripple, etc)
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        dark: false,
        colors: {
          primary: '#3F51B5',
          secondary: '#5C6BC0',
          background: '#F5F5F5'
        }
      }
    }
  }
})

export default vuetify
