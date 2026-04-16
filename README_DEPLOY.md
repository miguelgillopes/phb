Guia rápido para colocar este site no ar com domínio grátis

Resumo:
- Hosting PHP grátis sugerido: 000webhost (https://www.000webhost.com) ou InfinityFree (https://infinityfree.net)
- Domínio grátis sugerido: Freenom (https://www.freenom.com) — extensões .tk .ml .ga .cf .gq
- Opcional: Cloudflare (DNS + SSL grátis) para HTTPS e cache

Passos:

1) Escolher domínio grátis (Freenom)
- Vai a Freenom e regista o domínio grátis (.tk/.ml/.ga/.cf/.gq).
- Na configuração de DNS, podes apontar para os nameservers do host (se o hosting pedir) ou usar os nameservers do Freenom e apontar A/CNAME mais tarde.

2) Criar conta no hosting PHP grátis
- Regista-te em 000webhost ou InfinityFree.
- Cria um novo site/conta e escolhe usar um domínio personalizado (adiciona o teu domínio Freenom) ou usa o subdomínio do host para testar.
- Anota as credenciais FTP (host, user, password) e o diretório remoto (normalmente `public_html` ou `htdocs`).

3) Ajustes no código (já aplicados neste repositório)
- Os scripts `token.php` e `enviar.php` agora usam as pastas locais `_tokens/` e `_rl/` para armazenar tokens e rate-limit, o que evita problemas em hosts sem permissão sobre `sys_get_temp_dir()`.
- Verifica permissões: as pastas `_tokens` e `_rl` devem ser graváveis pelo servidor (chmod 755/775 se necessário).
- O ficheiro `_tokens/.htaccess` foi adicionado para bloquear acesso HTTP directo (funciona em Apache).

4) Upload dos ficheiros
- Compacta ou envia todos os ficheiros para a pasta pública (`public_html` / `htdocs`) do teu alojamento via FTP ou File Manager.
- Certifica-te de que `_tokens` e `_rl` estão presentes e graváveis.

5) Testar formulário e email
- O envio usa `mail()` do PHP. Muitos hosts grátis permitem envio de email mas limitam/alteram o remetente.
- Se os e-mails não sairem, considera usar um serviço SMTP (Mailgun, SendGrid) e adaptar `enviar.php` para usar SMTP via `PHPMailer`.

6) Configurar domínio e SSL
Opção A — Usar nomes do host (mais simples):
- No painel do host, adiciona/associa o teu domínio Freenom ao site.
- O host normalmente aceita e propaga o domínio e habilita SSL automático (Let's Encrypt).

Opção B — Usar Cloudflare (recomendado para HTTPS, caching e DNS):
- Regista-te no Cloudflare e adiciona o teu domínio.
- Cloudflare pedirá para mudares os nameservers no painel do Freenom para os do Cloudflare.
- No Cloudflare, adiciona um `A` record apontando para o IP do teu hosting (ou CNAME conforme instruções do host).
- Activa `Always Use HTTPS` e `Automatic HTTPS Rewrites` nas regras de SSL do Cloudflare.

7) Verificação final
- Acede ao domínio público e testa o formulário de contacto (preenche e envia).
- Se receberes um erro 403 relacionado com token, recarrega a página para obter novo token.
- Se o e-mail não for entregue, verifica logs do host ou configura SMTP.

Notas e recomendações:
- Freenom tem algumas limitações e políticas de renovação; para um projecto profissional, considera comprar um domínio `.pt` ou `.com` quando possível.
- Se preferires, posso: 1) preparar um ZIP com o site pronto para upload; 2) adaptar `enviar.php` para usar `PHPMailer`+SMTP (necessita credenciais SMTP); 3) guiar-te passo-a-passo na configuração do Freenom + 000webhost + Cloudflare.

Se queres, digo-te qual a próxima ação que faço (preparar ZIP, ajustar SMTP, ou só dar instruções passo-a-passo para fazeres tu).