/**
 * PrivacyPolicyPage — page /politique-de-confidentialite.
 *
 * Page statique en français uniquement (pas de traduction i18n) : c'est une
 * page légale exigée par Facebook (et Google) pour autoriser la connexion
 * sociale en production, pas un contenu éditorial du site.
 */

function Section({ title, children }) {
  return (
    <div className="mb-8">
      <h2 className="font-display font-700 text-xl text-teal-950 mb-3">{title}</h2>
      <div className="text-teal-900/80 leading-relaxed space-y-3">{children}</div>
    </div>
  )
}

function PrivacyPolicyPage() {
  return (
    <div className="max-w-2xl mx-auto p-6 py-12">
      <h1 className="font-display font-800 text-3xl text-teal-950 mb-2">Politique de confidentialité</h1>
      <p className="text-sm text-teal-900/60 mb-10">Dernière mise à jour : juillet 2026</p>

      <Section title="Qui sommes-nous ?">
        <p>
          Athar est un guide collaboratif du patrimoine culturel, historique et naturel de l'Algérie,
          édité par Baya SEBIA. Cette page explique quelles données sont collectées lors de l'utilisation
          du site et comment elles sont utilisées.
        </p>
      </Section>

      <Section title="Quelles données sont collectées">
        <p>Lors de la création d'un compte (par email ou via Google/Facebook), nous collectons :</p>
        <ul className="list-disc pl-5 space-y-1">
          <li>Votre nom et votre adresse email</li>
          <li>
            Si vous vous connectez via Google ou Facebook : un identifiant technique fourni par ces
            services (jamais votre mot de passe, que nous ne voyons ni ne stockons)
          </li>
        </ul>
        <p>Lors de l'utilisation du site, nous stockons également :</p>
        <ul className="list-disc pl-5 space-y-1">
          <li>Les sites que vous ajoutez en favoris ou marquez comme visités</li>
          <li>Les avis que vous publiez sur les sites</li>
          <li>Les itinéraires que vous créez ou proposez</li>
        </ul>
      </Section>

      <Section title="Pourquoi ces données">
        <p>
          Ces informations servent uniquement à faire fonctionner votre compte (connexion, favoris, avis,
          itinéraires personnalisés) et à vous envoyer un email de bienvenue lors de votre inscription.
          Nous ne vendons ni ne partageons vos données avec des tiers à des fins commerciales ou
          publicitaires.
        </p>
      </Section>

      <Section title="Connexion via Google ou Facebook">
        <p>
          Si vous choisissez de vous connecter via Google ou Facebook, seules les informations de base de
          votre profil (nom, adresse email) nous sont transmises par ces services, avec votre accord
          explicite au moment de la connexion. Nous n'accédons à aucune autre donnée de votre compte
          Google ou Facebook (contacts, publications, photos, etc.).
        </p>
      </Section>

      <Section title="Combien de temps vos données sont conservées">
        <p>
          Vos données sont conservées tant que votre compte existe. Vous pouvez demander la suppression de
          votre compte et de toutes les données associées à tout moment en nous contactant.
        </p>
      </Section>

      <Section title="Vos droits">
        <p>
          Vous pouvez à tout moment demander l'accès, la correction ou la suppression de vos données
          personnelles en nous écrivant à l'adresse ci-dessous.
        </p>
      </Section>

      <Section title="Contact">
        <p>
          Pour toute question concernant cette politique de confidentialité ou vos données personnelles,
          contactez-nous à : <a href="mailto:baya.sebia.dev@outlook.com" className="text-terracotta-600 underline">baya.sebia.dev@outlook.com</a>
        </p>
      </Section>
    </div>
  )
}

export default PrivacyPolicyPage
