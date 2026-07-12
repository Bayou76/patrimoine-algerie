import { useEffect, useRef, useState } from 'react'

/**
 * Reveal — enveloppe un élément et le fait apparaître en fondu+slide-up
 * quand il entre dans le viewport pendant le scroll.
 *
 * Fonctionnement :
 *   - IntersectionObserver observe l'élément et détecte le moment où il devient
 *     visible à l'écran (au moins 12% dans le viewport → threshold: 0.12).
 *   - À ce moment, on passe visible à true.
 *   - Le CSS anime la transition (opacity 0 → 100, translate-y-6 → 0).
 *   - observer.disconnect() : on n'observe plus après la première apparition
 *     (on ne veut pas réanimer si l'utilisateur remonte).
 *
 * Le `delay` permet un effet cascade dans une grille : passer 0, 60, 120…
 * pour que les cartes apparaissent en rafale plutôt que d'un coup.
 */
function Reveal({ children, delay = 0, className = '' }) {
  const ref = useRef(null) // référence au DOM pour l'observer
  const [visible, setVisible] = useState(false)

  useEffect(() => {
    const el = ref.current
    if (!el) return

    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setVisible(true)
          observer.disconnect() // Une seule animation, définitive
        }
      },
      { threshold: 0.12 },
    )

    observer.observe(el)
    // Cleanup si le composant est démonté avant intersection.
    return () => observer.disconnect()
  }, [])

  return (
    <div
      ref={ref}
      className={`${className} transition-all duration-700 ease-out ${
        visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'
      }`}
      style={{ transitionDelay: visible ? `${delay}ms` : '0ms' }}
    >
      {children}
    </div>
  )
}

export default Reveal
