# URL Shortener & Tracker
## Description

A simple and basic URL shortener service with back-end tracking and analytics.

Convert your long URL to a short URL referenced by a short code. The service also allows for `aliasses` to personalize your short URL.

By default, every URL request gets tracked. This includes basic timestamps and the geolocation. When `BrowserDetect` has been turned on for the shortened URL, a loading page will be previewed to fetch the client's browser information first before redirecting. This includes the platform, the browser, language, ..

## Features
### Public

- Shorten an URL


### Private (needs authentication)

- User Account
- URL Management
- URL Aliasses
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
# | ----------------------------------------------------------------------------------------------------
# | Handle the actual short URL's and aliasses and redirect them to the service.
# | ----------------------------------------------------------------------------------------------------
# |
# | `c/` and `a/` are just example paths, but you can rewrite the entire root or use different paths.
# | The example here would be:
# | https://shortdomain.com/c/123abc for codes.
# | https://shortdomain.com/a/name for aliasses.
# |
# | Make sure you redirect to your hosted service with the correct arguments `code` and or `alias`.
# |
# | ----------------------------------------------------------------------------------------------------

# Short-codes
RewriteRule ^c/(.*)$ https://api.shortdomain.com/url?code=$1 [R=301,NC,L]
# OR RewriteRule ^c/(.*)$ https://api.shortdomain.com/c/$1 [R=301,NC,L]

# Aliasses
RewriteRule ^a/(.*)$ https://api.shortdomain.com/url?alias=$1 [R=301,NC,L]
# OR RewriteRule ^a/(.*)$ https://api.shortdomain.com/a/$1 [R=301,NC,L]

```

## API
### Public endpoints
#### URLs

Go to the associated URL for given code/alias.  
| **Method**    | `GET` or `POST`   
| **Endpoint**  | `/url`   
| **Parameters**| `code` or `alias`   
| **Example**   | `GET /url?code=1ab2c3d4`  

Alias endpoints:  
| **Method**    | `GET` or `POST`  
| **Endpoint**  | `/c/:code`  
| **Example**   | `GET /c/1ab2c3d4`  


| **Method**    | `GET` or `POST`  
| **Endpoint**  | `/a/:alias`  
| **Example**   | `GET /a/my-short-url`  

Create a new shortened URL (as an anonymous user)  

| **Method**    | `POST`  
| **Endpoint**  | `/urls`  
| **Payload**   | `url:string`  
| **Example**   | `POST /urls { url: 'www.google.com', browser_detect: true }`  

#### Auth

Validate user credentials and request accessToken.  

| **Method**    | `POST`  
| **Endpoint**  | `/validate-login`  
| **Payload**   | `email:string | password: string`  
| **Example**   | `POST /validate-login { email: 'j.doe@company.com', password: 'cd4d9b143310f3b4a89cb9619addd588' }`  

Create a new shortened URL (as an anonymous user)  
  
| **Method**    | `POST`  
| **Endpoint**  | `/register`  
| **Payload**   | `email:string | password: string`  
| **Example**   | `POST /register { email: 'j.doe@company.com', password: 'cd4d9b143310f3b4a89cb9619addd588' }`  
  

### Auth-required endpoints

#### User Account

Get authenticated user account info.  

| **Method**    | `GET`  
| **Endpoint**  | `/me`  
| **Example**   | `GET /me`  


Update user info.  

| **Method**    | `PATCH`  
| **Endpoint**  | `/me`  
| **Payload**   | `first_name:string | last_name: string`  
| **Example**   | `PATCH /me { first_name: 'bert' }`  
  

Delete the user account

| **Method**    | `DELETE`  
| **Endpoint**  | `/me`  
| **Example**   | `DELETE /me` 
  

#### User URLs

Create a new shortened URL (as an anonymous user)  

| **Method**    | `POST`  
| **Endpoint**  | `/urls`  
| **Payload**   | `url:string`  
| **Example**   | `POST /urls { url: 'www.google.com', browser_detect: true }`  


Get list of user created URLs  

| **Method**    | `GET`  
| **Endpoint**  | `/my/urls`  
| **Parameters**| `short_code | url | browser_detect | take | skip`  
| **Example**   | `GET /my/urls?browser_detect=true&take=12&skip=0`  

Get specific User URL

| **Method**    | `GET`  
| **Endpoint**  | `/my/urls/:userUrlId`  
| **Example**   | `GET /my/urls/12`  

Delete a URL

| **Method**    | `DELETE`  
| **Endpoint**  | `/my/urls/:userUrlId`  
| **Payload**   | `delete_full: boolean`  
| **Example**   | `DELETE /my/urls/12` 
  
Update a URL.  

| **Method**    | `PATCH`  
| **Endpoint**  | `/my/urls/:id`  
| **Payload**   | `url:string | browser_detect: boolean`  
| **Example**   | `PATCH /my/urls/232 { browser_detect: true }`  
  
Get overview for given URL

| **Method**    | `GET`  
| **Endpoint**  | `/my/urls/:urlId/overview`  
| **Example**   | `GET /my/urls/3434/overview`

Create a new alias for given URL

| **Method**    | `POST`  
| **Endpoint**  | `/my/urls/:urlId/aliasses`  
| **Payload**   | `alias:string`  
| **Example**   | `POST /my/urls/12/aliasses { alias: 'my-short-code' }`  
  
Update a URL alias

| **Method**    | `PATCH`  
| **Endpoint**  | `/my/urls/:urlId/aliasses/:urlAliasId`  
| **Payload**   | `alias:string`  
| **Example**   | `PATCH /my/urls/12/aliasses { alias: 'my-short-code' }`  

Delete a URL alias

| **Method**    | `DELETE`  
| **Endpoint**  | `/my/urls/:userUrlId/aliasses/:urlAliasId`  
| **Example**   | `DELETE /my/urls/12/aliasses/1` 
