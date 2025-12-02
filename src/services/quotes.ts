import { Hono } from 'hono'
import prisma from '../lib/prisma.js'

const quotes = new Hono()

// Get all quotes for a request
quotes.get('/request/:requestId', async (c) => {
  try {
    const requestId = c.req.param('requestId')
    const requestQuotes = await prisma.quote.findMany({
      where: { requestId },
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
      },
      orderBy: {
        createdAt: 'desc'
      }
    })
    return c.json({ success: true, data: requestQuotes })
  } catch (error) {
    console.error('Error fetching quotes:', error)
    return c.json({ success: false, error: 'Failed to fetch quotes' }, 500)
  }
})

// Get quote by ID
quotes.get('/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const quote = await prisma.quote.findUnique({
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
        },
        request: {
          include: {
            service: true,
            client: {
              select: {
                name: true,
                email: true,
              }
            }
          }
        }
      }
    })

    if (!quote) {
      return c.json({ success: false, error: 'Quote not found' }, 404)
    }

    return c.json({ success: true, data: quote })
  } catch (error) {
    console.error('Error fetching quote:', error)
    return c.json({ success: false, error: 'Failed to fetch quote' }, 500)
  }
})

// Create new quote
quotes.post('/', async (c) => {
  try {
    const body = await c.req.json()
    const { requestId, artisanId, amount, description } = body

    const quote = await prisma.quote.create({
      data: {
        requestId,
        artisanId,
        amount,
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
        }
      }
    })

    return c.json({ success: true, data: quote }, 201)
  } catch (error) {
    console.error('Error creating quote:', error)
    return c.json({ success: false, error: 'Failed to create quote' }, 500)
  }
})

// Update quote status (accept/reject)
quotes.patch('/:id/status', async (c) => {
  try {
    const id = c.req.param('id')
    const body = await c.req.json()
    const { status } = body

    const quote = await prisma.quote.update({
      where: { id },
      data: { status },
    })

    return c.json({ success: true, data: quote })
  } catch (error) {
    console.error('Error updating quote:', error)
    return c.json({ success: false, error: 'Failed to update quote' }, 500)
  }
})

export default quotes
