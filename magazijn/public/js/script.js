// ---------- Demo Data ---------- DEMO MENSEN DEMO
const state = {
  items: [
    { id: 'LP01', name: 'Laptop Dell', location: 'A1', status: 'beschikbaar' },
    { id: 'ST01', name: 'Bureaustoel', location: 'B1', status: 'bezet' },
    { id: 'MN01', name: 'Monitor', location: 'A2', status: 'beschikbaar' },
    { id: 'KB01', name: 'Toetsenbord', location: 'B2', status: 'gereserveerd' }
  ],
  locations: ['A1','A2','B1','B2','C1','C2']
};

function mapLocationToItems(){
  const map = new Map();
  state.locations.forEach(l => map.set(l, []));
  state.items.forEach(it => map.get(it.location)?.push(it));
  return map;
}

// ---------- Navigation ----------
const pages = {
  dashboard: document.getElementById('page-dashboard'),
  zoeken: document.getElementById('page-zoeken'),
  verplaatsen: document.getElementById('page-verplaatsen'),
  statistieken: document.getElementById('page-statistieken')
};

const navLinks = document.querySelectorAll('.js-nav-link');
navLinks.forEach(link => link.addEventListener('click', (e)=>{
  e.preventDefault();
  setActivePage(link.dataset.page);
}));

function setActivePage(page){
  navLinks.forEach(l => l.classList.toggle('nav-link--active', l.dataset.page === page));
  Object.entries(pages).forEach(([key,el]) => el.classList.toggle('is-hidden', key !== page));
  if(page==='dashboard') renderDashboard();
  if(page==='zoeken') setupSearch();
  if(page==='verplaatsen') renderMove();
  if(page==='statistieken') renderStats();
}

// ---------- Dashboard ----------
function renderDashboard(){
  const map = mapLocationToItems();
  const total = state.items.length;
  const available = state.items.filter(i=>i.status==='beschikbaar').length;
  const busy = state.items.filter(i=>i.status==='bezet').length;
  const reserved = state.items.filter(i=>i.status==='gereserveerd').length;

  document.getElementById('kpi-total').textContent = total;
  document.getElementById('kpi-available').textContent = available;
  document.getElementById('kpi-busy').textContent = busy;
  document.getElementById('kpi-reserved').textContent = reserved;

  const warehouse = document.getElementById('warehouse');
  warehouse.innerHTML = '';
  ;['A','B','C'].forEach(zone => {
    const title = document.createElement('div');
    title.className = 'zone-title';
    title.textContent = `Zone ${zone}`;

    const slots = document.createElement('div');
    slots.className = 'slots';

    ;['1','2'].forEach(n => {
      const code = `${zone}${n}`;
      const items = map.get(code) || [];
      const dot = items.length === 0 ? 'legend__dot--grey' : items.length === 1 ? 'legend__dot--green' : 'legend__dot--red';

      const card = document.createElement('div');
      card.className = 'slot';
      card.innerHTML = `
        <span class="legend__dot ${dot}"></span>
        <div class="slot__code">${code}</div>
        <div class="slot__count">${items.length} items</div>`;
      slots.appendChild(card);
    });

    warehouse.appendChild(title);
    warehouse.appendChild(slots);
  });
}

// ---------- Search ----------
function setupSearch(){
  const input = document.getElementById('search-input');
  const list = document.getElementById('search-results');
  const details = document.getElementById('location-details');

  function renderList(q=''){
    const query = q.trim().toLowerCase();
    const results = state.items.filter(i => i.name.toLowerCase().includes(query) || i.id.toLowerCase().includes(query));
    list.innerHTML = '';

    if(results.length===0){
      list.innerHTML = '<div class="placeholder">Begin met typen om te zoeken</div>';
      details.textContent = 'Selecteer een item om de locatie te zien';
      return;
    }

    results.forEach(it => {
      const row = document.createElement('div');
      row.className = 'list__item';
      const badge = it.status==='beschikbaar' ? 'badge--green' : it.status==='bezet' ? 'badge--red' : 'badge--amber';
      row.innerHTML = `
        <div>
          <div class="list__title">${it.name}</div>
          <div class="list__meta">Code: ${it.id}</div>
        </div>
        <span class="badge ${badge}">${it.status}</span>`;
      row.addEventListener('click', ()=>{
        details.innerHTML = `
          <div class="list">
            <div class="list__item">
              <div>
                <div class="list__title">${it.name}</div>
                <div class="list__meta">Code: ${it.id}</div>
              </div>
              <span class="badge ${badge}">${it.status}</span>
            </div>
            <div class="list__item"><div>Huidige locatie: <strong>${it.location}</strong></div></div>
          </div>`;
      });
      list.appendChild(row);
    });
  }

  input.oninput = (e)=> renderList(e.target.value);
  renderList(input.value||'');
}

