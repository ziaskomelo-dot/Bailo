import { Hono } from 'hono'
import prisma from '../lib/prisma.js'

const services = new Hono()

// Get all services
services.get('/', async (c) => {
  try {
    const allServices = await prisma.service.findMany({
      include: {
        artisan: {
          include: {
            user: {
              select: {
                name: true,
              }
            }
          }
        }
      }
    })
    return c.json({ success: true, data: allServices })
  } catch (error) {
    console.error('Error fetching services:', error)
    return c.json({ success: false, error: 'Failed to fetch services' }, 500)
  }
})

// Get service by ID
services.get('/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const service = await prisma.service.findUnique({
      where: { id },
      include: {
        artisan: {
          include: {
            user: {
              select: {
                name: true,
              }
            }
          }
        }
      }
    })

    if (!service) {
      return c.json({ success: false, error: 'Service not found' }, 404)
    }

    return c.json({ success: true, data: service })
  } catch (error) {
    console.error('Error fetching service:', error)
    return c.json({ success: false, error: 'Failed to fetch service' }, 500)
  }
})

// Search services by category or name
services.get('/search', async (c) => {
  try {
    const query = c.req.query('q')
    const category = c.req.query('category')

    const servicesList = await prisma.service.findMany({
      where: {
        OR: [
          query ? {
            name: {
              contains: query,
              mode: 'insensitive'
            }
          } : {},
          query ? {
            description: {
              contains: query,
              mode: 'insensitive'
            }
          } : {},
          category ? {
            category: {
              contains: category,
              mode: 'insensitive'
            }
          } : {}
        ]
      },
      include: {
        artisan: {
          include: {
            user: {
              select: {
                name: true,
              }
            }
          }
        }
      }
    })

    return c.json({ success: true, data: servicesList })
  } catch (error) {
    console.error('Error searching services:', error)
    return c.json({ success: false, error: 'Failed to search services' }, 500)
  }
})

export default services
