@extends('frontend.main_master')

@section('main')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<!-- Inner Banner -->
<div class="inner-banner inner-bg7">
    <div class="container">
        <div class="inner-title">
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><i class='bx bx-chevron-right'></i></li>
                <li>Check Out</li>
            </ul>
            <h3>Check Out</h3>
        </div>
    </div>
</div>
<!-- Inner Banner End -->

 
<section class="checkout-area pt-100 pb-70">
    <div class="container">

        <form method="post"
              action="{{ route('checkout.store') }}"
              class="stripe_form require-validation"
              data-cc-on-file="false"
              data-stripe-publishable-key="{{ env('STRIPE_KEY') }}">
            @csrf

            <div class="row">

                <!-- LEFT : BILLING DETAILS -->
                <div class="col-lg-8">
                    <div class="billing-details">
                        <h3 class="title">Billing Details</h3>

                        <div class="row">

                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                    <label>Country <span class="required">*</span></label>
                                    <div class="select-box">
                                        <select name="country" class="form-control">
                                            <option value="Congo">Congo</option>
                                            <option value="India">India</option>
                                            <option value="France">France</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}">
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}">
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ Auth::user()->phone }}">
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label>Address *</label>
                                    <input type="text" name="address" class="form-control" value="{{ Auth::user()->address }}">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- RIGHT : BOOKING SUMMARY -->
                <div class="col-lg-4">
                    <section class="checkout-area pb-70">
                        <div class="card-body">
                            <div class="billing-details">
                                <h3 class="title">Booking Summary</h3>
                                <hr>

                                <div style="display:flex">
                                    <img style="height:100px;width:120px;object-fit:cover"
                                         src="{{ (!empty($room->image)) ? asset($room->image) : asset('upload/default_avatar.jpg') }}">
                                    <div style="padding-left:10px">
                                        <a style="font-size:20px;font-weight:bold">{{ @$room->type->name }}</a>
                                        <p><b>{{ $room->price }} / Night</b></p>
                                    </div>
                                </div>

                                <br>

                                <table class="table">
                                    @php
                                        $subtotal = $room->price * $nights * $book_data['number_of_rooms'];
                                        $discount = ($room->discount/100) * $subtotal;
                                    @endphp
                                    <tr>
                                        <td>Total Night</td>
                                        <td class="text-end">{{ $nights }} Days</td>
                                    </tr>
                                    <tr>
                                        <td>Total Room</td>
                                        <td class="text-end">{{ $book_data['number_of_rooms'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Subtotal</td>
                                        <td class="text-end">${{ $subtotal }}</td>
                                    </tr>
                                    <tr>
                                        <td>Discount</td>
                                        <td class="text-end">${{ $discount }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td class="text-end">${{ $subtotal - $discount }}</td>
                                    </tr>
                                </table>

                            </div>
                        </div>
                    </section>
                </div>

                <!-- PAYMENT -->
                <div class="col-lg-8">
                    <div class="payment-box">
                        <p>
                            <input type="radio" name="payment_method" value="COD"> Cash On Delivery
                        </p>
                        <p>
                            <input type="radio" class="pay_method" name="payment_method" value="Stripe"> Stripe
                        </p>

                        <div id="stripe_pay" class="d-none">
                            <br>
                            <div class="form-group required">
                                <label>Name on Card</label>
                                <input class="form-control" type="text">
                            </div>

                            <div class="form-group required">
                                <label>Card Number</label>
                                <input class="form-control card-number" type="text">
                            </div>

                            <div class="row">
                                <div class="col-md-4 required">
                                    <label>CVC</label>
                                    <input class="form-control card-cvc" type="text">
                                </div>
                                <div class="col-md-4 required">
                                    <label>Month</label>
                                    <input class="form-control card-expiry-month" type="text">
                                </div>
                                <div class="col-md-4 required">
                                    <label>Year</label>
                                    <input class="form-control card-expiry-year" type="text">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="order-btn" id="myButton">
                            Place To Order
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</section>

<script src="https://js.stripe.com/v2/"></script>

<script>
   $(document).ready(function () {
      $('.pay_method').on('click', function () {
        $('#stripe_pay').removeClass('d-none');
      });
    });


      $(function() {
            var $form = $(".require-validation");
            $('form.require-validation').bind('submit', function(e) {

                  var pay_method = $('input[name="payment_method"]:checked').val();
                  if (pay_method == undefined){
                        alert('Please select a payment method');
                        return false;
                  }else if(pay_method == 'COD'){

                  }else{
                        document.getElementById('myButton').disabled = true;

                        var $form         = $(".require-validation"),
                                inputSelector = ['input[type=email]', 'input[type=password]',
                                      'input[type=text]', 'input[type=file]',
                                      'textarea'].join(', '),
                                $inputs       = $form.find('.required').find(inputSelector),
                                $errorMessage = $form.find('div.error'),
                                valid         = true;
                        $errorMessage.addClass('hide');

                        $('.has-error').removeClass('has-error');
                        $inputs.each(function(i, el) {
                              var $input = $(el);
                              if ($input.val() === '') {
                                    $input.parent().addClass('has-error');
                                    $errorMessage.removeClass('hide');
                                    e.preventDefault();
                              }
                        });

                        if (!$form.data('cc-on-file')) {

                              e.preventDefault();
                              Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                              Stripe.createToken({
                                    number: $('.card-number').val(),
                                    cvc: $('.card-cvc').val(),
                                    exp_month: $('.card-expiry-month').val(),
                                    exp_year: $('.card-expiry-year').val()
                              }, stripeResponseHandler);
                        }
                  }



            });



            function stripeResponseHandler(status, response) {
                  if (response.error) {

                        document.getElementById('myButton').disabled = false;

                        $('.error')
                                .removeClass('hide')
                                .find('.alert')
                                .text(response.error.message);
                  } else {

                        document.getElementById('myButton').disabled = true;
                        document.getElementById('myButton').value = 'Please Wait...';

                        // token contains id, last4, and card type
                        var token = response['id'];
                        // insert the token into the form so it gets submitted to the server
                        $form.find('input[type=text]').empty();
                        $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                        $form.get(0).submit();
                  }
            }

      });







</script>

@endsection
