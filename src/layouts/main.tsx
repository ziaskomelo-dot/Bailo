import { FC } from "hono/jsx"

interface SiteData {
  title: string
  description: string
  image: string
  children: unknown
}

export const MainLayout: FC<SiteData> = ({ title, description, image, children }) => {
  return (
    <html class="no-js" lang="fr">
      <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <title>{title}</title>
        <meta name="description" content={description} />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:title" content={title} />
        <meta property="og:image" content={image} />
        {/* <!-- Place favicon.ico in the root directory --> */}

        {/* <!-- ========================= CSS here ========================= --> */}
        <link rel="stylesheet" href="/assets/css/bootstrap-5.0.0-beta1.min.css" />
        <link rel="stylesheet" href="/assets/css/LineIcons.2.0.css" />
        <link rel="stylesheet" href="/assets/css/tiny-slider.css" />
        <link rel="stylesheet" href="/assets/css/animate.css" />
        <link rel="stylesheet" href="/assets/css/lindy-uikit.css" />
      </head>

      <body>
        {/* <!-- ========================= preloader start ========================= --> */}
        <div class="preloader">
          <div class="loader">
            <div class="spinner">
              <div class="spinner-container">
                <div class="spinner-rotator">
                  <div class="spinner-left">
                    <div class="spinner-circle"></div>
                  </div>
                  <div class="spinner-right">
                    <div class="spinner-circle"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        {/* <!-- ========================= preloader end ========================= --> */}

        {children}

        <a href="#" class="scroll-top"> <i class="lni lni-chevron-up"></i> </a>
        <script src="/assets/js/bootstrap-5.0.0-beta1.min.js"></script>
        <script src="/assets/js/tiny-slider.js"></script>
        <script src="/assets/js/wow.min.js"></script>
        <script src="/assets/js/main.js"></script>
      </body>
    </html>
  )
}
