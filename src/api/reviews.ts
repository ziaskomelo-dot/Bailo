import { Hono } from 'hono'
import prisma from '../lib/prisma.js'

const reviews = new Hono()

// Get all reviews for an artisan (via their requests)
reviews.get('/artisan/:artisanId', async (c) => {
  try {
    const artisanId = c.req.param('artisanId')
    const artisanReviews = await prisma.review.findMany({
      where: {
        request: {
          artisanId
        }
      },
      include: {
        user: {
          select: {
            name: true,
          }
        },
        request: {
          select: {
            service: {
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
    return c.json({ success: true, data: artisanReviews })
  } catch (error) {
    console.error('Error fetching reviews:', error)
    return c.json({ success: false, error: 'Failed to fetch reviews' }, 500)
  }
})

// Get review by ID
reviews.get('/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const review = await prisma.review.findUnique({
      where: { id },
      include: {
        user: {
          select: {
            name: true,
          }
        },
        request: {
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
        }
      }
    })

    if (!review) {
      return c.json({ success: false, error: 'Review not found' }, 404)
    }

    return c.json({ success: true, data: review })
  } catch (error) {
    console.error('Error fetching review:', error)
    return c.json({ success: false, error: 'Failed to fetch review' }, 500)
  }
})

// Create new review for a request
reviews.post('/', async (c) => {
  try {
    const body = await c.req.json()
    const { userId, requestId, rating, comment } = body

    // Check if request exists and is completed
    const request = await prisma.request.findUnique({
      where: { id: requestId },
    })

    if (!request) {
      return c.json({ success: false, error: 'Request not found' }, 404)
    }

    // Check if review already exists for this request
    const existingReview = await prisma.review.findUnique({
      where: { requestId }
    })

    if (existingReview) {
      return c.json({ success: false, error: 'A review already exists for this request' }, 400)
    }

    const review = await prisma.review.create({
      data: {
        userId,
        requestId,
        rating,
        comment,
      },
      include: {
        user: {
          select: {
            name: true,
          }
        }
      }
    })

    return c.json({ success: true, data: review }, 201)
  } catch (error) {
    console.error('Error creating review:', error)
    return c.json({ success: false, error: 'Failed to create review' }, 500)
  }
})

// Update review
reviews.patch('/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const body = await c.req.json()
    const { rating, comment } = body

    const review = await prisma.review.update({
      where: { id },
      data: {
        rating,
        comment,
      },
    })

    return c.json({ success: true, data: review })
  } catch (error) {
    console.error('Error updating review:', error)
    return c.json({ success: false, error: 'Failed to update review' }, 500)
  }
})

// Delete review
reviews.delete('/:id', async (c) => {
  try {
    const id = c.req.param('id')

    await prisma.review.delete({
      where: { id },
    })

    return c.json({ success: true, message: 'Review deleted successfully' })
  } catch (error) {
    console.error('Error deleting review:', error)
    return c.json({ success: false, error: 'Failed to delete review' }, 500)
  }
})

export default reviews
