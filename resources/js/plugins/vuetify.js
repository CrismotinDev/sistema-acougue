import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
    components,
    directives,
    theme: {
        defaultTheme: 'light',
        themes: {
            light: {
                dark: false,
                colors: {
                    // A cor principal do MeatFlow (Grena)
                    primary: '#8D021F',

                    // Uma versão mais clara para contrastes ou estados de hover
                    secondary: '#A30324',

                    // Cor para a barra lateral ou elementos escuros
                    accent: '#4A0404',

                    // Fundo levemente cinza para destacar os cards brancos
                    background: '#F5F5F5',

                    // Cores de status para os pedidos
                    success: '#4caf50',
                    warning: '#fb8c00',
                    error: '#ff5252',
                    info: '#2196f3',
                }
            }
        }
    }
})

export default vuetify
