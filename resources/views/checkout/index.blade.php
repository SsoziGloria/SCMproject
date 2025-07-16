{{-- filepath: resources/views/checkout/index.blade.php --}}
@extends('user.app')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Checkout</h2>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
            @csrf
            <div class="row">
                <!-- Left Column: Customer & Shipping Information -->
                <div class="col-md-8">
                    <!-- Customer Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Customer Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name*</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number*</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone', auth()->user()->phone ?? '') }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', auth()->user()->email ?? '') }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="address" class="form-label">Street Address*</label>
                                    <input type="text" class="form-control" id="shipping_address" name="shipping_address"
                                        value="{{ old('address') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City*</label>
                                    <input type="text" class="form-control" id="shipping_city" name="shipping_city"
                                        value="{{ old('shipping_city') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="shipping_region" class="form-label">Region</label>
                                    <select id="shipping_region" name="shipping_region" class="form-select">
                                        <option value="">Select Region</option>
                                        {{-- Optionally, pre-fill if old value exists --}}
                                        @if(old('shipping_region'))
                                            <option value="{{ old('shipping_region') }}" selected>{{ old('shipping_region') }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="shipping_country" class="form-label">Country*</label>
                                    <select id="shipping_country" name="shipping_country" class="form-select">
                                        <option value="">Select Country</option>
                                        <option value="UG">Uganda</option>
                                        <option value="KE">Kenya</option>
                                        <option value="RW">Rwanda</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Method -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Shipping Method</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="shipping_method" id="standard_shipping"
                                    value="standard" checked>
                                <label class="form-check-label d-flex justify-content-between" for="standard_shipping">
                                    <span>Walk-in delivery (3-5 business days)</span>
                                    <span>Free</span>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="shipping_method" id="express_shipping"
                                    value="express">
                                <label class="form-check-label d-flex justify-content-between" for="express_shipping">
                                    <span>Transport by motorcycle (1-2 business days)</span>
                                    <span>UGX 3,000</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shipping_method" id="overnight_shipping"
                                    value="overnight">
                                <label class="form-check-label d-flex justify-content-between" for="overnight_shipping">
                                    <span>Transport by vehicle (Next business day)</span>
                                    <span>UGX 10,000</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment" id="mobile_money"
                                        value="mobile_money" checked>
                                    <label class="form-check-label" for="mobile_money">
                                        Mobile Money
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment" id="credit_card"
                                        value="credit_card">
                                    <label class="form-check-label" for="credit_card">
                                        Credit/Debit Card
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment" id="bank_transfer"
                                        value="bank_transfer">
                                    <label class="form-check-label" for="bank_transfer">
                                        Bank Transfer
                                    </label>
                                </div>
                            </div>

                            <!-- Credit Card Details (shown/hidden based on selection) -->
                            <div id="credit-card-details" class="d-none">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="card_number" class="form-label">Card Number*</label>
                                        <input type="text" class="form-control" id="card_number" name="card_number"
                                            placeholder="XXXX XXXX XXXX XXXX">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="card_expiry" class="form-label">Expiration Date*</label>
                                        <input type="text" class="form-control" id="card_expiry" name="card_expiry"
                                            placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="card_cvv" class="form-label">CVV*</label>
                                        <input type="text" class="form-control" id="card_cvv" name="card_cvv"
                                            placeholder="XXX">
                                    </div>
                                    <div class="col-12">
                                        <label for="card_name" class="form-label">Name on Card*</label>
                                        <input type="text" class="form-control" id="card_name" name="card_name">
                                    </div>
                                </div>
                            </div>

                            <!-- Bank Transfer Instructions (shown/hidden based on selection) -->
                            <div id="bank-transfer-instructions" class="d-none">
                                <p>After placing your order, please transfer the total amount to:</p>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Bank:</strong> Equity Bank</p>
                                    <p class="mb-1"><strong>Account Name:</strong> Chocolate SCM Ltd</p>
                                    <p class="mb-1"><strong>Account Number:</strong> XXXX-XXXX-XXXX-XXXX</p>
                                    <p class="mb-1"><strong>Reference:</strong> Your order number (will be provided after
                                        checkout)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Additional Notes</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                    placeholder="Special delivery instructions or other information">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="col-md-4">
                    <div class="card mb-4 sticky-top" style="top: 20px; z-index: 1;">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="p-3">
                                <p class="mb-1">{{ count($products) }} item(s) in cart</p>
                            </div>
                            <ul class="list-group list-group-flush">
                                @foreach($products as $id => $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $item['product']->name }}</h6>
                                            <small class="text-muted">Qty: {{ $item['quantity'] }}</small>
                                        </div>
                                        <span>UGX {{ number_format($item['product']->price * $item['quantity'], 0) }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>UGX {{ number_format($total, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Transport:</span>
                                    <span id="shipping-cost">Free</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>VAT (10%):</span>
                                    <span>UGX {{ number_format($total * 0.1, 0) }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-4">
                                    <strong>Total:</strong>
                                    <strong id="order-total">UGX {{ number_format($total + ($total * 0.1), 0) }}</strong>
                                    <input type="hidden" id="total-amount" name="total_amount"
                                        value="{{ $total + ($total * 0.1) }}">
                                </div>

                                {{-- <!-- Coupon Code -->
                                <div class="mb-3">
                                    <label for="coupon" class="form-label">Have a coupon?</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="coupon" name="coupon"
                                            placeholder="Enter coupon code">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="apply-coupon">Apply</button>
                                    </div>
                                </div> --}}

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms
                                            and conditions</a>*
                                    </label>
                                </div>

                                <!-- Add a hidden field for sales channel -->
                                <input type="hidden" name="sales_channel" value="online">
                                <!-- Add a hidden field for sales channel ID -->
                                <input type="hidden" name="sales_channel_id" value="1">

                                <button type="submit" class="btn btn-primary w-100">Place Order</button>

                                <div class="text-center mt-3">
                                    <a href="{{ route('cart.index') }}" class="text-decoration-none">
                                        <i class="bi bi-arrow-left"></i> Return to Cart
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <h6 class="mb-3">Guaranteed Safe Checkout</h6>
                            <div class="d-flex justify-content-between">
                                <i class="bi bi-shield-check" style="font-size: 2rem;"></i>
                                <i class="bi bi-lock" style="font-size: 2rem;"></i>
                                <i class="bi bi-credit-card" style="font-size: 2rem;"></i>
                                <i class="bi bi-truck" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Terms and Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>1. General</h6>
                    <p>These terms and conditions govern the purchase of products from our online store. By placing an
                        order, you agree to be bound by these terms.</p>

                    <h6>2. Orders and Payment</h6>
                    <p>All orders are subject to product availability. Payment must be made in full at the time of purchase.
                    </p>

                    <h6>3. Transport and Delivery</h6>
                    <p>Delivery times are estimates only. We are not responsible for delays caused by our carriers</p>

                    <h6>4. Returns and Refunds</h6>
                    <p>Please see our separate Returns Policy for details on returns, exchanges, and refunds.</p>

                    <h6>5. Privacy Policy</h6>
                    <p>Your personal information is handled in accordance with our Privacy Policy.</p>

                    <h6>6. Limitation of Liability</h6>
                    <p>Our liability is limited to the amount paid for the products ordered.</p>

                    <h6>7. Governing Law</h6>
                    <p>These terms are governed by the laws of Uganda.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cityInput = document.getElementById('shipping_city');
            const regionSelect = document.getElementById('shipping_region');
            const countrySelect = document.getElementById('shipping_country');

            if (!cityInput || !regionSelect) {
                console.log('City or region input not found!');
                return;
            }

            cityInput.addEventListener('change', function () {
                fetch('/get-region', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ city: this.value })
                })
                    .then(response => response.json())
                    .then(data => {
                        regionSelect.innerHTML = '<option value="">Select Region</option>';
                        if (data.region) {
                            regionSelect.innerHTML += `<option value="${data.region}" selected>${data.region}</option>`;
                        }
                    });

                fetch('/get-country', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ city: this.value })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.country) {
                            countrySelect.value = data.country;
                        }
                    });
            });

            const creditCardDetails = document.getElementById('credit-card-details');
            const bankTransferInstructions = document.getElementById('bank-transfer-instructions');

            document.querySelectorAll('input[name="payment"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    creditCardDetails.classList.add('d-none');
                    bankTransferInstructions.classList.add('d-none');

                    if (this.value === 'credit_card') {
                        creditCardDetails.classList.remove('d-none');
                    } else if (this.value === 'bank_transfer') {
                        bankTransferInstructions.classList.remove('d-none');
                    }
                });
            });

            // Shipping method price calculation
            const shippingCostEl = document.getElementById('shipping-cost');
            const orderTotalEl = document.getElementById('order-total');
            const totalAmountInput = document.getElementById('total-amount');
            const subtotal = {{ $total }};
            const tax = subtotal * 0.1;

            document.querySelectorAll('input[name="shipping_method"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    let shippingCost = 0;

                    if (this.value === 'express') {
                        shippingCost = 3000;
                        shippingCostEl.textContent = 'UGX 3,000';
                    } else if (this.value === 'overnight') {
                        shippingCost = 10000;
                        shippingCostEl.textContent = 'UGX 10,000';
                    } else {
                        shippingCostEl.textContent = 'Free';
                    }

                    const total = subtotal + tax + shippingCost;
                    orderTotalEl.textContent = 'UGX ' + total.toFixed(0);
                    totalAmountInput.value = total;
                });
            });

            // Form validation
            document.getElementById('checkout-form').addEventListener('submit', function (e) {
                const paymentMethod = document.querySelector('input[name="payment"]:checked').value;

                if (paymentMethod === 'credit_card') {
                    const cardNumber = document.getElementById('card_number').value;
                    const cardExpiry = document.getElementById('card_expiry').value;
                    const cardCVV = document.getElementById('card_cvv').value;
                    const cardName = document.getElementById('card_name').value;

                    if (!cardNumber || !cardExpiry || !cardCVV || !cardName) {
                        e.preventDefault();
                        alert('Please fill in all credit card details.');
                    }
                }

                const terms = document.getElementById('terms');
                if (!terms.checked) {
                    e.preventDefault();
                    alert('You must agree to the terms and conditions.');
                }
            });
        });
    </script>
@endsection