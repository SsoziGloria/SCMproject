<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $inventory_id
 * @property string $type
 * @property int $amount
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Inventory $inventory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Adjustment whereUpdatedAt($value)
 */
	class Adjustment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $cluster
 * @property string|null $description
 * @property float $customer_count
 * @property string|null $product_types
 * @property string|null $recommendation_strategy
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary whereCluster($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary whereCustomerCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary whereProductTypes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary whereRecommendationStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerClusterSummary whereUpdatedAt($value)
 */
	class CustomerClusterSummary extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $customer_id
 * @property float|null $quantity
 * @property float|null $total_quantity
 * @property int|null $purchase_count
 * @property int $cluster
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment whereCluster($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment wherePurchaseCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CustomerSegment whereTotalQuantity($value)
 */
	class CustomerSegment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $product_id
 * @property string $prediction_date
 * @property int $predicted_quantity
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DemandPrediction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DemandPrediction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DemandPrediction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DemandPrediction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DemandPrediction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DemandPrediction wherePredictedQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DemandPrediction wherePredictionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DemandPrediction whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DemandPrediction whereUpdatedAt($value)
 */
	class DemandPrediction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property string $product_name
 * @property int $quantity
 * @property string $unit
 * @property string|null $batch_number
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $received_date
 * @property int|null $supplier_id
 * @property string|null $location
 * @property \Illuminate\Support\Carbon|null $expiration_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $reorder_level
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\Supplier|null $supplier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory expiringSoon($days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory lowStock($threshold = 10)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereBatchNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereReceivedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereReorderLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Inventory whereUpdatedAt($value)
 */
	class Inventory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $inventory_id
 * @property string $adjustment_type
 * @property int $quantity_change
 * @property string $reason
 * @property string|null $notes
 * @property int|null $user_id
 * @property string|null $user_name
 * @property int|null $status_history_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Inventory $inventory
 * @property-read \App\Models\OrderStatusHistory|null $statusHistory
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereAdjustmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereQuantityChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereStatusHistoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryAdjustment whereUserName($value)
 */
	class InventoryAdjustment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $order_number
 * @property int $user_id
 * @property string|null $sales_channel
 * @property int|null $sales_channel_id
 * @property string|null $phone
 * @property numeric $total_amount
 * @property string|null $subtotal
 * @property string|null $shipping_fee
 * @property string $discount_amount
 * @property string $status
 * @property string $payment
 * @property string $payment_status
 * @property string|null $shipping_address
 * @property string|null $shipping_city
 * @property string|null $shipping_region
 * @property string|null $shipping_country
 * @property string|null $tracking_number
 * @property string|null $notes
 * @property string|null $referral_source
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property string|null $shipped_at
 * @property \Illuminate\Support\Carbon $order_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read string|null $customer_email
 * @property-read string $customer_name
 * @property-read string $formatted_price
 * @property-read string $formatted_total
 * @property-read \App\Models\Inventory|null $inventory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Product|null $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Promotion> $promotions
 * @property-read int|null $promotions_count
 * @property-read \App\Models\Retailer|null $retailer
 * @property-read \App\Models\SalesChannel|null $salesChannel
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Shipment> $shipments
 * @property-read int|null $shipments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderStatusHistory> $statusHistory
 * @property-read int|null $status_history_count
 * @property-read \App\Models\User|null $supplier
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order byStatus($status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereReferralSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSalesChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSalesChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereShippingRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property string|null $product_name
 * @property string|null $product_category
 * @property int $quantity
 * @property int $quantity_shipped
 * @property string $price
 * @property string|null $unit_cost
 * @property string $discount_amount
 * @property string $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantityShipped($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUnitCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property string $status
 * @property string|null $notes
 * @property int|null $user_id
 * @property string|null $user_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $status_color
 * @property-read mixed $status_icon
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InventoryAdjustment> $inventoryAdjustments
 * @property-read int|null $inventory_adjustments_count
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderStatusHistory whereUserName($value)
 */
	class OrderStatusHistory extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $product_id
 * @property string $name
 * @property string|null $ingredients
 * @property numeric $price
 * @property string|null $cost
 * @property string|null $weight
 * @property string|null $description
 * @property string|null $image
 * @property bool $featured
 * @property int $stock
 * @property int $allocated_stock
 * @property \App\Models\Category|null $category
 * @property int|null $supplier_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read mixed $available_stock
 * @property-read string $formatted_cost
 * @property-read string $formatted_price
 * @property-read mixed $image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inventory> $inventories
 * @property-read int|null $inventories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductReview> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\Supplier|null $supplier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereAllocatedStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIngredients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereWeight($value)
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property string $reviewer_name
 * @property int $rating
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview whereReviewerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductReview whereUpdatedAt($value)
 */
	class ProductReview extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string $discount_type
 * @property string $discount_value
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereDiscountValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Promotion whereUpdatedAt($value)
 */
	class Promotion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property \Illuminate\Support\Carbon $date_from
 * @property \Illuminate\Support\Carbon $date_to
 * @property string $format
 * @property string $status
 * @property int $generated_by
 * @property array<array-key, mixed>|null $data
 * @property string|null $file_path
 * @property string|null $email_recipients
 * @property string|null $schedule_frequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereDateFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereDateTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereEmailRecipients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereGeneratedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereScheduleFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 */
	class Report extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Retailer query()
 */
	class Retailer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $channel_type
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereChannelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SalesChannel whereUpdatedAt($value)
 */
	class SalesChannel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property int|null $user_id For user-specific settings
 * @property string|null $description
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $order_id
 * @property string $shipment_number
 * @property int|null $supplier_id
 * @property int|null $product_id
 * @property int $quantity
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $expected_delivery
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $progress_percentage
 * @property-read string $status_badge
 * @property-read string $status_icon
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\User|null $supplier
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment forOrders()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment forSuppliers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereExpectedDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereShipmentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shipment whereUpdatedAt($value)
 */
	class Shipment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $supplier_id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $company
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier whereUpdatedAt($value)
 */
	class Supplier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $required_workers
 * @property string $location
 * @property int $priority
 * @property bool $is_active
 * @property string $status_for_day
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workforces> $assignments
 * @property-read int|null $assignments_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereRequiredWorkers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatusForDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 */
	class Task extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $profile_photo
 * @property string|null $about
 * @property string|null $country
 * @property string|null $phone
 * @property string|null $twitter
 * @property string|null $facebook
 * @property string|null $instagram
 * @property string|null $linkedin
 * @property string|null $certification_status
 * @property int $is_active
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Namu\WireChat\Models\Conversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read string|null $cover_url
 * @property-read string|null $display_name
 * @property-read string|null $profile_url
 * @property-read mixed $total_spent
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Namu\WireChat\Models\Action> $performedActions
 * @property-read int|null $performed_actions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Supplier> $suppliers
 * @property-read int|null $suppliers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vendor> $vendors
 * @property-read int|null $vendors_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCertificationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

namespace App\Models{
/**
 * @property int $vendor_id
 * @property string $name
 * @property string $email
 * @property string $company_name
 * @property string|null $contact_person
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $bank_name
 * @property string|null $account_number
 * @property string|null $certification
 * @property string|null $certification_status
 * @property string|null $compliance_status
 * @property float|null $monthly_revenue
 * @property numeric|null $revenue
 * @property string $country
 * @property string $financial_score
 * @property int $regulatory_compliance
 * @property string $reputation
 * @property string $validation_status
 * @property string|null $visit_date
 * @property string|null $pdf_path
 * @property int|null $supplier_id
 * @property int|null $retailer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read bool|null $latest_validation_status
 * @property-read mixed $status_badge
 * @property-read array $validation_stats
 * @property-read \App\Models\VendorValidation|null $latestValidation
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VendorValidation> $validValidations
 * @property-read int|null $valid_validations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\VendorValidation> $validations
 * @property-read int|null $validations_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCertification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCertificationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereComplianceStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereFinancialScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereMonthlyRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor wherePdfPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereRegulatoryCompliance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereReputation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereRetailerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereValidationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVendorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor whereVisitDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor withValidDocuments()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vendor withoutValidDocuments()
 */
	class Vendor extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $vendor_id
 * @property string $original_filename
 * @property string $file_path
 * @property int $file_size
 * @property bool $is_valid
 * @property array<array-key, mixed>|null $validation_results
 * @property string|null $validation_message
 * @property \Illuminate\Support\Carbon|null $validated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $file_size_human
 * @property-read string $file_url
 * @property-read array $status_badge
 * @property-read array $validation_summary
 * @property-read \App\Models\Vendor $vendor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation invalid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation recent($days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereIsValid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereOriginalFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereValidatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereValidationMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereValidationResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|VendorValidation whereVendorId($value)
 */
	class VendorValidation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $position
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workforces> $assignments
 * @property-read int|null $assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Workforces> $workforce
 * @property-read int|null $workforce_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Worker whereUpdatedAt($value)
 */
	class Worker extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $worker_id
 * @property string $location
 * @property string $task
 * @property string $status
 * @property string|null $completed_at
 * @property string $assigned_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Worker $worker
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces whereAssignedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces whereTask($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Workforces whereWorkerId($value)
 */
	class Workforces extends \Eloquent {}
}

