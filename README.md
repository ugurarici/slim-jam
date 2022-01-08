# SLIM JAM

Slim Jam is a demo application to provide examples for composer package, PHPSpreadsheet, Shopify API etc. usages.

This project aims to take an Excel file from user, parse it and save each product from each row to projects own database. Then read that data and import each product to Shopify through their API.

To achive this first we need to install `phpoffice/phpspreadsheet` package to read data from Excel file. We want to translate some strings from English to other languages, so we need to install `google/cloud-translate` package. This one needs API keys from Google Cloud. Lastly; we need to send those data to shopify, so we will install `shopify/shopify-api`

-   A web page asks for an Excel file
-   User uploads an Excel file from that form
-   System reads Excel, saves each row
-   Import those data to Shopify
