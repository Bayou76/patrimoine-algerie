<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bienvenue sur Athar</title>
</head>
<body style="margin:0; padding:0; background:#f5ecdc; font-family:'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5ecdc; padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" style="max-width:480px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 2px 12px rgba(15,42,48,0.08);">

          <tr>
            <td style="background:#0f2a30; padding:32px 32px 28px; text-align:center;">
              <div style="font-size:40px; line-height:1;">🏺</div>
              <div style="color:#ffffff; font-size:22px; font-weight:800; margin-top:8px;">Athar</div>
            </td>
          </tr>

          <tr>
            <td style="padding:36px 32px;">
              <h1 style="margin:0 0 16px; font-size:22px; color:#0f2a30; font-weight:800;">
                Bienvenue, {{ $user->name }} !
              </h1>
              <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#374151;">
                Ton compte Athar est prêt. Tu peux dès maintenant explorer les 33 sites du patrimoine algérien,
                enregistrer tes favoris, laisser des avis et construire ton propre itinéraire de voyage.
              </p>
              <div style="text-align:center; margin:28px 0;">
                <a href="{{ env('FRONTEND_URL', 'https://patrimoine-algerie.vercel.app') }}"
                   style="display:inline-block; background:#c1502e; color:#ffffff; text-decoration:none; font-weight:700; font-size:15px; padding:12px 28px; border-radius:999px;">
                  Découvrir Athar
                </a>
              </div>
              <p style="margin:0; font-size:13px; line-height:1.6; color:#6b7280;">
                À très vite,<br>L'équipe Athar
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
