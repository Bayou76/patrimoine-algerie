/**
 * wikimediaCredit — construit le lien vers la page Wikimedia Commons d'une
 * image, où figurent l'auteur et la licence exacts (CC BY-SA le plus
 * souvent, qui impose de créditer l'auteur).
 *
 * On ne stocke pas l'auteur en base : Athar n'affiche pas de nom d'auteur
 * inventé, il renvoie vers la page source qui, elle, l'affiche toujours de
 * façon fiable et à jour — c'est la pratique standard pour respecter la
 * licence sans avoir à maintenir une base de crédits.
 *
 * Reconstruit l'URL à partir du chemin de l'image (le nom de fichier est
 * présent dans l'URL upload.wikimedia.org, qu'elle soit en taille "thumb"
 * ou originale) :
 *   .../commons/thumb/x/xx/Nom_du_fichier.jpg/500px-Nom_du_fichier.jpg
 *   .../commons/x/xx/Nom_du_fichier.jpg
 * → https://commons.wikimedia.org/wiki/File:Nom_du_fichier.jpg
 */
export function getWikimediaCreditUrl(imagePath) {
  if (!imagePath || !imagePath.includes('upload.wikimedia.org')) return null

  try {
    const url = new URL(imagePath)
    const segments = url.pathname.split('/').filter(Boolean)
    const isThumb = segments.includes('thumb')

    // En taille "thumb", le vrai nom de fichier est l'avant-dernier segment
    // (le dernier étant "500px-Nom_du_fichier.jpg" par ex.) ; sinon c'est
    // le tout dernier segment.
    const filename = isThumb ? segments[segments.length - 2] : segments[segments.length - 1]
    if (!filename) return null

    return `https://commons.wikimedia.org/wiki/File:${filename}`
  } catch {
    return null
  }
}
