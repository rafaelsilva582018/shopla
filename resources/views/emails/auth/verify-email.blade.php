<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirme seu e-mail na Shopla</title>
</head>
<body style="margin:0; padding:0; background:#fff8f5; color:#111827; font-family:Arial, Helvetica, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0;">
        Sua vitrine Shopla esta quase pronta. Confirme seu e-mail para liberar o painel.
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#fff8f5; padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;">
                    <tr>
                        <td align="center" style="padding:8px 0 22px;">
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width:48px; height:48px; border-radius:18px; background:#e1578b; color:#ffffff; font-size:22px; font-weight:900; text-align:center; vertical-align:middle; box-shadow:0 12px 28px rgba(225,87,139,.28);">
                                        S
                                    </td>
                                    <td style="padding-left:12px; text-align:left;">
                                        <div style="font-size:24px; font-weight:900; color:#111827;">Shopla</div>
                                        <div style="font-size:13px; color:#667085;">Vitrine, pedidos e gestao simples</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#ffffff; border:1px solid #f3cddd; border-radius:28px; box-shadow:0 24px 60px rgba(79,18,45,.10); padding:34px;">
                            <div style="display:inline-block; background:#fde8f1; color:#c53771; border-radius:999px; padding:8px 14px; font-size:13px; font-weight:800; margin-bottom:18px;">
                                Sua loja esta quase pronta
                            </div>

                            <h1 style="margin:0; font-size:30px; line-height:1.15; color:#111827; font-weight:900;">
                                Confirme seu e-mail para liberar o painel
                            </h1>

                            <p style="margin:18px 0 0; font-size:16px; line-height:1.7; color:#475467;">
                                Ola, {{ $user->name }}. Clique no botao abaixo para confirmar seu e-mail e continuar criando sua vitrine na Shopla.
                            </p>

                            <p style="margin:12px 0 0; font-size:16px; line-height:1.7; color:#475467;">
                                Depois disso voce ja pode escolher o tema, cadastrar produtos e compartilhar o link da sua loja.
                            </p>

                            <div style="text-align:center; margin:30px 0;">
                                <a href="{{ $verificationUrl }}" style="display:inline-block; background:#e1578b; color:#ffffff; text-decoration:none; border-radius:18px; padding:16px 26px; font-size:16px; font-weight:900; box-shadow:0 14px 32px rgba(225,87,139,.26);">
                                    Confirmar meu e-mail
                                </a>
                            </div>

                            <div style="background:#fff8f5; border:1px solid #f3cddd; border-radius:20px; padding:16px; margin-top:20px;">
                                <p style="margin:0; font-size:14px; line-height:1.6; color:#7a425d;">
                                    <strong>Nao encontrou o e-mail?</strong> Verifique Spam, Lixo eletronico ou Promocoes. Se estiver la, marque como "Nao e spam" para receber os proximos avisos da Shopla.
                                </p>
                            </div>

                            <p style="margin:24px 0 0; font-size:14px; line-height:1.7; color:#667085;">
                                Se voce nao criou uma conta na Shopla, pode ignorar este e-mail com tranquilidade.
                            </p>

                            <div style="height:1px; background:#f3cddd; margin:26px 0;"></div>

                            <p style="margin:0; font-size:12px; line-height:1.7; color:#98a2b3;">
                                Se o botao nao funcionar, copie e cole este link no navegador:
                            </p>
                            <p style="margin:8px 0 0; font-size:12px; line-height:1.7; word-break:break-all;">
                                <a href="{{ $verificationUrl }}" style="color:#c53771;">{{ $verificationUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:22px 10px 0; color:#98a2b3; font-size:12px; line-height:1.6;">
                            Shopla - vitrine online para vender pelo WhatsApp.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
