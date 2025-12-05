import { FC } from "hono/jsx";

export const Footer: FC = () => {
    return (
  <section id="service" class="feature-section feature-style-5">
    <div class="container">

      <div class="row">
        <div class="col-lg-4 col-md-6">
          <div class="single-feature wow fadeInUp" data-wow-delay=".2s">
            <div class="icon">
              <i class="lni lni-money-protection"></i>
              <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z"
                  fill="#EBF4FF" />
              </svg>
            </div>
            <div class="content">
              <h5>Payement</h5>
              <p>Les modalités proposées par les <span style="color:o;">utilisateurs</span> sont évaluées par la
                plateforme pour votre satisfaction.</p>
            </div>
          </div>
        </div>


        <div class="col-lg-4 col-md-6">
          <div class="single-feature wow fadeInUp" data-wow-delay=".4s">
            <div class="icon">
              <i class="lni lni-wallet"></i>
              <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z"
                  fill="#EBF4FF" />
              </svg>
            </div>
            <div class="content">
              <h5>Portefeuille</h5>
              <p>Ici, les prix des services proposés par nos <span style="color: #2F80ED;">artisans</span> restent en
                cohésion avec vos projets et de vos besoins.</p>
            </div>
          </div>
        </div>

         
        <div class="col-lg-4 col-md-6">
          <div class="single-feature wow fadeInUp" data-wow-delay=".6s">
            <div class="icon">
              <i class="lni lni-agenda"></i>
              <svg width="110" height="72" viewBox="0 0 110 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M110 54.7589C110 85.0014 85.3757 66.2583 55 66.2583C24.6243 66.2583 0 85.0014 0 54.7589C0 24.5164 24.6243 0 55 0C85.3757 0 110 24.5164 110 54.7589Z"
                  fill="#EBF4FF" />
              </svg>
            </div>
            <div class="content">
              <h5>Agenda</h5>
              <p>Les Artisans de <span style="color:#2F80ED">Bailo</span> vous propose des services en fonction de votre
                disponnibité.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
    )
}