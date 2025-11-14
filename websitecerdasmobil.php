<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Smart Car Repair Assistant — Anandito</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { background-image:url('https://static.tek.id/2023/06/11/60527/toyota-kenalkan-mobil-balap-berbasis-hidrogen-aEE6WsWyVd.jpg'); color: white; background-size: cover; }
    .glass { background: black; backdrop-filter: blur(6px); box-shadow: 0px 5px 10px yellow; }
    .tag { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.04); box-shadow: 0px 5px 10px red; }
    .score { color: #94f9c4 }
    pre { white-space: pre-wrap; }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
  <div class="max-w-4xl w-full glass rounded-2xl p-6 shadow-2xl border border-gray-800">
    <header class="flex items-center justify-between gap-4 mb-4">
      <div>
        <h1 class="text-2xl font-semibold">Smart Car Repair Assistant</h1>
        <p class="text-sm text-gray-300">Website cerdas untuk mendiagnosa masalah & memberi rekomendasi perbaikan mobil — tema gelap, responsif, compact.</p>
      </div>
      <div class="text-right">
        <span class="text-xs text-gray-400">by Anandito</span>
      </div>
    </header>

    <main class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <section class="md:col-span-2">
        <label class="block text-sm text-gray-300 mb-2">Deskripsikan masalah kendaraanmu (contoh: "mobil susah dinyalakan, lampu dashboard menyala")</label>
        <div class="flex gap-2">
          <input id="query" type="text" placeholder="Masukkan gejala / keluhan..." class="flex-1 rounded-lg p-3 bg-transparent border border-gray-700 focus:outline-none" />
          <button id="askBtn" class="px-4 py-2 rounded-lg bg-green-500 text-black font-semibold">Tanyakan</button>
        </div>

        <div id="results" class="mt-4 space-y-3"></div>
      </section>

      <aside class="p-4 rounded-xl border border-gray-800">
        <h3 class="text-sm text-gray-300 mb-2">Statistik & Kontrol</h3>
        <div class="text-xs text-gray-400 mb-2">Dataset internal: <span id="docCount">0</span> entri</div>
        <div class="text-xs text-gray-400 mb-2">Vocabulary: <span id="vocabCount">0</span> kata</div>
        <button id="exportBtn" class="w-full py-2 rounded-lg tag text-sm">Export dataset (JSON)</button>
        <button id="resetBtn" class="w-full mt-2 py-2 rounded-lg tag text-sm">Reset query</button>

        <hr class="my-3 border-gray-800" />
        <div>
          <h4 class="text-xs text-gray-300">Tips cepat</h4>
          <ul class="text-xs text-gray-400 mt-2 space-y-1">
            <li>Jelaskan gejala utama, bunyi, bau, perilaku saat gas ditambah.</li>
            <li>Sebutkan lampu indikator yang menyala (check engine, ABS, oil).</li>
            <li>Berikan kondisi: saat dingin/panas, berjalan/stasioner.</li>
          </ul>
        </div>
      </aside>
    </main>

    <footer class="mt-6 text-xs text-gray-500">Built-in lightweight NLP: tokenizing, stopword removal, stemming sederhana, TF–IDF + cosine similarity. Kamu bisa memperluas dataset di file JS.</footer>
  </div>

  <script>
  // ----------------------- Dataset ---------------------------------
  // Koleksi masalah mobil (judul, gejala, solusi singkat)
  const DATA = [
{id:1, title: 'Aki lemah / baterai soak', symptoms: 'mobil tidak bisa distarter, klik saat start, lampu redup', solution: 'Periksa terminal aki, coba jump-start, ganti aki jika tidak bisa mengisi daya.'},
{id:2, title: 'Starter bermasalah', symptoms: 'ada bunyi klik keras saat menyalakan, mesin tidak berputar', solution: 'Periksa kondisi starter, sambungan kabel, dan solenoid. Ganti starter jika rusak.'},
{id:3, title: 'Alternator rusak', symptoms: 'lampu dashboard berkedip, aki cepat tekor, wiper/AC melemah saat mesin hidup', solution: 'Periksa tegangan pengisian alternator, ganti alternator atau regulator jika perlu.'},
{id:4, title: 'Busi kotor atau aus', symptoms: 'mesin tersendat, sulit hidup, konsumsi BBM meningkat, misfire', solution: 'Ganti busi atau bersihkan. Periksa koil pengapian.'},
{id:5, title: 'Fuel pump / pompa bensin lemah', symptoms: 'mobil tersendat terutama saat akselerasi, mogok tiba-tiba', solution: 'Periksa tekanan bahan bakar, filter bensin, ganti pompa jika tekanan rendah.'},
{id:6, title: 'Filter bahan bakar tersumbat', symptoms: 'penurunan tenaga, tersendat saat beban tinggi', solution: 'Ganti filter bahan bakar.'},
{id:7, title: 'Radiator bocor / kebocoran coolant', symptoms: 'mesin cepat panas, ada cairan di bawah mobil, uap dari kap mesin', solution: 'Periksa kebocoran, perbaiki atau ganti radiator, tambahkan coolant.'},
{id:8, title: 'Thermostat macet', symptoms: 'mesin terlalu cepat panas atau terlalu lama mencapai suhu kerja', solution: 'Ganti thermostat.'},
{id:9, title: 'Kompresor AC rusak', symptoms: 'AC tidak dingin, ada bunyi saat AC aktif', solution: 'Periksa kompresor dan refrigerant, perbaiki kebocoran, isi ulang refrigerant.'},
{id:10, title: 'Kampas rem tipis', symptoms: 'rem menyipit, bunyi gesekan, jarak pengereman panjang', solution: 'Ganti kampas rem, periksa cakram/rotor.'},
{id:11, title: 'Minyak rem rendah / bocor', symptoms: 'rem terasa spongy, lampu rem menyala', solution: 'Periksa kebocoran sistem rem, isi minyak rem, ganti bagian bocor.'},
{id:12, title: 'Transmisi slip (OTOMATIS)', symptoms: 'putaran mesin naik tapi percepatan lambat, bau terbakar', solution: 'Periksa level & kualitas oli transmisi, lakukan servis atau perbaiki kopling transmisi.'},
{id:13, title: 'Kopling aus (MANUAL)', symptoms: 'putaran mesin naik tanpa akselerasi, kesulitan pindah gigi', solution: 'Ganti kampas kopling dan periksa pressure plate.'},
{id:14, title: 'Timing belt aus / putus', symptoms: 'mesin mendadak mati, bunyi keras dari mesin', solution: 'Ganti timing belt sesuai interval service, segera periksa jika putus.'},
{id:15, title: 'Bocor oli mesin', symptoms: 'noda oli di bawah mobil, oli turun cepat', solution: 'Periksa seal, gasket, paking, perbaiki dan ganti bagian yang bocor.'},
{id:16, title: 'Oil pressure rendah', symptoms: 'lampu tekanan oli menyala, oli mesin turun', solution: 'Periksa level oli, pompa oli, sensor tekanan oli.'},
{id:17, title: 'Sensor oksigen / O2 rusak', symptoms: 'consumption naik, check engine menyala, performa jelek', solution: 'Ganti sensor O2 dan reset ECU.'},
{id:18, title: 'Katalisator tersumbat', symptoms: 'tenaga berkurang drastis, knalpot panas', solution: 'Periksa backpressure, ganti catalytic converter jika tersumbat.'},
{id:19, title: 'Mesin knock / ngelitik', symptoms: 'bunyi ketukan pada mesin terutama saat akselerasi', solution: 'Periksa oktan bahan bakar, injeksi, kompresi mesin, sesuaikan timing.'},
{id:20, title: 'Getaran pada kecepatan tertentu', symptoms: 'getaran di setir atau bodi pada kecepatan tertentu', solution: 'Periksa keseimbangan roda, ban tidak rata, bearing roda, dan suspensi.'},
{id:21, title: 'Ban aus tidak merata', symptoms: 'tarikan ke samping, keausan ban tidak normal', solution: 'Lakukan spooring & balancing, periksa tekanan ban dan suspensi.'},
{id:22, title: 'Power steering bocor', symptoms: 'sulit kemudi, cairan power steering berkurang', solution: 'Periksa selang & pompa, perbaiki kebocoran dan isi ulang cairan.'},
{id:23, title: 'AC berbau tidak sedap', symptoms: 'bau saat AC dinyalakan', solution: 'Bersihkan evaporator, ganti filter kabin, semprot disinfektan AC.'},
{id:24, title: 'Lampu redup / kelistrikan lemah', symptoms: 'lampu redup, starter lemah, fuse putus', solution: 'Periksa alternator, kabel ground, fuse, dan kondisi baterai.'},
{id:25, title: 'Sensor MAF/MAF kotor', symptoms: 'stuck idle, akselerasi tersendat', solution: 'Bersihkan sensor MAF dengan cleaner khusus.'},
{id:26, title: 'Check engine lamp menyala', symptoms: 'lampu CEL aktif, performa turun', solution: 'Scan kode error OBD-II dan perbaiki sesuai kode.'},
{id:27, title: 'Mesin mati saat berhenti (stalling)', symptoms: 'mesin mati saat idle, susah hidup kembali', solution: 'Periksa idle control valve, injeksi, dan sistem bahan bakar.'},
{id:28, title: 'Rem mengunci (ABS non-aktif)', symptoms: 'rem terkunci, ABS lamp menyala', solution: 'Periksa sensor ABS, hydraulic unit, dan performa rem.'},
{id:29, title: 'Bunyi aneh suspensi', symptoms: 'ketukan saat melewati polisi tidur, bunyi dari shock', solution: 'Periksa shock absorber, mounting, dan bushing suspensi.'},
{id:30, title: 'Knalpot bocor', symptoms: 'suara lebih bising, asap kecil dari area knalpot', solution: 'Perbaiki sambungan knalpot atau ganti pipa/segment yang bocor.'}
   ];  


  // ----------------------- NLP Utilities ----------------------------
  const STOPWORDS = new Set(['yang','dan','di','ke','dari','pada','untuk','dengan','sebagai','ada','itu','atau','saat','tidak','nya','karena','karena','akan','jika','tetapi','lebih','kurang','saat','agar','jadi']);

  function normalize(text){
    return text.toLowerCase().replace(/[\.,\/#!$%\^&\*;:{}=\-_`~()\?\"]/g,' ').replace(/\s+/g,' ').trim();
  }

  function tokenize(text){
    return normalize(text).split(' ').filter(w=>w && !STOPWORDS.has(w));
  }

  function stemIndo(token){
    // very lightweight Indonesian suffix stripper (not full stemmer)
    return token.replace(/(lah|kah|ku|mu|nya)$/,'').replace(/(kan|i|an)$/,'');
  }

  function preprocess(text){
    return tokenize(text).map(stemIndo);
  }

  // Build vocabulary and document TF-IDF
  let vocab = {}; let vocabIndex = 0;
  const docs = DATA.map(d => {
    const text = (d.title + ' ' + d.symptoms + ' ' + d.solution);
    const terms = preprocess(text);
    const freqs = {};
    terms.forEach(t=> freqs[t] = (freqs[t]||0)+1);
    return {id:d.id, title:d.title, terms, freqs};
  });

  // populate vocab
  docs.forEach(doc=>{
    Object.keys(doc.freqs).forEach(term=>{
      if(!(term in vocab)){ vocab[term]=vocabIndex++; }
    })
  });

  // IDF
  const N = docs.length;
  const df = Array(vocabIndex).fill(0);
  docs.forEach(doc=>{
    const seen = new Set(Object.keys(doc.freqs));
    seen.forEach(t => { df[vocab[t]] += 1; });
  });
  const idf = df.map(x => Math.log((N+1)/(x+1)) + 1);

  // compute TF-IDF vectors
  docs.forEach(doc=>{
    const vec = Array(vocabIndex).fill(0);
    Object.entries(doc.freqs).forEach(([t,f])=>{ vec[vocab[t]] = f * idf[vocab[t]]; });
    // normalize
    const mag = Math.sqrt(vec.reduce((s,v)=>s+v*v,0)) || 1;
    doc.vector = vec.map(v=>v/mag);
  });

  // expose counts in UI
  document.addEventListener('DOMContentLoaded', ()=>{
    document.getElementById('docCount').innerText = DATA.length;
    document.getElementById('vocabCount').innerText = vocabIndex;
  });

  // Cosine similarity
  function cosineSim(v1, v2){
    let s = 0; for(let i=0;i<v1.length;i++) s += (v1[i]||0)*(v2[i]||0); return s;
  }

  function queryToVector(q){
    const terms = preprocess(q);
    const freqs = {};
    terms.forEach(t=> freqs[t] = (freqs[t]||0)+1);
    const vec = Array(vocabIndex).fill(0);
    Object.entries(freqs).forEach(([t,f])=>{
      if(t in vocab){ vec[vocab[t]] = f * idf[vocab[t]]; }
    });
    const mag = Math.sqrt(vec.reduce((s,v)=>s+v*v,0)) || 1;
    return vec.map(v=>v/mag);
  }

  // rank docs
  function search(query, topK=5){
    const qv = queryToVector(query);
    const scores = docs.map(d=> ({id:d.id, title:d.title, score: cosineSim(qv, d.vector)}));
    scores.sort((a,b)=>b.score-a.score);
    return scores.slice(0, topK).filter(s=>s.score>0);
  }

  // render results
  const resultsEl = document.getElementById('results');
  function renderResults(query){
    resultsEl.innerHTML = '';
    if(!query || !query.trim()){ resultsEl.innerHTML = '<div class="text-sm text-gray-400">Masukkan keluhan untuk melihat rekomendasi.</div>'; return; }
    const ranked = search(query, 5);
    if(ranked.length===0){ resultsEl.innerHTML = '<div class="text-sm text-yellow-300">Maaf, tidak menemukan kecocokan yang kuat. Coba gunakan kata lain atau jelaskan gejala lebih detail.</div>'; return; }
    ranked.forEach(r=>{
      const doc = DATA.find(d=>d.id===r.id);
      const card = document.createElement('div'); card.className = 'p-3 rounded-lg border border-gray-800';
      card.innerHTML = `<div class=\"flex justify-between items-start\">\n        <div>\n          <div class=\"text-sm font-semibold\">${escapeHtml(doc.title)}</div>\n          <div class=\"text-xs text-gray-400 mt-1\"><strong>Gejala:</strong> ${escapeHtml(doc.symptoms)}</div>\n        </div>\n        <div class=\"text-right\">\n          <div class=\"text-xs score\">Score: ${r.score.toFixed(3)}</div>\n        </div>\n      </div>\n      <div class=\"mt-2 text-sm text-gray-300\"><strong>Rekomendasi singkat:</strong> ${escapeHtml(doc.solution)}</div>\n      `;
      resultsEl.appendChild(card);
    });
  }

  document.getElementById('askBtn').addEventListener('click', ()=>{
    const q = document.getElementById('query').value;
    renderResults(q);
  });
  document.getElementById('query').addEventListener('keyup', (e)=>{ if(e.key==='Enter') renderResults(e.target.value); });
  document.getElementById('resetBtn').addEventListener('click', ()=>{ document.getElementById('query').value=''; renderResults(''); });

  document.getElementById('exportBtn').addEventListener('click', ()=>{
    const blob = new Blob([JSON.stringify(DATA, null, 2)], {type: 'application/json'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = 'car_issues_dataset.json'; a.click(); URL.revokeObjectURL(url);
  });

  function escapeHtml(s){ return String(s).replace(/[&<>\"]/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }

  // initial empty message
  renderResults('');
  </script>
</body>
</html>
