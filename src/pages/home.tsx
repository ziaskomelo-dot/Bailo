import { FC } from "hono/jsx"
import { MainLayout } from "../layouts/main.js"
import { Footer } from "../widgets/footer.js"
import { Header } from "../widgets/header.js"

export const HomePage: FC = () => {
  const meta = {
    title: "Bailo - La plateforme de mise en relation entre particuliers et artisans de confiance",
    description: "Bailo facilite la connexion entre particuliers et artisans qualifiés pour tous vos projets de rénovation, réparation et entretien.",
    image: "/assets/images/preview.png",
  }

  return (
    <MainLayout {...meta}>
      {/* <!-- ========================= hero-section-wrapper-5 start ========================= --> */}
      <section id="home" class="hero-section-wrapper-5">
        {/* <!-- ========================= header-6 start ========================= --> */}
        <Header />
        {/* <!-- ========================= header-6 end ========================= --> */}

        {/* <!-- ========================= hero-5 start ========================= --> */}
        <div class="hero-section hero-style-5 img-bg" style="background-image: url('assets/img/hero/hero-5/hero-bg.svg')">
          <div class="container">
            <div class="row">
              <div class="col-lg-6">
                <div class="hero-content-wrapper">
                  <h2 class="mb-30 wow fadeInUp" data-wow-delay=".2s">Découvrez les artisans locaux près de chez vous</h2>
                  <p class="mb-30 wow fadeInUp" data-wow-delay=".4s">Connectez-vous avec des professionnels qualifiés pour
                    tous vos besoins.</p>
                  <a href="artisan.html" class="button button-lg radius-50 wow fadeInUp" data-wow-delay=".6s">Voir les
                    Artisans<i class="lni lni-chevron-right"></i></a>
                </div>
              </div>
              <div class="col-lg-6 align-self-end">
                <div class="hero-image wow fadeInUp" data-wow-delay=".5s">
                  <img src="assets/img/hero/electro1.png" alt="" />
                </div>
              </div>
            </div>
          </div>
        </div>
        {/* <!-- ========================= hero-5 end ========================= --> */}
      </section>
      {/* <!-- ========================= hero-section-wrapper-6 end ========================= --> */}

      {/* <!-- ========================= service style-5 start ========================= --> */}
      <section id="service" class="feature-section feature-style-5">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-8">
              <div class="section-title text-center mb-60">
                <h3 class="mb-15 wow fadeInUp" data-wow-delay=".2s">Cherchez local, trouvez mieux.</h3>
                <p class="wow fadeInUp" data-wow-delay=".4s">Des services fiables, des artisans passionnés — découvrez-les
                  près de chez vous !</p>
              </div>
            </div>
          </div>


          {/* <!-- 1 --> */}
          <div class="row">
            <div class="col-lg-4 col-md-6">
              <div class="single-feature wow fadeInUp" data-wow-delay=".2s">
                <div class="icon">
                  <i class="lni lni-tag"></i>
                  <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z"
                      fill="#EBF4FF" />
                  </svg>
                </div>
                <div class="content">
                  <h5>Confiance</h5>
                  <p>Chaque prestation est réalisée avec le souci de rassurer et de garantir une expérience fiable du début
                    à la fin.</p>
                </div>
              </div>
            </div>

            {/* <!-- 2 --> */}
            <div class="col-lg-4 col-md-6">
              <div class="single-feature wow fadeInUp" data-wow-delay=".4s">
                <div class="icon">
                  <i class="lni lni-certificate"></i>
                  <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z"
                      fill="#EBF4FF" />
                  </svg>
                </div>
                <div class="content">
                  <h5>Professionnalisme</h5>
                  <p>Nous travaillons uniquement avec des artisans et prestataires reconnus pour leur professionnalisme.</p>
                </div>
              </div>
            </div>

            {/* <!-- 3 --> */}
            <div class="col-lg-4 col-md-6">
              <div class="single-feature wow fadeInUp" data-wow-delay=".6s">
                <div class="icon">
                  <i class="lni lni-checkmark-circle"></i>
                  <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z"
                      fill="#EBF4FF" />
                  </svg>
                </div>
                <div class="content">
                  <h5>Transparence</h5>
                  <p>Aucune surprise, aucune information cachée : nous croyons en une relation claire entre artisans et
                    clients.</p>
                </div>
              </div>
            </div>

            {/* <!-- 4 --> */}
            <div class="col-lg-4 col-md-6">
              <div class="single-feature wow fadeInUp" data-wow-delay=".2s">
                <div class="icon">
                  <i class="lni lni-handshake"></i>
                  <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z"
                      fill="#EBF4FF" />
                  </svg>
                </div>
                <div class="content">
                  <h5>Fiabilité</h5>
                  <p>Nous nous engageons à valoriser les artisans locaux et à offrir aux clients un accès simple à des
                    services de qualité.</p>
                </div>
              </div>
            </div>

            {/* <!-- 5 --> */}
            <div class="col-lg-4 col-md-6">
              <div class="single-feature wow fadeInUp" data-wow-delay=".4s">
                <div class="icon">
                  <i class="lni lni-lock"></i>
                  <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z"
                      fill="#EBF4FF" />
                  </svg>
                </div>
                <div class="content">
                  <h5>Sécurité</h5>
                  <p>Nous veillons à offrir un environnement fiable où chaque client peut réserver et communiquer en toute
                    sérénité.</p>
                </div>
              </div>
            </div>

            {/* <!-- 6 --> */}
            <div class="col-lg-4 col-md-6">
              <div class="single-feature wow fadeInUp" data-wow-delay=".6s">
                <div class="icon">
                  <i class="lni lni-star"></i>
                  <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z"
                      fill="#EBF4FF" />
                  </svg>
                </div>
                <div class="content">
                  <h5>Qualité</h5>
                  <p>Chaque artisan est sélectionné pour son savoir-faire, son sérieux et la satisfaction qu’il apporte à
                    ses clients.</p>
                </div>
              </div>
            </div>
          </div>
          <a href="service.html" class="button button-lg radius-10 wow fadeInUp" data-wow-delay=".5s">Voir les services</a>
        </div>
      </section>
      {/* <!-- ========================= feature style-5 end ========================= --> */}

      {/* <!-- ========================= artisan style-4 start ========================= --> */}
      <section id="artisan" class="pricing-section pricing-style-4 bg-light">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-xl-5 col-lg-6">
              <div class="section-title mb-60">
                <h3 class="mb-15 wow fadeInUp" data-wow-delay=".2s">Artisan Bailo</h3>
                <p class="wow fadeInUp" data-wow-delay=".4s">
                  <h6>Des artisans vérifiés. Des prix transparents. Un service fiable.</h6>
                  <br />
                  Chez nous, vous trouvez des artisans compétents au meilleur prix, avec une transparence totale.
                  Chaque prestation affiche un tarif clair, détaillé et sans frais cachés. Vous choisissez selon votre budget,
                  tout en bénéficiant d’un travail de qualité.
                </p>
              </div>
            </div>
            <div class="col-xl-7 col-lg-6">
              <div class="pricing-active-wrapper wow fadeInUp" data-wow-delay=".4s">
                <div class="pricing-active">

                  <div class="single-pricing-wrapper">
                    <div class="single-pricing">
                      <h6>MENUISIER</h6>
                      <h4>5ans<br /> d'expérience</h4>
                      <h3>$ 29.00</h3>
                      <ul>
                        <li>est mon prix basic</li>
                      </ul>
                      <a href="artisan.html" class="button radius-30">Commencer</a>
                    </div>
                  </div>

                  <div class="single-pricing-wrapper">
                    <div class="single-pricing">
                      <h6>PLOMBIER</h6>
                      <h4>5ans<br /> d'expérience</h4>
                      <h3>$ 60.00</h3>
                      <ul>
                        <li>est mon prix basic</li>
                      </ul>
                      <a href="artisan.html" class="button radius-30">Commencer</a>
                    </div>
                  </div>

                  <div class="single-pricing-wrapper">
                    <div class="single-pricing">
                      <h6>PEINTRE</h6>
                      <h4>Avec 7ans<br />d'expérience</h4>
                      <h3>$ 18.99</h3>
                      <ul>
                        <li>est mon prix basic</li>
                      </ul>
                      <a href="artisan.html" class="button radius-30">Commencer</a>
                    </div>
                  </div>

                  <div class="single-pricing-wrapper">
                    <div class="single-pricing">
                      <h6>ELECTRICIEN</h6>
                      <h4>Avec 18ans<br />d'expérience</h4>
                      <h3>$ 20.00</h3>
                      <ul>
                        <li>est mon prix basic</li>
                      </ul>
                      <a href="artisan.html" class="button radius-30">Commencer</a>
                    </div>
                  </div>

                  <div class="single-pricing-wrapper">
                    <div class="single-pricing">
                      <h6>COUTURIER</h6>
                      <h4>Femme &<br />homme</h4>
                      <h3>$ 08.00</h3>
                      <ul>
                        <li>est mon prix basic</li>
                      </ul>
                      <a href="artisan.html" class="button radius-30">Commencer</a>
                    </div>
                  </div>

                  <div class="single-pricing-wrapper">
                    <div class="single-pricing">
                      <h6>Jardinier</h6>
                      <h4>2ans<br />d'expérience</h4>
                      <h3>$ 12.00</h3>
                      <ul>
                        <li>est mon prix basic</li>
                      </ul>
                      <a href="artisan.html" class="button radius-30">Commencer</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      {/* <!-- ========================= pricing style-4 end ========================= --> */}

      {/* <!-- ========================= about style-4 start ========================= --> */}
      <section id="about" class="about-section about-style-4">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-xl-5 col-lg-6">
              <div class="about-content-wrapper">
                <div class="section-title mb-30">
                  <h3 class="mb-25 wow fadeInUp" data-wow-delay=".2s">À propos de notre plateforme</h3>
                  <p class="wow fadeInUp" data-wow-delay=".3s">Notre plateforme est une marketplace moderne dédiée aux artisans et aux services locaux, 
                    conçue pour faciliter la mise en relation entre les professionnels qualifiés et les clients à la recherche d’un service fiable, rapide et de qualité.</p>
                </div>
                <ul>
                  <li class="wow fadeInUp" data-wow-delay=".35s">
                    <i class="lni lni-checkmark-circle"></i>
                    Notre plateforme connecte les clients aux meilleurs artisans locaux.
                  </li>
                  <li class="wow fadeInUp" data-wow-delay=".4s">
                    <i class="lni lni-checkmark-circle"></i>
                    Les clients trouvent un service fiable, les artisans gagnent en visibilité.
                  </li>
                  <li class="wow fadeInUp" data-wow-delay=".45s">
                    <i class="lni lni-checkmark-circle"></i>
                    Un espace simple, professionnel où qualité et satisfaction sont au cœur de chaque service.
                  </li>
                </ul>
                <a href="apropo.html" class="button button-lg radius-10 wow fadeInUp" data-wow-delay=".5s">Voir plus</a>
              </div>
            </div>
            <div class="col-xl-7 col-lg-6">
              <div class="about-image text-lg-right wow fadeInUp" data-wow-delay=".5s">
                <img src="assets/img/hero/peinture1.jpg" alt="" />
              </div>
            </div>
          </div>
        </div>
      </section>
      {/* <!-- ========================= about style-4 end ========================= --> */}


      {/* <!-- ========================= contact-style-3 start ========================= --> */}
      <section id="contact" class="contact-section contact-style-3">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-xxl-5 col-xl-5 col-lg-7 col-md-10">
              <div class="section-title text-center mb-50">
                <h3 class="mb-15">Votre besoin, notre priorité.</h3>
                <p>Besoin d’aide ? Laissez-nous un mot, nous sommes là pour vous accompagner. Remplissez le formulaire
                  ci-dessous et nous vous recontactons rapidement.</p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-8">
              <div class="contact-form-wrapper">
                <form action="" method="post" class="contact-form">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="single-input">
                        <input type="text" id="name" name="name" class="form-input" placeholder="Name" />
                        <i class="lni lni-user"></i>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="single-input">
                        <input type="email" id="email" name="email" class="form-input" placeholder="Email" />
                        <i class="lni lni-envelope"></i>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="single-input">
                        <input type="text" id="number" name="number" class="form-input" placeholder="Number" />
                        <i class="lni lni-phone"></i>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="single-input">
                        <input type="text" id="subject" name="subject" class="form-input" placeholder="Subject" />
                        <i class="lni lni-comments"></i>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="single-input">
                        <textarea name="message" id="message" class="form-input" placeholder="Message" rows={6}></textarea>
                        <i class="lni lni-comments-alt"></i>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-button">
                        <button type="submit" class="button"> <i class="lni lni-telegram-original"></i> Submit</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="left-wrapper">
                <div class="row">
                  <div class="col-lg-12 col-md-6">
                    <div class="single-item">
                      <div class="icon">
                        <i class="lni lni-phone"></i>
                      </div>
                      <div class="text">
                        <p>Tel</p>
                        <p>+229 7 1234 5678</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-12 col-md-6">
                    <div class="single-item">
                      <div class="icon">
                        <i class="lni lni-envelope"></i>
                      </div>
                      <div class="text">
                        <p>Mail</p>
                        <p>bailomail@gmail.com</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-12 col-md-6">
                    <div class="single-item">
                      <div class="icon">
                        <i class="lni lni-map-marker"></i>
                      </div>
                      <div class="text">
                        <p>John's House, 13/5 Road, Sidny United State Of America</p>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </section>
      {/* <!-- ========================= contact-style-3 end ========================= --> */}

      {/* <!-- ========================= clients-logo start ========================= --> */}
      <section class="clients-logo-section pt-100 pb-100">
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <div class="client-logo wow fadeInUp" data-wow-delay=".2s">
                <img src="assets/img/clients/brands.svg" alt="" class="w-100" />
              </div>
            </div>
          </div>
        </div>
      </section>
      {/* <!-- ========================= clients-logo end ========================= --> */}

      {/* <!-- ========================= footer style-4 start ========================= --> */}
      <Footer />
      {/* <!-- ========================= footer style-4 end ========================= --> */}

      {/* <!-- Register Modal --> */}
      <div class="modal fade" id="registerModal" tabindex={-1} aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Créer un compte</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="pills-client-tab" data-bs-toggle="pill" data-bs-target="#pills-client"
                    type="button" role="tab">Client</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="pills-artisan-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-artisan" type="button" role="tab">Artisan</button>
                </li>
              </ul>
              <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-client" role="tabpanel">
                  <form>
                    <div class="mb-3">
                      <label for="clientName" class="form-label">Nom complet</label>
                      <input type="text" class="form-control" id="clientName" required />
                    </div>
                    <div class="mb-3">
                      <label for="clientEmail" class="form-label">Email</label>
                      <input type="email" class="form-control" id="clientEmail" required />
                    </div>
                    <div class="mb-3">
                      <label for="clientPassword" class="form-label">Mot de passe</label>
                      <input type="password" class="form-control" id="clientPassword" required />
                    </div>
                    <div class="mb-3">
                      <label for="clientConfirmPassword" class="form-label">Confirmer le mot de passe</label>
                      <input type="password" class="form-control" id="clientConfirmPassword" required />
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="clientTerms" required />
                      <label class="form-check-label" for="clientTerms">J'accepte les conditions d'utilisation</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Créer un compte client</button>
                  </form>
                </div>
                <div class="tab-pane fade" id="pills-artisan" role="tabpanel">
                  <form>
                    <div class="mb-3">
                      <label for="artisanName" class="form-label">Nom de l'entreprise</label>
                      <input type="text" class="form-control" id="artisanName" required />
                    </div>
                    <div class="mb-3">
                      <label for="artisanEmail" class="form-label">Email professionnel</label>
                      <input type="email" class="form-control" id="artisanEmail" required />
                    </div>
                    <div class="mb-3">
                      <label for="artisanPassword" class="form-label">Mot de passe</label>
                      <input type="password" class="form-control" id="artisanPassword" required />
                    </div>
                    <div class="mb-3">
                      <label for="artisanConfirmPassword" class="form-label">Confirmer le mot de passe</label>
                      <input type="password" class="form-control" id="artisanConfirmPassword" required />
                    </div>
                    <div class="mb-3">
                      <label for="artisanService" class="form-label">Service principal</label>
                      <select class="form-select" id="artisanService" required>
                        <option value="">Sélectionnez un service</option>
                        <option value="plomberie">Plomberie</option>
                        <option value="electricite">Électricité</option>
                        <option value="menuiserie">Menuiserie</option>
                        <option value="peinture">Peinture</option>
                        <option value="jardinage">Jardinage</option>
                      </select>
                    </div>
                    <div class="mb-3 form-check">
                      <input type="checkbox" class="form-check-input" id="artisanTerms" required />
                      <label class="form-check-label" for="artisanTerms">J'accepte les conditions d'utilisation</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Créer un compte artisan</button>
                  </form>
                </div>
              </div>
              <hr />
              <div class="text-center">
                <p>Vous avez déjà un compte ? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                  data-bs-dismiss="modal">Se connecter</a></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </MainLayout>
  )
}
