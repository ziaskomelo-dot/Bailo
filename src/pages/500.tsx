import { FC } from "hono/jsx"
import { MainLayout } from "../layouts/main.js"

export const ErrorPage: FC = () => {
  const meta = {
    title: "500 - Erreur du serveur",
    description: "Une erreur s'est produite sur le serveur.",
    image: "/assets/images/500.png",
  }

  return (
    <MainLayout {...meta}>
      <div class="container">
        <h1>Une erreur s'est produite</h1>
        <p>Veuillez réessayer plus tard.</p>
        <a href="/">Retour à l'accueil</a>
      </div>
    </MainLayout>
  )
}
