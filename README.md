# URL Shortener & Tracker
## Description

A simple and basic URL shortener service with back-end tracking and analytics.

Convert your long URL to a short URL referenced by a short code. The service also allows for `aliasses` to personalize your short URL.

By default, every URL request gets tracked. This includes basic timestamps and the geolocation. When `BrowserDetect` has been turned on for the shortened URL, a loading page will be previewed to fetch the client's browser information first before redirecting. This includes the platform, the browser, language, ..

## Features
### Public

- Shorten an URL


### Private (needs authentication)

- URL Management
- URL Aliasses
- Browser Detect
- Analytics


## Installation
### Host the backend shortening service

1. Download/clone the `service` repo on your backend host/domain.
2. Run `composer install`
3. Edit the `.env` values to match your environment and database configuration.
4. Open your browser and go to the index `/` to check if it's running correctly

### Redirect your short domain to the backend service (when running a seperate front and back).

You can put this within a `.htaccess` file or within the `virtual-host` configuration on your 'short-domain'.

```
RewriteEngine On
# | -------------------------------------------------------------------------------------------------------
# | Handle the actual short URL's and aliasses and redirect them to the service.
# | -------------------------------------------------------------------------------------------------------
# |
# | `c/` and `a/` are just example paths, but you can rewrite the entire root or use different paths.
# | The example here would be:
# | https://shortdomain.com/c/123abc for codes.
# | https://shortdomain.com/a/name for aliasses.
# |
# | Make sure you redirect to your hosted service with the correct arguments `code` and or `alias`.
# |
# | -------------------------------------------------------------------------------------------------------

# Short-codes
RewriteRule ^c/(.*)$ https://api.shortdomain.com/url?code=$1 [R=301,NC,L]
# OR RewriteRule ^c/(.*)$ https://api.shortdomain.com/c/$1 [R=301,NC,L]

# Aliasses
RewriteRule ^a/(.*)$ https://api.shortdomain.com/url?alias=$1 [R=301,NC,L]
# OR RewriteRule ^a/(.*)$ https://api.shortdomain.com/a/$1 [R=301,NC,L]

```

## API
### Public endpoints
#### Handle Short-URL

Go to the associated URL for given code/alias.
| **Method**    | `GET` or `POST` |
| **Endpoint**  | `/url` |
| **Arguments** | `code` or `alias` |
| **Example**   | `GET /url?code=1ab2c3d4` |

Alias endpoints:
| **Method**    | `GET` or `POST` |
| **Endpoint**  | `/c/:code` |
| **Example**   | `GET /c/1ab2c3d4` |

| **Method**    | `GET` or `POST` |
| **Endpoint**  | `/a/:alias` |
| **Example**   | `GET /a/1ab2c3d4` |


#### Create new Short URL
Create a new shortened URL (as an anonymous user)

| **Method**    | `POST` |
| **Endpoint**  | `/urls` |
| **Payload**   | `url:string` |
| **Example**   | `POST /urls` `{ url: 'www.google.com' }` |

#### Validate user account (login)

Validate user credentials and request accessToken.

*Method:* POST

*Endpoint:* `/validate-login`

*Required Payload:* `email:string | password: string`

*Example:* POST `/validate-login` `{ email: 'j.doe@company.com', password: 'cd4d9b143310f3b4a89cb9619addd588' }`

  

#### Create a new user account (register)

  

Create a new shortened URL (as an anonymous user)

  

*Method:* POST

*Endpoint:* `/register`

*Required Payload:* `email:string | password: string`

*Example:* POST `/register` `{ email: 'j.doe@company.com', password: 'cd4d9b143310f3b4a89cb9619addd588' }`

  

### Auth-required endpoints

  

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