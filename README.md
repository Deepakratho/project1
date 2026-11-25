# Project 1

## Database configuration

The application requires `DB_HOST`, `DB_USER`, `DB_PASSWORD`, and `DB_NAME`
to be available in the PHP process environment. Set them in the web server's
environment (for Apache, use its `SetEnv` configuration and restart the
server). PHP does not load `.env` files automatically; `.env.example` is a
reference only. Replace its example values with your own local configuration,
and do not commit passwords or other secrets.
