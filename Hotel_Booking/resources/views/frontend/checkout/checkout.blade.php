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
              data-stripe-publishable-key="{{ config('services.stripe.key') }}">
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
													<option value="Sweeden">Sweeden</option>
													<option value="Italy">Italy</option>
													<option value="Egypt">Egypt</option>
													<option value="China">China</option>
													<option value="United Kingdom">United Kingdom</option>
													<option value="Germany">Germany</option>
													<option value="France">France</option>
													<option value="Japan">Japan</option>
												</select>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6">
										<div class="form-group">
											<label>Name <span class="required">*</span></label>
											<input type="text" name="name" class="form-control" value="{{Auth::user()->name}}">
											@if($errors->has('name')) 
                                             <div class="text-danger">{{$errors->first('name')}}</div>
											@endif
										</div>
									</div>

									<div class="col-lg-6 col-md-6">
										<div class="form-group">
											<label>Email <span class="required">*</span></label>
											<input type="email" name="email" class="form-control" value="{{Auth::user()->email}}">
											@if($errors->has('email')) 
                                             <div class="text-danger">{{$errors->first('email')}}</div>
											@endif
										</div>
									</div>

									<div class="col-lg-6 col-md-6">
										<div class="form-group">
											<label>Phone</label>
											<input type="text" name="phone" class="form-control" value="{{Auth::user()->phone}}">
											@if($errors->has('phone')) 
                                             <div class="text-danger">{{$errors->first('phone')}}</div>
											@endif
										</div>
									</div>

									<div class="col-lg-6 col-md-6">
										<div class="form-group">
											<label>Address <span class="required">*</span></label>
											<input type="text" name="address" class="form-control" value="{{Auth::user()->address}}">
											@if($errors->has('address')) 
                                             <div class="text-danger">{{$errors->first('address')}}</div>
											@endif
										</div>
									</div>

									<div class="col-lg-6 col-md-6">
										<div class="form-group">
											<label>State <span class="required">*</span></label>
											<input type="text" name="state" class="form-control">
											@if($errors->has('state')) 
                                             <div class="text-danger">{{$errors->first('state')}}</div>
											@endif
										</div>
									</div>

									<div class="col-lg-6 col-md-6">
										<div class="form-group">
											<label>Zip Code <span class="required">*</span></label>
											<input type="text" name="zip_code" class="form-control">
											@if($errors->has('zip_code')) 
                                             <div class="text-danger">{{$errors->first('zip_code')}}</div>
											@endif
										</div>
									</div>

									
									{{--<p>Session Value : {{ json_encode(session('book_date')) }}</p>--}}

									
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
/**
 * On garde la référence du formulaire accessible partout
 */
var $form = $('.require-validation');

$(document).ready(function () {

    /**
     * Afficher le formulaire Stripe quand on choisit Stripe
     */
    $('.pay_method').on('click', function () {
        $('#stripe_pay').removeClass('d-none');
    });

    /**
     * Intercepter la soumission du formulaire
     */
    $form.on('submit', function (e) {

        // Vérifier si un moyen de paiement est sélectionné
        var pay_method = $('input[name="payment_method"]:checked').val();

        if (!pay_method) {
            alert('Please select a payment method');
            e.preventDefault();
            return false;
        }

        // Cash On Delivery → on laisse Laravel gérer
        if (pay_method === 'COD') {
            return true;
        }

        /**
         * Stripe sélectionné
         */
        e.preventDefault(); // on bloque l'envoi classique
        $('#myButton').prop('disabled', true);

        // Initialisation de Stripe avec la clé publique
        Stripe.setPublishableKey($form.data('stripe-publishable-key'));

        // Création du token Stripe
        Stripe.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
        }, stripeResponseHandler);
    });
});

/**
 * Callback Stripe
 */
function stripeResponseHandler(status, response) {

    if (response.error) {

        // Erreur Stripe (carte refusée, infos invalides, etc.)
        $('#myButton').prop('disabled', false);
        alert(response.error.message);

    } else {

        // Token Stripe généré avec succès
        var token = response.id;

        // On ajoute le token au formulaire
        $('<input>')
            .attr({
                type: 'hidden',
                name: 'stripeToken',
                value: token
            })
            .appendTo($form);

        // On soumet le formulaire vers Laravel
        $form.get(0).submit();
    }
}





</script>


@endsection
