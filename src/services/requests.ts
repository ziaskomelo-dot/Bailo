import { Hono } from 'hono'
import prisma from '../lib/prisma.js'

const requests = new Hono()

// Get all requests for a client
requests.get('/client/:clientId', async (c) => {
  try {
    const clientId = c.req.param('clientId')
    const clientRequests = await prisma.request.findMany({
      where: { clientId },
      include: {
        artisan: {
          include: {
            user: {
              select: {
                name: true,
              }
            }
          }
        },
        service: true,
        quotes: true,
      },
      orderBy: {
        createdAt: 'desc'
      }
    })
    return c.json({ success: true, data: clientRequests })
  } catch (error) {
    console.error('Error fetching client requests:', error)
    return c.json({ success: false, error: 'Failed to fetch requests' }, 500)
  }
})

// Get all requests for an artisan
requests.get('/artisan/:artisanId', async (c) => {
  try {
    const artisanId = c.req.param('artisanId')
    const artisanRequests = await prisma.request.findMany({
      where: { artisanId },
      include: {
        client: {
          select: {
            name: true,
            email: true,
          }
        },
        service: true,
        quotes: true,
      },
      orderBy: {
        createdAt: 'desc'
      }
    })
    return c.json({ success: true, data: artisanRequests })
  } catch (error) {
    console.error('Error fetching artisan requests:', error)
    return c.json({ success: false, error: 'Failed to fetch requests' }, 500)
  }
})

// Get request by ID
requests.get('/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const request = await prisma.request.findUnique({
      where: { id },
      include: {
        client: {
          select: {
            name: true,
            email: true,
          }
        },
        artisan: {
          include: {
            user: {
              select: {
                name: true,
              }
            }
          }
        },
        service: true,
        quotes: true,
      }
    })

    if (!request) {
      return c.json({ success: false, error: 'Request not found' }, 404)
    }

    return c.json({ success: true, data: request })
  } catch (error) {
    console.error('Error fetching request:', error)
    return c.json({ success: false, error: 'Failed to fetch request' }, 500)
  }
})

// Create new request
requests.post('/', async (c) => {
  try {
    const body = await c.req.json()
    const { clientId, artisanId, serviceId, description } = body

    const request = await prisma.request.create({
      data: {
        clientId,
        artisanId,
        serviceId,
        description,
        status: 'PENDING',
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
        },
        service: true,
      }
    })

    return c.json({ success: true, data: request }, 201)
  } catch (error) {
    console.error('Error creating request:', error)
    return c.json({ success: false, error: 'Failed to create request' }, 500)
  }
})

// Update request status
requests.patch('/:id/status', async (c) => {
  try {
    const id = c.req.param('id')
    const body = await c.req.json()
    const { status } = body

    const request = await prisma.request.update({
      where: { id },
      data: { status },
    })

    return c.json({ success: true, data: request })
  } catch (error) {
    console.error('Error updating request:', error)
    return c.json({ success: false, error: 'Failed to update request' }, 500)
  }
})

export default requests
