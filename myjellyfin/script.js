window.addEventListener("DOMContentLoaded", async () => {
    const authSection = document.getElementById("auth-section");
    const dashboard = document.getElementById("dashboard");
    const discordName = document.getElementById("discord-name");
    const jellyfinName = document.getElementById("jellyfin-name");
  
    try {
      const res = await fetch("/api/auth/user", { credentials: "include" });
      if (!res.ok) throw new Error("Not logged in");
  
      const user = await res.json();
      authSection.style.display = "none";
      dashboard.style.display = "block";
  
      discordName.textContent = user.username + "#" + user.discriminator;
  
      // 👇 Optional: fetch linked Jellyfin account
      const jfRes = await fetch(`/api/jellyfin/user/${user.id}`);
      const jfUser = await jfRes.json();
      jellyfinName.textContent = jfUser.username || "Not linked";
  
    } catch (err) {
      authSection.style.display = "block";
      dashboard.style.display = "none";
    }
  });
  
  function logout() {
    fetch("/api/auth/logout", { credentials: "include" })
      .then(() => location.reload());
  }
  