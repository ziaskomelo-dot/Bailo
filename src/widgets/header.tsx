import { FC } from "hono/jsx";

export const Header: FC = () => {
  const links = [
    { label: "Home", href: "#home" },
    { label: "Services", href: "#service" },
    { label: "Pricing", href: "#artisan" },
    { label: "About", href: "#about" },
    { label: "Contact", href: "#contact" },
  ];

  return (
    <header class="header header-6">
      <div class="navbar-area">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-12">
              <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand" href="/">
                  <img src="assets/img/logo/BAILO.png" alt="Logo" width="100" height="100" />
                </a>

                <button
                  class="navbar-toggler"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#navbarSupportedContent6"
                  aria-controls="navbarSupportedContent6"
                  aria-expanded="false"
                  aria-label="Toggle navigation"
                >
                  <span class="toggler-icon"></span>
                  <span class="toggler-icon"></span>
                  <span class="toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent6">
                  <ul id="nav6" class="navbar-nav ms-auto">
                    {links.map((link) => (
                      <li class="nav-item" key={link.href}>
                        <a class="page-scroll" href={link.href}>
                          {link.label}
                        </a>
                      </li>
                    ))}
                  </ul>
                </div>

                <div class="header-action d-flex">
                  <a href="login.php"> <i class="lni lni-alarm"></i> </a>
                  <div class="d-flex">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
                      Connexion
                    </button>
                  </div>
                </div>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </header>
  )
}
