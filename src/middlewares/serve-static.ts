import { readFile, stat } from 'node:fs/promises'
import { join, extname } from 'node:path'

import { Context, MiddlewareHandler, Next } from 'hono'

interface ServeStaticOptions {
  root?: string
  prefix?: string
  index?: string
  maxAge?: number
  immutable?: boolean
  onNotFound?: (path: string, c: Context) => void | Promise<void>
}

const MIME_TYPES: Record<string, string> = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'application/javascript; charset=utf-8',
  '.mjs': 'application/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.gif': 'image/gif',
  '.svg': 'image/svg+xml',
  '.ico': 'image/x-icon',
  '.webp': 'image/webp',
  '.woff': 'font/woff',
  '.woff2': 'font/woff2',
  '.ttf': 'font/ttf',
  '.otf': 'font/otf',
  '.eot': 'application/vnd.ms-fontobject',
  '.pdf': 'application/pdf',
  '.txt': 'text/plain; charset=utf-8',
  '.xml': 'application/xml',
  '.mp4': 'video/mp4',
  '.webm': 'video/webm',
  '.mp3': 'audio/mpeg',
  '.wav': 'audio/wav',
  '.zip': 'application/zip',
}

function getMimeType(filePath: string): string {
  const ext = extname(filePath).toLowerCase()
  return MIME_TYPES[ext] || 'application/octet-stream'
}

export function serveStatic(options: ServeStaticOptions = {}): MiddlewareHandler {
  const {
    root = './public',
    prefix = '',
    index = 'index.html',
    maxAge = 0,
    immutable = false,
    onNotFound,
  } = options

  return async (c: Context, next: Next) => {
    // Only handle GET and HEAD requests
    if (c.req.method !== 'GET' && c.req.method !== 'HEAD') {
      return next()
    }

    let path = c.req.path

    // Remove prefix from path
    if (prefix && path.startsWith(prefix)) {
      path = path.slice(prefix.length) || '/'
    }

    // Prevent directory traversal attacks
    if (path.includes('..')) {
      return next()
    }

    // Resolve the file path
    let filePath = join(process.cwd(), root, path)

    try {
      const stats = await stat(filePath)

      // If it's a directory, try to serve index file
      if (stats.isDirectory()) {
        filePath = join(filePath, index)
        await stat(filePath) // Check if index exists
      }

      const content = await readFile(filePath)
      const mimeType = getMimeType(filePath)

      // Build cache control header
      let cacheControl = maxAge > 0 ? `public, max-age=${maxAge}` : 'no-cache'
      if (immutable && maxAge > 0) {
        cacheControl += ', immutable'
      }

      c.header('Content-Type', mimeType)
      c.header('Content-Length', content.length.toString())
      c.header('Cache-Control', cacheControl)

      // HEAD requests should not return body
      if (c.req.method === 'HEAD') {
        return c.body(null, 200)
      }

      return c.body(content, 200)
    } catch (error) {
      if (onNotFound) {
        await onNotFound(path, c)
      }
      return next()
    }
  }
}
