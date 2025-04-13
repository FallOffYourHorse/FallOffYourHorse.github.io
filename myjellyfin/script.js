window.addEventListener("DOMContentLoaded", async () => {
  const authSection = document.getElementById("auth-section");
  const dashboard = document.getElementById("dashboard");
  const discordName = document.getElementById("discord-name");
  const jellyfinName = document.getElementById("jellyfin-name");
  const avatar = document.getElementById("avatar");
  const linkJellyfinWrapper = document.getElementById("linkJellyfinWrapper");

  try {
    const res = await fetch("/myjellyfin/api/auth/user", { credentials: "include" });
    if (!res.ok) throw new Error("Not logged in");

    const user = await res.json();
    authSection.style.display = "none";
    dashboard.style.display = "block";

    discordName.textContent = `${user.username}#${user.discriminator}`;
    if (avatar) {
      avatar.src = `https://cdn.discordapp.com/avatars/${user.id}/${user.avatar}.png`;
      avatar.alt = `${user.username}'s avatar`;
    }

    const jfRes = await fetch(`/myjellyfin/api/jellyfin/user/${user.id}`);
    const jfUser = await jfRes.json();
    if (jfUser && jfUser.username) {
      jellyfinName.textContent = jfUser.username;
      linkJellyfinWrapper.style.display = "none";
    } else {
      jellyfinName.textContent = "Not linked";
      linkJellyfinWrapper.style.display = "block";
    }
  } catch (err) {
    authSection.style.display = "block";
    dashboard.style.display = "none";
  }
});

function logout() {
  fetch("/myjellyfin/api/auth/logout", { credentials: "include" }).then(() => location.reload());
}

async function linkJellyfinAccount() {
  const input = document.getElementById("jellyfin-username");
  const status = document.getElementById("linkStatus");
  const username = input.value.trim();
  if (!username) {
    status.innerHTML = `<div class="alert alert-warning">Please enter a username.</div>`;
    return;
  }

  const res = await fetch("/myjellyfin/api/jellyfin/link", {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ username }),
  });

  if (res.ok) {
    status.innerHTML = `<div class="alert alert-success">✅ Linked successfully!</div>`;
    document.getElementById("jellyfin-name").textContent = username;
    document.getElementById("linkJellyfinWrapper").style.display = "none";
  } else {
    const msg = await res.text();
    status.innerHTML = `<div class="alert alert-danger">❌ ${msg}</div>`;
  }
}
