const cards = [
  {
    title: "Jellyfin",
    url: "https://anime.ariabelmonde.ca",
    desc: "Media server (anime)",
    tags: ["media", "self-hosted"],
    logo: "/assets/logos/jellyfin.svg",
  },
  {
    title: "Jellyseerr",
    url: "https://request.ariabelmonde.ca",
    desc: "Media requests",
    tags: ["media", "requests"],
    initials: "JS",
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
    title: "Nginx Proxy Manager",
    url: "https://npm.ariabelmonde.ca",
    desc: "Reverse proxy",
    tags: ["infra"],
    initials: "NPM",
  },
  {
    title: "UGREEN NAS",
    url: "https://nas.ariabelmonde.ca",
    desc: "NAS dashboard",
    tags: ["infra"],
    initials: "NAS",
  },
  {
    title: "Discord Bot Dashboard",
    url: "https://ariabelmonde.ca/discord",
    desc: "Bot & tools",
    tags: ["discord", "project"],
    initials: "BOT",
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

const $grid = document.getElementById("grid");
const $empty = document.getElementById("empty");
const $search = document.getElementById("search");
const $tag = document.getElementById("tagFilter");
const $layout = document.getElementById("layoutToggle");
document.getElementById("year").textContent = new Date().getFullYear();

function uniqTags(list) {
  return Array.from(new Set(list.flatMap((c) => c.tags || []))).sort();
}

function buildTagFilter() {
  for (const t of uniqTags(cards)) {
    const o = document.createElement("option");
    o.value = t;
    o.textContent = t;
    $tag.appendChild(o);
  }
}

function cardHTML(c) {
  const host = c.url.replace(/^https?:\/\//, "");
  const logo = c.logo
    ? `<div class="app-icon"><img src="${c.logo}" alt="${
        c.title
      } logo" onerror="this.closest('.app-icon').dataset.fallback='${(
        c.initials || c.title.slice(0, 2)
      ).toUpperCase()}'"></div>`
    : `<div class="app-icon" data-fallback="${(
        c.initials || c.title.slice(0, 2)
      ).toUpperCase()}"></div>`;
  return `
        <a class="app-card" href="${c.url}" target="_blank" rel="noopener">
          <span class="app-sheen" aria-hidden="true"></span>
          ${logo}
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
  $grid.innerHTML = "";
  const q = $search.value.trim().toLowerCase();
  const tg = $tag.value;
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
  $empty.hidden = filtered.length > 0;
}

function toggleLayout() {
  document.documentElement.classList.toggle("compact");
  const on = document.documentElement.classList.contains("compact");
  $layout.setAttribute("aria-pressed", on ? "true" : "false");
  $layout.textContent = on ? "Comfortable" : "Compact";
}

document.addEventListener("keydown", (e) => {
  if (e.key === "/" && document.activeElement !== $search) {
    e.preventDefault();
    $search.focus();
  }
});
$search.addEventListener("input", render);
$tag.addEventListener("change", render);
$layout.addEventListener("click", toggleLayout);

buildTagFilter();
render();
