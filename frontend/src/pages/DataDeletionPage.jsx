/**
 * DataDeletionPage — page /suppression-des-donnees.
 *
 * Exigée par Facebook (et Google) : explique comment un utilisateur peut
 * demander la suppression de son compte et de ses données, y compris s'il
 * s'est connecté via Google ou Facebook.
 */

function DataDeletionPage() {
  return (
    <div className="max-w-2xl mx-auto p-6 py-12">
      <h1 className="font-display font-800 text-3xl text-teal-950 mb-6">Suppression des données</h1>

      <div className="text-teal-900/80 leading-relaxed space-y-4">
        <p>
          Si vous souhaitez supprimer votre compte Athar et toutes les données associées (favoris, avis,
          itinéraires, informations de profil), y compris si vous vous êtes inscrit(e) via Google ou
          Facebook, envoyez une demande à l'adresse suivante :
        </p>
        <p>
          <a href="mailto:baya.sebia.dev@outlook.com" className="text-terracotta-600 underline font-600">
            baya.sebia.dev@outlook.com
          </a>
        </p>
        <p>
          Merci d'indiquer l'adresse email associée à votre compte. Votre compte et l'ensemble des données
          qui lui sont liées seront supprimés sous 7 jours ouvrés.
        </p>
      </div>
    </div>
  )
}

export default DataDeletionPage
