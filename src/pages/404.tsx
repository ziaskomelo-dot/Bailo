import { FC } from "hono/jsx"
import { MainLayout } from "../layouts/main.js"

export const NotFoundPage: FC = () => {
  const meta = {
    title: "404 - Page non trouvée",
    description: "La page que vous recherchez est introuvable.",
    image: "/assets/images/404.png",
  }

  return (
    <MainLayout {...meta}>
      <div class="container">
        <h1>404 - Page non trouvée</h1>
        <p>La page que vous recherchez est introuvable.</p>
        <a href="/">Retour à l'accueil</a>
      </div>
    </MainLayout>
  )
}
