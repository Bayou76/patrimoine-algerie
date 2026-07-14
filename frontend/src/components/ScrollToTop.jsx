/**
 * ScrollToTop — remonte en haut de page à chaque changement de route.
 *
 * React Router ne le fait pas tout seul (contrairement à une navigation
 * classique par rechargement de page) : sans ça, cliquer sur une carte
 * "À découvrir" ouvre la nouvelle page à la position de scroll où on était
 * avant, ce qui oblige à remonter manuellement.
 */

import { useEffect } from 'react'
import { useLocation } from 'react-router-dom'

function ScrollToTop() {
  const { pathname } = useLocation()

  useEffect(() => {
    window.scrollTo(0, 0)
  }, [pathname])

  return null
}

export default ScrollToTop
