import { Hono } from 'hono'

import artisans from './services/artisans.js'
import services from './services/services.js'
import users from './services/users.js'
import requests from './services/requests.js'
import reviews from './services/reviews.js'
import quotes from './services/quotes.js'

import { HomePage } from './pages/home.js'
import { ErrorPage } from './pages/500.js'
import { NotFoundPage } from './pages/404.js'

export const api = new Hono()

// Mount API routes
api.route('/artisans', artisans)
api.route('/services', services)
api.route('/users', users)
api.route('/requests', requests)
api.route('/reviews', reviews)
api.route('/quotes', quotes)

export const pages = new Hono()

// 404 page
pages.notFound((c) => {
  return c.html(<NotFoundPage />, 404)
})

// Error page
pages.onError((err, c) => {
  console.error(`${err}`)
  return c.html(<ErrorPage />, 500)
})

// Home page
pages.get('/', async (c) => {
  return c.html(<HomePage />)
})
