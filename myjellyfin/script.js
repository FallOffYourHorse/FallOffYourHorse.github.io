window.addEventListener("DOMContentLoaded", async () => {
    const authSection = document.getElementById("auth-section");
    const dashboard = document.getElementById("dashboard");
    const discordName = document.getElementById("discord-name");
    const jellyfinName = document.getElementById("jellyfin-name");
    const avatar = document.getElementById("avatar");
    const linkJellyfinWrapper = document.getElementById("linkJellyfinWrapper");
  
    try {
      const res = await fetch("/api/auth/user", { credentials: "include" });
      if (!res.ok) throw new Error("Not logged in");
  
      const user = await res.json();
      authSection.style.display = "none";
      dashboard.style.display = "block";
  
      discordName.textContent = `${user.username}#${user.discriminator}`;
  
      if (avatar) {
        avatar.src = `https://cdn.discordapp.com/avatars/${user.id}/${user.avatar}.png`;
        avatar.alt = `${user.username}'s avatar`;
      }
  
      // Fetch linked Jellyfin account
      const jfRes = await fetch(`/api/jellyfin/user/${user.id}`);
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
    fetch("/api/auth/logout", { credentials: "include" })
      .then(() => location.reload());
  }
  
  // Optional: You can wire this into a command or redirect
  function linkJellyfinAccount() {
    window.location.href = "https://discord.com/channels/your-server-id/your-channel-id"; // or trigger a bot DM or command
  }
  