import { Hono } from 'hono'
import prisma from '../lib/prisma.js'

const users = new Hono()

// Get user by ID
users.get('/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const user = await prisma.user.findUnique({
      where: { id },
      select: {
        id: true,
        name: true,
        email: true,
        role: true,
        createdAt: true,
      }
    })

    if (!user) {
      return c.json({ success: false, error: 'User not found' }, 404)
    }

    return c.json({ success: true, data: user })
  } catch (error) {
    console.error('Error fetching user:', error)
    return c.json({ success: false, error: 'Failed to fetch user' }, 500)
  }
})

// Register new user
users.post('/register', async (c) => {
  try {
    const body = await c.req.json()
    const { name, email, password, role } = body

    // Check if user already exists
    const existingUser = await prisma.user.findUnique({
      where: { email }
    })

    if (existingUser) {
      return c.json({ success: false, error: 'User with this email already exists' }, 400)
    }

    // Create user (Note: In production, hash the password!)
    const user = await prisma.user.create({
      data: {
        name,
        email,
        password, // TODO: Hash password before storing
        role: role || 'CLIENT',
      },
      select: {
        id: true,
        name: true,
        email: true,
        role: true,
        createdAt: true,
      }
    })

    // If registering as artisan, create artisan profile
    if (role === 'ARTISAN') {
      await prisma.artisan.create({
        data: {
          userId: user.id,
          bio: '',
        }
      })
    }

    return c.json({ success: true, data: user }, 201)
  } catch (error) {
    console.error('Error registering user:', error)
    return c.json({ success: false, error: 'Failed to register user' }, 500)
  }
})

// Login user
users.post('/login', async (c) => {
  try {
    const body = await c.req.json()
    const { email, password } = body

    const user = await prisma.user.findUnique({
      where: { email }
    })

    if (!user) {
      return c.json({ success: false, error: 'Invalid credentials' }, 401)
    }

    // TODO: In production, compare hashed passwords
    if (user.password !== password) {
      return c.json({ success: false, error: 'Invalid credentials' }, 401)
    }

    return c.json({
      success: true,
      data: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
      }
    })
  } catch (error) {
    console.error('Error logging in:', error)
    return c.json({ success: false, error: 'Failed to login' }, 500)
  }
})

export default users
