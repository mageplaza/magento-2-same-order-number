# Magento 2 Same Order Number Free Extensio

[Magento 2 Same Order Number Extension](https://www.mageplaza.com/magento-2-same-order-number/) by **Mageplaza** allows store owners to easily create the ID of Invoice, Shipment and Credit Memo the same as the ID of the original Order. This will significantly contribute to a well-managed order information system.

[![Latest Stable Version](https://poser.pugx.org/mageplaza/module-same-order-number/v/stable)](https://packagist.org/packages/mageplaza/module-same-order-number)
[![Total Downloads](https://poser.pugx.org/mageplaza/module-same-order-number/downloads)](https://packagist.org/packages/mageplaza/module-same-order-number)


## 1. Documentation
- [Installation guide](https://www.mageplaza.com/install-magento-2-extension/)
- [User guide](https://docs.mageplaza.com/same-order-number/index.html)
- [Introduction page](http://www.mageplaza.com/magento-2-same-order-number/)
- [Contribute on Github](https://github.com/mageplaza/magento-2-same-order-number)
- [Get Support](https://github.com/mageplaza/magento-2-same-order-number/issues)

## 2. FAQs

## 3. How to install Magento 2 Same Order Number Extension

- Install via composer (recommend)

Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-same-order-number
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## 4. Highlight Features

### Automatically same order ID update for billing documents

**Magento 2 Same Order Number by Mageplaza** helps create the ID number for billing documents, associated with the ID of the original order after customers purchase. Documents that are possible to be set with the same order number are Invoice, Shipment and Credit Memo.

![Magento 2 Same Order Number](https://i.imgur.com/5B3YhmK.png)

### Automatically add the extra suffix

In case there are more than one Shipments, Invoices or Credit Memos for a single Order, the system will automatically create an extra suffix for the next shipments, start from the second one. For instance, Order ID is #100, the ID of Invoice will accordingly be #100, and from the next one, the ID of the second Invoice will be #100-1, the third will be #100-2, and so on.

![Magento 2 Same Order Number extension](https://i.imgur.com/UaKwdCd.png)

### Billing documents united pack

The Invoice, Shipment and Credit Memo with the same increment IDs can be well displayed on the frontend. The united pack of billing documents allows both admin and customer to follow and track the order information easily and conveniently.

![Magento 2 Same Order Number module](https://i.imgur.com/1FOyZG1.png)

## 5. Full Magento 2 Same Order Number Features

### For store owners

- Enable/disable the module
- Select billing documents (Shipment/Invoice/Credit Memo) to set ID according to the Order ID
- Create an extra suffix for multiple billing documents of a single Order
- Be compatible with any payment methods that create automated invoices
- Work well with [Mageplaza PDF Invoice](https://www.mageplaza.com/magento-2-pdf-invoice-extension/)

### For customers

- Easy to follow and manage the relationships among billing documents
- View the record from the frontend, from the customer's login account.

## 6. Same Order Number User Guide

Login to your Magento Admin Panel, navigate to `Store > Settings > Configuration > Mageplaza Extensions > Same Order Number`.

![same order number4](https://i.imgur.com/J3ypdvW.png)

### 6.1. Same Order Number General Configuration 

![Magento 2 Same Order Number General Configuration](https://i.imgur.com/Aj5GAKj.png)

- **Enable**: Select `Yes` to activate the extension
- **Apply for**: Choose the billing documents to apply the extension. It is possible to apply to Invoice, Shipment and Credit Memo at the same time. 
  - Apply to **Shipment**: ID increment of **Shipment** will be configured the same as ID of the Order. In case there are more than one Shipments for a single Order, the system will automatically create a following suffix for the next shipments,  start from the second one. For instance, Order ID is #003, the ID of Shipment will accordingly be #003, and from the next one, the ID of Shipment will be in a format as follow: #003-1

![Magento 2 Same Order Number Configuration](https://i.imgur.com/Zrn0m0L.png)

- If you do not enable **Same Order Number** for Shipment ID, the ID of Shipment will be set as default.
    
  - Apply to **Invoice**: The ID of the Invoice will be set according to the Order ID.
  
    - **Same Order Number** is also possible to create Invoice ID automatically when purchasing via Paypal.
    
    - In case there are more than one Invoices for a single Order, ID of every next invoice will be added with an extra suffix. For example, when the ID of Order is Order #003, the Invoice ID will be #003 and the second Invoice will be #003-1.

![Mageplaza Same Order Number](https://i.imgur.com/nrlWXgJ.png)

- If you do not enable **Same Order Number** for Invoice ID, the ID of Invoice will be set as default.

  - Apply for **Credit Memo**: ID of Credit Memo will be set according to the Order ID. For Order that have more than one Credit Memos, ID of every next Credit Memo will be automatically added with a suffix. For example, ID of the Order is #003, ID of the Credit Memo will be #003, and the second Credit Memo ID will be #003-1.

![Mageplaza Same Order Number for Magento 2](https://i.imgur.com/6NCxY5U.png)

- If you do not enable **Same Order Number** for Credit Memo ID, the ID of Credit Memo will be set as default.
    
### 6.2. Configuration for multi-stores 

- Stores will be set as Default Config.
- To configure the extension for each store, go to `Store View > Store`. Next, uncheck the box `Use Website` of each option.
- The configuration of each store will only be available right on that store, other stores are still unchanged.

### 6.3. Frontend

#### 6.3.1. Invoice after being applied Same Order Number Extension:

![Invoice after being applied Same Order Number Extension](https://i.imgur.com/cvnU6NW.png)

#### 6.3.2. Shipment after being applied Same Order Number Extension:

![Shipment after being applied Same Order Number Extension](https://i.imgur.com/m08y5B8.png)

#### 6.3.3. Credit Memo after being applied Same Order Number Extension:

![Credit Memo after being applied Same Order Number Extension](https://i.imgur.com/UWNA6OS.png)



**Other free extension on Github**
- [Magento 2 seo all in one solution](https://github.com/mageplaza/magento-2-seo)
- [Magento 2 ReCaptcha](https://github.com/mageplaza/magento-2-google-recaptcha)
- [Magento 2 Delete Orders](https://github.com/mageplaza/magento-2-delete-orders)
- [MAGENTO 2 ADVANCED REPORTS](https://github.com/mageplaza/magento-2-reports)
- [Magento 2 Blog](https://github.com/mageplaza/magento-2-blog)
- [Magento 2 Same Order Number](https://github.com/mageplaza/magento-2-same-order-number)
- [Magento 2 Ajax Layered Navigation](https://github.com/mageplaza/magento-2-ajax-layered-navigation)
- [Magento 2 security module](https://github.com/mageplaza/magento-2-security)