// ---------- Move ----------
function renderMove(){
  const list = document.getElementById('move-items');
  const panel = document.getElementById('move-details');
  list.innerHTML = '';

  state.items.forEach(it => {
    const badge = it.status==='beschikbaar' ? 'badge--green' : it.status==='bezet' ? 'badge--red' : 'badge--amber';
    const row = document.createElement('div');
    row.className = 'list__item';
    row.innerHTML = `
      <div>
        <div class="list__title">${it.name}</div>
        <div class="list__meta">Code: ${it.id} · Huidige locatie: ${it.location}</div>
      </div>
      <span class="badge ${badge}">${it.status}</span>`;
    row.addEventListener('click', ()=>selectForMove(it));
    list.appendChild(row);
  });

  function selectForMove(item){
    panel.classList.remove('placeholder');
    panel.innerHTML = `
      <div class="form">
        <div><strong>${item.name}</strong> <span class="list__meta">(Code: ${item.id})</span></div>
        <div class="list__meta">Huidige locatie: <strong>${item.location}</strong></div>
        <label for="dest">Nieuwe locatie</label>
        <select id="dest" class="select">${state.locations.map(l=>`<option value="${l}">${l}</option>`).join('')}</select>
        <button class="button button--primary" id="do-move">Verplaatsen</button>
      </div>`;

    const select = panel.querySelector('#dest');
    select.value = item.location;
    panel.querySelector('#do-move').onclick = ()=>{
      const dest = select.value;
      if(dest===item.location){alert('Kies een andere locatie.');return}
      item.location = dest;
      renderDashboard();
      renderMove();
      renderStats();
      setActivePage('verplaatsen');
    };
  }
}

// ---------- Stats ----------
function renderStats(){
  const total = state.items.length;
  const available = state.items.filter(i=>i.status==='beschikbaar').length;
  const busy = state.items.filter(i=>i.status==='bezet').length;
  const reserved = state.items.filter(i=>i.status==='gereserveerd').length;
  const pct = (n)=> total===0 ? 0 : Math.round((n/total)*100);

  const av=pct(available), bz=pct(busy), rs=pct(reserved);
  document.getElementById('bar-available').style.width = av+'%';
  document.getElementById('bar-busy').style.width = bz+'%';
  document.getElementById('bar-reserved').style.width = rs+'%';
  document.getElementById('bar-available-label').textContent = `${available} items (${av}%)`;
  document.getElementById('bar-busy-label').textContent = `${busy} items (${bz}%)`;
  document.getElementById('bar-reserved-label').textContent = `${reserved} items (${rs}%)`;

  document.getElementById('fact-total').textContent = total;
  document.getElementById('fact-availability').textContent = av+'%';
  const map = mapLocationToItems();
  const busyLocations = [...map.values()].filter(a=>a.length>0).length;
  document.getElementById('fact-busy-locations').textContent = busyLocations;

  const occ = document.getElementById('location-occupancy');
  occ.innerHTML = '';
  [...map.entries()].sort().forEach(([code, arr]) => {
    const row = document.createElement('div');
    row.className = 'list__item';
    row.innerHTML = `<div>${code}</div><span class="pill">${arr.length}</span>`;
    occ.appendChild(row);
  });
}

// Init
renderDashboard();
setActivePage('dashboard');
