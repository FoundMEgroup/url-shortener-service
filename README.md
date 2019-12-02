

## Installation

### Host the backend shortening service

1. Download/clone the `service` repo on your backend host/domain.
2. Run `composer install`
3. Edit the `.env` values to match your environment and database configuration.
4. Open your browser and go to the index `/` to check if it's running correctly


### Redirect your short domain to the backend service.

You can put this within a `.htaccess` file or within the `virtual-host` configuration on your 'short-domain'.

```

RewriteEngine On

# | ---------------------------------------------------------------------------------------------------------
# |     Handle the actual short URL's and aliasses and redirect them to the service.
# | ---------------------------------------------------------------------------------------------------------
# |
# |   `c/` and `a/` are just example paths, but you can rewrite the entire root or use different paths.
# |   The example here would be: 
# |      https://my-short-domain/c/123abc for codes. 
# |      https://my-short-domain/a/name for aliasses.
# |
# |   Make sure you redirect to your hosted service with the correct arguments `code` and or `alias`.
# |
# | ---------------------------------------------------------------------------------------------------------

# Short-codes
RewriteRule ^c/(.*)$ https://api-services.bertmaurau.be/url-shortener/url?code=$1 [R=301,NC,L]

# Aliasses
RewriteRule ^a/(.*)$ https://api-services.bertmaurau.be/url-shortener/url?alias=$1 [R=301,NC,L]


```

## API

### Public endpoints

Go to the URL matching given code/alias
`GET /url`
`POST /url`

Create a new shortened URL (anonymous)
`POST /urls  `

Validate user account
`POST /validate-login`

Create a new user account
`POST /register`

### Auth endpoints

#### User Account

Get authenticated user account info
`GET /me`

Update user info
`PATCH /me`

Delete the user account
`DELETE /me`

#### User URL's

Get list of user created URL's 
`GET /my/urls`

Get specific URL
`GET /my/urls/:id`

Create a new shortened URL (from the user account)
`POST /my/urls`

Update a URL
`PATCH /my/urls/:id`

Delete a URL
`DELETE /my/urls/:id`

Get analytics information for given URL
`GET /my/urls/:id/analytics`

#### User URL Aliasses

Get list of user created aliasses for given URL
`GET /my/urls/:id/aliasses`

Get specific URL alias
`GET /my/urls/:id/aliasses/:id`

Create a new alias for given URL
`POST /my/urls/:id/aliasses`

Update a URL alias
`PATCH /my/urls/:id/aliasses/:id`

Delete a URL alias
`DELETE /my/urls/:id/aliasses/:id`

Get analytics information for given URL alias
`GET /my/urls/:id/aliasses/:id/analytics`