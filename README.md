Magento 2 q-invoice connect
==============================

"q-invoice connect" is an extension that creates an invoice in your q-invoice account for every order. It also lets you sync your inventory between your q-invoice and one or more (Magento) webshops.


[Click here to create an account or login](https://app.q-invoice.com/)

Installation instructions
=========================

<b>Composer:</b>

In the root of your Magento2 installation enter the following commands to install the extension

1. `composer config repositories.qinvoice-connect git https://github.com/q-invoice/magento2-qinvoice-connect.git` 
2. `composer require q-invoice/magento2-qinvoice-connect:dev-master` 
3. `php bin/magento setup:upgrade`
4. `php bin/magento setup:static-content:deploy`
    

<b>Manual:</b>
1. Create folder `app/code/Qinvoice/Connect/` and copy the content of this repository 
2. Run: `php bin/magento setup:upgrade`
3. Run: `php bin/magento setup:static-content:deploy`

# Configuration

* <b>API URL</b> 
The latest version is: https://app.q-invoice.com/api/1.2/

* <b>API Credentials</b>
Create a dedicated API user through the settings menu under 'API Access'. Copy the username and password.

* <b>Layout code</b>
Through settings > Layouts a layout code can be retrieved. Use the code that belongs to the layout you would like to use for this storeview. 

* <b>Invoice remark</b> 
A static string that can be displayed on the invoice.

* <b>Invoice tag</b>
A label that will be attached to the invoice. It will not be visible for the recipient but can help you organize your invoices.

* <b>Paid remark</b>
A static string that will be added to the invoice remark if the order has been paid upfront. 

* <b>Invoice trigger</b>
The trigger on which an invoice should be created.

* <b>Invoice action</b>
Select an action to perform. Invoices can be saved as <b>draft</b> for later editing, <b>saved as PDF</b> or <b>send</b> to the shopper.

* <b>Save relation</b>
Lets q-invoice know whether or not to store the shoppers\' data for later use.

* <b>Calculation method</b>
Lets q-invoice know if prices with or without VAT are leading. If you experience rounding issues in pricing, adjusting this value might help.

* <b>Product attributes</b>
Select one or more product attributes to display on the invoice lines. This helps you distinguish between a 'Sweater' and a 'Sweater - Red'.

* <b>Webshop secret</b>
Enter this value in your q-invoice account at the plugin settings in order to allow secure communication when syncing your q-invoice account products with your Magento shop and vice versa.


# Contribution

Want to contribute to this extension? The quickest way is to <a href="https://help.github.com/articles/about-pull-requests/">open a pull request</a> on GitHub.

# Support

If you encounter any problems or bugs, please <a href="https://github.com/qinvoice/magento2-qinvoice-connect/issues">open an issue</a> on GitHub.