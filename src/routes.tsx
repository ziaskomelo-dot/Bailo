import { Hono } from 'hono'

import artisans from './api/artisans.js'
import services from './api/services.js'
import users from './api/users.js'
import requests from './api/requests.js'
import reviews from './api/reviews.js'
import quotes from './api/quotes.js'

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
