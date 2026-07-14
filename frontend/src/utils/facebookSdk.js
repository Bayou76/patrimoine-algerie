/**
 * Chargement paresseux du SDK JS Facebook.
 *
 * Le SDK attend une fonction globale `window.fbAsyncInit` appelée dès qu'il
 * est prêt, et injecte lui-même son script dans le DOM. loadFacebookSdk()
 * encapsule tout ça dans une Promise réutilisable (appelée plusieurs fois,
 * elle ne charge le script qu'une seule fois).
 */

let sdkPromise = null

export function loadFacebookSdk() {
  if (sdkPromise) return sdkPromise

  sdkPromise = new Promise((resolve) => {
    window.fbAsyncInit = function () {
      window.FB.init({
        appId: import.meta.env.VITE_FACEBOOK_APP_ID,
        cookie: true,
        xfbml: false,
        version: 'v21.0',
      })
      resolve(window.FB)
    }

    const script = document.createElement('script')
    script.src = 'https://connect.facebook.net/fr_FR/sdk.js'
    script.async = true
    script.defer = true
    document.body.appendChild(script)
  })

  return sdkPromise
}
