<?php
session_start();

include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Magazijnbeheer</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="favicon.png" type="assets/icon.png">
  </head>
  <body class="page-body bg-spotify-dark text-white">
    <!-- Top App Bar -->
    <header class="navbar bg-spotify-dark">
      <div class="container navbar__row">
        <a class="navbar-brand" href="#">
          <span class="brand__icon"></span>
          <span class="brand__text">
            <span class="brand__title">Magazijn Systeem</span>
            <span class="brand__subtitle">Inventaris Beheer</span>
          </span>
        </a>
        <nav class="nav">
          <a href="#" class="nav-link nav-link--active js-nav-link" data-page="dashboard"> Dashboard</a>
          <a href="#" class="nav-link js-nav-link" data-page="zoeken">🔎 Zoeken</a>
          <a href="#" class="nav-link js-nav-link" data-page="verplaatsen"> Verplaatsen</a>
          <a href="#" class="nav-link js-nav-link" data-page="statistieken"> Statistieken</a>
        </nav>
        <div class="live-pill">
          <span class="live-pill__dot"></span>
          <span>Live</span>
        </div>
      </div>
    </header>

    <section class="hero">
      <div class="container hero__inner">
        <h1>Overzicht Magazijn</h1>
        <p class="text-muted">Beheer items, locaties en prestaties in realtime.</p>
      </div>
    </section>

    <main class="container main">
      <!-- DASHBOARD -->
      <section id="page-dashboard" class="page page--active">
        <div class="kpi-grid">
          <article class="card kpi kpi--blue">
            <div class="kpi__row">
              <div>
                <div class="kpi__label">Totaal Items</div>
                <div class="kpi__value" id="kpi-total">0</div>
              </div>
              <div class="kpi__icon">📦</div>
            </div>
          </article>
          <article class="card kpi kpi--green">
            <div class="kpi__row">
              <div>
                <div class="kpi__label">Beschikbaar</div>
                <div class="kpi__value" id="kpi-available">0</div>
              </div>
              <div class="kpi__icon"></div>
            </div>
          </article>
          <article class="card kpi kpi--red">
            <div class="kpi__row">
              <div>
                <div class="kpi__label">Bezet</div>
                <div class="kpi__value" id="kpi-busy">0</div>
              </div>
              <div class="kpi__icon"></div>
            </div>
          </article>
          <article class="card kpi kpi--amber">
            <div class="kpi__row">
              <div>
                <div class="kpi__label">Gereserveerd</div>
                <div class="kpi__value" id="kpi-reserved">0</div>
              </div>
              <div class="kpi__icon"></div>
            </div>
          </article>
        </div>

        <div class="card panel">
          <h2 class="panel__title"> Magazijn Overzicht</h2>
          <div id="warehouse" class="warehouse"></div>
          <div class="legend">
            <span class="legend__item"><span class="legend__dot legend__dot--green"></span> Beschikbaar (1 item)</span>
            <span class="legend__item"><span class="legend__dot legend__dot--red"></span> Vol (2+ items)</span>
            <span class="legend__item"><span class="legend__dot legend__dot--grey"></span> Leeg</span>
          </div>
        </div>
      </section>

      <!-- ZOEKEN -->
      <section id="page-zoeken" class="page is-hidden">
        <div class="card panel">
          <h2 class="panel__title"> Item Zoeken</h2>
          <div class="search">
            <input id="search-input" class="form-control" type="text" placeholder="Zoek op naam of code..." />
          </div>
        </div>
        <div class="grid-two">
          <div class="card panel">
            <h3 class="panel__subtitle">Resultaten</h3>
            <div id="search-results" class="list"></div>
          </div>
          <div class="card panel">
            <h3 class="panel__subtitle">Locatie Details</h3>
            <div id="location-details" class="placeholder">Selecteer een item om de locatie te zien</div>
          </div>
        </div>
      </section>

      <!-- VERPLAATSEN -->
      <section id="page-verplaatsen" class="page is-hidden">
        <div class="grid-two">
          <div class="card panel">
            <h2 class="panel__title"> Items Lijst</h2>
            <div id="move-items" class="list"></div>
          </div>
          <div class="card panel">
            <h2 class="panel__title">Item Verplaatsen</h2>
            <div id="move-details" class="placeholder">Selecteer een item om te verplaatsen</div>
          </div>
        </div>
      </section>

      <!-- STATISTIEKEN -->
      <section id="page-statistieken" class="page is-hidden">
        <div class="card panel">
          <h2 class="panel__title">📊 Statistieken Dashboard</h2>
          <div class="stats">
            <div class="stats__bar">
              <div class="stats__head"><span>Beschikbaar</span><span id="bar-available-label">0 items (0%)</span></div>
              <div class="progress"><div id="bar-available" class="progress__fill progress__fill--green"></div></div>
            </div>
            <div class="stats__bar">
              <div class="stats__head"><span>Bezet</span><span id="bar-busy-label">0 items (0%)</span></div>
              <div class="progress"><div id="bar-busy" class="progress__fill progress__fill--red"></div></div>
            </div>
            <div class="stats__bar">
              <div class="stats__head"><span>Gereserveerd</span><span id="bar-reserved-label">0 items (0%)</span></div>
              <div class="progress"><div id="bar-reserved" class="progress__fill progress__fill--amber"></div></div>
            </div>
          </div>
        </div>

        <div class="grid-two">
          <div class="card panel">
            <h3 class="panel__subtitle">Locatie Bezetting</h3>
            <div id="location-occupancy" class="list list--compact"></div>
          </div>
          <div class="card panel">
            <h3 class="panel__subtitle">Snelle Feiten</h3>
            <div class="facts">
              <div class="fact">
                <div class="fact__title">Totaal Items</div>
                <div id="fact-total" class="fact__value">0</div>
              </div>
              <div class="fact">
                <div class="fact__title">Beschikbaarheid</div>
                <div id="fact-availability" class="fact__value">0%</div>
              </div>
              <div class="fact">
                <div class="fact__title">Bezette Locaties</div>
                <div id="fact-busy-locations" class="fact__value">0</div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="footer bg-spotify-dark">
      <div class="container footer__inner">
        <span class="text-muted">© 2025 Magazijn Systeem</span>
      </div>
    </footer>

    <script src="js/script.js" defer></script>
  </body>
</html>
