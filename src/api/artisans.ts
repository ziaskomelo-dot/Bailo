import { Hono } from 'hono'
import prisma from '../lib/prisma.js'

const artisans = new Hono()

// Get all artisans
artisans.get('/', async (c) => {
  try {
    const allArtisans = await prisma.artisan.findMany({
      include: {
        user: {
          select: {
            id: true,
            name: true,
            email: true,
          }
        },
        services: true,
      }
    })
    return c.json({ success: true, data: allArtisans })
  } catch (error) {
    console.error('Error fetching artisans:', error)
    return c.json({ success: false, error: 'Failed to fetch artisans' }, 500)
  }
})

// Get artisan by ID
artisans.get('/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const artisan = await prisma.artisan.findUnique({
      where: { id },
      include: {
        user: {
          select: {
            id: true,
            name: true,
            email: true,
          }
        },
        services: true,
        requests: {
          include: {
            review: true,
          }
        },
      }
    })

    if (!artisan) {
      return c.json({ success: false, error: 'Artisan not found' }, 404)
    }

    return c.json({ success: true, data: artisan })
  } catch (error) {
    console.error('Error fetching artisan:', error)
    return c.json({ success: false, error: 'Failed to fetch artisan' }, 500)
  }
})

// Search artisans by service or city
artisans.get('/search', async (c) => {
  try {
    const service = c.req.query('service')
    const city = c.req.query('city')

    const artisansList = await prisma.artisan.findMany({
      where: {
        AND: [
          service ? {
            services: {
              some: {
                name: {
                  contains: service,
                  mode: 'insensitive'
                }
              }
            }
          } : {},
          city ? {
            city: {
              contains: city,
              mode: 'insensitive'
            }
          } : {}
        ]
      },
      include: {
        user: {
          select: {
            id: true,
            name: true,
            email: true,
          }
        },
        services: true,
      }
    })

    return c.json({ success: true, data: artisansList })
  } catch (error) {
    console.error('Error searching artisans:', error)
    return c.json({ success: false, error: 'Failed to search artisans' }, 500)
  }
})

export default artisans
