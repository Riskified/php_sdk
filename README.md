Riskified PHP SDK
=================

See *samples/* for examples on how to use this SDK.

Migrating to API Version 2
--------------------------

API Version 2 introduces new features (and breaks some old ones).

### Order Webhook ###

This version represents a shift from data-driven order handling to multiple API endpoints, each designed
for a specific purpose. These include:

* `/api/create` - served by `$transport->createOrder()`
* `/api/update` - served by `$transport->updateOrder()`
* `/api/submit` - served by `$transport->submitOrder()`
* `/api/refund` - served by `$transport->refundOrder()`
* `/api/cancel` - served by `$transport->cancelOrder()`

Refer to the online [documentation](http://apiref.riskified.com) for more details.
When migrating from version 1, you'll need to separate the different calls to Riskified's API to support this new process.


### Decision Notifications ###

#### Constructor $headers argument format ####
The format of the `$headers` argument when constructing a new `Riskified\DecisionNotification\Notification` instance has changed.
The constructor now expects an associative array of all the HTTP headers of the request, and *not* a flat array of strings, as
in previous versions of this SDK.

This change should simplify integration since the argument now follows the format of the return value of the popular PHP/Apache
function [`getallheaders()`](http://php.net/manual/en/function.getallheaders.php).


#### API v2 payload format ####
Notification requests in API version 2 now contain a JSON encoded payload which is more flexible and easily extended.

If you are already using the `Notification` class in version 1, there are no additional actions required to support the
migration to JSON, as this SDK handles the new data format seamlessly.





