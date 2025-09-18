<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Inline script to detect system dark mode preference and apply it immediately --}}
  <script>
      (function() {
          const appearance = '{{ $appearance ?? "system" }}';

          if (appearance === 'system') {
              const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

              if (prefersDark) {
                  document.documentElement.classList.add('dark');
              }
          }
      })();
  </script>

  {{-- Inline style to set the HTML background color based on our theme in app.css --}}
  <style>
      html {
          background-color: oklch(1 0 0);
      }

      html.dark {
          background-color: oklch(0.145 0 0);
      }
  </style>

  <link rel="icon" href="/favicon.ico" type="image/x-icon">

  <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon/favicon-16x16.png">
  <link rel="apple-touch-icon" href="/img/favicon/apple-touch-icon.png">

  <link rel="manifest" href="/site.webmanifest">

  <link rel="preconnect" href="https://fonts.bunny.net" />
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

  {{--  Google fonts--}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&display=swap" rel="stylesheet">

  @routes
  @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
  @inertiaHead


  <!-- Yandex.Metrika counter -->
  <script type="text/javascript">
      (function(m, e, t, r, i, k, a) {
          m[i] = m[i] || function() {
              (m[i].a = m[i].a || []).push(arguments);
          };
          m[i].l = 1 * new Date();
          for (var j = 0; j < document.scripts.length; j++) {
              if (document.scripts[j].src === r) {
                  return;
              }
          }
          k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a);
      })(window, document, 'script', 'https://mc.yandex.ru/metrika/tag.js?id=103954739', 'ym');

      ym(103954739, 'init', {
          ssr: true,
          webvisor: true,
          clickmap: true,
          ecommerce: 'dataLayer',
          accurateTrackBounce: true,
          trackLinks: true
      });
  </script>
  <noscript>
    <div><img src="https://mc.yandex.ru/watch/103954739" style="position:absolute; left:-9999px;" alt="" /></div>
  </noscript>
  <!-- /Yandex.Metrika counter -->
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">

@inertia
</body>
</html>
