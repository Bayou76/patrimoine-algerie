/**
 * ToastContext — affiche des notifications flottantes éphémères (« toasts »).
 *
 * Usage :
 *   const { showToast } = useToast()
 *   showToast('Ajouté aux favoris', '❤️')
 *
 * Le toast apparaît en haut de l'écran avec une animation slide-in,
 * puis disparaît après 2.2s. Un seul toast à la fois : un nouveau appel
 * annule l'ancien (reset du timer).
 */

import { createContext, useCallback, useContext, useState } from 'react'

const ToastContext = createContext(null)

export function ToastProvider({ children }) {
  const [toast, setToast] = useState(null)

  // useCallback : mémorise la fonction pour qu'elle ne change pas de référence
  // entre les rendus (utile si elle est passée à d'autres useEffect en deps).
  const showToast = useCallback((message, icon = '✨', duration = 2200) => {
    // id = Date.now() : force React à recréer le div avec key={id}, ce qui
    // relance l'animation même si on rappelle showToast plusieurs fois.
    setToast({ message, icon, id: Date.now() })
    // Astuce : on stocke le timer sur la fonction elle-même pour pouvoir
    // annuler l'ancien avant d'en démarrer un nouveau.
    window.clearTimeout(showToast._t)
    showToast._t = window.setTimeout(() => setToast(null), duration)
  }, [])

  return (
    <ToastContext.Provider value={{ showToast }}>
      {children}
      {toast && (
        <div
          key={toast.id}
          className="fixed top-20 left-1/2 z-[2000] bg-teal-950 text-white shadow-xl rounded-full px-5 py-2.5 text-sm font-600 flex items-center gap-2 animate-toast-in"
          style={{ transform: 'translateX(-50%)' }}
        >
          <span className="text-lg">{toast.icon}</span>
          {toast.message}
        </div>
      )}
    </ToastContext.Provider>
  )
}

export function useToast() {
  return useContext(ToastContext) ?? { showToast: () => {} }
}
