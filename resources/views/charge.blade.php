<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Charge
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('charge.post') }}" method="post" id="payment-form">
                        @csrf
                        <div>
                            Product Name and Price and description goes here
                        </div>

                        <div class="w-1/2 form-row">
                            <label for="cardholder-name">Cardholder's Name</label>
                            <div>
                                <input type="text" id="cardholder-name" class="px-2 py-2 border">
                            </div>

                            <label for="card-element">
                                Credit or debit card
                            </label>
                            <div id="card-element">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>

                            <!-- Used to display form errors. -->
                            <div id="card-errors" role="alert"></div>
                        </div>

                        <button id="btnsubmit" class="bg-black text-white px-4 py-2 rounded">
                            <div class="spinner hidden" id="spinner"></div>
                            <span id="button-text">Pay now</span>
                        </button>
                        <div id="payment-message" class="hidden"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            /**
             * Create a Stripe client.
             */
            const stripe = Stripe("pk_test_51MWf28KfQxRkIXlXqkN0uipSjX7SdJ0gA4bwe23NJAfTwnwfVTNMLt0H3qzCV9oI3q240IFoxusnwKAbzDlLT0q100a6qWZDQg");

            /**
             * Create an instance of Elements.
             */
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            var style = {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            /**
             *  Create an instance of the card Element.
             */
            var card = elements.create('card', {style: style});
            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');
            // Handle real-time validation errors from the card Element.
            card.on('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            /**
             * Handle the form
             */
            var form = document.getElementById('payment-form');
            var cardHolderName = document.getElementById('cardholder-name');
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                const { paymentMethod, error } = await stripe.createPaymentMethod(
                    'card', card, {
                        billing_details: { name: cardHolderName.value }
                    }
                );
                if (error) {
                    // Inform the user if there was an error.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = error.message;
                } else {
                    // Send the token to your server.
                    // console.log({paymentMethod});
                    // console.log({card});
                    stripeTokenHandler(paymentMethod);
                }
            });

            /**
             * Submit the form with the token ID.
             */
            function stripeTokenHandler(paymentMethod) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'paymentMethod');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);
                // Submit the form
                form.submit();
            }
        </script>
    @endpush
</x-app-layout>
