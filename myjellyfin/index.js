require("dotenv").config();
const express = require("express");
const session = require("express-session");
const passport = require("passport");
const mongoose = require("mongoose");

const app = express();
app.set("trust proxy", 1);
app.use(express.json());

mongoose.connect(process.env.MONGO_URI, {
  useNewUrlParser: true,
  useUnifiedTopology: true,
}).then(() => console.log("✅ MongoDB connected"))
  .catch(err => console.error("❌ MongoDB connection failed:", err));

app.use(session({
  secret: process.env.SESSION_SECRET,
  resave: false,
  saveUninitialized: false,
  cookie: {
    sameSite: "lax",
    secure: true,
    domain: ".ariabelmonde.ca",
  },
}));

app.use(passport.initialize());
app.use(passport.session());

require("./routes/auth")(app, "/myjellyfin/api/auth");

const jellyfinRoutes = require("./routes/jellyfin");
app.use("/myjellyfin/api/jellyfin", jellyfinRoutes);

const PORT = 3001;
app.listen(PORT, () => {
  console.log(`✅ Discord OAuth server running on port ${PORT}`);
});
