## API Setup Instructions:
1. Take clone from this repository: Github_Repo link

2. Run below command to install dependencies
omposer install

3. copy .env.example and rename it as .env and add database details

4. Run below command to create DB schema
php artisan migrate

5. To run test case
vendor/bin/phpunit tests/Unit

## API endpoints

1 - 
API Name - Login using email address and password
Endpoint: {base_url}/login
Method: POST
Content-type: application/json
Request Params: 
{
    "email" : "sweta@tatvasoft.com",
    "password": "12345678"
}

Response:
{
    "success": true,
    "token": "XYZToken"
}

2 - 
API Name - Register user
Endpoint: {base_url}/register
Method: POST
Content-type: application/json
Request Params: 
{
    "name" : "sweta",
    "email" : "sweta@tatvasoft.com",
    "password": "12345678"
    "confirm_password": "12345678"
}

Response:
{
    "success": true
}

3 - 
API Name - Generate QR code
Endpoint: {base_url}/generateQr
Method: POST
Content-type: application/json
Request Params: 
{
    "content" : "test content",
    "size" : 200,
    "background_color": "#0000"
    "fill_color": "#ffff"
}
Header:
{
    Authorization : "Bearer xyz"
}
Response:
{
    "success": true,
    "qrcode": "qrcode.svg"
}

4 - 
API Name - Logout
Endpoint: {base_url}/logout
Method: POST
Content-type: application/json
Request Params: 
{
    
}
Header:
{
    Authorization : "Bearer xyz"
}
Response:
{
    "success": true
}

## Front end Setup