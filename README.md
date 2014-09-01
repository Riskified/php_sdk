Riskified PHP SDK
=================

See *samples/* for examples on how to use this SDK.

Migrating to API Version 2
--------------------------

API Version 2 introduces new features (and breaks some old ones).

### Order Webhook ###

This version represents a shift from data-driven order handling to multiple API endpoints, each designed
for a specific purpose. These include:

* `/create` - served by `$transport->createOrder()`
* `/update` - served by `$transport->updateOrder()`
* `/submit` - served by `$transport->submitOrder()`
* `/refund` - served by `$transport->refundOrder()`
* `/cancel` - served by `$transport->cancelOrder()`

Refer to the online [documentation](http://apiref.riskified.com) for more details.
When migrating from version 1, you'll need to separate the different calls to Riskified's API to support this new process.


### Decision Notifications ###

Notification requests in version 2 now contain a JSON encoded payload which is more flexible and easily extended.

If you are already using the `Notification` class in version 1, there are no additional actions required
when migrating from version 1, as this SDK handles the new data format seamlessly.




