console.log("dashboard main.js loaded");

// Cards config (edit this)
const cards = [
  {
    title: "Jellyfin",
    url: "https://anime.ariabelmonde.ca",
    desc: "Media server (anime)",
    tags: ["media", "self-hosted"],
    logo: "/img/logo/jellyfin.webp",
    featured: true,
  },
  {
    title: "Komga",
    url: "https://manga.ariabelmonde.ca",
    desc: "Manga server",
    tags: ["media", "reading"],
    initials: "K",
  },
  {
    title: "qBittorrent",
    url: "https://qb.ariabelmonde.ca",
    desc: "Downloader UI",
    tags: ["download", "self-hosted"],
    initials: "QB",
  },
  {
    title: "Sonarr",
    url: "https://sonarr.ariabelmonde.ca",
    desc: "TV automation",
    tags: ["arr", "automation"],
    initials: "S",
  },
  {
    title: "Radarr",
    url: "https://radarr.ariabelmonde.ca",
    desc: "Movies automation",
    tags: ["arr", "automation"],
    initials: "R",
  },
  {
    title: "Prowlarr",
    url: "https://prowlarr.ariabelmonde.ca",
    desc: "Indexers",
    tags: ["arr", "automation"],
    initials: "P",
  },
  {
    title: "GitHub",
    url: "https://github.com/FallOffYourHorse",
    desc: "Repos & profile",
    tags: ["profile"],
    initials: "GH",
  },
  {
    title: "AniList",
    url: "https://anilist.co/user/JeanneDarcAlter",
    desc: "Anime list",
    tags: ["profile"],
    initials: "AL",
  },
];

// Elements
const $grid = document.getElementById("grid");
const $empty = document.getElementById("empty");
const $search = document.getElementById("search");
const $tag = document.getElementById("tagFilter");
const $year = document.getElementById("year");
if ($year) $year.textContent = new Date().getFullYear();

// Helpers
function uniqTags(list) {
  return Array.from(new Set(list.flatMap((c) => c.tags || []))).sort();
}
function buildTagFilter() {
  if (!$tag) return;
  for (const t of uniqTags(cards)) {
    const o = document.createElement("option");
    o.value = t;
    o.textContent = t;
    $tag.appendChild(o);
  }
}

function cardHTML(c) {
  const host = c.url.replace(/^https?:\/\//, "");
  const icon = c.logo
    ? `<div class="app-icon"><img src="${c.logo}" alt="${c.title} logo"></div>`
    : `<div class="app-icon" data-fallback="${(
        c.initials || c.title.slice(0, 2)
      ).toUpperCase()}"></div>`;
  return `
    <a class="app-card" href="${c.url}" target="_blank" rel="noopener"> 
      <span class="app-sheen" aria-hidden="true"></span>
      ${c.featured ? `<span class="featured-badge">Featured</span>` : ""}
      ${icon}
      <div class="app-meta">
        <h2 class="app-title">${c.title}</h2>
        <div class="app-host">${host}</div>
        <p class="app-desc">${c.desc || ""}</p>
        ${
          c.tags?.length
            ? `<div class="tags">${c.tags
                .map((t) => `<span class='tag'>${t}</span>`)
                .join("")}</div>`
            : ""
        }
      </div>
    </a>`;
}

function render() {
  if (!$grid) return;
  $grid.innerHTML = "";
  const q = $search ? $search.value.trim().toLowerCase() : "";
  const tg = $tag ? $tag.value : "";
  const filtered = cards.filter((c) => {
    const hay = `${c.title} ${c.url} ${c.desc || ""} ${(c.tags || []).join(
      " "
    )}`.toLowerCase();
    const okQ = q ? hay.includes(q) : true;
    const okT = tg ? (c.tags || []).includes(tg) : true;
    return okQ && okT;
  });
  filtered.forEach((c) => {
    const w = document.createElement("div");
    w.innerHTML = cardHTML(c);
    $grid.appendChild(w.firstElementChild);
  });
  if ($empty) $empty.hidden = filtered.length > 0;
}

// Wire up
if ($search) $search.addEventListener("input", render);
if ($tag) $tag.addEventListener("change", render);

buildTagFilter();
render();
