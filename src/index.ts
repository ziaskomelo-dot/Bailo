import { Hono } from 'hono'
import { cors } from 'hono/cors'
import { logger } from 'hono/logger'
import { compress } from 'hono/compress'

import { serveStatic } from './middlewares/serve-static.js'
import { api, pages} from './routes.js'

const app = new Hono()

// Middlewares
app.use('*', logger())
app.use('*', compress())
app.use('/api/*', cors())
app.use('/assets/*', serveStatic({
  root: './public/assets',
  prefix: '/assets',
  maxAge: 31536000, // 1 year
  immutable: true,
}))

// Routes
app.route('/', pages)
app.route('/api', api)

export default app
